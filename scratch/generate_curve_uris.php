<?php

function getWbcCurveDataUri() {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="115" height="85">
        <rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#999999" stroke-width="1"/>
        <!-- WBC Bimodal Curve -->
        <path d="M 12 72 
                 C 20 72, 24 60, 30 38 
                 C 34 22, 38 22, 42 42 
                 C 46 54, 52 56, 56 46 
                 C 64 12, 74 10, 80 32 
                 C 86 52, 94 70, 118 72 
                 L 12 72 Z" 
              fill="#7b828a" stroke="#2b333a" stroke-width="1.8" stroke-linejoin="round"/>
        <!-- Baseline -->
        <line x1="8" y1="72" x2="122" y2="72" stroke="#333333" stroke-width="1" />
        <!-- Label -->
        <text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="10" font-weight="bold" fill="#000000" text-anchor="middle">WBC CURVE</text>
    </svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

function getPltCurveDataUri() {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="115" height="85">
        <rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#999999" stroke-width="1"/>
        <!-- PLT Left-skewed Curve -->
        <path d="M 12 72 
                 C 16 72, 19 42, 23 22 
                 C 27 10, 30 14, 35 32 
                 C 42 56, 54 70, 80 71 
                 C 96 72, 108 72, 118 72 
                 L 12 72 Z" 
              fill="#7b828a" stroke="#2b333a" stroke-width="1.8" stroke-linejoin="round"/>
        <!-- Baseline -->
        <line x1="8" y1="72" x2="122" y2="72" stroke="#333333" stroke-width="1" />
        <!-- Label -->
        <text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="10" font-weight="bold" fill="#000000" text-anchor="middle">PLT CURVE</text>
    </svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

function getRbcCurveDataUri() {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="115" height="85">
        <rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#999999" stroke-width="1"/>
        <!-- RBC Bell Gaussian Curve -->
        <path d="M 12 72 
                 C 28 72, 42 71, 52 50 
                 C 58 22, 61 12, 65 12 
                 C 69 12, 72 22, 78 50 
                 C 88 71, 102 72, 118 72 
                 L 12 72 Z" 
              fill="#434a54" stroke="#1c2128" stroke-width="1.8" stroke-linejoin="round"/>
        <!-- Baseline -->
        <line x1="8" y1="72" x2="122" y2="72" stroke="#333333" stroke-width="1" />
        <!-- Label -->
        <text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="10" font-weight="bold" fill="#000000" text-anchor="middle">RBC CURVE</text>
    </svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

echo "WBC URI length: " . strlen(getWbcCurveDataUri()) . "\n";
echo "PLT URI length: " . strlen(getPltCurveDataUri()) . "\n";
echo "RBC URI length: " . strlen(getRbcCurveDataUri()) . "\n";
