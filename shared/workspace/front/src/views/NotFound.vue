<template>
  <div class="w-screen h-screen flex flex-col justify-center items-center gap-2">
    <h1 class="text-4xl font-bold">404</h1>
    <p class="text-lg text-muted-foreground">Page not found</p>
    <router-link to="/" class="mt-4">
      <Button>Go back to home</Button>
    </router-link>
  </div>
</template>
<script setup lang="ts">
import {Button} from '@/components/ui/button';
</script>