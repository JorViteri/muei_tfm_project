import Vue from 'vue';
import VueRouter from 'vue-router';
import Main from '../views/Main.vue';
import VideoVisualization from '../views/VideoVisualization.vue';
import StreamingView from '../views/StreamingView.vue';
import VideoBandwidth from '../views/VideoBandwidth.vue';
import UploadVideo from '../views/UploadVideo.vue';
import SearchResult from '../views/SearchResult.vue';

Vue.use(VueRouter);

const routes = [
  {
    path: '/',
    name: 'Main',
    component: Main
  },
  {
    path: '/video_visualization/:id',
    name: 'VideoVisualization',
    component: VideoVisualization
  },
  {
    path: '/streaming',
    name: 'StreamingView',
    component: StreamingView
  },
  {
    path: '/video_bandwidth',
    name: 'VideoBandwidth',
    component: VideoBandwidth
  },
  {
    path: '/upload_video',
    name: 'UploadVideo',
    component: UploadVideo
  },
  {
    path: '/search_result/:query',
    name: 'SearchResult',
    component: SearchResult
  }
];

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
});

export default router;
