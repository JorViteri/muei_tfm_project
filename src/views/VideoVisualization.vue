<template>
  <v-container fluid>
    <v-row no-gutters justify="center">
      <v-col cols="12" sm="12" md="8" lg="8">
        <v-skeleton-loader
          type="card-avatar, article"
          :loading="loading"
          tile
          large
        >
          <video-player
            :license-server="licenseServer"
            :manifest-url="manifestUrl"
            :poster-url="posterUrl"
            :getCurrentBandwidth="getCurrentBandwidth"
            v-on:get-current-bandwidth="updateBandwidthGraph($event)"
            v-on:video-is-buffering="videoIsBuffering($event)"
          />
          <v-card flat tile class="card">
            <v-card-title class="pl-0 pb-0">{{ video_title }}</v-card-title>
            <div class="d-flex flex-wrap justify-space-between" id="btns">
              <v-card-subtitle
                class="pl-0 pt-0 pb-0 subtitle-1"
                style="line-height: 2.4em;"
              >
                View count: {{ video_views
                }}<v-icon>mdi-circle-small</v-icon>Upload date: {{ video_date }}
              </v-card-subtitle>
            </div>
            <hr class="grey--text" />
            <p class="font-weight-bold text-left">
              Description:
            </p>
            <p class="text-left">
              {{ videoDescription }}
            </p>
          </v-card>
        </v-skeleton-loader>
      </v-col>

      <v-col cols="12" sm="12" md="4" lg="4">
        <hr class="grey--text align-center" />
        <v-btn v-on:click="isHidden = !isHidden"
          >Show the Bandwidth Graph below</v-btn
        >
        <BandwidthChart
          class="align-center"
          v-show="!isHidden"
          :currentBandwidth="currentBandwidth"
          :update="update"
        />

        <hr class="grey--text" />
        <h4 class="mb-3 mt-3" v-if="num_entries > 0">Other Videos</h4>
        <h4 class="mb-3 mt-3" v-else>No other videos available</h4>
        <div v-for="i in num_entries" :key="i" class="mb-5 ml-2">
          <v-skeleton-loader
            class="mx-auto"
            type="list-item-avatar-three-line"
            :loading="loading"
            tile
            large
          >
            <video-card
              :card="{ maxWidth: 500, maxHeight: 200 }"
              :video="videos[i - 1]"
            ></video-card>
          </v-skeleton-loader>
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import VideoPlayer from '@/components/VideoPlayer.vue';
import videoCard from '@/components/ListThumbnail';
import BandwidthChart from '@/components/BandwidthChart.vue';

