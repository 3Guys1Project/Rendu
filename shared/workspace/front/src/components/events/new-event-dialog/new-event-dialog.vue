<script setup lang="ts">
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger
} from '@/components/ui/dialog';
import {Button} from '@/components/ui/button';
import {Input} from '@/components/ui/input';
import {Label} from '@/components/ui/label';
import {Textarea} from '@/components/ui/textarea';
import {DatePicker} from '@/components/ui/date-picker';
import {Checkbox} from '@/components/ui/checkbox';
import {Popover, PopoverContent, PopoverTrigger} from '@/components/ui/popover';
import {Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList} from '@/components/ui/command';
import {computed, ref} from 'vue';
import {toast} from 'vue-sonner';
import {eventStore, sports} from '@/stores/event.store.ts';

const dateRange = ref({
  start: null,
  end: null
});
const openDialog = ref(false);
const selectedSports = ref<string[]>([]);
const searchQuery = ref('');

const filteredSports = computed(() =>
    sports.filter(sport => sport.toLowerCase().includes(searchQuery.value.toLowerCase()))
);

const toggleSportSelection = (sport: string) => {
  if (selectedSports.value.includes(sport)) {
    selectedSports.value = selectedSports.value.filter(s => s !== sport);
  } else {
    selectedSports.value.push(sport);
  }
};

async function handleSubmit(e: Event) {
  e.preventDefault();
  const form = new FormData(e.target as HTMLFormElement);
  const formEntries = Object.fromEntries(form.entries());

  if (!formEntries['start']) {
    toast.error('Veuillez sélectionner une plage de dates.');
    return;
  }

  const value = JSON.parse(formEntries['start'] as string);

  if (!value.start || !value.end) {
    toast.error('Veuillez sélectionner une plage de dates valide.');
    return;
  }

  if (selectedSports.value.length === 0) {
    toast.error('Veuillez sélectionner au moins un sport.');
    return;
  }

  const eventData = {
    name: formEntries['name'] as string,
    localisation: formEntries['localisation'] as string,
    description: formEntries['description'] as string,
    start_at: new Date(value.start),
    end_at: new Date(value.end),
    price: parseFloat(formEntries['price'] as string) || 0,
    max_participants: parseInt(formEntries['maxParticipants'] as string, 10) || 0,
    participants_visible: formEntries['showParticipants'] === 'true',
    sport: selectedSports.value
  };

  const newEvent = await eventStore.createEvent(eventData);

  if (newEvent) {
    openDialog.value = false;
  }
}
</script>


<template>
  <Dialog v-model:open="openDialog">
    <DialogTrigger as-child>
      <Button variant="outline">Créer un événement</Button>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[425px] overflow-y-auto max-h-[90vh]">
      <DialogHeader>
        <DialogTitle>Nouvel événement</DialogTitle>
        <DialogDescription>
          Créez un nouvel événement en remplissant les champs ci-dessous.
        </DialogDescription>
      </DialogHeader>
      <form class="grid gap-4 grid-cols-2" @submit="handleSubmit">
        <div class="grid gap-2 col-span-2">
          <Label for="name">Nom</Label>
          <Input id="name" name="name"/>
        </div>
        <div class="grid gap-2 col-span-2">
          <Label for="description">Description</Label>
          <Textarea id="description" name="description"/>
        </div>
        <div class="grid gap-2 col-span-2">
          <Label for="start">Dates</Label>
          <DatePicker id="start" name="start" v-model="dateRange"/>
          <input type="hidden" name="start" :value="JSON.stringify(dateRange)"/>
        </div>
        <div class="grid gap-2 col-span-2">
          <Label for="sport">Sports</Label>
          <Popover>
            <PopoverTrigger as-child>
              <Button variant="outline" class="w-full justify-between">
                {{ selectedSports.length > 0 ? selectedSports.join(', ') : 'Select Sports' }}
              </Button>
            </PopoverTrigger>
            <PopoverContent class="w-[200px]">
              <Command>
                <CommandInput
                    placeholder="Rechercher un sport..."
                    v-model="searchQuery"
                />
                <CommandEmpty>Aucun sport trouvé.</CommandEmpty>
                <CommandList>
                  <CommandGroup>
                    <CommandItem
                        v-for="sport in filteredSports"
                        :key="sport"
                        @select="toggleSportSelection(sport)"
                        :value="sport"
                    >
                      <Checkbox :checked="selectedSports.includes(sport)" class="mr-2"/>
                      {{ sport }}
                    </CommandItem>
                  </CommandGroup>
                </CommandList>
              </Command>
            </PopoverContent>
          </Popover>
        </div>
        <div class="grid gap-2 col-span-2">
          <Label for="localisation">Lieu</Label>
          <Input id="localisation" name="localisation"/>
        </div>
        <div class="grid gap-2">
          <Label for="price">Prix</Label>
          <Input id="price" name="price" placeholder="0 €"/>
        </div>
        <div class="grid gap-2">
          <Label for="maxParticipants">Nb max de participants</Label>
          <Input id="maxParticipants" name="maxParticipants"/>
        </div>
        <div class="flex items-center space-x-2 col-span-2">
          <input type="hidden" name="showParticipants" value="false"/>
          <Checkbox id="showParticipants" name="showParticipants" value="true"/>
          <Label for="showParticipants">Visibilité des participants</Label>
        </div>
        <Button class="mt-4 col-span-2" type="submit" :disabled="!dateRange.start || !dateRange.end">
          Ajouter
        </Button>
      </form>
    </DialogContent>
  </Dialog>
</template>
