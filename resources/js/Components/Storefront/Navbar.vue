<template>
    <div>
        <!-- ═══════════════════════════════════════════
             DESKTOP HEADER (hidden on mobile)
        ═══════════════════════════════════════════ -->
        <header class="hidden md:block bg-white sticky top-0 z-50">

            <!-- Top info bar (dark) -->
            <div class="bg-gray-900 text-xs text-gray-300">
                <div class="max-w-7xl mx-auto px-6 lg:px-8 flex items-center justify-between h-9">

                    <!-- Social icons -->
                    <div class="flex items-center gap-3">
                        <a v-if="social.facebook" :href="social.facebook" target="_blank" rel="noopener" aria-label="Facebook" class="hover:text-white transition">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                            </svg>
                        </a>
                        <a v-if="social.youtube" :href="social.youtube" target="_blank" rel="noopener" aria-label="YouTube" class="hover:text-white transition">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 001.46 6.42 29 29 0 001 12a29 29 0 00.46 5.58 2.78 2.78 0 001.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.96A29 29 0 0023 12a29 29 0 00-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/>
                            </svg>
                        </a>
                        <a v-if="social.instagram" :href="social.instagram" target="_blank" rel="noopener" aria-label="Instagram" class="hover:text-white transition">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2"/>
                                <circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/>
                                <circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>
                            </svg>
                        </a>
                        <a v-if="social.twitter" :href="social.twitter" target="_blank" rel="noopener" aria-label="Twitter / X" class="hover:text-white transition">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.213 5.567zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                    </div>

                    <!-- Contact + Track -->
                    <div class="flex items-center gap-4">
                        <a v-if="storePhone" :href="`tel:${storePhone}`" class="flex items-center gap-1.5 hover:text-white transition">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24 11.47 11.47 0 003.59.57 1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.45.57 3.57a1 1 0 01-.25 1.02l-2.2 2.2z"/>
                            </svg>
                            {{ storePhone }}
                        </a>
                        <a v-if="storeEmail" :href="`mailto:${storeEmail}`" class="flex items-center gap-1.5 hover:text-white transition">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ storeEmail }}
                        </a>
                        <span class="w-px h-4 bg-gray-700"></span>
                        <Link :href="route('products.index')" class="hover:text-white transition font-medium">Track Order</Link>
                        <span class="w-px h-4 bg-gray-700"></span>
                        <template v-if="!auth.user">
                            <Link :href="route('login')" class="hover:text-white transition">Sign In</Link>
                            <span>/</span>
                            <Link :href="route('register')" class="hover:text-white transition">Register</Link>
                        </template>
                        <template v-else>
                            <span class="text-gray-400">{{ auth.user.name.split(' ')[0] }}</span>
                            <Link :href="route('logout')" method="post" as="button" class="hover:text-red-400 transition">Logout</Link>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Main nav -->
            <div class="border-b border-gray-200 shadow-sm">
                <div class="max-w-7xl mx-auto px-6 lg:px-8 flex items-center gap-4 h-20">

                    <!-- Logo -->
                    <Link :href="route('home')" class="flex items-center gap-2 flex-shrink-0 mr-2">
                        <img v-if="siteSettings.logo_url" :src="siteSettings.logo_url" alt="Site logo"
                            class="h-16 w-auto max-w-[220px] object-contain"/>
                        <div v-else class="h-16 w-[160px] rounded-md bg-gray-100 animate-pulse"></div>
                    </Link>

                    <!-- Search bar -->
                    <form @submit.prevent="search" class="flex-1 flex h-11">
                        <input v-model="searchQuery" type="text" placeholder="Search products, brands and categories..."
                            class="flex-1 min-w-0 border-2 border-gray-900 border-r-0 rounded-l-md px-4 text-sm focus:outline-none bg-white placeholder-gray-400 focus:border-indigo-600 transition"/>
                        <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-6 rounded-r-md transition flex items-center gap-2 text-sm font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Search
                        </button>
                    </form>

                    <!-- Right actions -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <!-- Pre-Order button -->
                        <Link :href="route('products.index')"
                            class="flex items-center gap-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold px-4 py-2 rounded-md transition whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pre-Order
                        </Link>

                        <!-- Cart -->
                        <button @click="openCart" class="flex flex-col items-center px-3 py-1.5 text-gray-700 hover:text-indigo-600 transition rounded-lg hover:bg-gray-50 relative">
                            <div class="relative">
                                <svg class="w-6 h-6" :class="cartBounce ? 'cart-bump' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span v-if="cartCount > 0"
                                    class="absolute -top-2 -right-2.5 bg-red-500 text-white text-[10px] font-bold min-w-[18px] h-[18px] flex items-center justify-center rounded-full px-0.5 leading-none"
                                    :class="cartBounce ? 'badge-pop' : ''">
                                    {{ cartCount > 99 ? '99+' : cartCount }}
                                </span>
                            </div>
                            <span class="text-[10px] font-medium mt-0.5">Cart</span>
                        </button>

                        <!-- Account -->
                        <Link :href="auth.user ? route('dashboard') : route('login')"
                            class="flex flex-col items-center px-3 py-1.5 text-gray-700 hover:text-indigo-600 transition rounded-lg hover:bg-gray-50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0M19 20a7 7 0 10-14 0"/>
                            </svg>
                            <span class="text-[10px] font-medium mt-0.5">{{ auth.user ? 'Account' : 'Register or Login' }}</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Category nav bar -->
            <div class="bg-white border-b border-gray-200 shadow-sm">
                <div class="max-w-7xl mx-auto px-6 lg:px-8">
                    <div class="flex items-stretch h-10">

                        <!-- All Category dropdown -->
                        <div class="relative flex-shrink-0" @mouseenter="megaOpen = true" @mouseleave="megaOpen = false">
                            <button class="flex items-center gap-2 h-full px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                                All Category
                                <svg class="w-3 h-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <Transition name="drop">
                                <div v-if="megaOpen" class="absolute top-full left-0 w-56 bg-white border border-gray-200 shadow-xl rounded-b-xl z-50 py-1">
                                    <Link v-for="cat in categories" :key="cat.id"
                                        :href="route('products.index', { category_id: cat.id })"
                                        class="flex items-center justify-between px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition group">
                                        <span>{{ cat.name }}</span>
                                        <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </Link>
                                    <div class="border-t border-gray-100 mt-1">
                                        <Link :href="route('products.index')" class="flex items-center px-4 py-2.5 text-sm text-indigo-600 font-semibold hover:bg-indigo-50 transition">
                                            View all products →
                                        </Link>
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <!-- Nav links -->
                        <nav class="flex items-stretch flex-1">
                            <Link :href="route('home')"
                                class="flex items-center px-4 text-[13px] font-semibold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 transition whitespace-nowrap border-r border-gray-100"
                                :class="isRoute('home') ? 'text-indigo-600 bg-indigo-50' : ''">
                                HOME
                            </Link>
                            <Link :href="route('products.index', { sort_by: 'sold_count', sort_dir: 'desc' })"
                                class="flex items-center px-4 text-[13px] font-semibold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 transition whitespace-nowrap border-r border-gray-100">
                                BEST SELLING
                            </Link>
                            <Link :href="route('products.index', { sort_by: 'created_at', sort_dir: 'desc' })"
                                class="flex items-center px-4 text-[13px] font-semibold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 transition whitespace-nowrap border-r border-gray-100">
                                NEW ARRIVALS
                            </Link>
                            <Link :href="route('brands.index')"
                                class="flex items-center px-4 text-[13px] font-semibold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 transition whitespace-nowrap border-r border-gray-100">
                                BRANDS
                            </Link>
                            <Link :href="route('products.index', { featured: 1 })"
                                class="flex items-center gap-1 px-4 text-[13px] font-bold text-orange-500 hover:text-orange-600 hover:bg-orange-50 transition whitespace-nowrap border-r border-gray-100">
                                🔥 HOT OFFER
                            </Link>
                            <a href="#" class="flex items-center px-4 text-[13px] font-semibold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 transition whitespace-nowrap">
                                BLOG
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <!-- ═══════════════════════════════════════════
             MOBILE HEADER (hidden on desktop)
        ═══════════════════════════════════════════ -->
        <header class="md:hidden bg-white sticky top-0 z-50 shadow-sm">

            <!-- Row 1: Hamburger | Logo | Search + Call -->
            <div class="flex items-center h-14 px-3 gap-2 border-b border-gray-100">

                <!-- Hamburger -->
                <button @click="drawerOpen = true" class="w-10 h-10 flex items-center justify-center text-gray-700 hover:text-indigo-600 hover:bg-gray-100 rounded-xl transition flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Logo -->
                <Link :href="route('home')" class="flex-1 flex justify-center items-center gap-2 min-w-0">
                    <img v-if="siteSettings.logo_url" :src="siteSettings.logo_url" alt="Site logo"
                        class="h-8 w-auto max-w-[120px] object-contain"/>
                    <div v-else class="h-8 w-24 rounded-md bg-gray-100 animate-pulse"></div>
                </Link>

                <!-- Search icon -->
                <button @click="searchOpen = !searchOpen" class="w-10 h-10 flex items-center justify-center text-gray-700 hover:text-indigo-600 hover:bg-gray-100 rounded-xl transition flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                <!-- Call icon -->
                <a :href="storePhone ? `tel:${storePhone}` : '#'" class="w-10 h-10 flex items-center justify-center text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl transition flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </a>
            </div>

            <!-- Mobile search expand -->
            <Transition name="search-drop">
                <div v-if="searchOpen" class="px-3 pb-3 pt-2 bg-white border-b border-gray-100">
                    <form @submit.prevent="search" class="flex h-10">
                        <input ref="mobileSearchInput" v-model="searchQuery" type="text" placeholder="Search products..."
                            class="flex-1 border-2 border-gray-900 border-r-0 rounded-l-md px-4 text-sm focus:outline-none bg-white"/>
                        <button type="submit" class="bg-gray-900 text-white px-4 rounded-r-md flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </Transition>

            <!-- Row 2: Quick nav strip -->
            <div class="flex border-t border-gray-100 overflow-x-auto scrollbar-hide">
                <Link :href="route('products.index')" class="flex-shrink-0 flex items-center justify-center px-3 py-2 text-xs font-bold text-orange-500 hover:bg-orange-50 transition whitespace-nowrap">
                    🔥 HOT OFFER
                </Link>
                <div class="w-px bg-gray-200"></div>
                <Link :href="route('products.index', { sort_by: 'created_at', sort_dir: 'desc' })" class="flex-shrink-0 flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition whitespace-nowrap">
                    NEW ARRIVALS
                </Link>
                <div class="w-px bg-gray-200"></div>
                <Link :href="route('products.index', { sort_by: 'sold_count', sort_dir: 'desc' })" class="flex-shrink-0 flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition whitespace-nowrap">
                    BEST SELLING
                </Link>
                <div class="w-px bg-gray-200"></div>
                <Link :href="route('brands.index')" class="flex-shrink-0 flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition whitespace-nowrap">
                    BRANDS
                </Link>
            </div>
        </header>

        <!-- ═══════════════════════════════════════════
             MOBILE LEFT DRAWER
        ═══════════════════════════════════════════ -->
        <Transition name="fade">
            <div v-if="drawerOpen" class="md:hidden fixed inset-0 bg-black/50 z-[60]" @click="drawerOpen = false"/>
        </Transition>
        <Transition name="drawer">
            <div v-if="drawerOpen" class="md:hidden fixed top-0 left-0 h-full w-72 max-w-[85vw] bg-white z-[70] flex flex-col shadow-2xl">
                <!-- Drawer header -->
                <div class="flex items-center justify-between px-4 h-14 bg-gray-900 flex-shrink-0">
                    <div class="flex items-center gap-2">
                        <img v-if="siteSettings.logo_url" :src="siteSettings.logo_url" alt="Site logo"
                            class="h-7 w-auto max-w-[110px] object-contain"/>
                        <div v-else class="h-7 w-20 rounded-md bg-white/10 animate-pulse"></div>
                    </div>
                    <button @click="drawerOpen = false" class="text-white/80 hover:text-white p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- User status -->
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex-shrink-0">
                    <template v-if="auth.user">
                        <p class="text-sm font-semibold text-gray-800">{{ auth.user.name }}</p>
                        <p class="text-xs text-gray-500">{{ auth.user.email }}</p>
                    </template>
                    <template v-else>
                        <div class="flex gap-2">
                            <Link :href="route('login')" @click="drawerOpen = false"
                                class="flex-1 text-center bg-gray-900 text-white text-sm font-semibold py-2 rounded-lg">Sign In</Link>
                            <Link :href="route('register')" @click="drawerOpen = false"
                                class="flex-1 text-center border border-gray-900 text-gray-900 text-sm font-semibold py-2 rounded-lg">Register</Link>
                        </div>
                    </template>
                </div>

                <!-- Nav links -->
                <div class="px-4 py-2 border-b border-gray-100 flex-shrink-0">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest py-2">Navigation</p>
                    <div class="space-y-0.5">
                        <Link :href="route('home')" @click="drawerOpen = false" class="flex items-center justify-between px-3 py-2.5 text-sm font-semibold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">HOME</Link>
                        <Link :href="route('products.index', { sort_by: 'sold_count', sort_dir: 'desc' })" @click="drawerOpen = false" class="flex items-center justify-between px-3 py-2.5 text-sm font-semibold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">BEST SELLING</Link>
                        <Link :href="route('products.index', { sort_by: 'created_at', sort_dir: 'desc' })" @click="drawerOpen = false" class="flex items-center justify-between px-3 py-2.5 text-sm font-semibold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">NEW ARRIVALS</Link>
                        <Link :href="route('brands.index')" @click="drawerOpen = false" class="flex items-center justify-between px-3 py-2.5 text-sm font-semibold text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">BRANDS</Link>
                        <Link :href="route('products.index', { featured: 1 })" @click="drawerOpen = false" class="flex items-center justify-between px-3 py-2.5 text-sm font-bold text-orange-500 hover:bg-orange-50 rounded-lg transition">🔥 HOT OFFER</Link>
                    </div>
                </div>

                <!-- Categories scroll -->
                <nav class="flex-1 overflow-y-auto py-2">
                    <p class="px-4 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Categories</p>
                    <Link :href="route('products.index')" @click="drawerOpen = false"
                        class="flex items-center justify-between px-4 py-3 text-sm font-semibold text-indigo-600 hover:bg-indigo-50 border-b border-gray-50">
                        All Products
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </Link>
                    <Link v-for="cat in categories" :key="cat.id"
                        :href="route('products.index', { category_id: cat.id })"
                        @click="drawerOpen = false"
                        class="flex items-center justify-between px-4 py-3 text-sm text-gray-700 hover:text-indigo-600 hover:bg-gray-50 border-b border-gray-50 transition">
                        {{ cat.name }}
                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </Link>
                </nav>

                <!-- Drawer footer -->
                <div class="px-4 py-4 border-t border-gray-100 flex-shrink-0 space-y-2">
                    <a v-if="storePhone" :href="`tel:${storePhone}`" class="flex items-center gap-3 text-sm text-gray-600 hover:text-green-600 py-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ storePhone }}
                    </a>
                    <Link v-if="auth.user" :href="route('logout')" method="post" as="button"
                        class="w-full text-center text-sm text-red-500 border border-red-200 py-2 rounded-lg hover:bg-red-50 transition">
                        Logout
                    </Link>
                </div>
            </div>
        </Transition>

        <!-- ═══════════════════════════════════════════
             MOBILE BOTTOM NAV (floating sticky)
        ═══════════════════════════════════════════ -->
        <nav class="md:hidden fixed bottom-3 left-3 right-3 z-50 bg-white rounded-2xl shadow-2xl border border-gray-100 flex items-end">

            <!-- Menu -->
            <button @click="drawerOpen = true"
                class="flex-1 flex flex-col items-center justify-center pb-2.5 pt-2 gap-0.5 text-gray-500 hover:text-indigo-600 transition rounded-l-2xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <span class="text-[10px] font-semibold">Menu</span>
            </button>

            <!-- Offer -->
            <Link :href="route('products.index', { featured: 1 })"
                class="flex-1 flex flex-col items-center justify-center pb-2.5 pt-2 gap-0.5 text-orange-500 hover:text-orange-600 transition">
                <span class="text-lg leading-none">🔥</span>
                <span class="text-[10px] font-semibold">Hot Offer</span>
            </Link>

            <!-- Home (center, elevated) -->
            <Link :href="route('home')"
                class="flex-shrink-0 flex flex-col items-center justify-center px-5 pb-2.5 gap-0.5 text-white bg-gray-900 hover:bg-gray-800 transition -mt-3 rounded-2xl shadow-lg mx-1"
                :class="isRoute('home') ? 'bg-indigo-700' : ''">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-[10px] font-bold">Home</span>
            </Link>

            <!-- Cart -->
            <button @click="openCart"
                class="flex-1 flex flex-col items-center justify-center pb-2.5 pt-2 gap-0.5 transition"
                :class="cartCount > 0 ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-600'">
                <div class="relative">
                    <svg class="w-5 h-5" :class="cartBounce ? 'cart-bump' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span v-if="cartCount > 0"
                        class="absolute -top-2 -right-3 bg-red-500 text-white text-[9px] font-bold min-w-[16px] h-[16px] flex items-center justify-center rounded-full px-0.5 leading-none z-10"
                        :class="cartBounce ? 'badge-pop' : ''">
                        {{ cartCount > 99 ? '99+' : cartCount }}
                    </span>
                </div>
                <span class="text-[10px] font-semibold">Cart</span>
            </button>

            <!-- Login / Account -->
            <Link :href="auth.user ? route('dashboard') : route('login')"
                class="flex-1 flex flex-col items-center justify-center pb-2.5 pt-2 gap-0.5 text-gray-500 hover:text-indigo-600 transition rounded-r-2xl"
                :class="isRoute('login') || isRoute('dashboard') ? 'text-indigo-600' : ''">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0M19 20a7 7 0 10-14 0"/>
                </svg>
                <span class="text-[10px] font-semibold">{{ auth.user ? 'Account' : 'Login' }}</span>
            </Link>

        </nav>
    </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { useCartDrawer } from '@/Composables/useCartDrawer';

