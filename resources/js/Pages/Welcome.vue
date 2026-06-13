<template>
    <StorefrontLayout>
        <Head title="Home – Best Deals on Electronics & More" />

        <!-- Hero: Left Slider + Right Fixed Grid -->
        <section class="bg-gray-100 border-b border-gray-200">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 sm:py-3">
                <div class="h-[185px] sm:h-[480px] lg:h-[560px]">

                    <!-- Sliding Banner -->
                    <div class="relative w-full h-full sm:rounded-xl overflow-hidden bg-indigo-700 cursor-pointer select-none"
                         @mouseenter="pauseSlider" @mouseleave="resumeSlider">

                        <!-- Slides -->
                        <TransitionGroup name="slide-fade">
                            <div
                                v-for="(slide, i) in slides"
                                :key="slide.id"
                                v-show="currentSlide === i"
                                class="absolute inset-0 flex items-center justify-center"
                                :style="slide.image_url ? {} : { backgroundColor: slide.bg_color ?? '#4f46e5' }"
                            >
                                <!-- Full image: contain on mobile, cover on desktop -->
                                <img v-if="slide.image_url"
                                    :src="slide.image_url"
                                    class="w-full h-full object-cover"
                                    :alt="'Slide ' + slide.id"
                                />

                                <!-- Button overlay (optional) -->
                                <div v-if="slide.href?.trim() && slide.button_text?.trim()" class="absolute bottom-2 sm:bottom-8 left-2 sm:left-10 z-10">
                                    <Link :href="slide.href"
                                        class="inline-block bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold px-3 sm:px-4 py-1 sm:py-1.5 rounded-full text-[10px] sm:text-xs transition shadow-md">
                                        {{ slide.button_text }} →
                                    </Link>
                                </div>
                            </div>
                        </TransitionGroup>

                    </div>

                </div>
            </div>
        </section>

        <!-- Shop by Category -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-7 bg-indigo-600 rounded-full"></div>
                    <h2 class="text-xl font-bold text-gray-900">Shop by Category</h2>
                </div>
                <Link :href="route('products.index')" class="text-sm text-indigo-600 font-medium hover:underline">See all →</Link>
            </div>

            <div class="bg-white rounded-2xl p-3 sm:p-4">
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2 sm:gap-3">
                    <Link
                        v-for="cat in categories"
                        :key="cat.id"
                        :href="route('products.index', { category_id: cat.id })"
                        class="group flex flex-col items-center rounded-xl overflow-hidden transition-all duration-200 hover:scale-[1.03] hover:shadow-md bg-[#eeeeee]"
                    >
                        <!-- Icon area -->
                        <div class="w-full aspect-[5/3] flex items-center justify-center">
                            <img
                                v-if="cat.image"
                                :src="cat.image"
                                :alt="cat.name"
                                class="w-1/2 h-1/2 object-contain group-hover:scale-110 transition-transform duration-300"
                            />
                            <span v-else class="text-4xl select-none group-hover:scale-110 transition-transform duration-300">{{ categoryEmojis[cat.name] ?? '🛍️' }}</span>
                        </div>
                        <!-- Title at bottom -->
                        <div class="w-full bg-[#eeeeee] pt-1.5 pb-2.5 px-2 text-center">
                            <span class="text-[10px] sm:text-[11px] font-bold text-gray-700 leading-tight line-clamp-1">{{ cat.name }}</span>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Mobile view all -->
            <div class="sm:hidden text-center mt-4">
                <Link :href="route('products.index')" class="text-sm text-gray-500 font-medium hover:text-red-500 transition">View All ↓</Link>
            </div>
        </section>

        <!-- Trending Must-Have (sheitech style) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 overflow-hidden">
            <div class="rounded-2xl overflow-hidden flex bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 shadow-xl">

                <!-- Left panel -->
                <div class="hidden md:flex flex-col justify-center w-[220px] flex-shrink-0 px-8 py-10 bg-gradient-to-b from-gray-950 to-slate-900 border-r border-white/10 relative overflow-hidden">
                    <div class="absolute -bottom-8 -right-8 w-28 h-28 bg-indigo-700/20 rounded-full pointer-events-none"></div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-indigo-400 mb-2 relative z-10">This Week's</p>
                    <h2 class="text-2xl font-extrabold text-white leading-tight mb-3 relative z-10">Must-Have</h2>
                    <p class="text-xs text-gray-400 leading-relaxed mb-6 relative z-10">Trending Gadgets, Carefully Chosen for You</p>
                    <Link :href="route('products.index', { featured: 1 })"
                        class="relative z-10 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-lg transition shadow-md w-fit">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 448 512" fill="currentColor">
                            <path d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34zm192-34l-136-136c-9.4-9.4-24.6-9.4-33.9 0l-22.6 22.6c-9.4 9.4-9.4 24.6 0 33.9l96.4 96.4-96.4 96.4c-9.4 9.4-9.4 24.6 0 33.9l22.6 22.6c9.4 9.4 24.6 9.4 33.9 0l136-136c9.4-9.2 9.4-24.4 0-33.8z"/>
                        </svg>
                        Go Shopping
                    </Link>
                </div>

                <!-- Right: carousel -->
                <div class="flex-1 min-w-0 py-4 px-4 relative" @mouseenter="pauseTrending" @mouseleave="resumeTrending">

                    <!-- Mobile header -->
                    <div class="flex md:hidden items-center justify-between mb-3">
                        <h2 class="text-sm font-extrabold text-white">This Week's Must-Have</h2>
                        <Link :href="route('products.index', { featured: 1 })" class="text-xs text-indigo-300 hover:text-white transition">View All →</Link>
                    </div>

                    <div class="overflow-hidden" ref="trendingTrack">
                        <div class="flex gap-3 transition-transform duration-500 ease-in-out"
                             :style="{ transform: `translateX(-${trendingOffset}px)` }">
                            <Link
                                v-for="p in featuredProducts?.data ?? []" :key="p.id"
                                :href="route('products.show', p.slug)"
                                :style="{ width: trendingCardWidth + 'px', flexShrink: '0' }"
                                class="group bg-white rounded-xl overflow-hidden shadow hover:shadow-lg transition-all duration-300"
                            >
                                <div class="relative aspect-square bg-gray-50">
                                    <span v-if="p.sale_price"
                                        class="absolute top-2 left-2 z-10 bg-red-500 text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded leading-none">
                                        -{{ Math.round((1 - p.sale_price / p.price) * 100) }}%
                                    </span>
                                    <img v-if="p.thumbnail" :src="p.thumbnail" :alt="p.name"
                                        class="w-full h-full object-contain p-3 group-hover:scale-105 transition-transform duration-300" />
                                    <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="p-2.5">
                                    <p class="text-[11px] font-semibold text-gray-800 line-clamp-2 leading-snug mb-1.5">{{ p.name }}</p>
                                    <div class="flex items-center gap-px mb-1.5">
                                        <svg v-for="s in 5" :key="s" class="w-2.5 h-2.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span v-if="p.sale_price" class="text-[10px] text-gray-400 line-through mr-1">৳{{ p.price.toLocaleString() }}</span>
                                        <span class="text-sm font-extrabold text-gray-900">৳{{ (p.sale_price || p.price).toLocaleString() }}</span>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <button @click="prevTrending"
                        class="absolute left-1 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-50 transition">
                        <svg class="w-3.5 h-3.5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button @click="nextTrending"
                        class="absolute right-1 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-50 transition">
                        <svg class="w-3.5 h-3.5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        <!-- Trust bar -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-6">
            <div class="bg-gray-900 rounded-2xl overflow-hidden">

                <!-- Mobile: auto-slide, no arrows -->
                <div class="sm:hidden overflow-hidden relative h-[72px]">
                    <div class="flex transition-transform duration-500 ease-in-out h-full"
                         :style="{ transform: `translateX(-${trustSlide * 100}%)` }">
                        <div v-for="b in trustBadges" :key="b.title"
                            class="flex-shrink-0 w-full flex items-center gap-4 px-5 h-full">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-red-500/15 text-red-400"
                                v-html="b.icon">
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white leading-tight">{{ b.title }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ b.desc }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Dot indicators only -->
                    <div class="absolute bottom-1.5 left-1/2 -translate-x-1/2 flex gap-1">
                        <span v-for="(_, i) in trustBadges" :key="i"
                            class="block rounded-full transition-all duration-300"
                            :class="trustSlide === i ? 'w-4 h-1 bg-white' : 'w-1 h-1 bg-white/30'" />
                    </div>
                </div>

                <!-- Desktop: all 4 in a row -->
                <div class="hidden sm:flex divide-x divide-white/10">
                    <div v-for="b in trustBadges" :key="b.title"
                        class="flex-1 flex flex-col items-center text-center gap-2 px-4 py-5 hover:bg-white/5 transition">
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center bg-red-500/15 text-red-400"
                            v-html="b.icon">
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white leading-tight">{{ b.title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ b.desc }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- New Arrivals -->
        <section class="bg-white py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-7 bg-indigo-600 rounded-full"></div>
                        <h2 class="text-xl font-bold text-gray-900">New Arrivals</h2>
                    </div>
                    <Link :href="route('products.index', { sort_by: 'created_at', sort_dir: 'desc' })" class="text-sm text-indigo-600 font-medium hover:underline">See all →</Link>
                </div>
                <div v-if="latestProducts?.data?.length" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    <ProductCard v-for="p in latestProducts.data" :key="p.id" :product="p" />
                </div>
                <div v-else class="text-center py-12 text-gray-400">No products yet.</div>
            </div>
        </section>

        <!-- Shop by Brand -->
        <section v-if="brands?.length" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-7 bg-indigo-600 rounded-full"></div>
                    <h2 class="text-xl font-bold text-gray-900">Shop by Brand</h2>
                </div>
                <Link :href="route('products.index')" class="text-sm text-indigo-600 font-medium hover:underline">See all →</Link>
            </div>
            <div class="bg-white rounded-2xl p-2.5 sm:p-3">
                <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-8 gap-2">
                    <Link
                        v-for="brand in brands"
                        :key="brand.id"
                        :href="route('products.index', { brand_id: brand.id })"
                        class="group aspect-[2/1] rounded-lg border border-gray-100 bg-white overflow-hidden flex items-center justify-center hover:border-gray-300 hover:shadow-sm transition-all duration-200"
                    >
                        <img v-if="brand.logo" :src="brand.logo" :alt="brand.name"
                            class="w-full h-full object-contain p-2 group-hover:scale-105 transition-transform duration-300" />
                        <span v-else class="text-base font-extrabold text-indigo-600">{{ brand.name[0] }}</span>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Why choose us -->
        <section class="bg-gray-50 py-12 border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-xl font-bold text-gray-900 text-center mb-8">Why Shop With Us?</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div v-for="f in features" :key="f.title" class="bg-white rounded-xl p-5 text-center border border-gray-100 hover:border-indigo-200 transition">
                        <div class="text-3xl mb-2">{{ f.icon }}</div>
                        <p class="text-sm font-semibold text-gray-800 mb-1">{{ f.title }}</p>
                        <p class="text-xs text-gray-500">{{ f.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Newsletter -->
        <section class="bg-indigo-700 py-12">
            <div class="max-w-2xl mx-auto px-4 text-center text-white">
                <h2 class="text-2xl font-bold mb-2">Get Exclusive Deals First</h2>
                <p class="text-indigo-200 text-sm mb-6">Join 50,000+ subscribers and never miss a deal.</p>
                <form @submit.prevent class="flex flex-col sm:flex-row gap-2 max-w-md mx-auto">
                    <input type="email" placeholder="Your email address"
                        class="flex-1 min-w-0 rounded-full px-5 py-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                    <button type="submit" class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold px-6 py-3 rounded-full transition text-sm flex-shrink-0">
                        Subscribe
                    </button>
                </form>
            </div>
        </section>
    </StorefrontLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import ProductCard from '@/Components/Storefront/ProductCard.vue';

const props = defineProps({
    featuredProducts: Object,
    latestProducts: Object,
    categories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    heroSlides: { type: Array, default: () => [] },
});

// ── Hero Slider ──────────────────────────────────────────
const currentSlide = ref(0);

// ── Trust Slider (mobile) ────────────────────────────────
const trustSlide = ref(0);
let trustTimer = null;

// ── Trending Slider (single-step carousel) ─────────────────
const trendingTrack = ref(null);
const trendingIndex = ref(0);
const trendingContainerWidth = ref(0);
const trendingVisible = ref(4);
let trendingTimer = null;

const trendingCardWidth = computed(() => {
    const gap = 12;
    const n = trendingVisible.value;
    return Math.floor((trendingContainerWidth.value - gap * (n - 1)) / n);
});

const trendingOffset = computed(() => trendingIndex.value * (trendingCardWidth.value + 12));

const trendingMax = computed(() => {
    const total = props.featuredProducts?.data?.length ?? 0;
    return Math.max(0, total - trendingVisible.value);
});

function nextTrending() { trendingIndex.value = trendingIndex.value >= trendingMax.value ? 0 : trendingIndex.value + 1; }
function prevTrending() { trendingIndex.value = trendingIndex.value <= 0 ? trendingMax.value : trendingIndex.value - 1; }
function pauseTrending() { clearInterval(trendingTimer); }
function resumeTrending() { trendingTimer = setInterval(nextTrending, 3000); }

function updateTrending() {
    if (trendingTrack.value) trendingContainerWidth.value = trendingTrack.value.clientWidth;
    const w = window.innerWidth;
    trendingVisible.value = w < 400 ? 1 : w < 640 ? 2 : w < 1024 ? 3 : 4;
}

let sliderTimer = null;

const slides = computed(() => props.heroSlides?.length ? props.heroSlides : [
    { id: 0, image_url: null, bg_color: '#4f46e5', href: null, button_text: null },
]);


const catPalette = [
    'linear-gradient(135deg,#fff0f0,#ffe0e0)',
    'linear-gradient(135deg,#f0f4ff,#dce8ff)',
    'linear-gradient(135deg,#f0fff4,#d4f5e2)',
    'linear-gradient(135deg,#fffbf0,#ffefc0)',
    'linear-gradient(135deg,#f5f0ff,#e4d5ff)',
    'linear-gradient(135deg,#f0fcff,#cceeff)',
    'linear-gradient(135deg,#fff5f0,#ffddd0)',
    'linear-gradient(135deg,#f0fff9,#c8f5e8)',
    'linear-gradient(135deg,#fdfff0,#eaffc0)',
    'linear-gradient(135deg,#fff0fb,#ffd6f5)',
    'linear-gradient(135deg,#f0f9ff,#c8e8ff)',
    'linear-gradient(135deg,#fffff0,#ffffc0)',
];

const categoryEmojis = {
    'Electronics': '📱', 'Phone': '📱', 'Smartphone': '📱', 'Smart Phone': '📱', 'Tablet': '📟', 'Laptop': '💻',
    'Desktop': '🖥️', 'Mac': '🍎', 'Accessories': '🎧', 'Sound': '🔊',
    'Smart Watch': '⌚', 'Gaming': '🎮', 'Fashion': '👗', 'Books': '📚',
    'Home & Living': '🏠', 'Sports': '⚽', 'Power': '🔋', 'Camera': '📷',
};

function nextSlide() { currentSlide.value = (currentSlide.value + 1) % slides.value.length; }
function prevSlide() { currentSlide.value = (currentSlide.value - 1 + slides.value.length) % slides.value.length; }
function goToSlide(i) { currentSlide.value = i; }
function pauseSlider() { clearInterval(sliderTimer); }
function resumeSlider() { sliderTimer = setInterval(nextSlide, 4500); }

onMounted(() => {
    updateTrending();
    window.addEventListener('resize', updateTrending);
    sliderTimer = setInterval(nextSlide, 4500);
    trendingTimer = setInterval(nextTrending, 3000);
    trustTimer = setInterval(() => {
        trustSlide.value = (trustSlide.value + 1) % trustBadges.length;
    }, 2500);
});
onUnmounted(() => {
    window.removeEventListener('resize', updateTrending);
    clearInterval(sliderTimer);
    clearInterval(trendingTimer);
    clearInterval(trustTimer);
});

const trustBadges = [
    {
        title: 'Free Shipping', desc: 'Orders over ৳500',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>`,
    },
    {
        title: '100% Genuine', desc: 'Official products',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>`,
    },
    {
        title: 'Easy Returns', desc: '7-day policy',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>`,
    },
    {
        title: 'Secure Payment', desc: 'SSL encrypted',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>`,
    },
];

const features = [
    { icon: '🏆', title: 'Best Price', desc: 'Price match guarantee' },
    { icon: '🚀', title: 'Fast Delivery', desc: 'Same day in Dhaka' },
    { icon: '✅', title: 'Genuine Products', desc: 'Official warranty' },
    { icon: '↩️', title: 'Easy Return', desc: '7 days no question' },
    { icon: '🎧', title: '24/7 Support', desc: 'Always available' },
    { icon: '💳', title: 'EMI Available', desc: '0% interest' },
];
</script>

<style scoped>
.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: opacity 0.5s ease, transform 0.5s ease;
    position: absolute;
    inset: 0;
}
.slide-fade-enter-from { opacity: 0; transform: translateX(30px); }
.slide-fade-enter-to   { opacity: 1; transform: translateX(0); }
.slide-fade-leave-from { opacity: 1; transform: translateX(0); }
.slide-fade-leave-to   { opacity: 0; transform: translateX(-30px); }
</style>
