<template>
    <StorefrontLayout>
        <Head :title="pageTitle" />

        <!-- Page header -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ pageTitle }}</h1>
                <div class="flex items-center gap-2 text-sm text-gray-500 mt-1">
                    <Link :href="route('home')" class="hover:text-indigo-600">Home</Link>
                    <span>/</span>
                    <span class="text-gray-800">Products</span>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex gap-8">
                <!-- Sidebar Filters -->
                <aside class="hidden lg:block w-64 flex-shrink-0">
                    <div class="sticky top-24 space-y-6">
                        <!-- Active filters -->
                        <div v-if="hasActiveFilters" class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-700">Active Filters</span>
                            <button @click="clearFilters" class="text-xs text-red-500 hover:text-red-700 font-medium">Clear all</button>
                        </div>

                        <!-- Price Range (top) -->
                        <div class="bg-white rounded-xl border border-gray-200 p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-gray-800 text-sm">Price Range</h3>
                                <span class="text-xs text-gray-400">৳{{ priceMin.toLocaleString() }} – ৳{{ priceMax === PRICE_MAX ? '∞' : priceMax.toLocaleString() }}</span>
                            </div>
                            <div class="relative h-5 flex items-center mb-4">
                                <div class="absolute h-1.5 w-full bg-gray-200 rounded-full"></div>
                                <div class="absolute h-1.5 bg-indigo-500 rounded-full pointer-events-none"
                                     :style="{ left: (priceMin / PRICE_MAX * 100) + '%', right: (100 - priceMax / PRICE_MAX * 100) + '%' }">
                                </div>
                                <input type="range" :min="0" :max="PRICE_MAX" :step="100"
                                       v-model.number="priceMin"
                                       @input="onPriceInput"
                                       class="price-range absolute w-full" />
                                <input type="range" :min="0" :max="PRICE_MAX" :step="100"
                                       v-model.number="priceMax"
                                       @input="onPriceInput"
                                       class="price-range absolute w-full" />
                            </div>
                            <div class="flex justify-between text-[11px] text-gray-400">
                                <span>৳0</span>
                                <span>৳{{ (PRICE_MAX / 2).toLocaleString() }}</span>
                                <span>৳{{ PRICE_MAX.toLocaleString() }}+</span>
                            </div>
                        </div>

                        <!-- Search -->
                        <div class="bg-white rounded-xl border border-gray-200 p-4">
                            <h3 class="font-semibold text-gray-800 mb-3 text-sm">Search</h3>
                            <input
                                v-model="localFilters.search"
                                type="text"
                                placeholder="Search products..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                @keyup.enter="applyFilters"
                            />
                        </div>

                        <!-- Categories -->
                        <div class="bg-white rounded-xl border border-gray-200 p-4">
                            <h3 class="font-semibold text-gray-800 mb-3 text-sm">Categories</h3>
                            <div class="space-y-1">
                                <button
                                    @click="localFilters.category_id = null; applyFilters()"
                                    class="w-full text-left px-2 py-1.5 rounded-lg text-sm transition"
                                    :class="!localFilters.category_id ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-gray-50'"
                                >
                                    All Categories
                                </button>
                                <template v-for="cat in categories" :key="cat.id">
                                    <button
                                        @click="localFilters.category_id = cat.id; applyFilters()"
                                        class="w-full text-left px-2 py-1.5 rounded-lg text-sm transition"
                                        :class="localFilters.category_id == cat.id ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-gray-50'"
                                    >
                                        {{ cat.name }}
                                    </button>
                                    <button
                                        v-for="child in cat.children"
                                        :key="child.id"
                                        @click="localFilters.category_id = child.id; applyFilters()"
                                        class="w-full text-left pl-6 pr-2 py-1 rounded-lg text-xs transition"
                                        :class="localFilters.category_id == child.id ? 'bg-indigo-50 text-indigo-600 font-medium' : 'text-gray-500 hover:bg-gray-50'"
                                    >
                                        └ {{ child.name }}
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Brands -->
                        <div v-if="brands?.length" class="bg-white rounded-xl border border-gray-200 p-4">
                            <h3 class="font-semibold text-gray-800 mb-3 text-sm">Brands</h3>
                            <div class="space-y-1 max-h-48 overflow-y-auto">
                                <label
                                    v-for="brand in brands"
                                    :key="brand.id"
                                    class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-gray-50"
                                >
                                    <input
                                        type="checkbox"
                                        :value="brand.id"
                                        v-model="selectedBrands"
                                        @change="applyFilters"
                                        class="w-3.5 h-3.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <span class="text-sm text-gray-600">{{ brand.name }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Featured -->
                        <div class="bg-white rounded-xl border border-gray-200 p-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    v-model="localFilters.featured"
                                    @change="applyFilters"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span class="text-sm font-medium text-gray-700">Featured only</span>
                            </label>
                        </div>
                    </div>
                </aside>

                <!-- Main content -->
                <div class="flex-1 min-w-0">
                    <!-- Toolbar -->
                    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
                        <div class="text-sm text-gray-500">
                            <span class="font-semibold text-gray-800">{{ products.total }}</span> products found
                        </div>

                        <div class="flex items-center gap-3">
                            <!-- Mobile filter button -->
                            <button
                                @click="mobileFiltersOpen = !mobileFiltersOpen"
                                class="lg:hidden flex items-center gap-2 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-indigo-400"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filters
                            </button>

                            <!-- Sort -->
                            <select
                                v-model="sortValue"
                                @change="applySort"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="created_at:desc">Newest First</option>
                                <option value="created_at:asc">Oldest First</option>
                                <option value="price:asc">Price: Low to High</option>
                                <option value="price:desc">Price: High to Low</option>
                                <option value="name:asc">Name: A to Z</option>
                            </select>

                            <!-- Grid/List toggle -->
                            <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                                <button
                                    @click="viewMode = 'grid'"
                                    :class="viewMode === 'grid' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
                                    class="px-3 py-2 transition"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 3h8v8H3V3zm0 10h8v8H3v-8zM13 3h8v8h-8V3zm0 10h8v8h-8v-8z"/>
                                    </svg>
                                </button>
                                <button
                                    @click="viewMode = 'list'"
                                    :class="viewMode === 'list' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
                                    class="px-3 py-2 transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Active filter chips -->
                    <div v-if="hasActiveFilters" class="flex flex-wrap gap-2 mb-4">
                        <span v-if="filters.search" class="inline-flex items-center gap-1 bg-indigo-100 text-indigo-700 text-xs font-medium px-3 py-1 rounded-full">
                            "{{ filters.search }}"
                            <button @click="removeFilter('search')">✕</button>
                        </span>
                        <span v-if="filters.category_id" class="inline-flex items-center gap-1 bg-indigo-100 text-indigo-700 text-xs font-medium px-3 py-1 rounded-full">
                            Category
                            <button @click="removeFilter('category_id')">✕</button>
                        </span>
                        <span v-if="filters.min_price || filters.max_price" class="inline-flex items-center gap-1 bg-indigo-100 text-indigo-700 text-xs font-medium px-3 py-1 rounded-full">
                            ৳{{ filters.min_price || 0 }} – {{ filters.max_price || '∞' }}
                            <button @click="removeFilter('min_price'); removeFilter('max_price')">✕</button>
                        </span>
                    </div>

                    <!-- Grid view -->
                    <div
                        v-if="viewMode === 'grid'"
                        class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4"
                    >
                        <ProductCard
                            v-for="product in products.data"
                            :key="product.id"
                            :product="product"
                        />
                    </div>

                    <!-- List view -->
                    <div v-else class="space-y-3">
                        <div
                            v-for="product in products.data"
                            :key="product.id"
                            class="bg-white rounded-xl border border-gray-100 hover:shadow-md transition p-3 sm:p-4 flex gap-3 sm:gap-4"
                        >
                            <!-- Image -->
                            <Link :href="route('products.show', product.slug)" class="w-20 h-20 sm:w-28 sm:h-28 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                                <img
                                    v-if="product.thumbnail"
                                    :src="product.thumbnail"
                                    :alt="product.name"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </Link>

                            <!-- Content -->
                            <div class="flex-1 min-w-0 flex flex-col">
                                <p v-if="product.category" class="text-[11px] text-indigo-600 font-semibold uppercase tracking-wide mb-0.5 truncate">
                                    {{ product.category.name }}
                                </p>
                                <Link :href="route('products.show', product.slug)"
                                    class="text-sm sm:text-base font-semibold text-gray-800 hover:text-indigo-600 transition line-clamp-2 leading-snug">
                                    {{ product.name }}
                                </Link>
                                <p v-if="product.short_description" class="text-xs text-gray-400 mt-1 line-clamp-1 hidden sm:block">
                                    {{ product.short_description }}
                                </p>

                                <!-- Spacer -->
                                <div class="flex-1"></div>

                                <!-- Price + Button -->
                                <div class="flex items-center justify-between gap-2 mt-2">
                                    <div class="flex items-baseline gap-1.5 min-w-0">
                                        <span class="text-sm sm:text-base font-bold text-red-600 whitespace-nowrap">৳{{ formatPrice(product.effective_price) }}</span>
                                        <span v-if="product.sale_price" class="text-xs text-gray-400 line-through whitespace-nowrap hidden sm:inline">৳{{ formatPrice(product.price) }}</span>
                                    </div>
                                    <button
                                        v-if="product.in_stock"
                                        @click="addToCart(product)"
                                        class="flex-shrink-0 bg-indigo-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition whitespace-nowrap"
                                    >
                                        Add to Cart
                                    </button>
                                    <span v-else class="flex-shrink-0 text-xs text-red-500 font-medium whitespace-nowrap">Out of Stock</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div v-if="!products.data?.length" class="text-center py-20">
                        <div class="text-6xl mb-4">🔍</div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">No products found</h3>
                        <p class="text-gray-500 mb-6">Try adjusting your filters or search term.</p>
                        <button @click="clearFilters" class="bg-indigo-600 text-white px-6 py-2.5 rounded-full hover:bg-indigo-700 transition">
                            Clear Filters
                        </button>
                    </div>

                    <!-- Pagination -->
                    <div v-if="products.last_page > 1" class="mt-8 flex items-center justify-center gap-2">
                        <button
                            v-for="link in paginationLinks"
                            :key="link.label"
                            @click="link.url && goToPage(link.url)"
                            :disabled="!link.url"
                            v-html="link.label"
                            class="px-3 py-2 text-sm rounded-lg border transition disabled:opacity-40 disabled:cursor-not-allowed"
                            :class="link.active
                                ? 'bg-indigo-600 text-white border-indigo-600'
                                : 'border-gray-300 text-gray-700 hover:border-indigo-400 hover:text-indigo-600'"
                        ></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile filters drawer -->
        <Teleport to="body">
            <Transition name="fade">
                <div
                    v-if="mobileFiltersOpen"
                    class="fixed inset-0 bg-black/50 z-50 lg:hidden"
                    @click.self="mobileFiltersOpen = false"
                >
                    <div class="absolute left-0 inset-y-0 w-80 bg-white shadow-xl overflow-y-auto p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="font-bold text-gray-900">Filters</h2>
                            <button @click="mobileFiltersOpen = false" class="text-gray-500 hover:text-gray-700">✕</button>
                        </div>

                        <!-- same filter controls -->
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                                <input v-model="localFilters.search" type="text" placeholder="Search..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" @keyup.enter="applyFilters(); mobileFiltersOpen = false" />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                                <select v-model="localFilters.category_id" @change="applyFilters(); mobileFiltersOpen = false" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option :value="null">All</option>
                                    <template v-for="cat in categories" :key="cat.id">
                                        <option :value="cat.id">{{ cat.name }}</option>
                                        <option v-for="child in cat.children" :key="child.id" :value="child.id">— {{ child.name }}</option>
                                    </template>
                                </select>
                            </div>

                            <div class="bg-indigo-50/60 rounded-xl p-4">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-sm font-bold text-gray-800">Price Range</label>
                                </div>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-100 px-2 py-0.5 rounded-full">৳{{ priceMin.toLocaleString() }}</span>
                                    <span class="text-[10px] text-gray-400">to</span>
                                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-100 px-2 py-0.5 rounded-full">{{ priceMax === PRICE_MAX ? '৳∞' : '৳' + priceMax.toLocaleString() }}</span>
                                </div>
                                <div class="relative h-8 flex items-center px-1">
                                    <div class="absolute h-2 w-full bg-gray-200 rounded-full"></div>
                                    <div class="absolute h-2 bg-indigo-500 rounded-full pointer-events-none"
                                         :style="{ left: (priceMin / PRICE_MAX * 100) + '%', right: (100 - priceMax / PRICE_MAX * 100) + '%' }">
                                    </div>
                                    <input type="range" :min="0" :max="PRICE_MAX" :step="100"
                                           v-model.number="priceMin"
                                           @input="onPriceInput"
                                           class="price-range absolute w-full" />
                                    <input type="range" :min="0" :max="PRICE_MAX" :step="100"
                                           v-model.number="priceMax"
                                           @input="onPriceInput"
                                           class="price-range absolute w-full" />
                                </div>
                                <div class="flex justify-between text-[10px] text-gray-400 mt-2">
                                    <span>৳0</span>
                                    <span>৳{{ (PRICE_MAX / 2).toLocaleString() }}</span>
                                    <span>৳{{ PRICE_MAX.toLocaleString() }}+</span>
                                </div>
                            </div>

                            <button @click="applyFilters(); mobileFiltersOpen = false" class="w-full bg-indigo-600 text-white py-2.5 rounded-xl font-medium hover:bg-indigo-700 transition">
                                Apply Filters
                            </button>
                            <button @click="clearFilters(); mobileFiltersOpen = false" class="w-full border border-gray-300 text-gray-700 py-2.5 rounded-xl text-sm hover:border-red-400 hover:text-red-500 transition">
                                Clear All
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </StorefrontLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import ProductCard from '@/Components/Storefront/ProductCard.vue';

