<template>
    <AdminLayout title="Media Library" subtitle="Manage uploaded files">
        <template #header-actions>
            <button @click="$refs.uploadInput.click()"
                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Upload Files
            </button>
            <input ref="uploadInput" type="file" multiple accept="image/*" class="hidden" @change="uploadFiles" />
        </template>

        <!-- Upload drop zone -->
        <div
            class="border-2 border-dashed rounded-2xl p-8 text-center mb-6 transition"
            :class="dragging ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200 bg-white'"
            @dragover.prevent="dragging = true"
            @dragleave="dragging = false"
            @drop.prevent="onDrop"
        >
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            <p class="text-sm text-gray-500 mb-1">Drag & drop images here or
                <button @click="$refs.uploadInput.click()" class="text-indigo-600 font-semibold hover:underline">browse</button>
            </p>
            <p class="text-xs text-gray-400">PNG, JPG, WebP, SVG — max 4MB each</p>

            <!-- Upload progress -->
            <div v-if="uploading.length" class="mt-4 space-y-2 max-w-xs mx-auto">
                <div v-for="u in uploading" :key="u.name" class="flex items-center gap-2 text-xs text-gray-600">
                    <div class="flex-1 bg-gray-200 rounded-full h-1.5 overflow-hidden">
                        <div class="h-full bg-indigo-500 transition-all" :style="{ width: u.done ? '100%' : '60%' }"></div>
                    </div>
                    <span class="truncate max-w-[120px]">{{ u.name }}</span>
                    <span v-if="u.done" class="text-green-500">✓</span>
                </div>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-gray-500"><span class="font-semibold text-gray-800">{{ media.total }}</span> files</p>
            <input v-model="search" type="text" placeholder="Search files..."
                class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 w-48" />
        </div>

        <!-- Media grid -->
        <div v-if="filteredMedia.length" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
            <div v-for="item in filteredMedia" :key="item.id"
                class="group relative aspect-square rounded-xl overflow-hidden border border-gray-100 bg-gray-50 hover:border-indigo-300 transition cursor-pointer"
                @click="preview = item"
            >
                <img :src="item.url" :alt="item.original_name"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" />
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition"></div>
                <button
                    @click.stop="confirmDelete(item)"
                    class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full text-xs items-center justify-center hidden group-hover:flex shadow transition hover:bg-red-600"
                >✕</button>
            </div>
        </div>
        <div v-else class="text-center py-20 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p>No media yet. Upload some files above.</p>
        </div>

        <!-- Pagination -->
        <div v-if="media.last_page > 1" class="mt-6 flex justify-center gap-1">
            <Link v-for="link in media.links" :key="link.label" :href="link.url ?? '#'"
                :class="['px-3 py-1.5 rounded-lg text-xs transition', link.active ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-gray-100', !link.url ? 'opacity-40 pointer-events-none' : '']"
                v-html="link.label" />
        </div>

        <!-- Preview modal -->
        <Transition name="fade">
            <div v-if="preview" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4" @click.self="preview = null">
                <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
                    <div class="relative bg-gray-100">
                        <img :src="preview.url" :alt="preview.original_name" class="w-full max-h-72 object-contain" />
                    </div>
                    <div class="p-4 space-y-2">
                        <p class="font-semibold text-gray-800 text-sm truncate">{{ preview.original_name }}</p>
                        <p class="text-xs text-gray-400">{{ formatSize(preview.size) }} · {{ preview.mime_type }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <input :value="preview.url" readonly
                                class="flex-1 border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-600 focus:outline-none" />
                            <button @click="copyUrl(preview.url)"
                                class="text-xs bg-indigo-600 text-white px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition whitespace-nowrap">
                                {{ copied ? 'Copied!' : 'Copy URL' }}
                            </button>
                        </div>
                        <div class="flex gap-2 pt-1">
                            <button @click="preview = null"
                                class="flex-1 border border-gray-200 text-gray-600 text-sm py-2 rounded-xl hover:bg-gray-50 transition">
                                Close
                            </button>
                            <button @click="confirmDelete(preview); preview = null"
                                class="flex-1 bg-red-50 text-red-600 text-sm py-2 rounded-xl hover:bg-red-100 transition font-semibold">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Delete confirm -->
        <Transition name="fade">
            <div v-if="deleteTarget" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="deleteTarget = null">
                <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
                    <h3 class="font-bold text-gray-900 text-lg mb-2">Delete File?</h3>
                    <p class="text-gray-500 text-sm mb-5">This permanently removes <strong class="text-gray-700">{{ deleteTarget.original_name }}</strong> from storage.</p>
                    <div class="flex gap-3">
                        <button @click="deleteTarget = null" class="flex-1 border border-gray-200 text-gray-600 font-semibold py-2.5 rounded-xl hover:bg-gray-50 text-sm">Cancel</button>
                        <button @click="doDelete" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl text-sm">Delete</button>
                    </div>
                </div>
            </div>
        </Transition>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import axios from 'axios';

const props = defineProps({ media: Object });

const dragging = ref(false);
const uploading = ref([]);
const preview = ref(null);
const deleteTarget = ref(null);
const search = ref('');
const copied = ref(false);

const page = usePage();

const filteredMedia = computed(() => {
    if (!search.value) return props.media.data ?? [];
    const q = search.value.toLowerCase();
    return (props.media.data ?? []).filter(m => m.original_name.toLowerCase().includes(q));
});

async function uploadFiles(e) {
    const files = Array.from(e.target.files);
    await processFiles(files);
    e.target.value = '';
}

async function onDrop(e) {
    dragging.value = false;
    const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
    await processFiles(files);
}

async function processFiles(files) {
    for (const file of files) {
        const tracker = { name: file.name, done: false };
        uploading.value.push(tracker);

        const fd = new FormData();
        fd.append('file', file);
        fd.append('_token', page.props.csrf_token ?? document.querySelector('meta[name=csrf-token]')?.content);

        try {
            await axios.post(route('admin.media.store'), fd);
            tracker.done = true;
        } catch (err) {
            uploading.value = uploading.value.filter(u => u !== tracker);
        }
    }
    setTimeout(() => {
        uploading.value = [];
        router.reload({ only: ['media'] });
    }, 800);
}

function confirmDelete(item) { deleteTarget.value = item; }
function doDelete() {
    router.delete(route('admin.media.destroy', deleteTarget.value.id), {
        onSuccess: () => { deleteTarget.value = null; },
    });
}

function formatSize(bytes) {
    if (!bytes) return '—';
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

async function copyUrl(url) {
    await navigator.clipboard.writeText(url);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 1500);
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
