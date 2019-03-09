<template>
  <a href="" class="btn" @click.prevent="confirmPrune()"><slot></slot></a>
</template>

<script>
import axios from 'axios';

export default {
  props: {
    logsLimit: Number,
  },

  data() {
    return {
    };
  },

  mounted() {
  },

  methods: {
    confirmPrune () {
      this.$swal({
        title: 'Are you sure?',
        text: "Any logs older than "+ this.logsLimit + " days will be deleted. You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#a2a2a2',
        confirmButtonText: 'Yes, prune logs!'
      }).then((result) => {
        if (result.value) {
          this.prune();
        }
      });
    },

    prune () {
      axios.get('/actions/activitylog/logs/prune')
      .then((response) => {
        if ( !response.data.rows )
        {
          Craft.cp.displayNotice("No logs due for deletion");
          return;
        }
        Craft.cp.displayNotice(response.data.rows + " logs deleted.");
        setTimeout( function() { location.reload(); }, 1000);
      })
      .catch((error) => {
        Craft.cp.displayError("Failed to prune logs");
      });
    },
  },
}
</script>