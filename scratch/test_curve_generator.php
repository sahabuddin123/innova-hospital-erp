<?php
/**
 * Dynamic Hematology Histogram Generator for Rayto RT-7600
 */
class CbcCurveGenerator
{
    /**
     * Generate WBC 3-Part Differential Histogram SVG
     */
    public static function generateWbcCurve($total_wbc, $neut_pct, $lymph_pct, $mono_pct = 4, $eos_pct = 3, $baso_pct = 0)
    {
        // Defaults if missing or invalid
        $total_wbc = !empty($total_wbc) ? (float)str_replace([',', ' '], '', $total_wbc) : 7500;
        $neut_pct  = !empty($neut_pct)  ? (float)$neut_pct : 55;
        $lymph_pct = !empty($lymph_pct) ? (float)$lymph_pct : 35;
        $mid_pct   = ((float)$mono_pct + (float)$eos_pct + (float)$baso_pct);
        if ($mid_pct <= 0) $mid_pct = 10;

        // Scaling factor based on Total WBC count (normal: 4000 - 11000)
        $wbc_scale = max(0.75, min(1.30, $total_wbc / 8500));

        // Peak 1: Lymphocytes (~45 fL, X ≈ 34)
        // Peak 2: Granulocytes/Neutrophils (~200 fL, X ≈ 76)
        // Valley: Monocytes/Mid cells (~110 fL, X ≈ 52)
        // Available vertical height from baseline 72 down to ~12 (max 60px)
        $h_lymph = max(14, min(58, ($lymph_pct / 65) * 48 * $wbc_scale));
        $h_neut  = max(16, min(62, ($neut_pct / 70) * 52 * $wbc_scale));
        $h_mid   = max(6,  min(28, ($mid_pct / 18) * 22 * $wbc_scale));

        $baseline = 72;
        $y_lymph = round($baseline - $h_lymph, 1);
        $y_neut  = round($baseline - $h_neut, 1);
        $y_mid   = round($baseline - $h_mid, 1);

        // Control points for smooth dual-peak curve
        // Start: 12,72 -> lead-in: 18,72
        // Rise to Lymph Peak: control (24, 72) -> (28, y_lymph) -> peak (34, y_lymph)
        // Dip to Mid valley: (42, y_lymph + 10) -> (48, y_mid) -> trough (52, y_mid)
        // Rise to Neut Peak: (58, y_mid - 10) -> (66, y_neut) -> peak (76, y_neut)
        // Descend to base: (84, y_neut + 15) -> (96, 68) -> (114, 72) -> end (118, 72)
        $path_d = sprintf(
            "M 12 72 L 18 72 C 24 72, 28 %0.1f, 34 %0.1f C 40 %0.1f, 46 %0.1f, 52 %0.1f C 60 %0.1f, 68 %0.1f, 76 %0.1f C 84 %0.1f, 96 66, 114 72 L 118 72 L 12 72 Z",
            $y_lymph + 4, $y_lymph,
            $y_lymph + 8, $y_mid, $y_mid,
            $y_mid - 6, $y_neut, $y_neut,
            $y_neut + 12
        );

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="120" height="85">'
            . '<defs>'
            . '<pattern id="wbcHatchDynamic" width="2" height="4" patternUnits="userSpaceOnUse">'
            . '<line x1="1" y1="0" x2="1" y2="4" stroke="#68727d" stroke-width="0.9"/>'
            . '</pattern>'
            . '</defs>'
            . '<rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#777777" stroke-width="1"/>'
            . '<path d="' . $path_d . '" fill="#9ba5b0" stroke="#333d47" stroke-width="1.5"/>'
            . '<path d="' . $path_d . '" fill="url(#wbcHatchDynamic)"/>'
            . '<line x1="6" y1="72" x2="124" y2="72" stroke="#222222" stroke-width="1.2"/>'
            . '<text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="9.5" font-weight="bold" fill="#000000" text-anchor="middle" letter-spacing="0.5">WBC CURVE</text>'
            . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Generate PLT Histogram SVG
     */
    public static function generatePltCurve($plt_count, $mpv, $pdw = 13.5)
    {
        $plt_count = !empty($plt_count) ? (float)str_replace([',', ' '], '', $plt_count) : 280000;
        $mpv       = !empty($mpv)       ? (float)$mpv : 8.5;
        $pdw       = !empty($pdw)       ? (float)$pdw : 13.5;

        // MPV modulates peak X position (normal 7-11 fL)
        // lower MPV -> shift left (x ≈ 20); higher MPV -> shift right (x ≈ 36)
        $peak_x = max(18.0, min(38.0, 20.0 + (($mpv - 6.5) / 5.0) * 14.0));

        // Plt count modulates height (normal 150000 - 450000)
        $plt_scale = max(0.55, min(1.35, $plt_count / 300000));
        $h_plt = max(16.0, min(63.0, 52.0 * $plt_scale));
        $peak_y = round(72.0 - $h_plt, 1);

        // PDW modulates the tail spread towards right
        $tail_x = max(65.0, min(115.0, 75.0 + (($pdw - 12.0) / 6.0) * 20.0));

        $path_d = sprintf(
            "M 12 72 C 14 72, %0.1f %0.1f, %0.1f %0.1f C %0.1f %0.1f, %0.1f 55, %0.1f 70 C %0.1f 72, 108 72, 118 72 L 12 72 Z",
            $peak_x - 3.0, $peak_y + 18.0, $peak_x, $peak_y,
            $peak_x + 6.0, $peak_y + 12.0, $peak_x + 18.0, $tail_x,
            $tail_x + 12.0
        );

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="120" height="85">'
            . '<defs>'
            . '<pattern id="pltHatchDynamic" width="2" height="4" patternUnits="userSpaceOnUse">'
            . '<line x1="1" y1="0" x2="1" y2="4" stroke="#68727d" stroke-width="0.9"/>'
            . '</pattern>'
            . '</defs>'
            . '<rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#777777" stroke-width="1"/>'
            . '<path d="' . $path_d . '" fill="#9ba5b0" stroke="#333d47" stroke-width="1.5"/>'
            . '<path d="' . $path_d . '" fill="url(#pltHatchDynamic)"/>'
            . '<line x1="6" y1="72" x2="124" y2="72" stroke="#222222" stroke-width="1.2"/>'
            . '<text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="9.5" font-weight="bold" fill="#000000" text-anchor="middle" letter-spacing="0.5">PLT CURVE</text>'
            . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Generate RBC Histogram SVG
     */
    public static function generateRbcCurve($rbc_count, $mcv, $rdw_cv = 13.5, $rdw_sd = 42.0)
    {
        $rbc_count = !empty($rbc_count) ? (float)$rbc_count : 5.0;
        $mcv       = !empty($mcv)       ? (float)$mcv : 85.0;
        $rdw_cv    = !empty($rdw_cv)    ? (float)$rdw_cv : 13.5;

        // MCV dictates the horizontal center position (mean volume, normal 76-94 fL, center 85 fL)
        // Standard center in box is X = 64
        // If MCV < 76 (microcytic), shifts LEFT (down to X ≈ 46)
        // If MCV > 94 (macrocytic), shifts RIGHT (up to X ≈ 82)
        $center_x = max(42.0, min(86.0, 64.0 + ($mcv - 85.0) * 1.15));

        // RDW modulates curve width/spread (normal 11.5 - 14.5%)
        $half_width = max(13.0, min(28.0, 18.0 + ($rdw_cv - 13.0) * 1.6));

        // RBC count modulates peak height (normal 4.5 - 6.5)
        $rbc_scale = max(0.65, min(1.30, $rbc_count / 5.2));
        $h_rbc = max(24.0, min(62.0, 54.0 * $rbc_scale));
        $peak_y = round(72.0 - $h_rbc, 1);

        $left_base  = max(10.0, round($center_x - $half_width * 1.7, 1));
        $right_base = min(120.0, round($center_x + $half_width * 1.7, 1));
        $cp_left    = round($center_x - $half_width * 0.6, 1);
        $cp_right   = round($center_x + $half_width * 0.6, 1);

        // Gaussian bell curve
        $path_d = sprintf(
            "M 10 72 L %0.1f 72 C %0.1f 72, %0.1f %0.1f, %0.1f %0.1f C %0.1f %0.1f, %0.1f 72, %0.1f 72 L 120 72 L 10 72 Z",
            $left_base,
            $left_base + 6.0, $cp_left, $peak_y + 14.0, $center_x, $peak_y,
            $cp_right, $peak_y + 14.0, $right_base - 6.0, $right_base
        );

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 95" width="120" height="85">'
            . '<defs>'
            . '<pattern id="rbcHatchDynamic" width="2" height="4" patternUnits="userSpaceOnUse">'
            . '<line x1="1" y1="0" x2="1" y2="4" stroke="#68727d" stroke-width="0.9"/>'
            . '</pattern>'
            . '</defs>'
            . '<rect x="0.5" y="0.5" width="129" height="94" fill="#ffffff" stroke="#777777" stroke-width="1"/>'
            . '<path d="' . $path_d . '" fill="#333d4b" stroke="#11161d" stroke-width="1.5"/>'
            . '<path d="' . $path_d . '" fill="url(#rbcHatchDynamic)"/>'
            . '<line x1="6" y1="72" x2="124" y2="72" stroke="#222222" stroke-width="1.2"/>'
            . '<text x="65" y="87" font-family="Arial, Helvetica, sans-serif" font-size="9.5" font-weight="bold" fill="#000000" text-anchor="middle" letter-spacing="0.5">RBC CURVE</text>'
            . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Parse CBC report HTML and update the 3 histograms dynamically
     */
    public static function processReportHtml($html)
    {
        if (empty($html)) return $html;

        // Check if report contains CBC keywords
        if (stripos($html, 'WBC CURVE') === false && stripos($html, 'HAEMATOLOGY') === false && stripos($html, 'TOTAL WBC') === false) {
            return $html;
        }

        // Helper to extract numeric value for a given parameter name
        $extractVal = function($paramName, $default = null) use ($html) {
            // Regex to find parameter row e.g. <td>TOTAL WBC COUNT</td><td...>10,850</td>
            $quoted = preg_quote($paramName, '/');
            // Try table row pattern: <td>Param</td>\s*<td[^>]*>(value)</td>
            if (preg_match('/' . $quoted . '<\/td>\s*<td[^>]*>([0-9.,\s]+)<\/td>/i', $html, $m)) {
                return trim($m[1]);
            }
            // Alternative: inside span or div
            if (preg_match('/' . $quoted . '[\s\S]{1,60}?<td[^>]*>([0-9.,\s]+)<\/td>/i', $html, $m)) {
                return trim($m[1]);
            }
            return $default;
        };

        $total_wbc  = $extractVal('TOTAL WBC COUNT', '10850');
        $neut_pct   = $extractVal('Neutrophils', '52');
        $lymph_pct  = $extractVal('Lymphocytes', '41');
        $mono_pct   = $extractVal('Monocytes', '4');
        $eos_pct    = $extractVal('Eosinophils', '3');
        $baso_pct   = $extractVal('Basophil', '0');

        $plt_count  = $extractVal('TOTAL PLATELET COUNT', '314000');
        $mpv        = $extractVal('MPV', '7.3');
        $pdw        = $extractVal('PDW', '13.7');

        $rbc_count  = $extractVal('RBC COUNT', '6.19');
        $mcv        = $extractVal('MCV', '72.9');
        $rdw_cv     = $extractVal('RDW-CV', '15.2');
        $rdw_sd     = $extractVal('RDW-SD', '42.4');

        $wbc_uri = self::generateWbcCurve($total_wbc, $neut_pct, $lymph_pct, $mono_pct, $eos_pct, $baso_pct);
        $plt_uri = self::generatePltCurve($plt_count, $mpv, $pdw);
        $rbc_uri = self::generateRbcCurve($rbc_count, $mcv, $rdw_cv, $rdw_sd);

        // Replace WBC CURVE img src
        $html = preg_replace(
            '/<img[^>]+alt=["\']WBC CURVE["\'][^>]*>/i',
            '<img src="' . $wbc_uri . '" alt="WBC CURVE" width="120" height="85" style="display:block; margin:auto; border:none;" />',
            $html
        );

        // Replace PLT CURVE img src
        $html = preg_replace(
            '/<img[^>]+alt=["\']PLT CURVE["\'][^>]*>/i',
            '<img src="' . $plt_uri . '" alt="PLT CURVE" width="120" height="85" style="display:block; margin:auto; border:none;" />',
            $html
        );

        // Replace RBC CURVE img src
        $html = preg_replace(
            '/<img[^>]+alt=["\']RBC CURVE["\'][^>]*>/i',
            '<img src="' . $rbc_uri . '" alt="RBC CURVE" width="120" height="85" style="display:block; margin:auto; border:none;" />',
            $html
        );

        return $html;
    }
}

// Test with Lab 8 report
$sample_html = file_get_contents('scratch/lab8_report.html');
$processed = CbcCurveGenerator::processReportHtml($sample_html);
file_put_contents('scratch/lab8_processed.html', $processed);
echo "Processed successfully. Length: " . strlen($processed) . "\n";
