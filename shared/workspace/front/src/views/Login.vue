<script setup lang="ts">
import {Button} from '@/components/ui/button';
import {Card, CardContent, CardDescription, CardHeader, CardTitle} from '@/components/ui/card';
import {Input, PasswordInput} from '@/components/ui/input';
import {Label} from '@/components/ui/label';
import {computed, ref} from 'vue';
import {useRouter} from 'vue-router';
import {userStore} from "@/stores/user.store.ts";

const router = useRouter();
const username = ref('');
const password = ref('');
const loading = computed(() => userStore.loading);

async function handleSubmit(e: Event) {
  e.preventDefault();
  const success = await userStore.login(username.value, password.value);
  if (success) {
    await router.push('/');
  }
}
</script>

<template>
  <div class="w-screen h-screen flex flex-col gap-4 items-center justify-center">
    <Button variant="ghost" class="absolute top-4 left-4">
      <router-link to="/">Retour</router-link>
    </Button>
    <Card class="w-full max-w-md">
      <CardHeader>
        <CardTitle class="text-2xl">
          Login
        </CardTitle>
        <CardDescription>
          Enter your email below to login to your account.
        </CardDescription>
      </CardHeader>
      <CardContent>
        <form v-on:submit="handleSubmit" class="grid gap-4">
          <div class="grid gap-2">
            <Label for="username">Username</Label>
            <Input id="username" v-model="username" required/>
          </div>
          <div class="grid gap-2 relative">
            <Label for="password">Password</Label>
            <PasswordInput id="password" type="password" v-model="password" required />
          </div>
          <Button type="submit" :loading="loading" class="w-full">
            Sign in
          </Button>
        </form>
      </CardContent>
    </Card>
    <div class="text-center text-sm">
      Don't have an account?
      <router-link to="/register" class="underline">Register</router-link>
    </div>
  </div>
</template>