<template>
  <div>
    <h1>PubLC & Vue</h1>
    <p>Let's create something awesome!</p>
    <router-link :to="'/other'">other</router-link>
    <router-link :to="'/'">app</router-link>
    <router-view></router-view>
  </div>
</template>


<script>
import Other from './app/Other.vue';
export default {
  components: {
    Other
  }
}
</script>





