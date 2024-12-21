<script setup lang="ts">
import {ref} from 'vue';
import {Eye, EyeOff} from 'lucide-vue-next';

defineProps<{
  modelValue: string;
  placeholder?: string;
}>();

const emits = defineEmits(['update:modelValue']);

const isPasswordVisible = ref(false);

const togglePasswordVisibility = () => {
  isPasswordVisible.value = !isPasswordVisible.value;
};
</script>

<template>
  <div class="relative">
    <input
        :type="isPasswordVisible ? 'text' : 'password'"
        class="w-full h-10 rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
        :placeholder="placeholder || 'Enter password'"
        :value="modelValue"
        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
    />
    <button
        type="button"
        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-muted hover:text-primary"
        @click="togglePasswordVisibility"
        :aria-label="isPasswordVisible ? 'Hide password' : 'Show password'"
    >
      <component :is="isPasswordVisible ? EyeOff : Eye" class="w-5 h-5" aria-hidden="true"/>
    </button>
  </div>
</template>

<style scoped>
.text-muted {
  color: #6b7280;
}

.text-primary {
  color: #3b82f6;
}
</style>
