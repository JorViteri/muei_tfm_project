<template>
  <v-container>
    <v-row class="ma-15 pt-6" justify="center" align="center">
      <v-col cols="12" sm="8">
        <video-player
          :license-server="licenseServer"
          :manifest-url="manifestUrl"
          :poster-url="posterUrl"
          :getCurrentBandwidth="getCurrentBandwidth"
          v-on:get-current-bandwidth="updateBandwidthGraph($event)"
          v-on:video-is-buffering="videoIsBuffering($event)"
        />
      </v-col>
      <v-col cols="12" sm="4"
        ><BandwidthChart :currentBandwidth="currentBandwidth" :update="update"
      /></v-col>
    </v-row>
  </v-container>
</template>

<script>
import VideoPlayer from '@/components/VideoPlayer.vue';
import BandwidthChart from '@/components/BandwidthChart.vue';

export default {
  name: 'BandWidthView',
  components: { BandwidthChart, VideoPlayer },
  data() {
    return {
      intervalTime: 5000,
      intervalid: '',
      isBuffering: false,
      currentBandwidth: 0,
      getCurrentBandwidth: false,
      update: false,
      licenseServer: 'https://widevine-proxy.appspot.com/proxy',
      manifestUrl: 'https://dash.akamaized.net/akamai/bbb_30fps/bbb_30fps.mpd',
      posterUrl:
        'https://www.iamag.co/wp-content/uploads/2013/09/making-star-trek-into-darkness-title-animation.jpg'
    };
  },
  methods: {
    updateCurrentBandwidt: function() {
      console.log('Desde la vista, lanzamos el Watch del VideoPlayer');
      this.getCurrentBandwidth = !this.getCurrentBandwidth;
    },
    updateBandwidthGraph: function(currentBandwidth) {
      console.log('Desde la vista, lanzamos el Watch del BandwidthGraph');
      console.log(currentBandwidth);
      this.currentBandwidth = currentBandwidth;
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
    this.graphLoop();
  },
  destroyed() {
    clearInterval(this.intervalid);
  }
};
</script>

<style>
#flex-chart {
  max-width: 800px;
  height: 12px;
}

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
