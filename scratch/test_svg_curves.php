<?php
// Function to generate the Rayto RT-7600 style WBC curve SVG
function generateWbcCurveSvg() {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 85" width="105" height="75" style="border: 1px solid #888; background: #fff; display: block; margin: auto;">
        <!-- WBC Bimodal Curve -->
        <path d="M 10 65 
                 C 18 65, 22 55, 28 35 
                 C 32 20, 36 20, 40 38 
                 C 44 48, 48 50, 52 42 
                 C 58 12, 66 10, 72 30 
                 C 78 50, 85 64, 105 65 
                 L 105 65 L 10 65 Z" 
              fill="#7a8288" opacity="0.85" stroke="#2c3e50" stroke-width="1.5" stroke-linejoin="round"/>
        <!-- Baseline -->
        <line x1="8" y1="65" x2="112" y2="65" stroke="#444" stroke-width="1" />
        <!-- Label -->
        <text x="60" y="78" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#111" text-anchor="middle">WBC CURVE</text>
    </svg>';
}

// Function to generate the Rayto RT-7600 style PLT curve SVG
function generatePltCurveSvg() {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 85" width="105" height="75" style="border: 1px solid #888; background: #fff; display: block; margin: auto;">
        <!-- PLT Left-skewed Curve -->
        <path d="M 10 65 
                 C 14 65, 16 40, 20 22 
                 C 23 10, 26 12, 30 28 
                 C 36 50, 46 62, 70 64 
                 C 85 65, 95 65, 105 65 
                 L 10 65 Z" 
              fill="#7a8288" opacity="0.85" stroke="#2c3e50" stroke-width="1.5" stroke-linejoin="round"/>
        <!-- Baseline -->
        <line x1="8" y1="65" x2="112" y2="65" stroke="#444" stroke-width="1" />
        <!-- Label -->
        <text x="60" y="78" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#111" text-anchor="middle">PLT CURVE</text>
    </svg>';
}

// Function to generate the Rayto RT-7600 style RBC curve SVG
function generateRbcCurveSvg() {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 85" width="105" height="75" style="border: 1px solid #888; background: #fff; display: block; margin: auto;">
        <!-- RBC Bell Gaussian Curve -->
        <path d="M 10 65 
                 C 25 65, 38 64, 48 45 
                 C 54 20, 56 12, 60 12 
                 C 64 12, 66 20, 72 45 
                 C 82 64, 95 65, 105 65 
                 L 10 65 Z" 
              fill="#4a5568" opacity="0.9" stroke="#1a202c" stroke-width="1.5" stroke-linejoin="round"/>
        <!-- Baseline -->
        <line x1="8" y1="65" x2="112" y2="65" stroke="#444" stroke-width="1" />
        <!-- Label -->
        <text x="60" y="78" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#111" text-anchor="middle">RBC CURVE</text>
    </svg>';
}

echo "SVGs defined successfully.\n";
