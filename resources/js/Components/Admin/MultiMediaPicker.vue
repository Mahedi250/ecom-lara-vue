<template>
    <!-- Selected previews + add button -->
    <div class="space-y-3">
        <div v-if="selected.length" class="grid grid-cols-4 sm:grid-cols-6 gap-2">
            <div v-for="(item, i) in selected" :key="i"
                class="relative aspect-square rounded-xl overflow-hidden bg-gray-100 group">
                <img :src="item.url" class="w-full h-full object-cover" />
                <button type="button" @click="remove(i)"
                    class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition">×</button>
            </div>
        </div>

        <div
            class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center cursor-pointer hover:border-indigo-400 transition"
            @click="openModal">
            <svg class="w-8 h-8 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm text-gray-400">{{ selected.length ? 'Add more images' : 'Click to add images' }}</p>
            <p class="text-xs text-gray-300 mt-0.5">Upload new or choose from library</p>
        </div>
    </div>

    <!-- Modal -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="isOpen" class="fixed inset-0 bg-black/60 z-[100] flex items-center justify-center p-4" @click.self="isOpen = false">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[88vh] flex flex-col">

                    <!-- Header -->
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0">
                        <div class="flex gap-1 bg-gray-100 p-1 rounded-xl">
                            <button type="button" @click="tab = 'library'"
                                class="px-4 py-1.5 text-sm font-semibold rounded-lg transition"
                                :class="tab === 'library' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                                Media Library
                            </button>
                            <button type="button" @click="tab = 'upload'"
                                class="px-4 py-1.5 text-sm font-semibold rounded-lg transition"
                                :class="tab === 'upload' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                                Upload New
                            </button>
                        </div>
                        <button type="button" @click="isOpen = false" class="text-gray-400 hover:text-gray-600 w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 text-lg leading-none">✕</button>
                    </div>

                    <!-- Library tab -->
                    <template v-if="tab === 'library'">
                        <div class="px-4 py-3 border-b border-gray-50 flex-shrink-0">
                            <input v-model="search" type="text" placeholder="Search files..."
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                        </div>
                        <div class="flex-1 overflow-y-auto p-4">
                            <div v-if="loading" class="flex items-center justify-center py-20 text-gray-400 text-sm">Loading...</div>
                            <div v-else-if="filteredItems.length" class="grid grid-cols-4 sm:grid-cols-6 gap-2.5">
                                <div v-for="item in filteredItems" :key="item.id"
                                    @click="togglePick(item)"
                                    class="group relative aspect-square rounded-xl overflow-hidden border-2 transition cursor-pointer"
                                    :class="isPicked(item) ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-100 hover:border-indigo-300'"
                                >
                                    <img :src="item.url" :alt="item.original_name" class="w-full h-full object-cover" />
                                    <div v-if="isPicked(item)"
                                        class="absolute inset-0 bg-indigo-500/20 flex items-center justify-center">
                                        <div class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center shadow">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/60 to-transparent px-1.5 py-1 opacity-0 group-hover:opacity-100 transition">
                                        <p class="text-[9px] text-white truncate">{{ item.original_name }}</p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-16 text-gray-400 text-sm">
                                <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                No media found. Switch to Upload tab.
                            </div>
                        </div>
                        <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between flex-shrink-0">
                            <p class="text-xs text-gray-400">{{ picked.length }} image(s) selected</p>
                            <div class="flex gap-2">
                                <button type="button" @click="isOpen = false" class="border border-gray-200 text-gray-600 text-sm px-4 py-2 rounded-xl hover:bg-gray-50 transition">Cancel</button>
                                <button type="button" @click="confirmLibrary" :disabled="!picked.length"
                                    class="bg-indigo-600 text-white text-sm px-5 py-2 rounded-xl hover:bg-indigo-700 disabled:opacity-40 transition font-semibold">
                                    Add {{ picked.length || '' }} Image{{ picked.length !== 1 ? 's' : '' }}
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Upload tab -->
                    <template v-if="tab === 'upload'">
                        <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-4">
                            <div
                                class="border-2 border-dashed rounded-2xl p-10 text-center transition cursor-pointer"
                                :class="dragging ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300'"
                                @click="$refs.uploadInput.click()"
                                @dragover.prevent="dragging = true"
                                @dragleave="dragging = false"
                                @drop.prevent="onDrop"
                            >
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <p class="text-sm text-gray-500 font-medium mb-1">Drag & drop or <span class="text-indigo-600 font-semibold">browse</span></p>
                                <p class="text-xs text-gray-400">PNG, JPG, WebP — max 4MB each</p>
                            </div>
                            <input ref="uploadInput" type="file" accept="image/*" multiple class="hidden" @change="onUploadFiles" />

                            <div v-if="queue.length" class="space-y-2">
                                <div v-for="q in queue" :key="q.name"
                                    class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-2.5">
                                    <div class="flex-1">
                                        <p class="text-xs font-medium text-gray-700 truncate">{{ q.name }}</p>
                                        <div class="mt-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-indigo-500 rounded-full transition-all duration-500"
                                                :style="{ width: q.progress + '%' }"></div>
                                        </div>
                                    </div>
                                    <span v-if="q.done" class="text-green-500 text-sm font-bold flex-shrink-0">✓</span>
                                    <span v-else-if="q.error" class="text-red-500 text-xs flex-shrink-0 max-w-[120px] truncate" :title="q.error">✕ {{ q.error }}</span>
                                    <span v-else class="text-gray-400 text-xs flex-shrink-0">{{ q.progress }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-2 flex-shrink-0">
                            <button type="button" @click="isOpen = false" class="border border-gray-200 text-gray-600 text-sm px-4 py-2 rounded-xl hover:bg-gray-50 transition">Done</button>
                        </div>
                    </template>

                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, computed } from 'vue';

