<template>
  <div class="main-menu">
    <ul>
      <li :class="this.$route.path === '/' ? 'active' : ''"><router-link to="/">Table</router-link></li>
      <li :class="this.$route.path === '/graph' ? 'active' : ''"><router-link to="/graph">Graph</router-link></li>
      <li :class="this.$route.path === '/settings' ? 'active' : ''"><router-link to="/settings">Settings</router-link></li>
    </ul>
  </div>
  <router-view />
</template>

<script>
export default {};
</script>