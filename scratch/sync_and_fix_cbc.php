<?php
/**
 * Synchronize and Fix CBC Template & Lab 8 Record with Dynamic Curves
 */
require_once 'scratch/test_refined_curves.php';

$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lab 8 actual patient values:
// WBC: 10,850, Neut: 52%, Lymph: 41%, Mono: 4%, Eos: 3%, Baso: 0%
// Plt: 314,000, MPV: 7.3, PDW: 13.7
// RBC: 6.19, MCV: 72.9 (microcytic), RDW-CV: 15.2%

$wbc_uri = CbcCurveGeneratorRefined::generateWbcCurve(10850, 52, 41, 4, 3, 0);
$plt_uri = CbcCurveGeneratorRefined::generatePltCurve(314000, 7.3, 13.7);
$rbc_uri = CbcCurveGeneratorRefined::generateRbcCurve(6.19, 72.9, 15.2);

$wbc_img = '<img src="' . $wbc_uri . '" alt="WBC CURVE" width="120" height="85" style="display:block; margin:auto; border:none;" />';
$plt_img = '<img src="' . $plt_uri . '" alt="PLT CURVE" width="120" height="85" style="display:block; margin:auto; border:none;" />';
$rbc_img = '<img src="' . $rbc_uri . '" alt="RBC CURVE" width="120" height="85" style="display:block; margin:auto; border:none;" />';

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