const emit = defineEmits(['change']);

// selected = [{ url, path }] — already-added images shown in grid
const selected = ref([]);

const isOpen = ref(false);
const tab = ref('library');
const loading = ref(false);
const items = ref([]);
const search = ref('');
const picked = ref([]); // items checked in current modal session
const dragging = ref(false);
const queue = ref([]);

const filteredItems = computed(() => {
    const q = search.value.toLowerCase();
    return q ? items.value.filter(i => i.original_name.toLowerCase().includes(q)) : items.value;
});

function isPicked(item) {
    return picked.value.some(p => p.id === item.id);
}

function togglePick(item) {
    const idx = picked.value.findIndex(p => p.id === item.id);
    if (idx === -1) picked.value.push(item);
    else picked.value.splice(idx, 1);
}

async function openModal() {
    isOpen.value = true;
    tab.value = 'library';
    picked.value = [];
    await fetchMedia();
}

async function fetchMedia() {
    loading.value = true;
    try {
        const { data } = await window.axios.get(route('admin.media.index'), {
            params: { per_page: 300 },
            headers: { Accept: 'application/json', 'X-Inertia': false },
        });
        items.value = data.media?.data ?? [];
    } catch {
        items.value = [];
    } finally {
        loading.value = false;
    }
}

function confirmLibrary() {
    picked.value.forEach(item => {
        if (!selected.value.some(s => s.path === item.path)) {
            selected.value.push({ url: item.url, path: item.path });
        }
    });
    emitChange();
    isOpen.value = false;
}

function remove(i) {
    selected.value.splice(i, 1);
    emitChange();
}

function emitChange() {
    emit('change', selected.value.map(s => s.path));
}

async function onDrop(e) {
    dragging.value = false;
    const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
    await uploadFiles(files);
}

async function onUploadFiles(e) {
    const files = Array.from(e.target.files);
    await uploadFiles(files);
    e.target.value = '';
}

async function uploadFiles(files) {
    for (const file of files) {
        const entry = { name: file.name, progress: 0, done: false, error: null };
        queue.value.push(entry);
        const fd = new FormData();
        fd.append('file', file);
        try {
            const { data } = await window.axios.post(route('admin.media.store'), fd, {
                onUploadProgress: e => { entry.progress = Math.round((e.loaded / e.total) * 90); },
            });
            entry.progress = 100;
            entry.done = true;
            items.value.unshift(data);
            if (!selected.value.some(s => s.path === data.path)) {
                selected.value.push({ url: data.url, path: data.path });
            }
            emitChange();
        } catch (err) {
            entry.progress = 0;
            entry.error = err?.response?.data?.message ?? 'Upload failed';
        }
    }
    setTimeout(() => { queue.value = []; }, 1500);
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
