<template>
    <StorefrontLayout>
        <Head :title="product.name" />

        <!-- Breadcrumb (mobile: compact) -->
        <div class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5">
                <nav class="flex items-center gap-1.5 text-xs text-gray-400 overflow-x-auto scrollbar-hide whitespace-nowrap">
                    <Link :href="route('home')" class="hover:text-indigo-600 transition flex-shrink-0">Home</Link>
                    <span class="flex-shrink-0">›</span>
                    <Link :href="route('products.index')" class="hover:text-indigo-600 transition flex-shrink-0">Products</Link>
                    <template v-if="product.category">
                        <span class="flex-shrink-0">›</span>
                        <Link :href="route('products.index', { category_id: product.category.id })" class="hover:text-indigo-600 transition flex-shrink-0">
                            {{ product.category.name }}
                        </Link>
                    </template>
                    <span class="flex-shrink-0">›</span>
                    <span class="text-gray-600 font-medium truncate max-w-[160px]">{{ product.name }}</span>
                </nav>
            </div>
        </div>

        <div class="max-w-7xl mx-auto pb-10">
            <div class="lg:grid lg:grid-cols-2 lg:gap-10 xl:gap-16">

                <!-- ── IMAGE GALLERY ── -->
                <div class="lg:pt-8 lg:px-6 xl:px-8">
                    <!-- Main image -->
                    <div class="relative bg-white overflow-hidden aspect-square lg:rounded-2xl lg:border lg:border-gray-100 lg:shadow-sm">
                        <img
                            :src="activeImage"
                            :alt="product.name"
                            class="w-full h-full object-contain p-4 lg:p-6"
                        />

                        <!-- Badges -->
                        <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                            <span v-if="discountPercent" class="bg-red-500 text-white text-xs font-extrabold px-2.5 py-1 rounded-lg shadow">
                                -{{ discountPercent }}%
                            </span>
                            <span v-if="product.is_featured" class="bg-indigo-600 text-white text-xs font-bold px-2.5 py-1 rounded-lg shadow">
                                Featured
                            </span>
                        </div>

                        <!-- Out of stock overlay -->
                        <div v-if="!isInStock" class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center">
                            <span class="bg-gray-900 text-white font-bold px-6 py-2.5 rounded-full text-sm">Out of Stock</span>
                        </div>

                        <!-- Image nav arrows (multiple images) -->
                        <template v-if="allImages.length > 1">
                            <button @click="prevImage"
                                class="absolute left-2 top-1/2 -translate-y-1/2 w-9 h-9 bg-white/90 shadow-md rounded-full flex items-center justify-center text-gray-600 hover:bg-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button @click="nextImage"
                                class="absolute right-2 top-1/2 -translate-y-1/2 w-9 h-9 bg-white/90 shadow-md rounded-full flex items-center justify-center text-gray-600 hover:bg-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                            <!-- Dots indicator -->
                            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5">
                                <button v-for="(img, i) in allImages" :key="i"
                                    @click="activeImageIndex = i"
                                    class="w-1.5 h-1.5 rounded-full transition-all"
                                    :class="activeImageIndex === i ? 'bg-indigo-600 w-4' : 'bg-gray-300'">
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Thumbnail strip -->
                    <div v-if="allImages.length > 1" class="flex gap-2.5 px-4 lg:px-0 mt-3 overflow-x-auto scrollbar-hide pb-1">
                        <button
                            v-for="(img, i) in allImages" :key="i"
                            @click="activeImageIndex = i"
                            class="flex-shrink-0 w-16 h-16 lg:w-20 lg:h-20 rounded-xl overflow-hidden border-2 transition bg-white p-1"
                            :class="activeImageIndex === i ? 'border-indigo-500 shadow-md shadow-indigo-100' : 'border-gray-200 hover:border-gray-400'"
                        >
                            <img :src="img" :alt="`Image ${i+1}`" class="w-full h-full object-contain" />
                        </button>
                    </div>
                </div>

                <!-- ── PRODUCT INFO ── -->
                <div class="px-4 pt-5 lg:pt-8 lg:pr-6 xl:pr-8 lg:pl-0 space-y-5">

                    <!-- Brand + Category chips -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <span v-if="product.brand"
                            class="inline-flex items-center gap-1 text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 px-3 py-1 rounded-full">
                            {{ product.brand.name }}
                        </span>
                        <span v-if="product.category"
                            class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                            {{ product.category.name }}
                        </span>
                    </div>

                    <!-- Product name -->
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-gray-900 leading-tight">
                        {{ product.name }}
                    </h1>

                    <!-- SKU -->
                    <p class="text-xs text-gray-400">SKU: <span class="font-mono text-gray-500">{{ product.sku }}</span></p>

                    <!-- Price block -->
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl p-4 border border-indigo-100">
                        <div class="flex items-baseline gap-3 flex-wrap">
                            <span class="text-3xl font-extrabold text-indigo-700">
                                ৳{{ fmt(effectivePrice) }}
                            </span>
                            <span v-if="product.sale_price && !resolvedVariant" class="text-base text-gray-400 line-through">
                                ৳{{ fmt(product.price) }}
                            </span>
                            <span v-if="discountPercent && !resolvedVariant"
                                class="text-xs font-extrabold text-red-600 bg-red-100 px-2 py-0.5 rounded-lg">
                                Save ৳{{ fmt(product.price - product.sale_price) }}
                            </span>
                        </div>
                        <!-- Stock status -->
                        <div class="flex items-center gap-1.5 mt-2">
                            <div class="w-2 h-2 rounded-full" :class="isInStock ? 'bg-green-500' : 'bg-red-500'"></div>
                            <p class="text-xs font-semibold" :class="isInStock ? 'text-green-700' : 'text-red-600'">
                                {{ isInStock ? `In Stock (${effectiveStock} available)` : 'Out of Stock' }}
                            </p>
                        </div>
                    </div>

                    <!-- Short description -->
                    <p v-if="product.short_description" class="text-sm text-gray-600 leading-relaxed">
                        {{ product.short_description }}
                    </p>

                    <!-- Variants: attribute-based selectors -->
                    <div v-if="variantAxes.length" class="space-y-4">
                        <div v-for="axis in variantAxes" :key="axis.name">
                            <div class="flex items-center gap-2 mb-2">
                                <p class="text-sm font-bold text-gray-800">{{ axis.name }}</p>
                                <span v-if="selectedAttrs[axis.name]"
                                    class="text-xs text-indigo-600 font-semibold">{{ selectedAttrs[axis.name] }}</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <!-- Color swatch -->
                                <template v-if="axis.isColor">
                                    <button v-for="val in axis.values" :key="val.value"
                                        @click="selectAttr(axis.name, val.value)"
                                        :disabled="!isValueAvailable(axis.name, val.value)"
                                        :title="val.value"
                                        class="w-9 h-9 rounded-full border-4 transition active:scale-90 disabled:opacity-30 disabled:cursor-not-allowed"
                                        :style="{ background: val.color_code || '#ccc' }"
                                        :class="selectedAttrs[axis.name] === val.value
                                            ? 'border-indigo-500 scale-110 shadow-md shadow-indigo-200'
                                            : 'border-white shadow hover:border-gray-300'">
                                    </button>
                                </template>
                                <!-- Size / Storage / RAM pills -->
                                <template v-else>
                                    <button v-for="val in axis.values" :key="val.value"
                                        @click="selectAttr(axis.name, val.value)"
                                        :disabled="!isValueAvailable(axis.name, val.value)"
                                        class="px-3.5 py-1.5 rounded-xl border-2 text-sm font-semibold transition active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed"
                                        :class="selectedAttrs[axis.name] === val.value
                                            ? 'border-indigo-500 bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-200'
                                            : 'border-gray-200 bg-white text-gray-700 hover:border-indigo-300'">
                                        {{ val.value }}
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Variant not found warning -->
                        <p v-if="allAxesSelected && !activeVariant" class="text-xs text-red-500 font-medium">
                            This combination is not available.
                        </p>
                    </div>

                    <!-- Fallback: simple variant cards (no attributes) -->
                    <div v-else-if="product.variants?.length" class="space-y-3">
                        <p class="text-sm font-bold text-gray-800">Select Variant</p>
                        <div class="grid grid-cols-2 gap-2">
                            <button v-for="variant in product.variants" :key="variant.id"
                                @click="legacySelectVariant(variant)"
                                :disabled="variant.stock === 0"
                                class="border-2 rounded-xl px-3 py-2.5 text-sm text-left transition disabled:opacity-40 disabled:cursor-not-allowed active:scale-95"
                                :class="legacyVariant?.id === variant.id
                                    ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                                    : 'border-gray-200 hover:border-indigo-300 bg-white'">
                                <p class="font-semibold text-xs">{{ variant.sku }}</p>
                                <p class="text-indigo-600 font-bold mt-0.5">৳{{ fmt(variant.effective_price) }}</p>
                                <p v-if="variant.stock === 0" class="text-[10px] text-red-400 mt-0.5">Out of stock</p>
                            </button>
                        </div>
                    </div>

                    <!-- Qty + live total -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1">
                            <button @click="qty > 1 && qty--"
                                class="w-10 h-10 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-xl flex items-center justify-center text-gray-700 font-bold text-lg transition">−</button>
                            <span class="w-11 text-center font-extrabold text-gray-900 text-base">{{ qty }}</span>
                            <button @click="qty < (effectiveStock || 99) && qty++"
                                class="w-10 h-10 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-xl flex items-center justify-center text-gray-700 font-bold text-lg transition">+</button>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-400">Total</p>
                            <p class="font-extrabold text-indigo-700 text-xl leading-tight">
                                ৳{{ fmt(effectivePrice * qty) }}
                            </p>
                        </div>
                    </div>

                    <!-- CTA buttons -->
                    <div class="flex gap-2.5">
                        <button @click="addToCart" :disabled="!isInStock || adding"
                            class="flex-1 h-12 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold rounded-2xl transition flex items-center justify-center gap-2 shadow-lg shadow-indigo-200">
                            <svg v-if="!adding" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            {{ adding ? 'Adding...' : (isInStock ? 'Add to Cart' : 'Out of Stock') }}
                        </button>
                        <button v-if="isInStock" @click="buyNow"
                            class="flex-1 h-12 bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white font-bold rounded-2xl transition shadow-lg shadow-orange-200">
                            Buy Now
                        </button>
                        <button class="w-12 h-12 border-2 border-gray-200 hover:border-red-300 rounded-2xl flex items-center justify-center text-gray-400 hover:text-red-500 transition flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Toast -->
                    <Transition name="slide-up">
                        <div v-if="showToast"
                            class="flex items-center gap-3 bg-green-600 text-white px-4 py-3 rounded-2xl shadow-xl">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold text-sm">Added to cart!</span>
                            <Link :href="route('cart.index')" class="ml-auto text-xs underline font-bold whitespace-nowrap">View Cart →</Link>
                        </div>
                    </Transition>

                    <!-- Trust badges -->
                    <div class="grid grid-cols-3 gap-2">
                        <div class="flex flex-col items-center gap-1.5 p-3 bg-gray-50 rounded-2xl text-center">
                            <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2L19 8"/>
                                </svg>
                            </div>
                            <p class="text-[10px] font-bold text-gray-700 leading-tight">Free Delivery</p>
                            <p class="text-[9px] text-gray-400 leading-tight">Over ৳999</p>
                        </div>
                        <div class="flex flex-col items-center gap-1.5 p-3 bg-gray-50 rounded-2xl text-center">
                            <div class="w-8 h-8 bg-green-100 rounded-xl flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                            <p class="text-[10px] font-bold text-gray-700 leading-tight">7-Day Return</p>
                            <p class="text-[9px] text-gray-400 leading-tight">Easy returns</p>
                        </div>
                        <div class="flex flex-col items-center gap-1.5 p-3 bg-gray-50 rounded-2xl text-center">
                            <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <p class="text-[10px] font-bold text-gray-700 leading-tight">Secure Pay</p>
                            <p class="text-[9px] text-gray-400 leading-tight">SSL encrypted</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── TABS ── -->
            <div class="mt-8 px-4 lg:px-6 xl:px-8">
                <!-- Tab buttons -->
                <div class="flex overflow-x-auto scrollbar-hide gap-1 border-b border-gray-200 mb-0">
                    <button
                        v-for="tab in tabs" :key="tab"
                        @click="activeTab = tab"
                        class="flex-shrink-0 px-5 py-3 text-sm font-semibold border-b-2 transition -mb-px whitespace-nowrap"
                        :class="activeTab === tab
                            ? 'border-indigo-600 text-indigo-700'
                            : 'border-transparent text-gray-400 hover:text-gray-700'"
                    >
                        {{ tab }}
                    </button>
                </div>

                <!-- Description -->
                <div v-if="activeTab === 'Description'" class="py-6">
                    <p v-if="product.description" class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ product.description }}</p>
                    <p v-else class="text-gray-400 text-sm">No description available.</p>
                </div>

                <!-- Specifications -->
                <div v-if="activeTab === 'Specifications'" class="py-6">
                    <div v-if="product.attribute_values?.length" class="divide-y divide-gray-50">
                        <div v-for="av in product.attribute_values" :key="av.id"
                            class="flex justify-between py-3 text-sm">
                            <span class="text-gray-500 font-medium">{{ av.attribute?.name }}</span>
                            <span class="text-gray-900 font-semibold text-right ml-4">
                                {{ av.attribute_value?.label || av.attribute_value?.value || av.custom_value }}
                            </span>
                        </div>
                    </div>
                    <p v-else class="text-gray-400 text-sm py-4">No specifications listed.</p>
                </div>

                <!-- Reviews -->
                <div v-if="activeTab === 'Reviews'" class="py-6">
                    <!-- Rating summary -->
                    <div class="flex items-center gap-5 mb-6 p-4 bg-indigo-50 rounded-2xl">
                        <div class="text-center flex-shrink-0">
                            <p class="text-5xl font-extrabold text-indigo-700">4.0</p>
                            <div class="flex justify-center gap-0.5 mt-1">
                                <svg v-for="i in 5" :key="i" class="w-4 h-4" :class="i <= 4 ? 'text-amber-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">24 reviews</p>
                        </div>
                        <div class="flex-1 space-y-1.5">
                            <div v-for="(count, i) in ratingBreakdown" :key="i" class="flex items-center gap-2">
                                <span class="text-xs text-gray-600 w-3">{{ 5 - i }}</span>
                                <div class="flex-1 bg-white rounded-full h-2 overflow-hidden">
                                    <div class="bg-amber-400 h-2 rounded-full" :style="{ width: count + '%' }"></div>
                                </div>
                                <span class="text-[10px] text-gray-400 w-7">{{ count }}%</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div v-for="review in sampleReviews" :key="review.name"
                            class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                    {{ review.name[0] }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-bold text-gray-800 text-sm">{{ review.name }}</p>
                                        <span class="text-[10px] text-gray-400 flex-shrink-0">{{ review.date }}</span>
                                    </div>
                                    <div class="flex gap-0.5 mt-0.5">
                                        <svg v-for="i in review.rating" :key="i" class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1.5 leading-relaxed">{{ review.text }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related products -->
            <div v-if="related?.data?.length" class="mt-8 px-4 lg:px-6 xl:px-8">
                <h2 class="text-base font-bold text-gray-900 mb-4">You may also like</h2>
                <!-- Mobile: horizontal scroll -->
                <div class="flex gap-3 overflow-x-auto scrollbar-hide pb-2 lg:hidden">
                    <div v-for="p in related.data" :key="p.id" class="flex-shrink-0 w-40">
                        <ProductCard :product="p" />
                    </div>
                </div>
                <!-- Desktop: grid -->
                <div class="hidden lg:grid grid-cols-3 xl:grid-cols-4 gap-4">
                    <ProductCard v-for="p in related.data" :key="p.id" :product="p" />
                </div>
            </div>
        </div>

    </StorefrontLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import ProductCard from '@/Components/Storefront/ProductCard.vue';
import { useCartDrawer } from '@/Composables/useCartDrawer';

const props = defineProps({
    product: Object,
    related: Object,
});

const qty = ref(1);
const adding = ref(false);
const showToast = ref(false);
const activeTab = ref('Description');
const activeImageIndex = ref(0);
const tabs = ['Description', 'Specifications', 'Reviews'];

// Attribute-based variant selection
const selectedAttrs = ref({});
const legacyVariant = ref(null);

const { open: openCart } = useCartDrawer();

const allImages = computed(() => {
    const imgs = [];
    if (props.product.thumbnail) imgs.push(props.product.thumbnail);
    if (props.product.images?.length) {
        props.product.images.forEach(img => {
            if (img.url && !imgs.includes(img.url)) imgs.push(img.url);
        });
    }
    // Include variant images in gallery (for color switching)
    if (props.product.variants?.length) {
        props.product.variants.forEach(v => {
            if (v.image && !imgs.includes(v.image)) imgs.push(v.image);
        });
    }
    return imgs.length ? imgs : ['/images/placeholder.png'];
});

const activeImage = computed(() => allImages.value[activeImageIndex.value] ?? allImages.value[0]);

const discountPercent = computed(() => {
    if (!props.product.sale_price) return 0;
    return Math.round(((props.product.price - props.product.sale_price) / props.product.price) * 100);
});

// Derive variant axes from variant attributes
const variantAxes = computed(() => {
    if (!props.product.variants?.length) return [];
    const axesMap = {};
    props.product.variants.forEach(v => {
        (v.attributes ?? []).forEach(a => {
            if (!axesMap[a.attribute]) {
                axesMap[a.attribute] = {
                    name: a.attribute,
                    isColor: Boolean(a.color_code) || a.attribute.toLowerCase().includes('color') || a.attribute.toLowerCase().includes('colour'),
                    values: [],
                };
            }
            if (!axesMap[a.attribute].values.find(x => x.value === a.value)) {
                axesMap[a.attribute].values.push({ value: a.value, color_code: a.color_code });
            }
        });
    });
    return Object.values(axesMap);
});

const allAxesSelected = computed(() =>
    variantAxes.value.length > 0 && variantAxes.value.every(axis => selectedAttrs.value[axis.name] !== undefined)
);

const activeVariant = computed(() => {
    if (!allAxesSelected.value || !props.product.variants?.length) return null;
    const selected = selectedAttrs.value;
    return props.product.variants.find(v => {
        const attrMap = Object.fromEntries((v.attributes ?? []).map(a => [a.attribute, a.value]));
        return Object.entries(selected).every(([k, val]) => attrMap[k] === val);
    }) ?? null;
});

// The resolved variant (attribute-based or legacy)
const resolvedVariant = computed(() => activeVariant.value ?? legacyVariant.value);

const effectivePrice = computed(() =>
    resolvedVariant.value?.effective_price ?? props.product.effective_price
);
const effectiveStock = computed(() =>
    resolvedVariant.value?.stock ?? props.product.stock ?? 0
);
const isInStock = computed(() =>
    resolvedVariant.value ? resolvedVariant.value.stock > 0 : props.product.in_stock
);

// Switch image when variant changes
watch(activeVariant, (variant) => {
    if (variant?.image) {
        const idx = allImages.value.indexOf(variant.image);
        if (idx >= 0) activeImageIndex.value = idx;
    }
});

const ratingBreakdown = [40, 30, 15, 10, 5];
const sampleReviews = [
    { name: 'Rahim Ahmed',  rating: 5, date: '2 days ago',   text: 'Excellent product! Exactly as described. Fast delivery and great packaging.' },
    { name: 'Fatima Khan',  rating: 4, date: '1 week ago',   text: 'Good quality. Delivery was quick. Minor packaging issue but product is great.' },
    { name: 'Karim Uddin',  rating: 4, date: '2 weeks ago',  text: 'Value for money. Would buy again from this store.' },
];

function fmt(val) { return Number(val).toLocaleString('en-BD'); }

function prevImage() {
    activeImageIndex.value = (activeImageIndex.value - 1 + allImages.value.length) % allImages.value.length;
}
function nextImage() {
    activeImageIndex.value = (activeImageIndex.value + 1) % allImages.value.length;
}

function selectAttr(axisName, value) {
    if (selectedAttrs.value[axisName] === value) {
        const copy = { ...selectedAttrs.value };
        delete copy[axisName];
        selectedAttrs.value = copy;
    } else {
        selectedAttrs.value = { ...selectedAttrs.value, [axisName]: value };
    }
}

function isValueAvailable(axisName, value) {
    const testSelection = { ...selectedAttrs.value, [axisName]: value };
    const testKeys = Object.keys(testSelection);
    return props.product.variants.some(v => {
        const attrMap = Object.fromEntries((v.attributes ?? []).map(a => [a.attribute, a.value]));
        return testKeys.every(k => attrMap[k] === testSelection[k]);
    });
}

function legacySelectVariant(variant) {
    legacyVariant.value = legacyVariant.value?.id === variant.id ? null : variant;
    if (variant.image) {
        const idx = allImages.value.indexOf(variant.image);
        if (idx >= 0) activeImageIndex.value = idx;
    }
}

function addToCart() {
    adding.value = true;
    router.post(route('cart.add'), {
        product_id: props.product.id,
        quantity: qty.value,
        variant_id: resolvedVariant.value?.id ?? null,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showToast.value = true;
            setTimeout(() => { showToast.value = false; }, 3500);
        },
        onFinish: () => { adding.value = false; },
    });
}

function buyNow() {
    router.post(route('cart.add'), {
        product_id: props.product.id,
        quantity: qty.value,
        variant_id: resolvedVariant.value?.id ?? null,
    }, {
        preserveScroll: false,
        onSuccess: () => router.visit(route('checkout')),
    });
}
</script>

<style scoped>
.slide-up-enter-active, .slide-up-leave-active { transition: all 0.25s ease; }
.slide-up-enter-from, .slide-up-leave-to { opacity: 0; transform: translateY(8px); }
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
