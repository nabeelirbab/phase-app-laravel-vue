<template>
  <div>
    <!-- MAIN CONTENT -->
    <!--        <nav class="sub-nav">-->
    <!--            <ul>-->
    <!--                <li class="active">Songs</li>-->
    <!--                <li>Albums</li>-->
    <!--                <li>Artists</li>-->
    <!--                <li>EP/LPs</li>-->
    <!--                <li>Genres</li>-->
    <!--            </ul>-->
    <!--        </nav>-->
    <div v-if="loaded">
      <div v-if="myMusic.length">
        <div v-for="(items, index) in myMusic" style="margin-bottom: 20px;" :key="index">
          <div v-if="items.length > 1">
            <my-music-release :item="items" :background="index % 2" />
          </div>
          <div v-else>
            <my-music-track v-for="item in items" :item="item" :key="item.id" :background="index % 2" />
          </div>
        </div>
      </div>
      <div v-else>
        <p style="text-align: center;">
          You haven’t purchased any music yet…perhaps <router-link to="/discover">discover</router-link> some?
        </p>
      </div>
    </div>

    <!-- <spinner style="margin: 5em auto;" :animation-duration="1000" :size="60" color="black" v-else /> -->
    <div class="phase-loading widget-center" v-else>
      <img src="/img/phase-loading.gif" alt="" srcset="">
    </div>
  </div>
</template>

<script>
import SidebarGroup from 'global/sidebar-group';
import { HalfCircleSpinner } from 'epic-spinners';
import { UserEvents } from "events";
import Item from '../../../global/items/item'
import MyMusicRelease from './my-music-release'
import MyMusicTrack from './my-music-track'

export default {
  props: {
    user: {
      type: Object,
      required: true,
    }
  },
  data() {
    return {
      loaded: false,
      myMusic: null,
    }
  },
  mounted: function () {
    this.fetchMyMusic()
    UserEvents.$emit('updateTitle', 'My Music')
  },
  methods: {
    fetchMyMusic() {
      this.loaded = false;
      axios.get('/api/mymusic/').then((response) => {
        this.myMusic = response.data;
        this.loaded = true;
      });
    }
  },
  components: {
    Item,
    SidebarGroup,
    MyMusicRelease,
    MyMusicTrack,
    'spinner': HalfCircleSpinner,
  }
}
</script>

<style lang="scss" scoped></style>
