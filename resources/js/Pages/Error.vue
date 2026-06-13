<template>
    <StorefrontLayout :title="title">
    <div class="min-h-[80vh] bg-[#0f0c29] flex flex-col items-center justify-center overflow-hidden relative px-4 -mx-4 sm:-mx-0">

        <!-- Stars -->
        <div class="stars absolute inset-0 pointer-events-none">
            <span v-for="n in 80" :key="n" class="star" :style="starStyle(n)" />
        </div>

        <!-- 404 Text -->
        <div class="relative z-10 text-center">
            <div class="flex items-center justify-center gap-2 sm:gap-4 mb-2">
                <span class="text-[80px] sm:text-[120px] font-black text-white leading-none drop-shadow-2xl digit">4</span>

                <!-- Astronaut character -->
                <div class="astronaut w-20 h-20 sm:w-32 sm:h-32 flex-shrink-0">
                    <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <!-- Jetpack -->
                        <rect x="42" y="68" width="36" height="22" rx="6" fill="#4f46e5" opacity="0.9"/>
                        <rect x="47" y="88" width="8" height="6" rx="2" fill="#fbbf24"/>
                        <rect x="65" y="88" width="8" height="6" rx="2" fill="#fbbf24"/>

                        <!-- Body suit -->
                        <rect x="38" y="58" width="44" height="32" rx="12" fill="#6366f1"/>

                        <!-- Helmet -->
                        <circle cx="60" cy="46" r="26" fill="#818cf8"/>
                        <!-- Helmet visor -->
                        <circle cx="60" cy="46" r="18" fill="#1e1b4b"/>
                        <circle cx="60" cy="46" r="17" fill="url(#visor)"/>
                        <!-- Visor reflection -->
                        <ellipse cx="53" cy="40" rx="5" ry="3" fill="white" opacity="0.3" transform="rotate(-20 53 40)"/>
                        <ellipse cx="66" cy="38" rx="2" ry="1.5" fill="white" opacity="0.2"/>

                        <!-- Arms -->
                        <rect x="16" y="62" width="24" height="12" rx="6" fill="#6366f1"/>
                        <rect x="80" y="62" width="24" height="12" rx="6" fill="#6366f1"/>
                        <!-- Gloves -->
                        <circle cx="14" cy="68" r="7" fill="#818cf8"/>
                        <circle cx="106" cy="68" r="7" fill="#818cf8"/>

                        <!-- Legs -->
                        <rect x="44" y="88" width="14" height="18" rx="6" fill="#4f46e5"/>
                        <rect x="62" y="88" width="14" height="18" rx="6" fill="#4f46e5"/>
                        <!-- Boots -->
                        <rect x="42" y="102" width="18" height="10" rx="5" fill="#312e81"/>
                        <rect x="60" y="102" width="18" height="10" rx="5" fill="#312e81"/>

                        <!-- Helmet bolt details -->
                        <circle cx="36" cy="46" r="3" fill="#4f46e5"/>
                        <circle cx="84" cy="46" r="3" fill="#4f46e5"/>

                        <!-- Antenna -->
                        <line x1="60" y1="20" x2="60" y2="8" stroke="#818cf8" stroke-width="2.5" stroke-linecap="round"/>
                        <circle cx="60" cy="6" r="3.5" fill="#fbbf24" class="antenna-blink"/>

                        <!-- Suit details -->
                        <rect x="52" y="64" width="16" height="8" rx="3" fill="#4338ca" opacity="0.6"/>
                        <circle cx="56" cy="68" r="1.5" fill="#fbbf24"/>
                        <circle cx="64" cy="68" r="1.5" fill="#34d399"/>

                        <defs>
                            <radialGradient id="visor" cx="40%" cy="35%" r="60%">
                                <stop offset="0%" stop-color="#3730a3" stop-opacity="0.8"/>
                                <stop offset="100%" stop-color="#1e1b4b" stop-opacity="1"/>
                            </radialGradient>
                        </defs>
                    </svg>
                </div>

                <span class="text-[80px] sm:text-[120px] font-black text-white leading-none drop-shadow-2xl digit">4</span>
            </div>

            <!-- Tether rope -->
            <div class="flex justify-center mb-6">
                <svg width="120" height="20" viewBox="0 0 120 20" class="tether opacity-60">
                    <path d="M10 10 Q30 2 60 10 Q90 18 110 10" stroke="#818cf8" stroke-width="2" fill="none" stroke-dasharray="4 3"/>
                </svg>
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold text-white mb-3">Lost in Space!</h1>
            <p class="text-indigo-300 text-sm sm:text-base mb-8 max-w-sm mx-auto">
                {{ description }}
            </p>

            <div class="flex items-center justify-center gap-3 flex-wrap">
                <a href="/"
                    class="inline-flex items-center gap-2 bg-indigo-500 hover:bg-indigo-400 text-white font-bold px-6 py-3 rounded-full transition-all text-sm shadow-lg shadow-indigo-500/30 hover:shadow-indigo-400/40 hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Back to Home
                </a>
                <button @click="history.back()"
                    class="inline-flex items-center gap-2 border border-indigo-500/40 text-indigo-300 hover:bg-indigo-500/10 font-semibold px-6 py-3 rounded-full transition-all text-sm hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Go Back
                </button>
            </div>

            <!-- Error code badge -->
            <div class="mt-10 inline-flex items-center gap-2 bg-white/5 border border-white/10 rounded-full px-4 py-1.5">
                <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></span>
                <span class="text-xs text-indigo-300 font-mono">ERROR {{ status }} — {{ title }}</span>
            </div>
        </div>

        <!-- Planet -->
        <div class="planet absolute bottom-0 right-0 w-48 h-48 sm:w-72 sm:h-72 rounded-full bg-gradient-to-br from-indigo-700 to-purple-900 translate-x-1/3 translate-y-1/3 opacity-60">
            <div class="absolute inset-4 rounded-full bg-gradient-to-br from-indigo-600/30 to-transparent"></div>
            <div class="absolute top-8 left-6 w-24 h-3 rounded-full bg-indigo-500/20 rotate-12"></div>
            <div class="absolute top-14 left-4 w-16 h-2 rounded-full bg-purple-400/20 rotate-12"></div>
        </div>

        <!-- Small planet left -->
        <div class="absolute top-10 left-6 w-16 h-16 rounded-full bg-gradient-to-br from-purple-600 to-pink-800 opacity-40"></div>

    </div>
    </StorefrontLayout>
