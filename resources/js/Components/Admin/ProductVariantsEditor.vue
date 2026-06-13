<template>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <!-- Header + toggle -->
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="font-semibold text-gray-900 text-base">Product Variants</h2>
                <p class="text-xs text-gray-400 mt-0.5">e.g. Color, Size, Storage, RAM</p>
            </div>
            <button type="button" @click="enabled = !enabled"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                :class="enabled ? 'bg-indigo-600' : 'bg-gray-200'">
                <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform"
                    :class="enabled ? 'translate-x-5' : 'translate-x-0.5'"></span>
            </button>
        </div>

        <template v-if="enabled">
            <!-- Step 1: Select attribute axes -->
            <div class="border border-gray-100 rounded-xl p-4 mb-4 bg-gray-50">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">1. Which attributes create variants?</p>
                <div class="flex flex-wrap gap-2">
                    <label v-for="attr in attributes" :key="attr.id"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-lg border cursor-pointer transition text-sm select-none"
                        :class="selectedAttrIds.includes(attr.id)
                            ? 'bg-indigo-50 border-indigo-400 text-indigo-700 font-semibold'
                            : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300'">
                        <input type="checkbox" :value="attr.id" v-model="selectedAttrIds" class="hidden" />
                        <span class="w-3.5 h-3.5 rounded flex items-center justify-center border-2 flex-shrink-0 transition"
                            :class="selectedAttrIds.includes(attr.id) ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300'">
                            <svg v-if="selectedAttrIds.includes(attr.id)" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                        {{ attr.name }}
                    </label>
                </div>
            </div>

            <!-- Step 2: Select values per attribute -->
            <div v-if="selectedAttrIds.length" class="border border-gray-100 rounded-xl p-4 mb-4 bg-gray-50 space-y-4">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">2. Select available values</p>
                <div v-for="attrId in selectedAttrIds" :key="attrId">
                    <p class="text-xs font-semibold text-gray-700 mb-2">{{ attrName(attrId) }}</p>
                    <div class="flex flex-wrap gap-2">
                        <label v-for="val in attrValues(attrId)" :key="val.id"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border cursor-pointer transition text-xs font-medium select-none"
                            :class="isValueSelected(attrId, val.id)
                                ? 'bg-indigo-50 border-indigo-400 text-indigo-700'
                                : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300'">
                            <input type="checkbox" :value="val.id" v-model="selectedValues[attrId]" class="hidden" />
                            <span v-if="val.color_code"
                                class="w-3.5 h-3.5 rounded-full border border-white shadow-sm flex-shrink-0"
                                :style="{ background: val.color_code }"></span>
                            {{ val.label || val.value }}
                        </label>
                    </div>
                </div>
                <button type="button" @click="generateCombinations"
                    class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-4 py-2 rounded-xl transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                    Generate Combinations
                </button>
            </div>

            <!-- Step 3: Variant rows -->
            <div v-if="localVariants.length">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">3. Set details per variant</p>
                    <span class="text-xs text-gray-400">{{ localVariants.length }} variant{{ localVariants.length !== 1 ? 's' : '' }}</span>
                </div>

                <div class="space-y-3">
                    <div v-for="(variant, i) in localVariants" :key="i"
                        class="border rounded-xl p-3 transition"
                        :class="variant.is_active !== false ? 'border-gray-200 bg-white' : 'border-gray-100 bg-gray-50 opacity-60'">

                        <div class="flex gap-3">
                            <!-- Variant image -->
                            <div class="flex-shrink-0">
                                <div class="relative w-20 h-20 rounded-xl overflow-hidden border-2 border-dashed border-gray-200 hover:border-indigo-400 cursor-pointer transition bg-gray-50 group"
                                    @click="triggerVariantImageInput(i)">
                                    <img v-if="variantPreviews[i] || variant.existingImage"
                                        :src="variantPreviews[i] || variant.existingImage"
                                        class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full flex flex-col items-center justify-center gap-1">
                                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-[10px] text-gray-400">Photo</span>
                                    </div>
                                    <!-- Edit overlay -->
                                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <input :ref="el => { if (el) variantImageInputs[i] = el }" type="file" accept="image/*" class="hidden"
                                        @change="onVariantImage($event, i)" />
                                </div>
                                <p class="text-[10px] text-gray-400 text-center mt-1">{{ variantPreviews[i] ? 'Changed' : (variant.existingImage ? 'Click to replace' : 'Add image') }}</p>
                            </div>

                            <!-- Main fields -->
                            <div class="flex-1 min-w-0">
                                <!-- Attribute pills -->
                                <div class="flex items-center gap-1.5 mb-2 flex-wrap">
                                    <template v-for="(valueId, attrId) in variant.attributes" :key="attrId">
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            <span v-if="getValueColorCode(attrId, valueId)"
                                                class="w-2.5 h-2.5 rounded-full border border-white shadow-sm"
                                                :style="{ background: getValueColorCode(attrId, valueId) }"></span>
                                            <span class="text-indigo-400 font-normal">{{ attrName(attrId) }}:</span>
                                            {{ getValueLabel(attrId, valueId) }}
                                        </span>
                                    </template>
                                    <span v-if="!Object.keys(variant.attributes ?? {}).length" class="text-xs text-gray-400 italic">No attributes</span>
                                </div>

                                <!-- Inputs -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="col-span-2 sm:col-span-1">
                                        <label class="block text-xs text-gray-500 mb-1">SKU</label>
                                        <input v-model="variant.sku" type="text" placeholder="VAR-001"
                                            class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Price (৳)</label>
                                        <input v-model="variant.price" type="number" step="0.01" min="0" placeholder="Inherit"
                                            class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Sale (৳)</label>
                                        <input v-model="variant.sale_price" type="number" step="0.01" min="0" placeholder="Optional"
                                            class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Stock</label>
                                        <input v-model="variant.stock" type="number" min="0"
                                            class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                                    </div>
                                </div>

                                <!-- Active + remove -->
                                <div class="flex items-center justify-between mt-2">
                                    <label class="flex items-center gap-2 cursor-pointer select-none">
                                        <input v-model="variant.is_active" type="checkbox" class="w-4 h-4 text-indigo-600 rounded border-gray-300" />
                                        <span class="text-xs text-gray-600">Active</span>
                                    </label>
                                    <button type="button" @click="removeVariant(i)"
                                        class="flex items-center gap-1 text-xs text-red-400 hover:text-red-600 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" @click="addBlankVariant"
                    class="mt-3 flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-700 font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Variant Manually
                </button>
            </div>

            <!-- Empty state -->
            <div v-else-if="selectedAttrIds.length && !localVariants.length" class="text-center py-8 text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h7"/>
                </svg>
                <p class="text-sm">Select values above then click "Generate Combinations"</p>
                <button type="button" @click="addBlankVariant" class="mt-3 text-sm text-indigo-600 hover:text-indigo-700 font-semibold transition">
                    or add one manually
                </button>
            </div>
            <div v-else-if="!selectedAttrIds.length" class="text-center py-6 text-gray-400 text-sm">
                Select attributes above to define variant dimensions.
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    attributes: { type: Array, default: () => [] },
});
const emit = defineEmits(['update:modelValue', 'variant-image-change']);

