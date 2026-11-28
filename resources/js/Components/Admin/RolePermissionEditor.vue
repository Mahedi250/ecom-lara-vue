<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <p class="text-sm font-semibold text-gray-700">Access list</p>
            <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-600">
                <input
                    type="checkbox"
                    :checked="allSelected"
                    @change="toggleAll"
                    class="w-4 h-4 text-indigo-600 rounded"
                />
                Allow all
            </label>
        </div>

        <div v-for="group in groups" :key="group.name" class="border border-gray-100 rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between bg-gray-50/70 px-4 py-2.5">
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500">{{ group.name }}</p>
                <label class="flex items-center gap-2 cursor-pointer text-xs text-gray-500 font-medium">
                    <input
                        type="checkbox"
                        :checked="groupAllSelected(group)"
                        @change="toggleGroup(group)"
                        class="w-3.5 h-3.5 text-indigo-600 rounded"
                    />
                    All
                </label>
            </div>

            <div class="divide-y divide-gray-50">
                <div v-for="item in group.items" :key="item.key" class="px-4 py-3">
                    <div class="flex items-center justify-between gap-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                :checked="modelValue.includes(item.permission)"
                                @change="toggle(item.permission)"
                                class="w-4 h-4 text-indigo-600 rounded"
                            />
                            <span class="text-sm font-semibold text-gray-800">{{ item.label }}</span>
                        </label>
                        <button
                            v-if="item.actions.length"
                            type="button"
                            @click="toggleExpand(item.key)"
                            class="text-xs text-gray-400 hover:text-indigo-600 font-medium flex items-center gap-1"
                        >
                            {{ expanded.includes(item.key) ? 'Hide actions' : `${item.actions.length} actions` }}
                            <svg class="w-3 h-3 transition-transform" :class="expanded.includes(item.key) ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Action permissions -->
                    <div v-if="expanded.includes(item.key) && item.actions.length" class="flex flex-wrap gap-2 mt-2.5 pl-7">
                        <label
                            v-for="action in item.actions"
                            :key="action.name"
                            class="flex items-center gap-1.5 cursor-pointer border rounded-lg px-2.5 py-1.5 text-xs transition"
                            :class="modelValue.includes(action.name)
                                ? 'border-indigo-300 bg-indigo-50 text-indigo-700'
                                : 'border-gray-200 text-gray-500 hover:border-gray-300'"
                        >
                            <input
                                type="checkbox"
                                :checked="modelValue.includes(action.name)"
                                @change="toggle(action.name)"
                                class="w-3.5 h-3.5 text-indigo-600 rounded"
                            />
                            {{ action.label }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    menuItems: { type: Array, required: true },
    modelValue: { type: Array, default: () => [] },
});
const emit = defineEmits(['update:modelValue']);

const expanded = ref([]);

const groups = computed(() => {
    const out = [];
    props.menuItems.forEach((item) => {
        let g = out.find((x) => x.name === item.group);
        if (!g) { g = { name: item.group, items: [] }; out.push(g); }
        g.items.push(item);
    });
    return out;
});

const allPermissions = computed(() =>
    props.menuItems.flatMap((i) => [i.permission, ...i.actions.map((a) => a.name)]).filter(Boolean)
);

const allSelected = computed(() =>
    allPermissions.value.every((p) => props.modelValue.includes(p))
);

function update(permissions) {
    emit('update:modelValue', permissions);
}

function toggle(permission) {
    if (!permission) return;
    update(props.modelValue.includes(permission)
        ? props.modelValue.filter((p) => p !== permission)
        : [...props.modelValue, permission]);
}

function toggleAll() {
    update(allSelected.value ? [] : allPermissions.value);
}

function groupAll(group) {
    return group.items.flatMap((i) => [i.permission, ...i.actions.map((a) => a.name)]).filter(Boolean);
}

function groupAllSelected(group) {
    return groupAll(group).every((p) => props.modelValue.includes(p));
}

function toggleGroup(group) {
    const perms = groupAll(group);
    update(groupAllSelected(group)
        ? props.modelValue.filter((p) => !perms.includes(p))
        : [...new Set([...props.modelValue, ...perms])]);
}

function toggleExpand(key) {
    expanded.value = expanded.value.includes(key)
        ? expanded.value.filter((k) => k !== key)
        : [...expanded.value, key];
}
</script>
