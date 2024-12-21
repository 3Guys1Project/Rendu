<script setup lang="ts">
import {computed, PropType} from 'vue';
import {Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle} from '@/components/ui/card';
import dayjs from 'dayjs';
import {Badge} from '@/components/ui/badge';
import {SportType} from '@/lib/services/event.service.ts';
import {MapPin, Users, DollarSign, Calendar} from 'lucide-vue-next';
import {userStore} from '@/stores/user.store';

const props = defineProps({
  event: {
    type: Object as PropType<SportType>,
    required: true,
  },
});

const event = props.event as SportType;

const isParticipating = computed(() => {
  return event?.participations.some(
      (participation) => participation.user.id === userStore.getUser()?.id
  ) || event.participating
});

</script>

<template>
  <router-link :to="'/event/' + event.id" class="group block">
    <Card
        v-if="event"
        class="relative border shadow-sm transition-all duration-200 group-hover:shadow-lg group-hover:border-gray-300 motion-translate-x-in-[0%] motion-translate-y-in-[10%] motion-opacity-in-[0%] motion-blur-in-[8px] motion-duration-[500ms]/opacity"
    >
      <CardHeader
          class="relative bg-gradient-to-r from-blue-300 via-blue-200 to-blue-100 text-white p-4 rounded-t-lg"
      >
        <CardTitle class="text-lg font-semibold text-black capitalize">
          {{ event.name }}
        </CardTitle>
        <div class="mt-4 flex justify-end w-full">
          <span
              v-if="isParticipating"
              class="px-3 py-1 text-sm font-medium absolute top-0 right-0 text-white bg-red-500 rounded-tr-md rounded-bl-md m-[0.1px]"
          >
            Vous participez
          </span>
        </div>
        <CardDescription class="flex items-center gap-2 text-sm">
          <Calendar class="w-4 h-4"/>
          {{ dayjs(event.start_at).format('DD/MM/YYYY') }} au
          {{ dayjs(event.end_at).format('DD/MM/YYYY') }}
        </CardDescription>
        <div class="flex flex-row gap-1 mt-2 w-full overflow-x-auto">
          <Badge
              v-for="sport in event.sport"
              :key="sport"
              variant="secondary"
              class="bg-blue-400 text-white hover:bg-blue-500"
          >
            {{ sport }}
          </Badge>
        </div>
      </CardHeader>

      <CardContent class="p-4">
        <p class="text-gray-700 line-clamp-3 capitalize">{{ event.description }}</p>
      </CardContent>

      <CardFooter class="flex flex-wrap gap-2 p-4 border-t rounded-b-lg w-full">
        <div class="flex items-center gap-2">
          <Badge variant="secondary" class="flex items-center gap-1 bg-orange-100 text-orange-700">
            <MapPin class="w-4 h-4"/>
            {{ event.localisation }}
          </Badge>
        </div>

        <div class="flex items-center gap-2">
          <Badge variant="secondary" class="flex items-center gap-1 bg-violet-100 text-violet-700">
            <Users class="w-4 h-4"/>
            {{ event.countOfParticipation }}/{{ event.max_participants }} participants
          </Badge>
        </div>

        <div class="flex items-center gap-2">
          <Badge variant="secondary" class="flex items-center gap-1 bg-rose-100 text-rose-700">
            <DollarSign class="w-4 h-4"/>
            {{ event.price }}€
          </Badge>
        </div>
      </CardFooter>
    </Card>
    <p v-else class="text-gray-500 text-sm">Loading event details...</p>
  </router-link>
</template>