</template>

<script setup>
import { computed } from 'vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

const props = defineProps({ status: { type: Number, default: 404 } });

const title = computed(() => ({
    404: 'Page Not Found',
    403: 'Access Forbidden',
    500: 'Server Error',
    503: 'Service Unavailable',
}[props.status] ?? 'Something Went Wrong'));

const description = computed(() => ({
    404: 'The page you are looking for does not exist or has been moved to another galaxy.',
    403: 'You do not have permission to access this page.',
    500: 'Something went wrong on our end. Our crew is working on it.',
    503: 'We are down for maintenance. Please check back soon.',
}[props.status] ?? 'An unexpected error occurred.'));

function starStyle(n) {
    const size = Math.random() * 2.5 + 0.5;
    return {
        position: 'absolute',
        width: size + 'px',
        height: size + 'px',
        borderRadius: '50%',
        background: 'white',
        top: (Math.sin(n * 137.508) * 0.5 + 0.5) * 100 + '%',
        left: (Math.cos(n * 137.508 + 1) * 0.5 + 0.5) * 100 + '%',
        opacity: Math.random() * 0.7 + 0.3,
        animation: `twinkle ${2 + (n % 3)}s ease-in-out ${(n % 20) * 0.1}s infinite alternate`,
    };
}
</script>

<style scoped>
.astronaut {
    animation: float 4s ease-in-out infinite;
}

.digit {
    animation: pulse-glow 3s ease-in-out infinite alternate;
}

.antenna-blink {
    animation: blink 1.5s ease-in-out infinite;
}

.tether {
    animation: sway 4s ease-in-out infinite;
}

.planet {
    animation: planet-rotate 20s linear infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(-3deg); }
    50% { transform: translateY(-18px) rotate(3deg); }
}

@keyframes pulse-glow {
    0% { text-shadow: 0 0 20px rgba(99,102,241,0.5); }
    100% { text-shadow: 0 0 50px rgba(139,92,246,0.8), 0 0 80px rgba(99,102,241,0.3); }
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.2; }
}

@keyframes twinkle {
    0% { opacity: 0.2; transform: scale(0.8); }
    100% { opacity: 1; transform: scale(1.2); }
}

@keyframes sway {
    0%, 100% { transform: scaleX(1); }
    50% { transform: scaleX(0.85) translateX(5px); }
}

@keyframes planet-rotate {
    from { transform: translate(33%, 33%) rotate(0deg); }
    to { transform: translate(33%, 33%) rotate(360deg); }
}
</style>
