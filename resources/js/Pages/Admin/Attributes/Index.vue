<template>
    <AdminLayout title="Attributes" subtitle="Manage product attribute types and values">
        <template #header-actions>
            <button @click="showNewAttr = true"
                class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-3 sm:px-4 py-2 rounded-xl transition flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">New Attribute</span>
            </button>
        </template>

        <div class="space-y-4">

            <!-- New Attribute form -->
            <div v-if="showNewAttr" class="bg-white rounded-2xl border border-indigo-200 shadow-sm p-5">
                <h3 class="font-semibold text-gray-900 mb-4">New Attribute</h3>
                <form @submit.prevent="submitNewAttr" class="grid grid-cols-2 sm:grid-cols-4 gap-3 items-end">
                    <div class="col-span-2 sm:col-span-1">
                        <label class="label">Name <span class="text-red-500">*</span></label>
                        <input v-model="newAttr.name" type="text" placeholder="e.g. Color" class="input" required />
                        <p v-if="errors['name']" class="error">{{ errors['name'] }}</p>
                    </div>
                    <div>
                        <label class="label">Type</label>
                        <select v-model="newAttr.type" class="input">
                            <option value="select">Select</option>
                            <option value="color">Color</option>
                            <option value="text">Text</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Sort Order</label>
                        <input v-model.number="newAttr.sort_order" type="number" min="0" class="input" />
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2.5 rounded-xl transition">
                            Create
                        </button>
                        <button type="button" @click="showNewAttr = false" class="px-3 py-2.5 text-sm text-gray-500 hover:text-gray-700 border border-gray-200 rounded-xl transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Attribute cards -->
            <div v-for="attr in attributes" :key="attr.id"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

                <!-- Attribute header -->
                <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-50">
                    <div v-if="editingAttr?.id === attr.id" class="flex-1 grid grid-cols-2 sm:grid-cols-4 gap-2 items-end">
                        <input v-model="editingAttr.name" type="text" class="input col-span-2 sm:col-span-1" />
                        <select v-model="editingAttr.type" class="input">
                            <option value="select">Select</option>
                            <option value="color">Color</option>
                            <option value="text">Text</option>
                        </select>
                        <input v-model.number="editingAttr.sort_order" type="number" class="input" placeholder="Sort" />
                        <div class="flex gap-2">
                            <button @click="saveAttr(attr)" class="flex-1 bg-indigo-600 text-white text-xs font-bold py-2 rounded-lg">Save</button>
                            <button @click="editingAttr = null" class="px-3 py-2 text-xs text-gray-500 border border-gray-200 rounded-lg">×</button>
                        </div>
                    </div>
                    <template v-else>
                        <div class="flex-1 min-w-0">
                            <span class="font-semibold text-gray-900">{{ attr.name }}</span>
                            <span class="ml-2 text-xs text-gray-400 font-mono">{{ attr.type ?? 'select' }}</span>
                        </div>
                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full font-medium">
                            {{ attr.values.length }} value{{ attr.values.length !== 1 ? 's' : '' }}
                        </span>
                        <button @click="startEditAttr(attr)" class="text-gray-400 hover:text-indigo-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button @click="deleteAttr(attr)"
                            class="text-gray-400 hover:text-red-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </template>
                </div>

                <!-- Values -->
                <div class="p-4">
                    <div class="flex flex-wrap gap-2 mb-3">
                        <template v-for="val in attr.values" :key="val.id">
                            <!-- Editing a value -->
                            <div v-if="editingValue?.id === val.id"
                                class="flex items-center gap-1.5 border border-indigo-300 rounded-xl px-2 py-1.5 bg-indigo-50">
                                <input v-model="editingValue.color_code" v-if="attr.type === 'color'"
                                    type="color" class="w-6 h-6 rounded cursor-pointer border-0 p-0" />
                                <input v-model="editingValue.value" type="text" placeholder="value"
                                    class="w-20 text-xs border border-gray-200 rounded px-1.5 py-1 focus:outline-none focus:ring-1 focus:ring-indigo-300" />
                                <input v-model="editingValue.label" type="text" placeholder="label"
                                    class="w-20 text-xs border border-gray-200 rounded px-1.5 py-1 focus:outline-none focus:ring-1 focus:ring-indigo-300" />
                                <button @click="saveValue(attr, val)" class="text-xs bg-indigo-600 text-white px-2 py-1 rounded-lg font-bold">✓</button>
                                <button @click="editingValue = null" class="text-xs text-gray-400 hover:text-gray-600">×</button>
                            </div>
                            <!-- Display value -->
                            <div v-else class="group flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-gray-200 bg-gray-50 text-sm">
                                <span v-if="val.color_code" class="w-3.5 h-3.5 rounded-full border border-white shadow-sm flex-shrink-0"
                                    :style="{ background: val.color_code }"></span>
                                <span class="font-medium text-gray-700">{{ val.label || val.value }}</span>
                                <span v-if="val.label && val.label !== val.value" class="text-gray-400 text-xs font-mono">({{ val.value }})</span>
                                <div class="hidden group-hover:flex items-center gap-1 ml-1">
                                    <button @click="startEditValue(val)" class="text-gray-400 hover:text-indigo-600">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button @click="deleteValue(attr, val)" class="text-gray-400 hover:text-red-500">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- Add value inline -->
                        <div v-if="addingValueTo === attr.id" class="flex items-center gap-1.5 border border-indigo-300 rounded-xl px-2 py-1.5 bg-indigo-50">
                            <input v-if="attr.type === 'color'" v-model="newValue.color_code"
                                type="color" class="w-6 h-6 rounded cursor-pointer border-0 p-0" />
                            <input v-model="newValue.value" type="text" placeholder="value" autofocus
                                class="w-20 text-xs border border-gray-200 rounded px-1.5 py-1 focus:outline-none focus:ring-1 focus:ring-indigo-300" />
                            <input v-model="newValue.label" type="text" placeholder="label (optional)"
                                class="w-28 text-xs border border-gray-200 rounded px-1.5 py-1 focus:outline-none focus:ring-1 focus:ring-indigo-300" />
                            <button @click="submitNewValue(attr)" class="text-xs bg-indigo-600 text-white px-2 py-1 rounded-lg font-bold">Add</button>
                            <button @click="addingValueTo = null" class="text-xs text-gray-400 hover:text-gray-600">×</button>
                        </div>

                        <button v-else @click="startAddValue(attr.id)"
                            class="flex items-center gap-1 px-3 py-1.5 rounded-xl border border-dashed border-gray-300 text-sm text-gray-400 hover:border-indigo-400 hover:text-indigo-600 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add value
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-if="!attributes.length" class="text-center py-16 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <p>No attributes yet. Create one above.</p>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ attributes: Array });
const page = usePage();
const errors = computed(() => page.props.errors ?? {});

