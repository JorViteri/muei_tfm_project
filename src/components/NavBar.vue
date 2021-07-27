<template>
  <nav>
    <v-app-bar app clipped-left color="primary" dark>
      <div class="d-flex align-center">
        <v-img
          alt="Vuetify Logo"
          class="shrink mr-2"
          contain
          src="https://cdn.vuetifyjs.com/images/logos/vuetify-logo-dark.png"
          transition="scale-transition"
          width="40"
        />
        <v-img
          alt="Vuetify Name"
          class="shrink mt-1 hidden-sm-and-down"
          contain
          min-width="100"
          src="https://cdn.vuetifyjs.com/images/logos/vuetify-name-dark.png"
          width="100"
        />
      </div>
      <v-spacer></v-spacer>
      <v-text-field
        flat
        hide-details
        append-icon="mdi-magnify"
        placeholder="Search"
        outlined
        dense
        v-model="searchText"
        @click:append="search"
        @keydown.enter="search"
      ></v-text-field>
      <v-spacer></v-spacer>
    </v-app-bar>

    <v-navigation-drawer clipped permanent expand-on-hover app class="indigo">
      <v-list>
        <v-list-item
          v-for="link in links"
          :key="link.text"
          router
          :to="link.route"
        >
          <v-list-item-action>
            <v-icon>{{ link.icon }}</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <span class="mr-2">{{ link.text }}</span>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
  </nav>
</template>

<script>
export default {
  data() {
    return {
      searchText: '',
      links: [
        {
          icon: 'mdi-home',
          text: 'Inicio',
          route: '/'
        },
        {
          icon: 'mdi-video-vintage',
          text: 'Live Streaming',
          route: '/streaming'
        },
        {
          icon: 'mdi-video',
          text: 'Video Bandwidth',
          route: '/video_bandwidth'
        },
        {
          icon: 'mdi-file-upload',
          text: 'Upload Video',
          route: '/upload_video'
        }
      ]
    };
  },
  methods: {
    search() {
      if (!this.searchText) return;
      let path = this.$route.fullPath;
      let new_path = '/search_result/'.concat(this.searchText);
      if (path != new_path) {
        let tmp = this.searchText;
        this.searchText = '';
        this.$router.push({
          name: 'SearchResult',
          params: { query: tmp }
        });
      } else {
        this.searchText = '';
        window.location.reload();
      }
    }
  },
  mounted() {
    this.drawer = this.$vuetify.breakpoint.mdAndDown ? false : true;
    this.drawer = this.$route.name === 'Watch' ? false : this.drawer;
  }
};
</script>
