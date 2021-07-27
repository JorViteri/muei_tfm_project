<template>
  <div id="file-drag-drop">
    <form
      ref="fileform"
      @dragover="dragover"
      @dragleave="dragleave"
      @drop="drop"
    >
      <input
        type="file"
        id="assetsFieldHandle"
        class=".d-none"
        v-show="false"
        @change="directInput"
      />
      <label for="assetsFieldHandle" class="block cursor-pointer">
        <div class="inputText">
          Drop the video file in here or
          <span class="text-decoration-underline">click here</span> to upload
          the file
        </div>
      </label>

      <div
        v-for="(file, key) in files"
        class="file-listing"
        v-bind:key="(file, key)"
        wrap
      >
        <span class="preview">{{ file.name }}</span>
        <div class="remove-container">
          <a class="remove" v-on:click="removeFile(key)">Remove</a>
        </div>
      </div>
    </form>

    <v-progress-linear
      name="progbar"
      max="100"
      class="mt-6"
      :value="uploadPercentage"
    ></v-progress-linear>
    <v-btn
      class="ma-2 submit-button"
      v-on:click="submitFiles()"
      v-show="files.length > 0"
      >Submit</v-btn
    >
  </div>
</template>

<script>
export default {
  data() {
    return {
      dragAndDropCapable: false,
      uploadPercentage: 0,
      files: new Array()
    };
  },
  props: {
    videoName: {
      type: String,
      required: false
    },
    videoDescription: {
      type: String,
      required: false
    }
  },
  methods: {
    determineDragAndDropCapable: function() {
      var div = document.createElement('div');
      return (
        ('draggable' in div || ('ondragstart' in div && 'ondrop' in div)) &&
        'FormData' in window &&
        'FileReader' in window
      );
    },
    removeFile: function(key) {
      console.log(key);
      console.log(this.files);
      var newFileList = Array.from(this.files);
      console.log(newFileList);
      newFileList.splice(key, 1);
      this.files.splice(key, 1);
    },

    directInput: function(event) {
      console.log('Subiendo con INPUT', event.target.files.item(0));
      this.files = [];
      this.files.push(event.target.files.item(0));
    },

    isEmptyOrSpaces: function(str) {
      return str === null || str.match(/^ *$/) !== null;
    },

    submitFiles: function() {
      if (this.videoDescription.length > 255 || this.videoName.length > 70) {
        window.alert('The video name or the description is too long');
        return;
      }
      let description = this.videoDescription;
      if (this.isEmptyOrSpaces(this.videoDescription)) {
        description = 'NO DESCIPTION PROVIDED';
        console.log(description);
      }
      const axios = require('axios');

      let formData = new FormData();

      let file = this.files[0];

      console.log('Ext: ', file.name.split('.').pop());
      console.log(
        'Tipo del archivo ',
        'video/'.concat(file.name.split('.').pop())
      );
      console.log('Nombre del archivo', file.name);

      formData.append('file', file);
      formData.append('type', 'video/'.concat(file.name.split('.').pop()));
      formData.append('name', file.name);
      formData.append('description', description);
      if (!this.isEmptyOrSpaces(this.videoName)) {
        formData.append('opt_name', this.videoName);
      }

      axios
        .post('/api/ajax_upload.php', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          onUploadProgress: function(progressEvent) {
            this.uploadPercentage = parseInt(
              Math.round((progressEvent.loaded * 100) / progressEvent.total)
            );
            console.log(this.uploadPercentage);
          }.bind(this)
        })
        .then((response) => response.data)
        .then((data) => {
          console.log(data);
          console.log(data.result_msg);
          window.alert(data.result_msg);
        })
        .catch(function() {
          //Esto es para errores de Axios o no controlados
          console.log('FAILURE!!');
        });
      this.removeFile(0);
    },

    onFileSelected: function(event) {
      for (let i = 0; i < event.dataTransfer.files.length; i++) {
        this.files.push(event.dataTransfer.files[i]);
      }
    },
    dragover: function(event) {
      event.preventDefault();
      // Add some visual fluff to show the user can drop its files
      if (!event.currentTarget.classList.contains('bg-green-300')) {
        event.currentTarget.classList.remove('bg-gray-100');
        event.currentTarget.classList.add('bg-green-300');
      }
    },
    dragleave: function(event) {
      // Clean up
      event.currentTarget.classList.add('bg-gray-100');
      event.currentTarget.classList.remove('bg-green-300');
    },
    drop: function(event) {
      event.preventDefault();
      //this.files = Array.from(event.dataTransfer.files);
      this.files = [];
      this.files.push(event.dataTransfer.files.item(0));
      //this.onChange(); // Trigger the onChange event manually
      // Clean up
      event.currentTarget.classList.add('bg-gray-100');
      event.currentTarget.classList.remove('bg-green-300');
    }
  }
};
</script>

<style>
form {
  display: block;
  background: #ccc;
  margin: auto;
  margin-top: 40px;
  text-align: center;
  border-radius: 4px;
}

div.inputText {
  padding: 20px;
}

div.file-listing {
  width: auto;
  margin: auto;
  padding: 10px;
  border-bottom: 1px solid #ddd;
}

div.file-listing img {
  height: 100px;
}
div.remove-container {
  text-align: center;
}

div.remove-container a {
  color: red;
  cursor: pointer;
}

a.submit-button {
  display: block;
  margin: auto;
  text-align: center;
  width: auto;
  padding: 10px;
  text-transform: uppercase;
  background-color: #ccc;
  color: white;
  font-weight: bold;
  margin-top: 20px;
}

progress {
  width: 400px;
  margin: auto;
  display: block;
  margin-top: 20px;
  margin-bottom: 20px;
}
</style>
