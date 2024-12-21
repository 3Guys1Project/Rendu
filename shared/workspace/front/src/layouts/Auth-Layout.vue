<template>
  <div class="w-screen h-screen flex flex-col gap-4 items-center justify-center">
    <Button variant="ghost" class="absolute top-4 left-4">
      <router-link to="/">Retour</router-link>
    </Button>

    <router-view/>
  </div>
</template>

<script setup lang="ts">
import {Button} from '@/components/ui/button';
</script>