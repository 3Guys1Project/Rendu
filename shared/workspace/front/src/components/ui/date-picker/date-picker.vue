<script setup lang="ts">
import {Ref, ref, watch} from 'vue';
import {Popover, PopoverContent, PopoverTrigger} from '@/components/ui/popover';

import {
  RangeCalendarCell,
  RangeCalendarCellTrigger,
  RangeCalendarGrid,
  RangeCalendarGridBody,
  RangeCalendarGridHead,
  RangeCalendarGridRow,
  RangeCalendarHeadCell
} from '@/components/ui/range-calendar';

import {CalendarDate, type DateValue, isEqualMonth} from '@internationalized/date';

import {Calendar as CalendarIcon, ChevronLeft, ChevronRight} from 'lucide-vue-next';

import {type DateRange, RangeCalendarRoot, useDateFormatter} from 'radix-vue';
import {createMonth, type Grid, toDate} from 'radix-vue/date';
import {cn} from '@/lib/utils.ts';

// Accept modelValue with optional start/end
const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({start: null, end: null})
  }
});

const emit = defineEmits(['update:modelValue']);

// Ensure we have a fallback date if null
const fallbackStart = new CalendarDate(2022, 1, 1);
const fallbackEnd = fallbackStart.add({days: 7}); // Arbitrary default range

// Convert modelValue to CalendarDate if available, else use fallback
const initialStart = props.modelValue.start
    ? new CalendarDate(props.modelValue.start.getFullYear(), props.modelValue.start.getMonth() + 1, props.modelValue.start.getDate())
    : fallbackStart;

const initialEnd = props.modelValue.end
    ? new CalendarDate(props.modelValue.end.getFullYear(), props.modelValue.end.getMonth() + 1, props.modelValue.end.getDate())
    : fallbackEnd;

const value = ref<DateRange>({
  start: initialStart,
  end: initialEnd
});

const locale = ref('en-US');
const formatter = useDateFormatter(locale.value);

// Ensure placeholders are never null
const placeholder = ref<CalendarDate>(initialStart);
const secondMonthPlaceholder = ref<CalendarDate>(initialEnd);

// Create initial months
const firstMonth = ref(
    createMonth({
      dateObj: initialStart,
      locale: locale.value,
      fixedWeeks: true,
      weekStartsOn: 0
    })
) as unknown as Ref<Grid<DateValue>>;

const secondMonth = ref(
    createMonth({
      dateObj: initialEnd,
      locale: locale.value,
      fixedWeeks: true,
      weekStartsOn: 0
    })
) as unknown as Ref<Grid<DateValue>>;

function updateMonth(reference: 'first' | 'second', months: number) {
  if (reference === 'first') {
    placeholder.value = placeholder.value.add({months});
  } else {
    secondMonthPlaceholder.value = secondMonthPlaceholder.value.add({months});
  }
}

watch(placeholder, (_placeholder) => {
  if (!_placeholder) return;
  firstMonth.value = createMonth({
    dateObj: _placeholder as CalendarDate,
    weekStartsOn: 0,
    fixedWeeks: false,
    locale: locale.value
  });
  if (isEqualMonth(secondMonthPlaceholder.value as CalendarDate, _placeholder as CalendarDate)) {
    secondMonthPlaceholder.value = secondMonthPlaceholder.value.add({months: 1});
  }
});

watch(secondMonthPlaceholder, (_secondMonthPlaceholder) => {
  if (!_secondMonthPlaceholder) return;
  secondMonth.value = createMonth({
    dateObj: _secondMonthPlaceholder as CalendarDate,
    weekStartsOn: 0,
    fixedWeeks: false,
    locale: locale.value
  });
  if (isEqualMonth(_secondMonthPlaceholder as CalendarDate, placeholder.value as CalendarDate)) {
    placeholder.value = placeholder.value.subtract({months: 1});
  }
});

// Emit updates as JS Dates when the user changes the selection
watch(value, (newVal) => {
  emit('update:modelValue', {
    start: newVal.start
        ? new Date(newVal.start.year, newVal.start.month - 1, newVal.start.day)
        : null,
    end: newVal.end
        ? new Date(newVal.end.year, newVal.end.month - 1, newVal.end.day)
        : null
  });
});
</script>

