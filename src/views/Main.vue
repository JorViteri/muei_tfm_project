<template>
  <div id="home" class="pa-4">
    <v-container fluid>
      <h3 class="headline font-weight-medium">Recommended</h3>
      <v-row>
        <v-col
          cols="12"
          sm="6"
          md="4"
          lg="3"
          v-for="i in loading ? num_max : num_entries"
          :key="i"
          class="mx-xs-auto"
        >
          <v-skeleton-loader type="card-avatar" :loading="loading">
            <video-card
              :card="{ maxWidth: 350 }"
              :video="videos[i - 1]"
            ></video-card>
          </v-skeleton-loader>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import videoCard from '@/components/Thumbnail';

export default {
  name: 'Home',
  data() {
    return {
      loading: true,
      video_data: null,
      num_max: 12,
      num_entries: 12,
      videos: []
    };
  },
  components: {
    videoCard
  },
  methods: {
    randomSelect: function() {
      const axios = require('axios');
      axios
        .get('/api/random_search.php')
        .then((response) => response.data)
        .then((data) => {
          if (data.result_msg) {
            console.log('ERROR CON LA API');
            window.alert(data.result_msg);
          } else {
            this.video_data = data;
            this.num_entries = this.video_data.length;
            this.updateCards();
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
          video_views: element.view_count
        };
        this.videos.push(video);
      });
      console.log(this.videos);
      this.loading = false;
    }
  },
  created() {
    console.log('Se hace la peticion a BD');
    this.randomSelect();
  }
};
</script>

<style lang="scss">
.card {
  background: #f9f9f9 !important;
}
</style>

48
