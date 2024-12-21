<script setup lang="ts">
import {CircleUser, Medal} from 'lucide-vue-next';
import {Button} from '@/components/ui/button';
import {userStore} from '@/stores/user.store.ts';
import {LogoutButton} from '@/components/navigation/logout-button';
import {computed} from 'vue';
import {Loader} from '@/components/ui/loader';
import {NewEventDialog} from '@/components/events/new-event-dialog';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';

const loading = computed(() => userStore.loading);
const initialized = computed(() => userStore.initialized);
const isOrga = computed(() => userStore.hasRole(['ROLE_ORGA']));

</script>

<template>
  <header
      class="sticky top-0 flex h-16 z-50 items-center justify-between gap-4 border-b bg-white px-4 md:px-6 w-full shadow-sm"
  >
    <nav class="font-medium flex items-center gap-5 text-sm lg:gap-6">
      <router-link
          to="/"
          class="flex items-center gap-2 text-lg font-semibold md:text-base"
      >
        <Medal
            class="h-6 w-6 drop-shadow motion-translate-x-in-[0%] motion-translate-y-in-[-25%] motion-opacity-in-[0%] motion-duration-[500ms]/translate motion-duration-[500ms]/opacity motion-ease-bounce"/>
        <span class="sr-only">Hapi Trail</span>
      </router-link>
      <router-link
          to="/"
          class="flex items-center gap-2 text-muted-foreground [&.router-link-exact-active]:text-foreground md:text-base"
      >
        <span
        >Événements</span>
      </router-link>
      <router-link v-if="userStore.isLoggedIn()"
                   to="/participations"
                   class="flex items-center gap-2 text-muted-foreground [&.router-link-exact-active]:text-foreground md:text-base"
      >
        <span>Participations</span>
      </router-link>
    </nav>
    <div class="flex gap-4 items-center">
      <template v-if="!initialized || loading">
        <Loader/>
      </template>
      <template v-else-if="!userStore.isLoggedIn()">
        <router-link to="/login">
          <Button class="h-full">Login</Button>
        </router-link>
        <div class="w-[1px] h-6 bg-black rounded-full opacity-20"/>
        <router-link to="/register">
          <Button class="h-full">Register</Button>
        </router-link>
      </template>
      <template v-else>
        <NewEventDialog v-if="isOrga"/>
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <Button variant="secondary" size="icon" class="rounded-full border border-input bg-transparent">
              <CircleUser class="h-5 w-5"/>
              <span class="sr-only">Toggle user menu</span>
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end">
            <DropdownMenuLabel>{{ userStore.user?.login }}</DropdownMenuLabel>
            <DropdownMenuSeparator/>
            <router-link to="/profile">
              <DropdownMenuItem class="cursor-pointer">Profile</DropdownMenuItem>
            </router-link>
            <DropdownMenuSeparator/>
            <DropdownMenuItem class="p-0">
              <LogoutButton/>
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </template>
    </div>
  </header>
</template>