const showNewAttr = ref(false);
const editingAttr = ref(null);
const editingValue = ref(null);
const addingValueTo = ref(null);

const newAttr = ref({ name: '', type: 'select', sort_order: 0 });
const newValue = ref({ value: '', label: '', color_code: '' });

function submitNewAttr() {
    router.post(route('admin.attributes.store'), newAttr.value, {
        onSuccess: () => {
            showNewAttr.value = false;
            newAttr.value = { name: '', type: 'select', sort_order: 0 };
        },
        preserveScroll: true,
    });
}

function startEditAttr(attr) {
    editingAttr.value = { id: attr.id, name: attr.name, type: attr.type ?? 'select', sort_order: attr.sort_order ?? 0 };
}

function saveAttr(attr) {
    router.put(route('admin.attributes.update', attr.id), editingAttr.value, {
        onSuccess: () => { editingAttr.value = null; },
        preserveScroll: true,
    });
}

function deleteAttr(attr) {
    if (!confirm(`Delete "${attr.name}" and all its values? This cannot be undone.`)) return;
    router.delete(route('admin.attributes.destroy', attr.id), { preserveScroll: true });
}

function startAddValue(attrId) {
    addingValueTo.value = attrId;
    newValue.value = { value: '', label: '', color_code: '' };
    editingValue.value = null;
}

function submitNewValue(attr) {
    if (!newValue.value.value.trim()) return;
    router.post(route('admin.attributes.values.store', attr.id), newValue.value, {
        onSuccess: () => {
            addingValueTo.value = null;
            newValue.value = { value: '', label: '', color_code: '' };
        },
        preserveScroll: true,
    });
}

function startEditValue(val) {
    editingValue.value = { id: val.id, value: val.value, label: val.label ?? '', color_code: val.color_code ?? '' };
    addingValueTo.value = null;
}

function saveValue(attr, val) {
    router.put(route('admin.attributes.values.update', [attr.id, val.id]), editingValue.value, {
        onSuccess: () => { editingValue.value = null; },
        preserveScroll: true,
    });
}

function deleteValue(attr, val) {
    if (!confirm(`Delete value "${val.label || val.value}"?`)) return;
    router.delete(route('admin.attributes.values.destroy', [attr.id, val.id]), { preserveScroll: true });
}
</script>

<style scoped>
.label { @apply block text-sm font-medium text-gray-700 mb-1; }
.input { @apply w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent; }
.error { @apply text-red-500 text-xs mt-1; }
</style>
