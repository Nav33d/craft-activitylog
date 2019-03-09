<template>
  <div>
    <div class="mb-8">
      <vuetable-filter-bar></vuetable-filter-bar>
    </div>
    <vuetable ref="vuetable"
      api-url="/actions/activitylog/logs/get-all"
      :per-page="20"
      :fields=fields
      :css="css"
      :sort-order="sortOrder"
      :append-params="moreParams"
      @vuetable:pagination-data="onPaginationData"
    >
    </vuetable>

    <div class="mt-4 mb-8">
      <vuetable-pagination-info ref="paginationInfo"></vuetable-pagination-info>
      <vuetable-pagination ref="pagination"
        @vuetable-pagination:change-page="onChangePage"
      >
      </vuetable-pagination>
    </div>

  </div>
</template>

<script>
import FieldDefs from './FieldDefs.js';
import Vuetable from 'vuetable-2/src/components/Vuetable';
import VuetablePagination from '../vuetable/VuetablePagination';
import VuetablePaginationInfo from '../vuetable/VuetablePaginationInfo';
import VueTableFilterBar from '../vuetable/VuetableFilterBar';

export default {
  components: {
    'vuetable': Vuetable,
    'vuetable-pagination': VuetablePagination,
    'vuetable-pagination-info': VuetablePaginationInfo,
    'vuetable-filter-bar': VueTableFilterBar,
  },
  data() {
    return {
      moreParams: {},
      css: {
        tableClass: 'data fullwidth',
        ascendingIcon: 'menubtn activitylog-menubtn-asc',
        descendingIcon: 'menubtn activitylog-menubtn-desc'
      },
      sortOrder: [
        {
          field: 'dateCreated',
          sortField: 'dateCreated',
          direction: 'desc'
        }
      ],
      fields: FieldDefs,
    };
  },

  mounted() {
    this.$events.$on('filter-set', eventData => this.onFilterSet(eventData));
    this.$events.$on('action-filter-set', eventData => this.onActionFilterSet(eventData));
    this.$events.$on('filter-reset', e => this.onFilterReset());
  },

  methods: {
    onFilterSet (filterText) {
      this.moreParams = {
        'filter': filterText,
      };

      this.$events.fire('refresh-table', this.$refs.vuetable);
    },

    onActionFilterSet (filter) {
      this.moreParams = {
        'action_filter': filter,
      };

      this.$events.fire('refresh-table', this.$refs.vuetable);
    },

    onFilterReset () {
      this.$events.fire('refresh-table', this.$refs.vuetable);
    },

    onPaginationData (paginationData) {
      this.$refs.pagination.setPaginationData(paginationData);
      this.$refs.paginationInfo.setPaginationData(paginationData);
    },
    
    onChangePage (page) {
      this.$refs.vuetable.changePage(page)
    },

    formatDateTime (value) {
      return this.$moment(value).format('MMMM Do YYYY, H:mm:ss');
    },

    humanize (value) {
      return value.charAt(0).toUpperCase() + value.slice(1).replace(/-/g, " ");
    },

    addViewLink (value) {
      if (value === '') {
        return '';
      }
      return `
      <a class="go" href="${value}" title="${value}" rel="noopener">View</a>
      `;
    }
  },
}
</script>

