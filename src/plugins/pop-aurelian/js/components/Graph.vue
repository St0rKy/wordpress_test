<template>
  <loading v-if="loading"/>
  <div  id="graph">
    <h2 class="main-title">Graph Data <img v-on:click="refreshList" :src="refreshIcon" alt="refresh"/></h2>
    <canvas ref="canvas" id="chart"></canvas>
  </div>
</template>

<script>

import refreshIcon from "../../images/arrows-rotate-solid.svg";
import Loading from "./Loading";

export default {
  components: {Loading},
  /**
   * Received data
   */
  props: {
    graph: Object,
    routes: Object
  },

  /**
   * Default Data
   *
   * @returns {{graphData: string, loading: boolean, chart: string, refreshIcon: *[]}}
   */
  data() {
    return {
      graphData: '',
      refreshIcon: [],
      chart: '',
      loading: false
    };
  },
  methods: {
    /**
     * Adds the Chart to the wrapper
     */
    addChart() {
      const ctx = this.$refs.canvas;

      if(this.chart) {
        this.chart.destroy();
      }

      /**
       * Sets the dates as readable format
       *
       * @type {string[]}
       */
      const labels = Object.values(this.graphData).map((item) => {
        const d = new Date(item.date * 1000);
        return [d.getMonth() + 1,
          d.getDate(),
          d.getFullYear()].join('/');

      });

      /**
       * Add the chart
       */
      this.chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'Graph Data',
            data: Object.values(this.graphData).map((item) => item.value),
            borderWidth: 3
          }]
        },
        options: {
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    },
    /**
     * Refreshes the list of posts and graph
     *
     * @returns {Promise<void>}
     */
    async refreshList() {
      this.loading = true;
      /**
       * Fetch the data
       *
       * @type {Response}
       */
      const res = await fetch(this.routes.general_data + '/refresh', {
        headers: {
          'X-WP-Nonce': this.routes.nonce
        }
      });

      /**
       * Set the data for re-render
       * @type {any}
       */
      const finalRes = await res.json();
      PAApp.data = finalRes;
      this.graphData = Object.assign({}, this.graphData, finalRes.graph);
      this.addChart();
      this.loading = false;
    }
  },
  /**
   * Set the data
   */
  mounted() {
    this.graphData = this.graph;
    this.refreshIcon = refreshIcon;

    if(!this.loading) {
      this.addChart()
    }
  }
};
</script>