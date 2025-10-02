<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/'" class="inline-flex items-center justify-center">
        <svg
            class="h-20 w-auto"
            viewBox="0 0 88 88"
            xmlns="http://www.w3.org/2000/svg"
            role="img"
            aria-label="JCT Agency emblem"
        >
            <defs>
                <linearGradient id="authCardGradient" x1="12" y1="12" x2="72" y2="76" gradientUnits="userSpaceOnUse">
                    <stop offset="0" stop-color="#38BDF8" />
                    <stop offset="1" stop-color="#1D4ED8" />
                </linearGradient>
            </defs>
            <rect x="12" y="12" width="64" height="64" rx="18" fill="url(#authCardGradient)" />
            <path
                d="M44 60.96c-8.96 0-14.8-5.92-14.8-15.36V28h8.96v17.28c0 4.24 2.48 6.88 6.48 6.88 3.84 0 6.16-2.32 6.16-6.16V28h8.96v17.36c0 9.36-5.76 15.6-15.76 15.6z"
                fill="#F8FAFC"
            />
        </svg>
    </Link>
</template>
