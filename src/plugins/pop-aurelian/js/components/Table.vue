<template>
  <loading v-if="loading"/>
  <div class="elements-wrapper" v-if="!loading">
    <h2 class="main-title">{{ tableData.title }} <img v-on:click="refreshList" :src="refreshIcon" alt="refresh"/></h2>
    <table class="wp-list-table widefat fixed striped table-view-list">
      <thead>
      <tr>
        <th v-for="header in tableData.data.headers">
          <strong>{{ header }}</strong>
        </th>
      </tr>
      </thead>
      <tbody id="the-list">
      <tr v-for="row in filteredItems">
        <td v-for="header in tableData.data.headers">
          <a :href="row[header.toLowerCase()]" target="_blank"
             v-if="isUrl(row[header.toLowerCase()])">{{ row[header.toLowerCase()] }}</a>
          <span v-if="!isUrl(row[header.toLowerCase()]) && header !== 'Date'">{{ row[header.toLowerCase()] }}</span>
          <span v-if="!isUrl(row[header.toLowerCase()]) && header === 'Date'">{{
              setDate(row[header.toLowerCase()])
            }}</span>
        </td>
      </tr>
      </tbody>
    </table>

    <template v-if="emails.length">
      <h2>Emails</h2>
      <ul>
        <li v-for="email in emails"><a :href="`mailto:${email}`">{{ email }}</a></li>
      </ul>
    </template>
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
    table: Object,
    settings: Object,
    routes: Object
  },
  /**
   * Default data
   *
   * @returns {{emails: *[], tableData: {}, humandate: boolean, rows: number, loading: boolean, refreshIcon: *[], settingsData: {}}}
   */
  data() {
    return {
      tableData: {},
      settingsData: {},
      humandate: false,
      rows: 0,
      emails: [],
      refreshIcon: [],
      loading: false,
    };
  },
  /**
   * Computed Data
   */
  computed: {
    /**
     * Filter how many rows to show based on the "numrows" property
     * @returns {string}
     */
    filteredItems: function () {
      return this.tableData.data.rows.slice(0, this.rows);
    }
  },
  methods: {
    /**
     * Check if value is an url
     *
     * @param urlString
     * @returns {boolean}
     */
    isUrl(urlString) {
      var urlPattern = new RegExp('^(https?:\\/\\/)?' + // validate protocol
          '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // validate domain name
          '((\\d{1,3}\\.){3}\\d{1,3}))' + // validate OR ip (v4) address
          '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // validate port and path
          '(\\?[;&a-z\\d%_.~+=-]*)?' + // validate query string
          '(\\#[-a-z\\d_]*)?$', 'i'); // validate fragment locator
      return !!urlPattern.test(urlString);
    },
    /**
     * Sets the date to readable format in case humandate is set to true
     *
     * @param date
     * @returns {string|*}
     */
    setDate(date) {
      this.humandate = Object.values(this.settingsData).find((setting) => setting.name === 'humandate').value;

      if (this.humandate) {
        const d = new Date(date * 1000);
        if (d) {
          return [d.getMonth() + 1,
                d.getDate(),
                d.getFullYear()].join('/') + ' ' +
              [d.getHours(),
                d.getMinutes(),
                d.getSeconds()].join(':')

        }
      }

      return date
    },
    /**
     * Sets the rows data
     */
    setRows() {
      this.rows = Object.values(this.settingsData).find((setting) => setting.name === 'numrows').value;
    },
    /**
     * Sets the email data
     */
    setEmails() {
      this.emails = Object.values(this.settingsData).find((setting) => setting.name === 'emails').value;
    },
    /**
     * Reloads the data from the API
     *
     * @returns {Promise<void>}
     */
    async refreshList() {
      this.loading = true;
      /**
       * Fetch the data
       * @type {Response}
       */
      const res = await fetch(this.routes.general_data + '/refresh', {
        headers: {
          'X-WP-Nonce': this.routes.nonce
        }
      });

      /**
       * Sets the data for component rerender and other
       * components to know about the changes
       * @type {any}
       */
      const finalRes = await res.json();
      PAApp.data = finalRes;
      this.tableData = Object.assign({}, this.tableData, finalRes.table);
      this.loading = false;
    }
  },
  /**
   * Pre-set the data
   */
  beforeMount() {
    this.refreshIcon = refreshIcon;
    this.tableData = this.table;
    this.settingsData = this.settings;
    this.setRows();
    this.setEmails();

  },
  /**
   * Set the Data
   */
  mounted() {
    this.tableData = this.table;
    this.settingsData = this.settings;
  }

};
</script>