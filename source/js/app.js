import Vue from 'vue';
import App from './App.vue';
import Router from 'vue-router';
import Other from './app/Other.vue'

Vue.use(Router);

const router = new Router({
    mode: 'history',
    routes: [
      {path: '/other', component: Other, name: 'other'},
      {path: '/', component: App, name: 'app'}
  ]
});

new Vue({
  el: '#app',
  render: h => h(App),
  router: router
});
