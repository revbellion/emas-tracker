<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: { type: String, default: 'right' },
    width: { type: String, default: '48' },
    contentClasses: { type: String, default: 'py-1 bg-white dark:bg-gray-800 border border-amber-200/50 dark:border-gray-700 rounded-xl shadow-xl shadow-amber-200/10 dark:shadow-black/20' },
});

const open = ref(false);
const closeOnEscape = (e) => { if (open.value && e.key === 'Escape') open.value = false; };

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const widthClass = computed(() => ({ '48': 'w-48' }[props.width]) ?? 'w-48');
const alignmentClasses = computed(() => {
    if (props.align === 'left') return 'start-0 ltr:origin-top-left rtl:origin-top-right';
    if (props.align === 'right') return 'end-0 ltr:origin-top-right rtl:origin-top-left';
    return 'end-0 ltr:origin-top-right rtl:origin-top-left';
});
</script>

<template>
    <div class="relative">
        <div @click="open = !open">
            <slot name="trigger" />
        </div>
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
            >
                <div v-show="open" class="absolute z-50 mt-2 rounded-xl shadow-xl" :class="[widthClass, alignmentClasses]">
                    <div class="rounded-xl ring-1 ring-black/5 overflow-hidden" :class="props.contentClasses" @click="open = false">
                        <slot name="content" />
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>