<template>
  <Popover>
    <PopoverTrigger as-child>
      <button
          class="w-full justify-start text-left font-normal border border-gray-300 rounded-md px-3 py-2 flex flex-row items-center"
      >
        <CalendarIcon class="mr-2 h-4 w-4"/>
        <template v-if="modelValue.start && modelValue.end">
          {{ modelValue.start.toLocaleDateString() }} - {{ modelValue.end.toLocaleDateString() }}
        </template>
        <template v-else-if="modelValue.start">
          {{ modelValue.start.toLocaleDateString() }}
        </template>
        <template v-else>
          Selectionnez une date
        </template>
      </button>
    </PopoverTrigger>
    <PopoverContent class="w-auto p-0">
      <RangeCalendarRoot v-slot="{ weekDays }" v-model="value as DateRange"
                         v-model:placeholder="placeholder as DateValue" class="p-3">
        <div class="flex flex-col gap-y-4 mt-4 sm:flex-row sm:gap-x-4 sm:gap-y-0">
          <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
              <button
                  :class="cn('h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100 border border-transparent rounded')"
                  @click="updateMonth('first', -1)"
              >
                <ChevronLeft class="h-4 w-4"/>
              </button>
              <div class="text-sm font-medium">
                {{ formatter.fullMonthAndYear(toDate(firstMonth.value)) }}
              </div>
              <button
                  :class="cn('h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100 border border-transparent rounded')"
                  @click="updateMonth('first', 1)"
              >
                <ChevronRight class="h-4 w-4"/>
              </button>
            </div>
            <RangeCalendarGrid>
              <RangeCalendarGridHead>
                <RangeCalendarGridRow>
                  <RangeCalendarHeadCell
                      v-for="day in weekDays"
                      :key="day"
                      class="w-full"
                  >
                    {{ day }}
                  </RangeCalendarHeadCell>
                </RangeCalendarGridRow>
              </RangeCalendarGridHead>
              <RangeCalendarGridBody>
                <RangeCalendarGridRow
                    v-for="(weekDates, index) in firstMonth.rows"
                    :key="`weekDate-${index}`"
                    class="mt-2 w-full"
                >
                  <RangeCalendarCell
                      v-for="weekDate in weekDates"
                      :key="weekDate.toString()"
                      :date="weekDate"
                  >
                    <RangeCalendarCellTrigger
                        :day="weekDate"
                        :month="firstMonth.value"
                    />
                  </RangeCalendarCell>
                </RangeCalendarGridRow>
              </RangeCalendarGridBody>
            </RangeCalendarGrid>
          </div>
          <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
              <button
                  :class="cn('h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100 border border-transparent rounded')"
                  @click="updateMonth('second', -1)"
              >
                <ChevronLeft class="h-4 w-4"/>
              </button>
              <div class="text-sm font-medium">
                {{ formatter.fullMonthAndYear(toDate(secondMonth.value)) }}
              </div>
              <button
                  :class="cn('h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100 border border-transparent rounded')"
                  @click="updateMonth('second', 1)"
              >
                <ChevronRight class="h-4 w-4"/>
              </button>
            </div>
            <RangeCalendarGrid>
              <RangeCalendarGridHead>
                <RangeCalendarGridRow>
                  <RangeCalendarHeadCell
                      v-for="day in weekDays"
                      :key="day"
                      class="w-full"
                  >
                    {{ day }}
                  </RangeCalendarHeadCell>
                </RangeCalendarGridRow>
              </RangeCalendarGridHead>
              <RangeCalendarGridBody>
                <RangeCalendarGridRow
                    v-for="(weekDates, index) in secondMonth.rows"
                    :key="`weekDate-${index}`"
                    class="mt-2 w-full"
                >
                  <RangeCalendarCell
                      v-for="weekDate in weekDates"
                      :key="weekDate.toString()"
                      :date="weekDate"
                  >
                    <RangeCalendarCellTrigger
                        :day="weekDate"
                        :month="secondMonth.value"
                    />
                  </RangeCalendarCell>
                </RangeCalendarGridRow>
              </RangeCalendarGridBody>
            </RangeCalendarGrid>
          </div>
        </div>
      </RangeCalendarRoot>
    </PopoverContent>
  </Popover>
</template>
