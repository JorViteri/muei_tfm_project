<template>
  <v-container>
    <v-row class="ma-15 pt-6" no-gutters justify="center" align="center">
      <v-col cols="12" sm="4">
        <v-img
          contain
          class="imageHeader"
          lazy-src="../assets/logo.png"
          src="../assets/logo.png"
        ></v-img>
      </v-col>
    </v-row>
    <v-row class="ma-2 pt-6" no-gutters justify="center" align="center">
      <v-col cols="12" sm="4"
        ><DragAndDrop
          :videoName="videoName"
          :videoDescription="videoDescription"
      /></v-col>
    </v-row>
    <v-row class="ma-15 pt-6" justify="center" align="center">
      <v-col cols="6">
        <span>Introduzca el nombre del vídeo de 70 caracteres máximo:</span>
        <br />
        <v-text-field
          v-model="videoName"
          outlined
          counter="70"
          placeholder=""
        ></v-text-field>
      </v-col>
    </v-row>
    <v-row class="ma-15 pt-6" justify="center" align="center">
      <v-col cols="8"
        ><span>Introduza una descripción de 255 caracteres máximo:</span>
        <br />
        <v-textarea
          v-model="videoDescription"
          outlined
          counter="255"
          auto-grow
          placeholder=""
        ></v-textarea
      ></v-col>
    </v-row>
  </v-container>
</template>

<script>
import DragAndDrop from '@/components/DragAndDrop.vue';

export default {
  components: { DragAndDrop },
  data() {
    return {
      videoDescription: '',
      videoName: ''
    };
  }
};
</script>

<style>
#logo_flex {
  align-self: flex-start;
}
#drag_flex {
  align-self: flex-start;
}

.container {
  margin: 0 auto;
  min-height: 100vh;
  justify-content: center;
  align-items: center;
  text-align: center;
}
.imageHeader {
  max-width: 800px;
  height: 300px;
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
