<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);
const props = defineProps({
    checked: { type: [Array, Boolean], default: false },
    value: { type: String, default: null },
});

const proxyChecked = computed({
    get() { return props.checked; },
    set(val) { emit('update:checked', val); },
});
</script>

<template>
    <input
        v-model="proxyChecked"
        :value="value"
        type="checkbox"
        class="rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-amber-600 shadow-sm focus:ring-2 focus:ring-amber-400/30 focus:ring-offset-0 transition-all duration-200"
    />
</template>
