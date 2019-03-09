<template>
  <div :class="['vuetable-pagination-info', css.infoClass]"
    v-html="paginationInfo">
  </div>
</template>

<script>
import PaginationInfoMixin from './VuetablePaginationInfoMixin';
export default {
  mixins: [PaginationInfoMixin],
}
</script>
