<template>
    <div class="page-content-padded">
        <!-- <aside class="sidebar-left">
            <sidebar-group title="Notifications" :view-all="true" :items="[]"></sidebar-group>
        </aside> -->
        
        <div  class="page-main">
            <h1 class="no-top">My Messages</h1>
            <add-text :userid="userid" v-if="composing" type="newMessage" @success="fetchThreads"></add-text>
            <ph-button size="large" @click.native="composing = true" v-else>New Message</ph-button>
            <div class="user-messages-container">
                <message-thread v-if="thread.last_message" v-for="thread in threads" :key="thread.id" :thread="thread"></message-thread>
            </div>
        </div>
<!--         
        <aside class="sidebar-right">
            <sidebar-group title="Followed" :view-all="true" :items="[]"></sidebar-group>
            <sidebar-group title="Favourites" :view-all="true" :items="[]"></sidebar-group>
        </aside> -->
    </div>
</template>

<script>
    import AddText from 'global/add-text/add-text';
    import SidebarGroup from 'global/sidebar-group';
    import MessageThread from './messages/message-thread';
    import PhButton from 'global/ph-button';
    import { mapState } from 'vuex';
    import store from 'store';

    export default {
        data () {
            return {
                composing: (this.$route.params.userid) ? true : false,
                userid: this.$route.params.userid
            }
        },
 
        computed: {
            ...mapState( 'messenger', {
                threads: state => state.threads
            })
        },
        created: function() {
            if(!this.$store.state.app.user.loggedin) {
                this.$router.push({path: '/login'});
            }
        },
        beforeRouteEnter (to, from, next) {
            store.dispatch('messenger/fetchThreads').then(() => {
              next();
            });
          },
        
        methods: {
            fetchThreads() {
                store.dispatch('messenger/fetchThreads').then(() => {
                  next();
                });
            }
        },
        components: {
            AddText,
            SidebarGroup,
            MessageThread,
            PhButton,
        }
    }
</script>

<style lang="scss" scoped>
    .user-messages-container {
        margin: 3em 0;
    }
</style>