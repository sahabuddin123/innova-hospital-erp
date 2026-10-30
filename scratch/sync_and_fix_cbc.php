<?php
/**
 * Synchronize and Fix CBC Template & Lab 8 Record
 * Pixel-perfect match for Life Care Diagnostic Center RAYTO RT-7600 Physical Report
 */

$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getRaytoWbcSvgUri() {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="115" height="82">
        <defs>
            <pattern id="wbcHatch" width="2" height="4" patternUnits="userSpaceOnUse">
                <line x1="1" y1="0" x2="1" y2="4" stroke="#68727d" stroke-width="0.9"/>
            </pattern>
        </defs>
        <rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#777777" stroke-width="1"/>
        <path d="M 12 72 L 18 72 C 24 72, 28 56, 32 38 C 36 22, 40 22, 44 42 C 48 54, 52 56, 56 46 C 64 12, 72 10, 78 30 C 84 50, 92 68, 114 72 L 118 72 L 12 72 Z" fill="#9ba5b0" stroke="#333d47" stroke-width="1.5"/>
        <path d="M 12 72 L 18 72 C 24 72, 28 56, 32 38 C 36 22, 40 22, 44 42 C 48 54, 52 56, 56 46 C 64 12, 72 10, 78 30 C 84 50, 92 68, 114 72 L 118 72 L 12 72 Z" fill="url(#wbcHatch)"/>
        <line x1="6" y1="72" x2="124" y2="72" stroke="#222222" stroke-width="1.2"/>
        <text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="9.5" font-weight="bold" fill="#000000" text-anchor="middle" letter-spacing="0.5">WBC CURVE</text>
    </svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

function getRaytoPltSvgUri() {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="115" height="82">
        <defs>
            <pattern id="pltHatch" width="2" height="4" patternUnits="userSpaceOnUse">
                <line x1="1" y1="0" x2="1" y2="4" stroke="#68727d" stroke-width="0.9"/>
            </pattern>
        </defs>
        <rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#777777" stroke-width="1"/>
        <path d="M 12 72 C 15 72, 17 48, 20 25 C 23 9, 26 12, 30 28 C 36 50, 48 68, 76 71 C 92 72, 104 72, 118 72 L 12 72 Z" fill="#9ba5b0" stroke="#333d47" stroke-width="1.5"/>
        <path d="M 12 72 C 15 72, 17 48, 20 25 C 23 9, 26 12, 30 28 C 36 50, 48 68, 76 71 C 92 72, 104 72, 118 72 L 12 72 Z" fill="url(#pltHatch)"/>
        <line x1="6" y1="72" x2="124" y2="72" stroke="#222222" stroke-width="1.2"/>
        <text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="9.5" font-weight="bold" fill="#000000" text-anchor="middle" letter-spacing="0.5">PLT CURVE</text>
    </svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

function getRaytoRbcSvgUri() {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="115" height="82">
        <rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#777777" stroke-width="1"/>
        <path d="M 10 72 L 40 72 C 48 72, 54 60, 58 35 C 60 16, 62 10, 64 10 C 66 10, 68 16, 70 35 C 74 60, 80 72, 88 72 L 120 72 L 10 72 Z" fill="#2c343f" stroke="#11161d" stroke-width="1.5"/>
        <line x1="6" y1="72" x2="124" y2="72" stroke="#222222" stroke-width="1.2"/>
        <text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="9.5" font-weight="bold" fill="#000000" text-anchor="middle" letter-spacing="0.5">RBC CURVE</text>
    </svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

$wbc_img = '<img src="' . getRaytoWbcSvgUri() . '" alt="WBC CURVE" width="115" height="82" style="display:block; margin:auto; border:none;" />';
$plt_img = '<img src="' . getRaytoPltSvgUri() . '" alt="PLT CURVE" width="115" height="82" style="display:block; margin:auto; border:none;" />';
$rbc_img = '<img src="' . getRaytoRbcSvgUri() . '" alt="RBC CURVE" width="115" height="82" style="display:block; margin:auto; border:none;" />';

$content = '<div class="cbc-report-body" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #000; line-height: 1.4;">
    <!-- Analyzer Method Note (Placed immediately below patient box) -->
    <div style="text-align: center; font-style: italic; font-size: 12px; color: #222; margin-top: 4px; margin-bottom: 8px;">
        ( Relevent estimations were carried out by RAYTO RT-7600 Automated Haematology Analyzer &amp; checked manually )
    </div>

    <!-- Title Badge -->
    <div style="text-align: center; margin-bottom: 12px;">
        <span style="display: inline-block; border: 1.5px solid #333; border-radius: 3px; padding: 3px 28px; font-weight: bold; font-size: 13.5px; text-transform: uppercase; letter-spacing: 1px; color: #000; background: #fff; box-shadow: 2px 2px 0px rgba(0,0,0,0.2);">
            HAEMATOLOGY REPORT
        </span>
    </div>

    <!-- Results Table -->
    <table style="width: 100%; border-collapse: collapse; font-family: Arial, Helvetica, sans-serif; font-size: 12.5px; color: #000;">
        <thead>
            <tr style="border-top: 1.5px solid #222; border-bottom: 1.5px solid #222;">
                <th style="text-align: left; padding: 5px 4px; font-weight: bold; width: 34%;">Parameter</th>
                <th style="text-align: left; padding: 5px 4px; font-weight: bold; width: 13%;">Results</th>
                <th style="text-align: left; padding: 5px 4px; font-weight: bold; width: 13%;">Unit</th>
                <th style="text-align: left; padding: 5px 4px; font-weight: bold; width: 24%;">Reference Values <!-- Reference Interval --></th>
                <th style="text-align: center; padding: 5px 4px; font-weight: bold; width: 16%;">Histogram</th>
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
            <tr><td colspan="5" style="height: 6px;"></td></tr>

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
                <td style="padding: 2.5px 4px 2.5px 18px;">Neutrophils</td>
                <td style="padding: 2.5px 4px; font-weight: bold;">52</td>
                <td style="padding: 2.5px 4px;">%</td>
                <td style="padding: 2.5px 4px;">40 -70 %</td>
            </tr>
            <tr>
                <td style="padding: 2.5px 4px 2.5px 18px;">Lymphocytes</td>
                <td style="padding: 2.5px 4px; font-weight: bold;">41</td>
                <td style="padding: 2.5px 4px;">%</td>
                <td style="padding: 2.5px 4px;">20-50 %</td>
            </tr>
            <tr>
                <td style="padding: 2.5px 4px 2.5px 18px;">Monocytes</td>
                <td style="padding: 2.5px 4px; font-weight: bold;">04</td>
                <td style="padding: 2.5px 4px;">%</td>
                <td style="padding: 2.5px 4px;">02-10 %</td>
            </tr>
            <tr>
                <td style="padding: 2.5px 4px 2.5px 18px;">Eosinophils</td>
                <td style="padding: 2.5px 4px; font-weight: bold;">03</td>
                <td style="padding: 2.5px 4px;">%</td>
                <td style="padding: 2.5px 4px;">01-06 %</td>
            </tr>
            <tr>
                <td style="padding: 2.5px 4px 2.5px 18px;">Basophil</td>
                <td style="padding: 2.5px 4px; font-weight: bold;">00</td>
                <td style="padding: 2.5px 4px;">%</td>
                <td style="padding: 2.5px 4px;">00-01 %</td>
            </tr>

            <!-- Spacing -->
            <tr><td colspan="5" style="height: 6px;"></td></tr>

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
                <td style="padding: 2.5px 4px 2.5px 18px;">MPV</td>
                <td style="padding: 2.5px 4px; font-weight: bold;">7.3</td>
                <td style="padding: 2.5px 4px;">m^3</td>
                <td style="padding: 2.5px 4px;">7.0 -11.0 m^3</td>
            </tr>
            <tr>
                <td style="padding: 2.5px 4px 2.5px 18px;">PDW</td>
                <td style="padding: 2.5px 4px; font-weight: bold;">13.7</td>
                <td style="padding: 2.5px 4px;">%</td>
                <td style="padding: 2.5px 4px;">10 - 18 %</td>
            </tr>
            <tr>
                <td style="padding: 2.5px 4px 2.5px 18px;">PCT</td>
                <td style="padding: 2.5px 4px; font-weight: bold;">0.23</td>
                <td style="padding: 2.5px 4px;">%</td>
                <td style="padding: 2.5px 4px;">0.2 - 0.5 %</td>
            </tr>

            <!-- Spacing -->
            <tr><td colspan="5" style="height: 6px;"></td></tr>

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
                <td style="padding: 2.5px 4px 2.5px 18px;">HCT/PCV</td>
                <td style="padding: 2.5px 4px; font-weight: bold;">45.2</td>
                <td style="padding: 2.5px 4px;">%</td>
                <td style="padding: 2.5px 4px;">M: 40-54%, F: 37-47%</td>
            </tr>
            <tr>
                <td style="padding: 2.5px 4px 2.5px 18px;">MCV</td>
                <td style="padding: 2.5px 4px; font-weight: bold;">72.9</td>
                <td style="padding: 2.5px 4px;">fL</td>
                <td style="padding: 2.5px 4px;">76-94 fL</td>
            </tr>
            <tr>
                <td style="padding: 2.5px 4px 2.5px 18px;">MCH</td>
                <td style="padding: 2.5px 4px; font-weight: bold;">24.8</td>
                <td style="padding: 2.5px 4px;">pg</td>
                <td style="padding: 2.5px 4px;">27-32 pg</td>
            </tr>
            <tr>
                <td style="padding: 2.5px 4px 2.5px 18px;">MCHC</td>
                <td style="padding: 2.5px 4px; font-weight: bold;">34</td>
                <td style="padding: 2.5px 4px;">g/dL</td>
                <td style="padding: 2.5px 4px;">29-34 g/dl</td>
            </tr>
            <tr>
                <td style="padding: 2.5px 4px 2.5px 18px;">RDW-SD</td>
                <td style="padding: 2.5px 4px; font-weight: bold;">42.4</td>
                <td style="padding: 2.5px 4px;">fL</td>
                <td style="padding: 2.5px 4px;">37.0 - 54.0 fL</td>
            </tr>
            <tr>
                <td style="padding: 2.5px 4px 2.5px 18px;">RDW-CV</td>
                <td style="padding: 2.5px 4px; font-weight: bold;">15.2</td>
                <td style="padding: 2.5px 4px;">%</td>
                <td style="padding: 2.5px 4px;">11.5 - 14.5%</td>
            </tr>
            <tr>
                <td style="padding: 2.5px 4px 2.5px 18px;">P LCR</td>
                <td style="padding: 2.5px 4px; font-weight: bold;">12.1</td>
                <td style="padding: 2.5px 4px;">%</td>
                <td style="padding: 2.5px 4px;">13.0 - 43.0 %</td>
            </tr>
        </tbody>
    </table>
</div>';

// 1. Update template ID 99 in template table
$stmt = $conn->prepare("UPDATE template SET template = ? WHERE id = 99");
$stmt->bind_param("s", $content);
$stmt->execute();
echo "Template 99 updated successfully! Affected rows: " . $stmt->affected_rows . "\n";

// 2. Update Lab ID 8's report in lab table
$stmt2 = $conn->prepare("UPDATE lab SET report = ? WHERE id = 8");
$stmt2->bind_param("s", $content);
$stmt2->execute();
echo "Lab ID 8 report updated successfully! Affected rows: " . $stmt2->affected_rows . "\n";

// 3. Verify Lab 8 content
$res = $conn->query("SELECT id, LENGTH(report) as len FROM lab WHERE id = 8");
$row = $res->fetch_assoc();
echo "Lab ID 8 current report length: {$row['len']} bytes\n";
