<?php

function getRaselSignatureSvg() {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 180 60" width="140" height="48" style="display:inline-block;">
        <path d="M 25 45 
                 C 20 40, 15 22, 28 12 
                 C 38 4, 46 15, 38 28 
                 C 30 42, 22 48, 45 35 
                 C 55 28, 62 18, 68 22 
                 C 74 26, 70 38, 78 30 
                 C 86 22, 92 20, 98 28 
                 C 104 35, 110 32, 116 26 
                 C 122 20, 128 22, 134 32
                 M 35 34 L 145 22
                 M 40 48 C 65 42, 110 32, 155 18" 
              fill="none" stroke="#0f172a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>';
}

echo "Rasel signature SVG generated successfully.\n";
