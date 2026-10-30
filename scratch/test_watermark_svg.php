<?php

function getLcdcWatermarkSvg() {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 260 260" width="320" height="320" style="opacity: 0.065; pointer-events: none;">
        <!-- Outer double circle -->
        <circle cx="130" cy="130" r="120" fill="none" stroke="#b91c1c" stroke-width="3"/>
        <circle cx="130" cy="130" r="112" fill="none" stroke="#b91c1c" stroke-width="1.5"/>
        <circle cx="130" cy="130" r="80" fill="none" stroke="#b91c1c" stroke-width="1.5"/>
        
        <!-- Curved Text -->
        <path id="textCircleTop" d="M 30,130 A 100,100 0 0,1 230,130" fill="none"/>
        <text font-family="Arial, Helvetica, sans-serif" font-size="12" font-weight="bold" fill="#b91c1c" letter-spacing="2">
            <textPath href="#textCircleTop" startOffset="50%" text-anchor="middle">LIFE CARE DIAGNOSTIC CENTER</textPath>
        </text>

        <path id="textCircleBottom" d="M 230,130 A 100,100 0 0,1 30,130" fill="none"/>
        <text font-family="Arial, Helvetica, sans-serif" font-size="11" font-weight="bold" fill="#b91c1c" letter-spacing="3">
            <textPath href="#textCircleBottom" startOffset="50%" text-anchor="middle">★ KHASERHAT ★ NOAKHALI ★</textPath>
        </text>

        <!-- Center Cross -->
        <rect x="120" y="95" width="20" height="70" fill="#b91c1c" rx="3"/>
        <rect x="95" y="120" width="70" height="20" fill="#b91c1c" rx="3"/>

        <!-- Center Letters LCDC -->
        <text x="130" y="90" font-family="Arial, Helvetica, sans-serif" font-size="18" font-weight="900" fill="#b91c1c" text-anchor="middle">L C D C</text>
    </svg>';
}

echo "Watermark SVG generated.\n";
