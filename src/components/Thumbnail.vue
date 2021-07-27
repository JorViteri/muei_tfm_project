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
    :to="{ name: 'VideoVisualization', params: { id: video.id } }"
  >
    <v-img :src="video.thumb"></v-img>
    <v-row no-gutters>
      <v-col class="text-left">
        <v-card-title class="pl-2 pt-3 font-weight-bold">
          {{ video.title }}
        </v-card-title>
        <v-card-text class="pl-2 pt-3  text--primary">
          <div>Duration: {{ video.video_duration }}</div>
          <div>Upload date: {{ video.createdAt }}</div>
          <div>View count: {{ video.video_views }}</div>
        </v-card-text>
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
  }
};
</script>

<style></style>