const page = usePage();
const auth = computed(() => page.props.auth);
const cartCount = computed(() => page.props.cart_count ?? 0);
const categories = computed(() => page.props.nav_categories ?? []);
const siteSettings = computed(() => page.props.site_settings ?? {});

const siteSettings = computed(() => page.props.site_settings ?? {});
const social       = computed(() => siteSettings.value.social ?? {});
const storePhone   = computed(() => siteSettings.value.store_phone ?? '');
const storeEmail   = computed(() => siteSettings.value.store_email ?? '');

const drawerOpen = ref(false);
const megaOpen = ref(false);
const searchOpen = ref(false);
const mobileSearchInput = ref(null);
const searchQuery = ref('');
const cartBounce = ref(false);

const { open: openCart } = useCartDrawer();

watch(cartCount, (newVal, oldVal) => {
    if (newVal > oldVal) {
        cartBounce.value = false;
        nextTick(() => {
            cartBounce.value = true;
            setTimeout(() => { cartBounce.value = false; }, 600);
        });
    }
});

function isRoute(name) {
    try { return page.url === route(name) || page.url.startsWith(route(name)); } catch { return false; }
}

async function toggleSearch() {
    searchOpen.value = !searchOpen.value;
    if (searchOpen.value) {
        await nextTick();
        mobileSearchInput.value?.focus();
    }
}