const enabled = ref(false);
const selectedAttrIds = ref([]);
const selectedValues = ref({});
const localVariants = ref([]);

// Ensure selectedValues[id] is always an array when attribute is added
watch(selectedAttrIds, (ids) => {
    ids.forEach(id => {
        if (!Array.isArray(selectedValues.value[id])) {
            selectedValues.value[id] = [];
        }
    });
}, { deep: true, immediate: true });

// Image previews (blob URLs) keyed by variant index
const variantPreviews = ref({});
const variantImageInputs = ref({});

function triggerVariantImageInput(i) {
    variantImageInputs.value[i]?.click();
}

function onVariantImage(e, i) {
    const file = e.target.files[0];
    if (!file) return;
    variantPreviews.value[i] = URL.createObjectURL(file);
    emit('variant-image-change', { index: i, file });
}

// Initialize from existing variants (edit mode)
function initFromExisting(variants) {
    if (!variants?.length) return;
    localVariants.value = variants.map(v => ({
        id: v.id ?? null,
        sku: v.sku ?? '',
        price: v.price ?? '',
        sale_price: v.sale_price ?? '',
        stock: v.stock ?? 0,
        is_active: v.is_active ?? true,
        attributes: v.attributes_map ?? {},
        existingImage: v.image ?? null,
    }));
    enabled.value = true;

    // Derive selected attr IDs and values from existing variants
    const attrIds = new Set();
    const valMap = {};
    localVariants.value.forEach(v => {
        Object.entries(v.attributes ?? {}).forEach(([attrId, valueId]) => {
            const aid = Number(attrId);
            attrIds.add(aid);
            if (!valMap[aid]) valMap[aid] = new Set();
            valMap[aid].add(Number(valueId));
        });
    });
    selectedAttrIds.value = [...attrIds];
    const vals = {};
    Object.entries(valMap).forEach(([aid, vids]) => { vals[aid] = [...vids]; });
    selectedValues.value = vals;
}

