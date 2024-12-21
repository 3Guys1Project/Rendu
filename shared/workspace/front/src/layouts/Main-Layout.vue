<script setup lang="ts">
import {NavigationBar} from "@/components/navigation/navigation-bar";
</script>

<template>
  <div class=" flex flex-col items-center justify-between w-full ">
    <NavigationBar/>
    <main class="p-6 w-full h-[calc(100dvh-121px)]">
      <router-view/>
    </main>
    <footer class="border-t w-full flex items-center justify-center py-4 ">
      <p class="text-muted-foreground">&copy; 2024 Hapi Trail</p>
    </footer>
  </div>
</template>

