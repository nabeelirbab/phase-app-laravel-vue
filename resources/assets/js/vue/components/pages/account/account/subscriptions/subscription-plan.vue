<template>
  <div class="subscription-plan" v-if="app.user.roles[0].name !== 'standard'">
    <div class="plan-info">
      <h3>{{ subscription.stripe_plan }}</h3>
      <p>
        <em>{{ status }}</em>
      </p>
      <!-- <p>
        {{ plan.description }}
      </p> -->
    </div>
    <div class="plan-price">
      <strong> {{ plan.currency == "gbp" ? '£' : '€' }} {{ (plan.unit_amount / 100).toFixed(2) }}</strong>
    </div>
    <div class="plan-subscribe">
      <ph-button v-if="button === 'start_trial'" size="medium" @click.native="subscribe" :loading="loading"
        :disabled="disabledbtn">
        Start Trial
      </ph-button>

      <ph-button v-else-if="button === 'cancel'" size="medium" @click.native="unsubscribe" :loading="loading"
        :disabled="disabledbtn">
        Cancel
      </ph-button>
      <ph-button v-else-if="button === 'resume'" size="medium" @click.native="resume" :loading="loading"
        :disabled="disabledbtn">
        Resume
      </ph-button>
      <ph-button v-else-if="button === 'restart'" size="medium" @click.native="restart" :loading="loading"
        :disabled="disabledbtn">
        Restart
      </ph-button>
      <ph-button v-else-if="button === 'add_card'" size="medium" @click.native="addCard" :loading="loading"
        :disabled="disabledbtn">
        Add Card
      </ph-button>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import Vue from 'vue'

