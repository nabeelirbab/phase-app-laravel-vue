<template>
    <div class="p-item">
        <div class="p-item-image">
                <avatar
                    :size="130"
                    :src="post.user.avatar.files.thumb.url"
                    
                />
        </div>
        <div class="p-item-main">
        <input type="hidden" v-model="post.type" />

            <div class="p-post-text">
                <div v-if="postBody != ''" class="p-item-detail">
                    {{ postBody }}
                </div>
                <div class="p-post-text p-post-attachment" v-if="post.attachment">
                    <img
                            v-if="post.attachment"
                            :src="post.attachment.files.medium.url"
                            :alt="post.attachment.alt"
                            class="p-post-image"
                        />

                </div>
            </div>
            <div class="p-item-meta">
                <actions :actionable="post" :id="id" :actiontype='"post"' ></actions>
                <div class="p-item-time">
                    {{ moment(post.created_at).fromNow() }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Actions from 'global/actions/actions';
    import ActionMenu from 'global/actions/action-menu';
    import Avatar from 'global/avatar';

    export default {
        props: {
            post: {
                type: Object,
                required: true,
            },
          id: {
              type: Number,
              default: null,
          }
        },
        
        data () {
            return {
                moment: window.moment,
            }
        },
        created: function() {
            this.updateType();
        },
        computed: {
          postBody() {
              if(this.post.body){
                return this.post.body
              }else{
                  return '';
              }
          }
        },
        methods: {
            updateType() {
              this.post.type = 'post';
            },

        },
        components: {
            Actions,
            ActionMenu,
            Avatar,
        }
    }
</script>

<style lang="scss" scoped>
    .p-post-text {
        flex: 1;
        font-size: 14px;
        display: flex;
        align-items: flex-start;
        flex-direction: column;
        margin-bottom: 2em;
    }
    .p-post-attachment {
        margin-top: 8px;
    }
    .p-item-title {
        font-size: 19px;
    }
    .p-item-main {
        justify-content: flex-start;
    }
    .p-post-image {
        height: 120px;
        width: auto;
        margin-bottom: 1em;
    }
</style>
