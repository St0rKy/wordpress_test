<template>
  <loading v-if="loading"/>
  <div id="settings">
    <h2 class="main-title">Settings</h2>
    <div class="settings-wrapper">
      <div class="input-field">
        <label for="numrows">Number Of Rows</label>
        <input v-on:change="setInputData" name="numrows" id="numrows" type="number" :value="numrows"/>
      </div>
      <div class="input-field">
        <label for="humandate">Should Dates be human readable ?</label>
        <input v-on:change="setInputData" name="humandate" id="humandate" type="checkbox" :checked="humandate"/>
      </div>

      <div class="input-field" v-for="index in 5" :key="index">
        <label :for="`email${index}`">Email #{{ index }}</label>
        <input v-on:change="setInputData" :id="`email${index}`" :name="`emails-${index-1}`" type="text" :value="emails[index-1]"/>
      </div>
    </div>
  </div>
</template>

<script>
import Loading from "./Loading";

export default {
  components: {Loading},
  /**
   * Received Data
   */
  props: {
    settings: Object,
    routes: Object
  },
  /**
   * Default Data
   * @returns {{emails: *[], numrows: number, humandate: boolean, loading: boolean}}
   */
  data() {
    return {
      numrows: 0,
      humandate: false,
      emails: [],
      loading: false
    };
  },
  methods: {
    /**
     * Sets each setting depending on the input name
     *
     * @param e
     * @returns {Promise<void>}
     */
    async setInputData(e) {
      this.loading = true;
      const name = e.target.name.split('-')
      const item = Object.values(this.settings).find((setting) => setting.name === name[0]);
      let value = '';

      /**
       * Filter by name to know what to set
       */
      switch (item.name) {
        case 'numrows':
          value = parseInt(e.target.value)
          break;
        case 'humandate':
          value = e.target.checked
          break;
        case 'emails':
          value = item.value ? item.value :  [];

          if(item.value[name[1]]) {
            /**
             * We're editing here
             */
            if(e.target.value) {
              value[name[1]] = e.target.value
            } else {
              value.splice(name[1], 1);
            }
          } else {
            value.push(e.target.value);
          }
          break;
      }

      /**
       * Send the data to the backend
       *
       * @type {Response}
       */
      const res = await fetch(this.routes.settings + '/' + item.id, {
        method: "PUT",
        body: JSON.stringify({
          value: value
        }),
        headers: {
          'X-WP-Nonce': this.routes.nonce
        }
      });

      const finalRes = await res.json();

      /**
       * Set the data for other components
       */
      if(finalRes) {
        this[item.name] = value;
        item.value = value;
      }

      this.loading = false;
    },
  },
  /**
   * Set the settings data
   */
  mounted() {
    Object.values(this.settings).forEach((setting) => this[setting.name] = setting.value)
  }
};
</script>