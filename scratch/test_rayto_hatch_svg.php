<?php
/**
 * Test Rayto RT-7600 Histograms with Frequency Lines Pattern
 */

function generateRaytoWbcSvg() {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="115" height="82">
        <defs>
            <pattern id="wbcHatch" width="2" height="4" patternUnits="userSpaceOnUse">
                <line x1="1" y1="0" x2="1" y2="4" stroke="#68727d" stroke-width="0.9"/>
            </pattern>
        </defs>
        <rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#777777" stroke-width="1"/>
        <!-- WBC Double Peak Path -->
        <path d="M 12 72 L 18 72 C 24 72, 28 56, 32 38 C 36 22, 40 22, 44 42 C 48 54, 52 56, 56 46 C 64 12, 72 10, 78 30 C 84 50, 92 68, 114 72 L 118 72 L 12 72 Z" fill="#9ba5b0" stroke="#333d47" stroke-width="1.5"/>
        <path d="M 12 72 L 18 72 C 24 72, 28 56, 32 38 C 36 22, 40 22, 44 42 C 48 54, 52 56, 56 46 C 64 12, 72 10, 78 30 C 84 50, 92 68, 114 72 L 118 72 L 12 72 Z" fill="url(#wbcHatch)"/>
        <line x1="6" y1="72" x2="124" y2="72" stroke="#222222" stroke-width="1.2"/>
        <text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="9.5" font-weight="bold" fill="#000000" text-anchor="middle" letter-spacing="0.5">WBC CURVE</text>
    </svg>';
}

function generateRaytoPltSvg() {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="115" height="82">
        <defs>
            <pattern id="pltHatch" width="2" height="4" patternUnits="userSpaceOnUse">
                <line x1="1" y1="0" x2="1" y2="4" stroke="#68727d" stroke-width="0.9"/>
            </pattern>
        </defs>
        <rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#777777" stroke-width="1"/>
        <!-- PLT Left-skewed curve -->
        <path d="M 12 72 C 15 72, 17 48, 20 25 C 23 9, 26 12, 30 28 C 36 50, 48 68, 76 71 C 92 72, 104 72, 118 72 L 12 72 Z" fill="#9ba5b0" stroke="#333d47" stroke-width="1.5"/>
        <path d="M 12 72 C 15 72, 17 48, 20 25 C 23 9, 26 12, 30 28 C 36 50, 48 68, 76 71 C 92 72, 104 72, 118 72 L 12 72 Z" fill="url(#pltHatch)"/>
        <line x1="6" y1="72" x2="124" y2="72" stroke="#222222" stroke-width="1.2"/>
        <text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="9.5" font-weight="bold" fill="#000000" text-anchor="middle" letter-spacing="0.5">PLT CURVE</text>
    </svg>';
}

function generateRaytoRbcSvg() {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="115" height="82">
        <rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#777777" stroke-width="1"/>
        <!-- RBC Sharp Bell curve -->
        <path d="M 10 72 L 40 72 C 48 72, 54 60, 58 35 C 60 16, 62 10, 64 10 C 66 10, 68 16, 70 35 C 74 60, 80 72, 88 72 L 120 72 L 10 72 Z" fill="#2c343f" stroke="#11161d" stroke-width="1.5"/>
        <line x1="6" y1="72" x2="124" y2="72" stroke="#222222" stroke-width="1.2"/>
        <text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="9.5" font-weight="bold" fill="#000000" text-anchor="middle" letter-spacing="0.5">RBC CURVE</text>
    </svg>';
}

echo "Rayto SVGs generated.\n";
echo "WBC URI length: " . strlen('data:image/svg+xml;base64,' . base64_encode(generateRaytoWbcSvg())) . "\n";
echo "PLT URI length: " . strlen('data:image/svg+xml;base64,' . base64_encode(generateRaytoPltSvg())) . "\n";
echo "RBC URI length: " . strlen('data:image/svg+xml;base64,' . base64_encode(generateRaytoRbcSvg())) . "\n";
