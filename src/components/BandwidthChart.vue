<template>
  <div class="small">
    <line-chart :chart-data="datacollection"></line-chart>
  </div>
</template>

<script>
import LineChart from './LineChart.js';
let moment = 0;
let tmp = [0, 0];
let newData = [0, 0];

export default {
  components: {
    LineChart
  },
  props: {
    currentBandwidth: {
      type: Number,
      default: 0
    },
    update: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      datacollection: null,
      timeStringList: [],
      bandwidths: [],
      currentData: []
    };
  },
  methods: {
    constructArrays() {
      console.log('Se construyen los arrays');
      let tmpList = [];
      let tmpList2 = [];
      let bandwidth;
      this.currentData.forEach((element) => {
        moment = element[0];
        bandwidth = element[1];
        tmpList.push(this.transformToTimeString(moment));
        tmpList2.push(bandwidth);
      });
      this.timeStringList = tmpList;
      this.bandwidths = tmpList2;
    },
    transformToTimeString(moment) {
      console.log('Se transforma el tiempo a String');
      let sec_num = moment / 1000;
      let hours = Math.floor(sec_num / 3600);
      let minutes = Math.floor((sec_num - hours * 3600) / 60);
      let seconds = sec_num - hours * 3600 - minutes * 60;

      if (hours < 10) {
        hours = '0' + hours;
      }
      if (minutes < 10) {
        minutes = '0' + minutes;
      }
      if (seconds < 10) {
        seconds = '0' + seconds;
      }
      return hours + ':' + minutes + ':' + seconds;
    },
    clearData() {
      this.datacollection = null;
      this.timeStringList = [];
      this.bandwidths = [];
      this.currentData = [];
      this.update = false;
    }
  },
  watch: {
    update: function() {
      console.log('La grafica se esta actualizando...');
      if (this.currentBandwidth < 0) {
        console.log('LIMPIAR');
        this.clearData();
        return;
      }
      if (this.currentData.length == 0) {
        console.log('Primera entrada en la grafica');
        moment = 5000;
      } else {
        console.log('Nueva entrada en la grafica');
        tmp = this.currentData[this.currentData.length - 1];
        moment = tmp[0] + 5000;
      }
      newData = [moment, this.currentBandwidth];
      if (this.currentData.length == 16) {
        console.log(
          'Se ha superado el numero de puntos maximo, por lo que se borra el primero'
        );
        this.currentData.splice(0, 1);
      }
      console.log('Se añade la nueva entrada');
      this.currentData.push(newData);
      console.log(
        'Se construyen los nuevos arrays a partir del currentData actualizado'
      );
      this.constructArrays();
      console.log('Se modifican los datos de la grafica');
      this.datacollection = {
        labels: this.timeStringList,
        datasets: [
          {
            label: 'Ancho de Banda',
            backgroundColor: '#4287f5',
            fill: false,
            borderColor: '#4287f5',
            tension: 0.1,
            data: this.bandwidths
          }
        ]
      };
      console.log('Fin de tabla actualizada.');
    }
  }
};
</script>

<style>
.small {
  max-width: 525px;
  margin: 80px auto;
}
</style>