const props = defineProps({
    products: Object,
    categories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const PRICE_MAX = 20000;

const viewMode = ref('grid');
const mobileFiltersOpen = ref(false);
const selectedBrands = ref(props.filters.brand_id ? [props.filters.brand_id] : []);

const priceMin = ref(Number(props.filters.min_price) || 0);
const priceMax = ref(Number(props.filters.max_price) || PRICE_MAX);

let priceDebounce = null;
function onPriceInput() {
    if (priceMin.value > priceMax.value) {
        const tmp = priceMin.value;
        priceMin.value = priceMax.value;
        priceMax.value = tmp;
    }
    clearTimeout(priceDebounce);
    priceDebounce = setTimeout(() => {
        localFilters.value.min_price = priceMin.value > 0 ? priceMin.value : '';
        localFilters.value.max_price = priceMax.value < PRICE_MAX ? priceMax.value : '';
        applyFilters();
    }, 400);
}

const localFilters = ref({
    search: props.filters.search ?? '',
    category_id: props.filters.category_id ?? null,
    brand_id: props.filters.brand_id ?? null,
    min_price: props.filters.min_price ?? '',
    max_price: props.filters.max_price ?? '',
    featured: props.filters.featured ?? false,
});

const sortValue = ref(
    props.filters.sort_by
        ? `${props.filters.sort_by}:${props.filters.sort_dir ?? 'desc'}`
        : 'created_at:desc'
);

const pageTitle = computed(() => {
    if (props.filters.search) return `Search: "${props.filters.search}"`;
    return 'All Products';
});

const hasActiveFilters = computed(() =>
    !!(props.filters.search || props.filters.category_id || props.filters.brand_id || props.filters.min_price || props.filters.max_price || props.filters.featured)
);

const paginationLinks = computed(() =>
    props.products.links?.filter(l => l.label !== '&laquo; Previous' && l.label !== 'Next &raquo;') ?? []
);

function applyFilters() {
    const params = {};
    if (localFilters.value.search) params.search = localFilters.value.search;
    if (localFilters.value.category_id) params.category_id = localFilters.value.category_id;
    if (selectedBrands.value.length === 1) params.brand_id = selectedBrands.value[0];
    if (localFilters.value.min_price) params.min_price = localFilters.value.min_price;
    if (localFilters.value.max_price) params.max_price = localFilters.value.max_price;
    if (localFilters.value.featured) params.featured = 1;

    const [sortBy, sortDir] = sortValue.value.split(':');
    params.sort_by = sortBy;
    params.sort_dir = sortDir;

    router.get(route('products.index'), params, { preserveScroll: false });
}

function applySort() {
    applyFilters();
}

function clearFilters() {
    localFilters.value = { search: '', category_id: null, brand_id: null, min_price: '', max_price: '', featured: false };
    selectedBrands.value = [];
    sortValue.value = 'created_at:desc';
    priceMin.value = 0;
    priceMax.value = PRICE_MAX;
    router.get(route('products.index'));
}

function removeFilter(key) {
    localFilters.value[key] = key === 'category_id' || key === 'brand_id' ? null : '';
    applyFilters();
}

function goToPage(url) {
    router.get(url, {}, { preserveScroll: true });
}

function formatPrice(val) {
    return Number(val).toLocaleString('en-BD');
}

function addToCart(product) {
    router.post(route('cart.add'), { product_id: product.id, quantity: 1 }, { preserveScroll: true });
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Dual range slider */
.price-range {
    -webkit-appearance: none;
    appearance: none;
    height: 6px;
    background: transparent;
    pointer-events: none;
    outline: none;
}
.price-range::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #4f46e5;
    border: 3px solid #fff;
    box-shadow: 0 2px 6px rgba(79,70,229,0.4);
    cursor: pointer;
    pointer-events: all;
    transition: transform 0.15s, box-shadow 0.15s;
}
.price-range::-webkit-slider-thumb:active {
    transform: scale(1.2);
    box-shadow: 0 2px 12px rgba(79,70,229,0.5);
}
.price-range::-moz-range-thumb {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #4f46e5;
    border: 3px solid #fff;
    box-shadow: 0 2px 6px rgba(79,70,229,0.4);
    cursor: pointer;
    pointer-events: all;
}
@media (max-width: 640px) {
    .price-range::-webkit-slider-thumb {
        width: 26px;
        height: 26px;
    }
    .price-range::-moz-range-thumb {
        width: 26px;
        height: 26px;
    }
}
</style>
