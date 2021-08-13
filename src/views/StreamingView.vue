<template>
  <v-container fluid>
    <div class="media_container">
      <v-row no-gutters justify="center"
        ><v-col cols="12" sm="12" md="8" lg="8"
          ><v-responsive
            ><video
              id="gum"
              class="w-full h-full"
              playsinline
              autoplay
              muted
              v-show="!isShown"
            ></video
          ></v-responsive>
          <v-responsive
            ><video
              ref="recorded"
              v-show="isShown"
              class="w-full h-full"
              playsinline
              loop
            ></video></v-responsive></v-col
      ></v-row>
      <div id="buttons">
        <v-btn v-on:click="startCamera()" ref="start">Start camera</v-btn>
        <v-btn
          v-on:click="startOrStopRecording()"
          ref="record"
          :disabled="isDisabled('record')"
          >{{ textStartOrStop }}</v-btn
        >
        <v-btn
          v-on:click="playRecording()"
          ref="play"
          :disabled="isDisabled('play')"
          >Play</v-btn
        >
        <v-btn
          v-on:click="downloadRecording()"
          ref="download"
          :disabled="isDisabled('download')"
          >Download</v-btn
        >
        <v-btn
          v-on:click="saveRecording()"
          ref="save"
          :disabled="isDisabled('save')"
          >Upload</v-btn
        >
        <v-btn
          v-on:click="switchVideo()"
          ref="change"
          :disabled="isDisabled('change')"
          >Switch
        </v-btn>
      </div>
      <div>
        <span id="errorMsg">{{ errorMsg }}</span>
      </div>
    </div>

    <v-row class="pt-6" justify="center" align="center">
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
    <v-row justify="center" align="center">
      <v-col cols="8"
        ><span>Introduzca una descripción de 255 caracteres máximo:</span>
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
export default {
  data() {
    return {
      mediaRecorder: null,
      stream: null,
      recordedBlobs: [],
      errorMsg: null,
      recordedVideo: null,
      recordButtonState: true,
      playButtonState: true,
      downloadButtonState: true,
      saveButtonState: true,
      changeButtonSate: true,
      startRecordingState: true,
      textStartOrStop: 'Start Recording',
      startTime: null,
      fixedBlob: null,
      isShown: false,
      videoName: '',
      videoDescription: ''
    };
  },

  methods: {
    isDisabled(button) {
      switch (button) {
        case 'play':
          return this.playButtonState;
        case 'record':
          return this.recordButtonState;
        case 'download':
          return this.downloadButtonState;
        case 'save':
          return this.saveButtonState;
        case 'change':
          return this.changeButtonSate;
        default:
          return false;
      }
    },
    startOrStopRecording() {
      if (this.startRecordingState) {
        this.startRecordingState = false;
        this.startRecording();
      } else {
        this.stopRecording();
        this.textStartOrStop = 'Start Recording';
        this.startRecordingState = true;
        this.playButtonState = false;
        this.downloadButtonState = false;
        this.saveButtonState = false;
        this.changeButtonSate = false;
      }
    },
    switchVideo: function() {
      this.isShown = !this.isShown;
      if (!this.isShown) {
        this.recordedVideo.pause();
      }
    },
    startRecording() {
      if (this.isShown) {
        this.isShown = false;
        this.recordedVideo.pause();
      }
      this.recordedBlobs = [];
      let options = { mimeType: 'video/webm;codecs=vp9,opus' };
      if (!MediaRecorder.isTypeSupported(options.mimeType)) {
        console.error(`${options.mimeType} is not supported`);
        options = { mimeType: 'video/webm;codecs=vp8,opus' };
        if (!MediaRecorder.isTypeSupported(options.mimeType)) {
          console.error(`${options.mimeType} is not supported`);
          options = { mimeType: 'video/webm' };
          if (!MediaRecorder.isTypeSupported(options.mimeType)) {
            console.error(`${options.mimeType} is not supported`);
            options = { mimeType: '' };
          }
        }
      }

      try {
        this.mediaRecorder = new MediaRecorder(this.stream, options);
      } catch (e) {
        console.error('Exception while creating MediaRecorder:', e);
        this.errorMsg = `Exception while creating MediaRecorder: ${JSON.stringify(
          e
        )}`;
        return;
      }

      console.log(
        'Created MediaRecorder',
        this.mediaRecorder,
        'with options',
        this.options
      );

      this.textStartOrStop = 'Stop Recording';
      this.recordButtonState = false;
      this.playButtonState = true;
      this.downloadButtonState = true;
      this.saveButtonState = true;
      this.changeButtonSate = true;
      this.mediaRecorder.onstop = (event) => {
        const util = require('util');
        const ysFixWebmDuration = require('fix-webm-duration');
        const promiseYsFixedWebmDuration = util.promisify(ysFixWebmDuration);
        let duration = Date.now() - this.startTime;
        console.log('DURATION: ', duration);
        console.log('Recorder stopped: ', event);
        console.log('Recorded Blobs: ', this.recordedBlobs);
        let buggyBlob = new Blob(this.recordedBlobs, { type: 'video/webm' });
        promiseYsFixedWebmDuration(buggyBlob, duration)
          .then((newBlob) => {
            this.fixedBlob = newBlob;
            console.log('Blob is correct', newBlob);
          })
          .catch((err) => {
            this.fixedBlob = err;
            console.log('Webm has no time. Time added', err);
          });

        console.log('Fixed Blobs: ', this.fixedBlob);
      };
      this.mediaRecorder.ondataavailable = this.handleDataAvailable;
      this.mediaRecorder.start();
      this.startTime = Date.now();
      console.log('MediaRecorder started', this.mediaRecorder);
    },
    handleDataAvailable(event) {
      console.log('handleDataAvailable', event);
      if (event.data && event.data.size > 0) {
        this.recordedBlobs.push(event.data);
      }
    },
    stopRecording() {
      this.mediaRecorder.stop();
    },
    handleSuccess(stream) {
      this.recordButtonState = false;
      console.log('getUserMedia() got stream:', stream);
      window.stream = stream;

      const gumVideo = document.querySelector('video#gum');
      gumVideo.srcObject = stream;
    },
    playRecording() {
      this.recordedVideo.src = null;
      this.recordedVideo.srcObject = null;
      this.recordedVideo.src = window.URL.createObjectURL(this.fixedBlob);
      this.recordedVideo.controls = true;
      if (!this.isShown) {
        this.isShown = true;
      }
      this.recordedVideo.play();
    },
    downloadRecording() {
      if (this.isEmptyOrSpaces(this.videoName)) {
        window.alert('You must provide a name for the video!');
        return;
      }

      if (this.videoName.length > 70) {
        window.alert('The video name is too long');
        return;
      }

      const url = window.URL.createObjectURL(this.fixedBlob);
      const a = document.createElement('a');
      a.style.display = 'none';
      a.href = url;
      a.download = this.videoName.concat('.webm');
      document.body.appendChild(a);
      a.click();
      setTimeout(() => {
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
      }, 100);
    },
    saveRecording() {
      if (this.videoDescription.length > 255 || this.videoName.length > 70) {
        window.alert('The video name or the description is too long');
        return;
      }
      let description = this.videoDescription;
      if (this.isEmptyOrSpaces(this.videoDescription)) {
        description = 'NO DESCIPTION PROVIDED';
      }
      if (this.isEmptyOrSpaces(this.videoName)) {
        window.alert('You must provide a name for the video!');
        return;
      }

      const axios = require('axios');
      let formData = new FormData();

      formData.append('file', this.fixedBlob);
      formData.append('type', 'video/webm');
      formData.append('name', this.videoName.concat('.webm'));
      formData.append('description', description);
      for (let key of formData.keys()) {
        console.log(key);
      }
      axios
        .post('/api/ajax_upload.php', formData)
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
    },

    async startCamera() {
      const constraints = {
        audio: {
          echoCancellation: { exact: true }
        },
        video: {
          width: 1280,
          height: 720,
          frameRate: { ideal: 15, max: 30 }
        }
      };
      console.log('Using media constraints:', constraints);
      await this.init(constraints);
    },

    async init(constraints) {
      try {
        this.stream = await navigator.mediaDevices.getUserMedia(constraints);
        this.handleSuccess(this.stream);
      } catch (e) {
        console.error('navigator.getUserMedia error:', e);
        this.errorMsg = `navigator.getUserMedia error:${e.toString()}`;
      }
    },
    stopBothVideoAndAudio() {
      this.stream.getTracks().forEach(function(track) {
        console.log('Deteniendo grabación');
        if (track.readyState == 'live') {
          track.stop();
        }
      });
    },
    isEmptyOrSpaces: function(str) {
      return str === null || str.match(/^ *$/) !== null;
    }
  },
  mounted() {
    this.recordedVideo = this.$refs.recorded;
  },
  destroyed() {
    this.stopBothVideoAndAudio();
  }
};
</script>

<style></style>
