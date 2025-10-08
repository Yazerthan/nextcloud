<template>
  <a
    :href="href"
    rel="noopener"
    target="_blank"
  >
    <slot></slot>
  </a>
</template>

<script setup>
defineProps({
  href: {
    type: String,
    required: true,
  },
})
</script>