function search() {
    if (!searchQuery.value.trim()) return;
    router.get(route('products.index'), { search: searchQuery.value });
    searchOpen.value = false;
    drawerOpen.value = false;
}
</script>

<style scoped>
.drop-enter-active, .drop-leave-active { transition: opacity 0.15s, transform 0.15s; }
.drop-enter-from, .drop-leave-to { opacity: 0; transform: translateY(-6px); }

.drawer-enter-active, .drawer-leave-active { transition: transform 0.25s cubic-bezier(0.4,0,0.2,1); }
.drawer-enter-from, .drawer-leave-to { transform: translateX(-100%); }

.fade-enter-active, .fade-leave-active { transition: opacity 0.25s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.search-drop-enter-active, .search-drop-leave-active { transition: opacity 0.15s, transform 0.15s; }
.search-drop-enter-from, .search-drop-leave-to { opacity: 0; transform: translateY(-8px); }

.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

@keyframes cartBump {
    0%   { transform: scale(1) rotate(0deg); }
    25%  { transform: scale(1.4) rotate(-8deg); }
    50%  { transform: scale(1.2) rotate(6deg); }
    75%  { transform: scale(1.1) rotate(-3deg); }
    100% { transform: scale(1) rotate(0deg); }
}
.cart-bump { animation: cartBump 0.55s cubic-bezier(0.36, 0.07, 0.19, 0.97) both; }

@keyframes badgePop {
    0%   { transform: scale(1); }
    40%  { transform: scale(1.6); }
    70%  { transform: scale(0.85); }
    100% { transform: scale(1); }
}
.badge-pop { animation: badgePop 0.45s cubic-bezier(0.36, 0.07, 0.19, 0.97) both; }
</style>
