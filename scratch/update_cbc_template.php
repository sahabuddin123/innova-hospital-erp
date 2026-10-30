<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getWbcCurveDataUri() {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="110" height="80">
        <rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#888888" stroke-width="1"/>
        <path d="M 10 72 C 18 72, 23 58, 28 38 C 32 22, 36 22, 40 42 C 45 54, 50 56, 55 45 C 63 12, 73 10, 80 32 C 86 52, 94 70, 118 72 L 10 72 Z" fill="#78818a" stroke="#252c33" stroke-width="1.8" stroke-linejoin="round"/>
        <line x1="6" y1="72" x2="124" y2="72" stroke="#333333" stroke-width="1"/>
        <text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="10" font-weight="bold" fill="#000000" text-anchor="middle">WBC CURVE</text>
    </svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

function getPltCurveDataUri() {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="110" height="80">
        <rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#888888" stroke-width="1"/>
        <path d="M 10 72 C 14 72, 18 42, 22 22 C 26 10, 29 14, 34 32 C 40 54, 52 70, 78 71 C 94 72, 106 72, 118 72 L 10 72 Z" fill="#78818a" stroke="#252c33" stroke-width="1.8" stroke-linejoin="round"/>
        <line x1="6" y1="72" x2="124" y2="72" stroke="#333333" stroke-width="1"/>
        <text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="10" font-weight="bold" fill="#000000" text-anchor="middle">PLT CURVE</text>
    </svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

function getRbcCurveDataUri() {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="110" height="80">
        <rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#888888" stroke-width="1"/>
        <path d="M 10 72 C 26 72, 40 71, 50 50 C 56 22, 59 12, 63 12 C 67 12, 70 22, 76 50 C 86 71, 100 72, 118 72 L 10 72 Z" fill="#434a54" stroke="#1a2027" stroke-width="1.8" stroke-linejoin="round"/>
        <line x1="6" y1="72" x2="124" y2="72" stroke="#333333" stroke-width="1"/>
        <text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="10" font-weight="bold" fill="#000000" text-anchor="middle">RBC CURVE</text>
    </svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

$wbc_img = '<img src="' . getWbcCurveDataUri() . '" alt="WBC CURVE" width="110" height="80" style="display:block; margin:auto; border:none;" />';
$plt_img = '<img src="' . getPltCurveDataUri() . '" alt="PLT CURVE" width="110" height="80" style="display:block; margin:auto; border:none;" />';
$rbc_img = '<img src="' . getRbcCurveDataUri() . '" alt="RBC CURVE" width="110" height="80" style="display:block; margin:auto; border:none;" />';

$content = '<div class="cbc-report-body" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #000; line-height: 1.4;">
    <!-- Analyzer Method Note -->
    <div style="text-align: center; font-style: italic; font-size: 11.5px; color: #444; margin-bottom: 8px;">
        ( Relevent estimations were carried out by RAYTO RT-7600 Automated Haematology Analyzer &amp; checked manually )
    </div>

    <!-- Title Badge -->
    <div style="text-align: center; margin-bottom: 12px;">
        <span style="display: inline-block; border: 1.5px solid #222; border-radius: 4px; padding: 4px 26px; font-weight: bold; font-size: 13.5px; text-transform: uppercase; letter-spacing: 1px; color: #000; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            HAEMATOLOGY REPORT
        </span>
    </div>

    <!-- Results Table -->
    <table style="width: 100%; border-collapse: collapse; font-family: Arial, Helvetica, sans-serif; font-size: 12.5px; color: #000;">
        <thead>
            <tr style="border-top: 1.5px solid #222; border-bottom: 1.5px solid #222;">
                <th style="text-align: left; padding: 5px 4px; font-weight: bold; width: 35%;">Parameter</th>
                <th style="text-align: left; padding: 5px 4px; font-weight: bold; width: 14%;">Results</th>
                <th style="text-align: left; padding: 5px 4px; font-weight: bold; width: 13%;">Unit</th>
                <th style="text-align: left; padding: 5px 4px; font-weight: bold; width: 24%;">Reference Values <!-- Reference Interval --></th>
                <th style="text-align: center; padding: 5px 4px; font-weight: bold; width: 14%;">Histogram</th>
            </tr>
        </thead>
        <tbody>
            <!-- 1. Haemoglobin & ESR -->
            <tr>
                <td style="padding: 4px 4px; font-weight: bold;">Haemoglobin(Hb)</td>
                <td style="padding: 4px 4px; font-weight: bold;">15.4</td>
                <td style="padding: 4px 4px;">g/dl</td>
                <td style="padding: 4px 4px;">M:12.0-18.0, F:11.5-16.5 g/dl</td>
                <td></td>
            </tr>
            <tr>
                <td style="padding: 4px 4px; font-weight: bold;">ESR(Westergren)</td>
                <td style="padding: 4px 4px; font-weight: bold;">08</td>
                <td style="padding: 4px 4px;">mm/1st hr</td>
                <td style="padding: 4px 4px;">M:0-10, F:0-20 mm/1st hr</td>
                <td></td>
            </tr>

            <!-- Spacing -->
            <tr><td colspan="5" style="height: 8px;"></td></tr>

            <!-- 2. WBC Count -->
            <tr>
                <td style="padding: 4px 4px; font-weight: bold;">TOTAL WBC COUNT</td>
                <td style="padding: 4px 4px; font-weight: bold;">10,850</td>
                <td style="padding: 4px 4px;">/cumm</td>
                <td style="padding: 4px 4px;">4,000 - 11,000 /cumm</td>
                <td></td>
            </tr>

            <!-- 3. Differential Count + WBC Histogram Curve -->
            <tr>
                <td style="padding: 4px 4px; font-weight: bold; text-decoration: underline;" colspan="4">DIFFERENTIAL COUNT</td>
                <td rowspan="6" style="vertical-align: middle; text-align: center; padding: 2px;">
                    ' . $wbc_img . '
                </td>
            </tr>
            <tr>
                <td style="padding: 3px 4px 3px 18px;">Neutrophils</td>
                <td style="padding: 3px 4px; font-weight: bold;">52</td>
                <td style="padding: 3px 4px;">%</td>
                <td style="padding: 3px 4px;">40 -70 %</td>
            </tr>
            <tr>
                <td style="padding: 3px 4px 3px 18px;">Lymphocytes</td>
                <td style="padding: 3px 4px; font-weight: bold;">41</td>
                <td style="padding: 3px 4px;">%</td>
                <td style="padding: 3px 4px;">20-50 %</td>
            </tr>
            <tr>
                <td style="padding: 3px 4px 3px 18px;">Monocytes</td>
                <td style="padding: 3px 4px; font-weight: bold;">04</td>
                <td style="padding: 3px 4px;">%</td>
                <td style="padding: 3px 4px;">02-10 %</td>
            </tr>
            <tr>
                <td style="padding: 3px 4px 3px 18px;">Eosinophils</td>
                <td style="padding: 3px 4px; font-weight: bold;">03</td>
                <td style="padding: 3px 4px;">%</td>
                <td style="padding: 3px 4px;">01-06 %</td>
            </tr>
            <tr>
                <td style="padding: 3px 4px 3px 18px;">Basophil</td>
                <td style="padding: 3px 4px; font-weight: bold;">00</td>
                <td style="padding: 3px 4px;">%</td>
                <td style="padding: 3px 4px;">00-01 %</td>
            </tr>

            <!-- Spacing -->
            <tr><td colspan="5" style="height: 8px;"></td></tr>

            <!-- 4. Eosinophil & Platelets + PLT Histogram Curve -->
            <tr>
                <td style="padding: 4px 4px; font-weight: bold;">TOTAL CIR. EOSIONOPHIL COUNT</td>
                <td style="padding: 4px 4px; font-weight: bold;">326</td>
                <td style="padding: 4px 4px;">/cumm</td>
                <td style="padding: 4px 4px;">40 - 450 /cumm</td>
                <td></td>
            </tr>
            <tr>
                <td style="padding: 4px 4px; font-weight: bold;">TOTAL PLATELET COUNT(PC)</td>
                <td style="padding: 4px 4px; font-weight: bold;">314,000</td>
                <td style="padding: 4px 4px;">/cumm</td>
                <td style="padding: 4px 4px;">1,50,000-4,50,000 /cumm</td>
                <td rowspan="4" style="vertical-align: middle; text-align: center; padding: 2px;">
                    ' . $plt_img . '
                </td>
            </tr>
            <tr>
                <td style="padding: 3px 4px 3px 18px;">MPV</td>
                <td style="padding: 3px 4px; font-weight: bold;">7.3</td>
                <td style="padding: 3px 4px;">m^3</td>
                <td style="padding: 3px 4px;">7.0 -11.0 m^3</td>
            </tr>
            <tr>
                <td style="padding: 3px 4px 3px 18px;">PDW</td>
                <td style="padding: 3px 4px; font-weight: bold;">13.7</td>
                <td style="padding: 3px 4px;">%</td>
                <td style="padding: 3px 4px;">10 - 18 %</td>
            </tr>
            <tr>
                <td style="padding: 3px 4px 3px 18px;">PCT</td>
                <td style="padding: 3px 4px; font-weight: bold;">0.23</td>
                <td style="padding: 3px 4px;">%</td>
                <td style="padding: 3px 4px;">0.2 - 0.5 %</td>
            </tr>

            <!-- Spacing -->
            <tr><td colspan="5" style="height: 8px;"></td></tr>

            <!-- 5. RBC Count & Indices + RBC Histogram Curve -->
            <tr>
                <td style="padding: 4px 4px; font-weight: bold;">RBC COUNT</td>
                <td style="padding: 4px 4px; font-weight: bold;">6.19</td>
                <td style="padding: 4px 4px;">m/ul</td>
                <td style="padding: 4px 4px;">M: 4.5-6.5, F: 3.8-5.8 m/ul</td>
                <td rowspan="9" style="vertical-align: middle; text-align: center; padding: 2px;">
                    ' . $rbc_img . '
                </td>
            </tr>
            <tr>
                <td style="padding: 3px 4px 3px 18px;">HCT/PCV</td>
                <td style="padding: 3px 4px; font-weight: bold;">45.2</td>
                <td style="padding: 3px 4px;">%</td>
                <td style="padding: 3px 4px;">M: 40-54%, F: 37-47%</td>
            </tr>
            <tr>
                <td style="padding: 3px 4px 3px 18px;">MCV</td>
                <td style="padding: 3px 4px; font-weight: bold;">72.9</td>
                <td style="padding: 3px 4px;">fL</td>
                <td style="padding: 3px 4px;">76-94 fL</td>
            </tr>
            <tr>
                <td style="padding: 3px 4px 3px 18px;">MCH</td>
                <td style="padding: 3px 4px; font-weight: bold;">24.8</td>
                <td style="padding: 3px 4px;">pg</td>
                <td style="padding: 3px 4px;">27-32 pg</td>
            </tr>
            <tr>
                <td style="padding: 3px 4px 3px 18px;">MCHC</td>
                <td style="padding: 3px 4px; font-weight: bold;">34</td>
                <td style="padding: 3px 4px;">g/dL</td>
                <td style="padding: 3px 4px;">29-34 g/dl</td>
            </tr>
            <tr>
                <td style="padding: 3px 4px 3px 18px;">RDW-SD</td>
                <td style="padding: 3px 4px; font-weight: bold;">42.4</td>
                <td style="padding: 3px 4px;">fL</td>
                <td style="padding: 3px 4px;">37.0 - 54.0 fL</td>
            </tr>
            <tr>
                <td style="padding: 3px 4px 3px 18px;">RDW-CV</td>
                <td style="padding: 3px 4px; font-weight: bold;">15.2</td>
                <td style="padding: 3px 4px;">%</td>
                <td style="padding: 3px 4px;">11.5 - 14.5%</td>
            </tr>
            <tr>
                <td style="padding: 3px 4px 3px 18px;">P LCR</td>
                <td style="padding: 3px 4px; font-weight: bold;">12.1</td>
                <td style="padding: 3px 4px;">%</td>
                <td style="padding: 3px 4px;">13.0 - 43.0 %</td>
            </tr>
        </tbody>
    </table>
</div>';

$stmt = $conn->prepare("UPDATE template SET template = ? WHERE id = 99");
$stmt->bind_param("s", $content);
$stmt->execute();
echo "Updated template ID 99 successfully! Affected rows: " . $stmt->affected_rows . "\n";
