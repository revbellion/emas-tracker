<template>
    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <!-- Outer ring -->
        <circle cx="50" cy="50" r="45" fill="none" stroke="url(#gold-grad)" stroke-width="3.5"/>
        <!-- Inner circle -->
        <circle cx="50" cy="50" r="30" fill="url(#gold-grad)" opacity="0.15"/>
        <!-- Gold bar icon -->
        <rect x="28" y="42" width="44" height="16" rx="3" fill="url(#gold-grad)"/>
        <rect x="32" y="40" width="36" height="4" rx="2" fill="url(#gold-light)"/>
        <rect x="32" y="56" width="36" height="4" rx="2" fill="url(#gold-dark)"/>
        <!-- AU text -->
        <text x="50" y="54" font-size="18" font-weight="800" fill="#FEF3C7" text-anchor="middle" font-family="system-ui, sans-serif">AU</text>
        <defs>
            <linearGradient id="gold-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#EAB308"/>
                <stop offset="50%" stop-color="#D97706"/>
                <stop offset="100%" stop-color="#B45309"/>
            </linearGradient>
            <linearGradient id="gold-light" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#FDE68A"/>
                <stop offset="100%" stop-color="#F59E0B"/>
            </linearGradient>
            <linearGradient id="gold-dark" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#D97706"/>
                <stop offset="100%" stop-color="#92400E"/>
            </linearGradient>
        </defs>
    </svg>
</template>
