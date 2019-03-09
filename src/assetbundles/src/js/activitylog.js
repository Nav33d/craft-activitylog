import Vue from 'vue';
import VueEvents from 'vue-events';
import Moment from 'vue-moment';
import VueHighlightJS from 'vue-highlightjs';
import VueSweetalert2 from 'vue-sweetalert2';
import VueConfetti from 'vue-confetti';
import VActivitylogLogs from './components/logs/Logs.vue';
import VPrune from './components/logs/Prune.vue';
import VDeleteAll from './components/logs/DeleteAll.vue';
import VConfetti from './components/Confetti.vue';


Vue.use(Moment);
Vue.use(VueEvents);
Vue.use(VueHighlightJS);
Vue.use(VueSweetalert2);
Vue.use(VueConfetti);

const vm = new Vue({
  el: '#activitylog-app',
  delimiters: ["<%","%>"],
  components: {
    'v-activitylog-logs': VActivitylogLogs,
    'v-confetti': VConfetti,
  },
  methods: {
    onTableRefresh (vuetable) {
      Vue.nextTick( () => vuetable.refresh());
    }
  },
  mounted() {
    this.$events.$on('refresh-table', eventData => this.onTableRefresh(eventData));
  },
});

const activitylogActionButtons = new Vue({
  el: '#activitylog-action-buttons',
  delimiters: ["<%","%>"],
  components: {
    'v-prune': VPrune,
    'v-delete-all': VDeleteAll,
  },
});