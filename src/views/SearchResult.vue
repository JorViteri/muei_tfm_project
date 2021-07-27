<template>
  <div id="home" class="pa-4">
    <v-container fluid>
      <h3 class="headline font-weight-medium">{{ header }} "{{ query }}"</h3>
      <v-col cols="12">
        <hr class="grey--text" />
        <h4 class="mb-3 mt-3"></h4>
        <div v-for="i in num_entries" :key="i" class="mb-5 ml-2">
          <v-skeleton-loader
            class="mx-auto"
            type="list-item-avatar-three-line"
            :loading="loading"
            tile
            large
          >
            <video-card
              :card="{ maxWidth: 900, maxHeight: 200 }"
              :video="videos[i - 1]"
            ></video-card>
          </v-skeleton-loader>
        </div>
      </v-col>
    </v-container>
  </div>
</template>

<script>
import videoCard from '@/components/ListThumbnail';

export default {
  name: 'Home',
  data() {
    return {
      header: '',
      query: this.$route.params.query,
      header_result: 'Search Results for the query: ',
      header_no_result: 'No Search Results.',
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
  watch: {
    '$route.params.query': function() {
      this.loading = true;
      this.videos = [];
      this.query = this.$route.params.query;
      this.searchQuery();
    }
  },
  methods: {
    searchQuery: function() {
      const axios = require('axios');
      axios
        .get('/api/search_vid.php', {
          params: { name: this.query }
        })
        .then((response) => response.data)
        .then((data) => {
          if (data.result_msg) {
            console.log('ERROR CON LA API');
            window.alert(data.result_msg);
          } else if (data.length > 0) {
            console.log('Se han obtenido los datos');
            this.header = this.header_result;
            console.log(data);
            this.video_data = data;
            this.num_entries = this.video_data.length;
            this.updateCards();
          } else {
            console.log('No hay resultados');
            this.header = this.header_no_result;
            this.num_entries = 0;
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
          processed: Boolean(element.processed),
          video_views: element.view_count
        };
        this.videos.push(video);
      });
      console.log(this.videos);
      this.loading = false;
    }
  },
  mounted() {
    console.log('Se hace la peticion a BD');
    this.searchQuery();
  }
};
</script>

<style lang="scss">
.card {
  background: #f9f9f9 !important;
}
</style>

48
