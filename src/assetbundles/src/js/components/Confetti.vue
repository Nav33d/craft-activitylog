<template>
</template>

<script>
export default {
  mounted: function() {
    this.$confetti.start({
        shape: 'heart',
        size: 8,
        dropRate: 16,
        colors: ['DodgerBlue', 'OliveDrab', 'Gold', 'pink', 'SlateBlue', 'lightblue', 'Violet', 'PaleGreen', 'SteelBlue', 'SandyBrown', 'Chocolate', 'Crimson']
    });
    setTimeout(() => {
        this.$confetti.stop();
    }, 5000);
  },
}
</script>