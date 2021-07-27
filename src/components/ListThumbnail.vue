<template>
  <v-card
    class="content-bg card mx-auto"
    :max-width="card.maxWidth"
    :max-height="card.maxHeight"
    flat
    elevation="1"
    tile
    router
    contain
    :disabled="!video.processed"
    :to="{ name: 'VideoVisualization', params: { id: video.id } }"
  >
    <v-row no-gutters>
      <v-col class="mx-auto" cols="3" sm="3" md="5" lg="5">
        <v-img
          class="align-center"
          :max-width="card.maxWidth"
          :max-height="card.maxHeight"
          :min-width="card.minWidth"
          :min-height="card.minHeight"
          :src="video.thumb"
        ></v-img>
      </v-col>
      <v-col class="text-left">
        <div class="ml-2">
          <v-card-title class="pl-2 pt-3 subtitle-1 font-weight-bold">
            {{ video.title }}
          </v-card-title>
          <v-card-text class="pl-2 pt-n8 text--primary">
            <div>Duration: {{ video.video_duration }}</div>
            <div>Upload date: {{ video.createdAt }}</div>
            <div>View count: {{ video.video_views }}</div>
          </v-card-text>
          <v-card-subtitle class="red--text" v-if="!video.processed"
            >Video not processed</v-card-subtitle
          >
        </div>
      </v-col>
    </v-row>
  </v-card>
</template>

<script>
export default {
  props: {
    video: {
      type: Object,
      required: true
    },
    card: Object
  },
  data() {
    return {
      visibility: false
    };
  }
};
</script>

<style></style>