export default {
  props: {
    plan: {
      type: Object,
      required: true,
    },
    subscription: {
      type: Object,
      required: false,
    },
  },
  data() {
    return {
      loading: false,
      localSubscription: this.subscription,
    }
  },
  mounted() {
    console.log("from sub plan");
  },
  computed: {
    ...mapState([
      'app',
    ]),
    status: function () {
      console.log("sp-subs", this.localSubscription);
      console.log("sp-plan", this.plan);
      if (this.localSubscription && this.localSubscription.stripe_status == "trialing" && this.localSubscription.trial_ends_at) {
        return `Free trial active. Renews ${this.ends_at}.`
      }

      // Previous Logic
      // if (! this.localSubscription && (this.app.user.is_on_trial && this.app.user.card_last_four)) {
      //   return `Free trial active. Renews ${this.ends_at}.`
      // }
      // if (! this.localSubscription && (this.app.user.is_on_trial && ! this.app.user.card_last_four)) {
      //   return `Free trial active. Trial ends ${this.ends_at}.`
      // }

      if (this.localSubscription && this.localSubscription.stripe_status == "active" && this.localSubscription.ends_at) {
        let s = this.localSubscription
        if (s.stripe_status === 'active') {
          // if (this.app.user.is_on_trial && this.app.user.on_grace_period) {
          //   return `Free trial cancelled but still active. Expires ${this.ends_at}.`
          // } else if (s.ends_at) {
          //   return `Cancelled but still active. Expires ${this.ends_at}.`
          // }

          return `Subscribed. Renews ${moment(this.localSubscription.ends_at).format('DD/MM/YYYY')}.`
        } else {
          return 'Expired.'
        }

        // Used when need
        // if (s.stripe_status === 'active' && s.trial_ends_at > moment()) {
        //     return 'Free trial cancelled but still active. Expires ' + this.ends_at + '.';
        // } else if (s.on_trial && s.renew) {
        //     return 'Free trial active. Renews ' + this.ends_at + '.';
        // } else if (!s.on_trial > new Date() && s.ends_at > new Date()) {
        //     return 'Cancelled but still active. Expires ' + this.ends_at + '.';
        // } else if (!s.on_trial && s.renew) {
        //     return 'Active. Renews ' + this.ends_at + '.';
        // } else if (s.stripe_status === 'canceled') {
        //     return 'Expired.';
        // }
      }
      if (this.localSubscription && this.localSubscription.stripe_status == "canceled") {
        return 'Your current Subsciption is canceled, please upgrade your account to Artist Pro'
      }

      return 'Not subscribed. Free trial available.'
    },
    ends_at: function () {
      if (this.localSubscription.stripe_status == 'active') {
        return moment(this.localSubscription.ends_at).format('DD/MM/YYYY')
      } else if (this.app.user.trial_ends_at) {
        return moment(this.localSubscription.trial_ends_at).format('DD/MM/YYYY')
      }

      return ''
    },
    button: function () {
      // if (!this.localSubscription && (this.app.user.is_on_trial && this.app.user.cast_last_four)) {
      //   return 'cancel'
      // }
      // if (!this.localSubscription && (this.app.user.is_on_trial && !this.app.user.cast_last_four)) {
      //   return 'add_card'
      // }
      console.log(this.loadSubscription);
      if (this.localSubscription) {
        let s = this.localSubscription
        if ((s.stripe_status === 'active' || s.stripe_status === 'trialing') && (s.ends_at || s.trial_ends_at)) {
          return 'cancel'
        } else if (s.stripe_status === 'active' && s.ends_at) {
          return 'resume'
        } else if (s.expired) {
          return 'restart'
        }
      } else {
        return 'start_trial'
      }
    },
    disabledbtn: function () {
      return (this.app.user.roles[0].name !== 'artist' && this.app.user.roles[0].name !== 'pro')
    }
  },
  methods: {
    subscribe() {
      if (this.disabledbtn) {
        Vue.notify({
          group: 'main',
          type: 'error',
          title: 'You cannot subscribe.',
        })
        return false
      }
      this.loading = true
      axios.get('/api/account/subscription/plan/' + this.plan.id + '/subscribe').then(response => {
        this.loading = false
        if (response.data.success) {
          this.localSubscription = response.data.subscription
          Vue.notify({
            group: 'main',
            type: 'success',
            title: '<img src="/img/confirm.gif" alt="success" width="60%">',
          })
        } else {
          Vue.notify({
            group: 'main',
            type: 'error',
            title: response.data.message,
          })
        }
      }).finally(response => {
        this.loading = false
      })
    },
    unsubscribe() {
      this.loading = true
      axios.get('/api/account/subscription/plan/' + this.subscription.stripe_id + '/unsubscribe').then(response => {
        this.loading = false
        if (response.data.success) {
          this.unsubscriptionSuccessful(response.data.subscription)
          Vue.notify({
            group: 'main',
            type: 'success',
            title: '<img src="/img/confirm.gif" alt="success" width="60%">',
          })
        }
      }).finally(response => {
        this.loading = false
      })
    },
    unsubscriptionSuccessful(subscription) {
      this.localSubscription = subscription
    },
    resume() {
      if (this.disabledbtn) {
        Vue.notify({
          group: 'main',
          type: 'error',
          title: 'You cannot resume.',
        })
        return false
      }
      this.loading = true
      axios.get('/api/account/subscription/plan/' + this.plan.id + '/resume').then(response => {
        this.loading = false
        if (response.data.success) {
          this.resumeSuccessful(response.data.subscription)
          Vue.notify({
            group: 'main',
            type: 'success',
            title: '<img src="/img/confirm.gif" alt="success" width="60%">',
          })
        }
      }).finally(response => {
        this.loading = false
      })
    },
    resumeSuccessful(subscription) {
      this.localSubscription = subscription
    },
    restart() {
      this.loading = true
      axios.get('/api/account/subscription/plan/' + this.plan.id + '/restart').then(response => {
        this.loading = false
        if (response.data.success) {
          this.restartSuccessful(response.data.subscription)
        }
      }).finally(response => {
        this.loading = false
      })
    },
    restartSuccessful(subscription) {
      this.localSubscription = subscription
    },
    addCard() {
      document.getElementById('billing').scrollIntoView(true)
    },
  },
  components: {},
}
</script>

<style lang="scss" scoped>
.subscription-plan {
  width: 100%;
  box-sizing: border-box;
  background: white;
  padding: 15px;
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;

  &:last-child {
    margin-bottom: 0;
  }

  h3 {
    margin: 0;
  }

  p {
    margin: 10px 0;
  }
}

.plan-info {
  flex: 1;
}

.plan-price {
  width: 80px;
  display: flex;
  align-items: center;
  font-size: 22px;
}

.plan-subscribe {
  width: 180px;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: center;
}


</style>
