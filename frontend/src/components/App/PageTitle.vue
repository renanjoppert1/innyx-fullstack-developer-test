<script setup lang="ts">
const props = defineProps({
  title: {
    type: String,
    required: true
  }
})
</script>

<template>
  <h2>{{ props.title}}</h2>
</template>

<style scoped>

</style>