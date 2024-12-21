<script setup lang="ts">
import {onMounted, ref, watch} from 'vue';
import {Loader} from '@/components/ui/loader';
import {EventCard} from '@/components/events/event-card';
import {EventService, SportType} from '@/lib/services/event.service.ts';
import {userStore} from '@/stores/user.store.ts';

const loading = ref(true);
const events = ref<SportType[] | null>(null);

// on userStore user change, fetch events
watch(() => userStore.getUser(), async () => {
  if (userStore.getUser()) {
    await loadEvents();
  }
});

async function loadEvents() {
  if (!userStore.getUser() || events.value) return;
  loading.value = true;
  const data = await EventService.getParticipations();
  console.log(data);
  events.value = data ?? [];
  loading.value = false;
}

onMounted(async () => {
  await loadEvents();
});
</script>

<template>
  <div v-if="loading || !events" class="flex justify-center items-center h-80">
    <Loader v-if="loading"/>
  </div>
  <div v-else class="grid grid-cols-3 gap-4 max-lg:grid-cols-2 max-md:grid-cols-1">
    <EventCard v-for="event in events" :key="event.id" :event="event"/>
  </div>
</template>
