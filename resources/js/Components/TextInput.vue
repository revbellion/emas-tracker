<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({ type: String, required: true });
const input = ref(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        v-model="model"
        ref="input"
        class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-amber-400 focus:ring-2 focus:ring-amber-400/30 focus:ring-offset-0 transition-all duration-200"
    />
</template>
