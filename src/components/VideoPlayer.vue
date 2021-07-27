<template>
  <div ref="videoContainer">
    <v-responsive>
      <video
        id="video"
        ref="videoPlayer"
        class="w-full h-full"
        :poster="posterUrl"
      ></video>
    </v-responsive>
  </div>
</template>

<script>
export default {
  data() {
    return {
      dataPlayer: null,
      toEmit: true
    };
  },
  props: {
    manifestUrl: {
      type: String,
      required: true
    },
    licenseServer: {
      type: String,
      required: true
    },
    posterUrl: {
      type: String,
      required: false,
      default: ''
    },
    getCurrentBandwidth: {
      type: Boolean,
      default: false
    }
  },
  mounted() {
    this.toEmit = true;
    const shaka = require('shaka-player/dist/shaka-player.ui.js');
    const player = new shaka.Player(this.$refs.videoPlayer);
    const eventManager = new shaka.util.EventManager();
    const ui = new shaka.ui.Overlay(
      player,
      this.$refs.videoContainer,
      this.$refs.videoPlayer
    );

    ui.getControls();

    console.log(Object.keys(shaka.ui));

    player.configure({
      drm: {
        servers: { 'com.widevine.alpha': this.licenseServer }
      }
    });

    player
      .load(this.manifestUrl)
      .then(() => {
        console.log('The video has now been loaded!');
        this.dataPlayer = player;
      })
      .catch(this.onError);

    eventManager.listen(player, 'buffering', () => {
      if (this.toEmit) {
        console.log('EMITING BUFFERING BEGGINS');
        this.$emit('video-is-buffering', true);
      }
    });

    player.addEventListener('adaptation', function() {
      //TODO no recuerdo la razon de esto
      let tracks = player.getTracks(); // all tracks
      tracks.forEach(function(t) {
        if (t.active) {
          // an active track, that is, in use by the player right now
          console.log('ACTIVE:', t.type, t);
        }
      });
    });
  },

  methods: {
    onError(error) {
      console.error('Error code', error.code, 'object', error);
    },

    getEstimatedWidth() {
      let mbps = 0;
      console.log('Banda ancha estimada en tiempo real:');
      console.log(this.dataPlayer.getStats().estimatedBandwidth);
      mbps =
        parseFloat(this.dataPlayer.getStats().estimatedBandwidth) / 1000000;
      mbps = parseFloat(Number.parseFloat(mbps).toPrecision(4));
      console.log(mbps);
      return mbps;
    }
  },
  watch: {
    manifestUrl: function() {
      //Esto se hace para iniciar el proceso una vez se cargue otro video
      this.getCurrentBandwidth = false;
    },
    getCurrentBandwidth: function() {
      console.log('Se lanza el watch del  VideoPlayer');
      this.$emit('get-current-bandwidth', this.getEstimatedWidth());
    }
  },
  destroyed() {
    this.toEmit = false;
  }
};
</script>

<style>
@import '../../node_modules/shaka-player/dist/controls.css'; /* Shaka player CSS import */
</style>