export default {
  components: { VideoPlayer, videoCard, BandwidthChart },
  data() {
    return {
      video: {
        url: '',
        thumb: '',
        title: '',
        views: '',
        createdAt: ''
      },
      video_data: null,
      loading: true,
      licenseServer: 'https://widevine-proxy.appspot.com/proxy',
      manifestUrl: '',
      posterUrl: '',
      video_title: '',
      video_views: 0,
      video_date: '',
      videos: [],
      num_entries: 12,
      num_max: 12,
      isHidden: true,
      intervalTime: 5000,
      intervalid: '',
      isBuffering: false,
      currentBandwidth: 0,
      getCurrentBandwidth: false,
      videoDescription: '',
      update: false
    };
  },
  //Esto permite que se pueda acceder a un video de los que aparecen listados
  watch: {
    '$route.params.id': function() {
      this.loading = true;
      this.videos = [];
      this.isHidden = true;
      this.clearGraph = !this.clearGraph;
      this.intervalid = '';
      this.isBuffering = false;
      this.currentBandwidth = -1;
      this.update = !this.update;
      this.getCurrentBandwidth = false;
      this.posterUrl = '';
      this.manifestUrl = '';
      this.selectId();
      this.randomSelect();
    }
  },
  methods: {
    selectId: function() {
      const axios = require('axios');
      console.log(this.$route.params.id);
      axios
        .get('/api/video_by_id.php', { params: { id: this.$route.params.id } })
        .then((response) => response.data)
        .then((data) => {
          if (data.result_msg) {
            console.log('ERROR CON LA API');
            window.alert(data.result_msg);
          } else {
            console.log('Los datos');
            console.log(data);
            console.log(data.length);
            if (data.length == 0) {
              this.$router.push({ name: 'Main' });
            }
            this.video_data = data;
            this.updateVideo();
          }
        })
        .catch(function() {
          console.log('FAILURE!!');
        });
    },
    updateVideo: function() {
      console.log('Se actualizan el contenido del video');
      this.video_data.forEach((element) => {
        console.log(element.processed);
        if (element.processed == 0) {
          this.$router.push({ name: 'Main' });
        }
        let tmp = element.thumbnail_path;
        let mpd = element.mpd_path;
        console.log('COSA: ', mpd);
        console.log('COSA: ', tmp);
        this.posterUrl = element.thumbnail_path;
        this.manifestUrl = element.mpd_path;
        this.video_date = element.upload_date;
        this.video_title = element.video_name;
        this.video_views = element.view_count.toString();
        this.videoDescription = element.video_description;
      });
    },
    randomSelect: function() {
      const axios = require('axios');
      axios
        .get('/api/random_search.php', {
          params: { id: this.$route.params.id }
        })
        .then((response) => response.data)
        .then((data) => {
          if (data.result_msg) {
            console.log('ERROR CON LA API');
            window.alert(data.result_msg);
          } else {
            this.video_data = data;
            this.num_entries = this.video_data.length;
            if (this.num_entries > 0) {
              this.updateCards();
            }
            this.loading = false;
          }
        })
        .catch(function() {
          console.log('FAILURE!!');
        });
    },
    updateCards: function() {
      let video = {};
      console.log('Se actualizan los thumbnails');
      this.video_data.forEach((element) => {
        video = {
          url: element.mpd_path,
          thumb: element.thumbnail_path,
          title: element.video_name,
          video_duration: element.video_duration,
          createdAt: element.upload_date,
          id: element.video_id.toString(),
          processed: element.processed,
          video_views: element.view_count.toString()
        };
        this.videos.push(video);
      });
      console.log(this.videos);
    },
    updateCurrentBandwidt: function() {
      console.log('Desde la vista, lanzamos el Watch del VideoPlayer');
      this.getCurrentBandwidth = !this.getCurrentBandwidth;
    },
    updateBandwidthGraph: function(currentBandwidth) {
      console.log('Desde la vista, lanzamos el Watch del BandwidthGraph');
      console.log(currentBandwidth);
      this.currentBandwidth = currentBandwidth; //TODO no se actualiza al repetir valores, porque esto únicamente funciona si el valor cambia respecto al anterior!!
      this.update = !this.update;
    },
    videoIsBuffering: function(value) {
      console.log('isBuffering set to true');
      this.isBuffering = value;
    },
    graphLoop: function() {
      this.intervalid = setInterval(() => {
        if (this.isBuffering) {
          console.log('Se esta en buffering');
          this.updateCurrentBandwidt();
        } else {
          console.log('Aun no se esta en buffering');
        }
      }, this.intervalTime);
    }
  },
  mounted() {
    this.selectId();
    this.randomSelect();
    this.graphLoop();
  },
  beforeDestroy() {
    clearInterval(this.intervalid);
  }
};
</script>

<style>
.container {
  margin: 0 auto;
  min-height: 100vh;
  justify-content: center;
  align-items: center;
  text-align: center;
}

.mx-auto {
  margin-left: auto;
  margin-right: auto;
}

.shadow-lg {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
    0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.max-w-full {
  max-width: 100%;
}

.w-full {
  width: 100%;
}

.h-full {
  height: 100%;
}
</style>
