<script setup lang="ts">
import type {HTMLAttributes} from 'vue';
import {cn} from '@/lib/utils';
import {Primitive, type PrimitiveProps} from 'radix-vue';
import {type ButtonVariants, buttonVariants} from '.';
import {Loader2} from 'lucide-vue-next';

interface Props extends PrimitiveProps {
  variant?: ButtonVariants['variant'];
  size?: ButtonVariants['size'];
  class?: HTMLAttributes['class'];
  loading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  as: 'button',
  loading: false
});
</script>

<template>
  <Primitive
      :as="as"
      :as-child="asChild"
      :class="cn(
      buttonVariants({ variant, size }),
      props.class,
      { 'opacity-50 cursor-not-allowed': loading }
    )"
      :disabled="loading"
  >
  <span v-if="loading" class="flex items-center justify-center gap-2">
      <Loader2 class="animate-spin w-4 h-4"/>
      <slot v-if="!asChild"/>
    </span>
    <slot v-else/>
  </Primitive>
</template>