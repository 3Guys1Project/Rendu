<script setup lang="ts">
import {Toaster} from 'vue-sonner';
import {TooltipProvider} from '@/components/ui/tooltip';
import {userStore} from "@/stores/user.store";
import {ref, watchEffect} from 'vue';
import {Loader} from "@/components/ui/loader";

const isLoading = ref(true);

watchEffect(() => {
  if (userStore.initialized) isLoading.value = false;
});

</script>

<template>
  <TooltipProvider>
    <div v-if="isLoading" class="flex justify-center items-center min-h-screen">
      <Loader/>
    </div>
    <div v-else id="app" class="relative min-h-screen">
      <router-view class="motion-opacity-in-[0%] motion-blur-in-[5px]"/>
    </div>
    <Toaster position="bottom-right"/>
  </TooltipProvider>
</template>
