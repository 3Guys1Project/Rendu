
<template>
  <div class="flex justify-center items-center w-fit animate-pulse motion-opacity-in-[0%] motion-duration-[500ms]/opacity">
    <LoadingSpinner/>
    <p class="pl-2 ">Loading...</p>
  </div>
</template>

<script setup lang="ts">
import {LoadingSpinner} from "@/components/ui/loading-spinner";
</script>
