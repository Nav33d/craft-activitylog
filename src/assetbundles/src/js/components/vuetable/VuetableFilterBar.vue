<template>
  <div class="filter-bar">
    <h2 class="mb-4">Filters</h2>

    <div class="flex items-end">
      <div>
        <label class="block text-grey-dark mb-2">Title</label>
        <div>
          <input type="text" v-model="filterText" class="text nicetext" @keyup="doFilter" placeholder="">
        </div>
      </div>
      <div>
        <label class="block text-grey-dark mb-2">Action</label>
        <div>
          <v-select v-model="actionSelect" :options="actionSelectOptions" placeholder="Select an action" :on-change="doActionFilter()"></v-select>
        </div>
      </div>
      <button class="mt-4 btn" @click="resetFilter">Clear filters</button>
    </div>


  </div>
</template>

<script>
import vSelect from 'vue-select'

export default {
  components: {
    vSelect,
  },
  data () {
    return {
      filterText: '',
      actionSelect: '',
      actionSelectOptions: [],
    }
  },
  
  mounted () {
    this.populateActionSelectOptions();
  },

  methods: {
    doFilter () {
      this.$events.fire('filter-set', this.filterText);
    },

    doActionFilter () {
      this.$events.fire('action-filter-set', this.actionSelect.value);
    },

    resetFilter () {
      this.filterText = '';
      this.actionSelect = '';
      this.$events.fire('filter-reset');
    },

    populateActionSelectOptions () {
      this.actionSelectOptions = [
        {label: 'Saved element', value: 'saved-element'},
        {label: 'Created element', value: 'created-element'},
        {label: 'Deleted element', value: 'deleted-element'},
        {label: 'Plugin installed', value: 'plugin-installed'},
        {label: 'Plugin uninstalled', value: 'plugin-uninstalled'},
        {label: 'Plugin enabled', value: 'plugin-enabled'},
        {label: 'Plugin disabled', value: 'plugin-disabled'},
        {label: 'Logged in', value: 'logged-in'},
        {label: 'Logged out', value: 'logged-out'},
      ];
    },
  }
}
</script>