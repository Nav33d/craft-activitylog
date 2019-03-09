<template>
  <a href="" class="btn submit" @click.prevent="confirmDelete()"><slot></slot></a>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
    };
  },

  mounted() {
  },

  methods: {
    confirmDelete () {
      this.$swal({
        title: 'Are you sure?',
        text: "All activity logs will be deleted. You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#a2a2a2',
        confirmButtonText: 'Yes, Delete all!'
      }).then((result) => {
        if (result.value) {
          this.delete();
        }
      });
    },

    delete () {
      axios.get('/actions/activitylog/logs/delete-all')
      .then((response) => {
        if ( !response.data.rows )
        {
          Craft.cp.displayNotice("No logs to delete");
          return;
        }
        Craft.cp.displayNotice(response.data.rows + " logs deleted.");
        setTimeout( function() { location.reload(); }, 1000);
      })
      .catch((error) => {
        Craft.cp.displayError("Failed to delete logs");
      });
    },
  },
}
</script>