watch(() => props.modelValue, (val) => {
    if (val?.length && !localVariants.value.length) {
        initFromExisting(val);
    }
}, { immediate: true });

watch(localVariants, (val) => emit('update:modelValue', val), { deep: true });

watch(enabled, (val) => {
    if (!val) {
        localVariants.value = [];
        variantPreviews.value = {};
        emit('update:modelValue', []);
    }
});

// Helpers
function attrName(id) { return props.attributes.find(a => a.id == id)?.name ?? String(id); }
function attrValues(id) { return props.attributes.find(a => a.id == id)?.values ?? []; }
function isValueSelected(attrId, valueId) { return (selectedValues.value[attrId] ?? []).includes(valueId); }
function getValueLabel(attrId, valueId) {
    const val = attrValues(attrId).find(v => v.id == valueId);
    return val?.label || val?.value || String(valueId);
}
function getValueColorCode(attrId, valueId) {
    return attrValues(attrId).find(v => v.id == valueId)?.color_code ?? null;
}

function cartesian(arrays) {
    if (!arrays.length) return [[]];
    const [first, ...rest] = arrays;
    const restProduct = cartesian(rest);
    return first.flatMap(v => restProduct.map(r => [v, ...r]));
}

function generateCombinations() {
    const axes = selectedAttrIds.value
        .map(id => ({ id, values: (selectedValues.value[id] ?? []).map(vid => ({ attrId: id, valueId: vid })) }))
        .filter(a => a.values.length);
    if (!axes.length) return;

    const combos = cartesian(axes.map(a => a.values));
    const existingKeys = new Set(
        localVariants.value.map(v =>
            Object.entries(v.attributes ?? {}).sort(([a], [b]) => a - b).map(([k, v]) => `${k}:${v}`).join('|')
        )
    );

    combos.forEach(combo => {
        const attrs = Object.fromEntries(combo.map(c => [c.attrId, c.valueId]));
        const key = Object.entries(attrs).sort(([a], [b]) => a - b).map(([k, v]) => `${k}:${v}`).join('|');
        if (!existingKeys.has(key)) {
            existingKeys.add(key);
            localVariants.value.push({ id: null, sku: '', price: '', sale_price: '', stock: 0, is_active: true, attributes: attrs, existingImage: null });
        }
    });
}

function removeVariant(i) {
    localVariants.value.splice(i, 1);
    const newPreviews = {};
    Object.entries(variantPreviews.value).forEach(([idx, url]) => {
        const ni = Number(idx);
        if (ni < i) newPreviews[ni] = url;
        else if (ni > i) newPreviews[ni - 1] = url;
    });
    variantPreviews.value = newPreviews;
}

function addBlankVariant() {
    localVariants.value.push({ id: null, sku: '', price: '', sale_price: '', stock: 0, is_active: true, attributes: {}, existingImage: null });
}
</script>
