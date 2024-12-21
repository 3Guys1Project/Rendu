<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { ref, computed, onMounted } from 'vue';
import {PasswordInput} from '@/components/ui/input';
import { userStore } from '@/stores/user.store';

const email = ref('');
const password = ref('');
const nom = ref('');
const prenom = ref('');
const telephone = ref('');

onMounted(() => {
  const user = userStore.getUser();
  if (user) {
    email.value = user.email || '';
    nom.value = user.nom || '';
    prenom.value = user.prenom || '';
    telephone.value = user.telephone || '';
  }
});

const updatedData = computed(() => {
  const data: {
    login?: string;
    email?: string;
    plainPassword?: string;
    nom?: string;
    prenom?: string;
    telephone?: string;
  } = {};
  if (email.value.trim()) data.email = email.value.trim();
  if (password.value.trim()) data.plainPassword = password.value.trim();
  if (nom.value.trim()) data.nom = nom.value.trim();
  if (prenom.value.trim()) data.prenom = prenom.value.trim();
  if (telephone.value.trim()) data.telephone = telephone.value.trim();
  return data;
});

async function handleUpdate(e: Event) {
  e.preventDefault();
  const success = await userStore.updateProfile(updatedData.value);
  if (success) password.value = '';
}

async function handleDelete() {
  await userStore.deleteProfile();
}
</script>

<template>
  <div class="w-full mx-auto">
    <h1 class="text-2xl font-bold mb-4">Profile</h1>
    <Card>
      <CardHeader>
        <CardTitle>Edit Profile</CardTitle>
        <CardDescription>Update your account information below.</CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit="handleUpdate" class="grid grid-cols-2 gap-4">
          <div class="grid gap-2">
            <Label for="email">Email</Label>
            <Input id="email" name="email" type="email" v-model="email" placeholder="Leave blank to keep current email" />
          </div>
          <div class="grid gap-2 relative">
            <Label for="password">Password</Label>
            <PasswordInput v-model="password" placeholder="Leave blank to keep current password" />
          </div>
          <div class="grid gap-2">
            <Label for="nom">Nom</Label>
            <Input id="nom" name="nom" type="text" v-model="nom" placeholder="Enter your last name" />
          </div>
          <div class="grid gap-2">
            <Label for="prenom">Prenom</Label>
            <Input id="prenom" name="prenom" type="text" v-model="prenom" placeholder="Enter your first name" />
          </div>
          <div class="grid gap-2">
            <Label for="telephone">Telephone</Label>
            <Input id="telephone" name="telephone" type="tel" v-model="telephone" placeholder="Enter your phone number" />
          </div>
          <Button type="submit" :loading="userStore.saving" class="w-full col-span-2">Update Profile</Button>
        </form>
      </CardContent>
    </Card>

    <div class="mt-6">
      <Card>
        <CardHeader>
          <CardTitle>Danger Zone</CardTitle>
          <CardDescription>Delete your account permanently.</CardDescription>
        </CardHeader>
        <CardContent>
          <Button variant="destructive" @click="handleDelete" :loading="userStore.loading">Delete Account</Button>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
