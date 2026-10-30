<?php
class CbcCurveGeneratorRefined
{
    public static function generateWbcCurve($total_wbc, $neut_pct, $lymph_pct, $mono_pct = 4, $eos_pct = 3, $baso_pct = 0)
    {
        $total_wbc = !empty($total_wbc) ? (float)str_replace([',', ' '], '', $total_wbc) : 7500;
        $neut_pct  = !empty($neut_pct)  ? (float)$neut_pct : 55;
        $lymph_pct = !empty($lymph_pct) ? (float)$lymph_pct : 35;
        $mid_pct   = ((float)$mono_pct + (float)$eos_pct + (float)$baso_pct);
        if ($mid_pct <= 0) $mid_pct = 10;

        $wbc_scale = max(0.75, min(1.30, $total_wbc / 8500));

        $h_lymph = max(14, min(58, ($lymph_pct / 65) * 48 * $wbc_scale));
        $h_neut  = max(16, min(62, ($neut_pct / 70) * 52 * $wbc_scale));
        $h_mid   = max(6,  min(28, ($mid_pct / 18) * 22 * $wbc_scale));

        $baseline = 72;
        $y1 = round($baseline - $h_lymph, 1);
        $y2 = round($baseline - $h_neut, 1);
        $yv = round($baseline - $h_mid, 1);

        $x1 = 34.0;
        $xv = 52.0;
        $x2 = 76.0;

        $path_d = sprintf(
            "M 12 72 L 18 72 C 24 72, %0.1f %0.1f, %0.1f %0.1f C %0.1f %0.1f, %0.1f %0.1f, %0.1f %0.1f C %0.1f %0.1f, %0.1f %0.1f, %0.1f %0.1f C %0.1f %0.1f, %0.1f 66, 112 72 L 118 72 L 12 72 Z",
            $x1 - 5.0, $y1, $x1, $y1,
            $x1 + 6.0, $y1, $xv - 6.0, $yv, $xv, $yv,
            $xv + 6.0, $yv, $x2 - 6.0, $y2, $x2, $y2,
            $x2 + 7.0, $y2, $x2 + 20.0
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

    public static function generatePltCurve($plt_count, $mpv, $pdw = 13.5)
    {
        $plt_count = !empty($plt_count) ? (float)str_replace([',', ' '], '', $plt_count) : 280000;
        $mpv       = !empty($mpv)       ? (float)$mpv : 8.5;
        $pdw       = !empty($pdw)       ? (float)$pdw : 13.5;

        $peak_x = max(18.0, min(36.0, 20.0 + (($mpv - 6.5) / 5.0) * 14.0));
        $plt_scale = max(0.55, min(1.35, $plt_count / 300000));
        $h_plt = max(16.0, min(63.0, 52.0 * $plt_scale));
        $peak_y = round(72.0 - $h_plt, 1);

        $tail_x = max(65.0, min(112.0, 75.0 + (($pdw - 12.0) / 6.0) * 18.0));

        $path_d = sprintf(
            "M 12 72 C 14 72, %0.1f %0.1f, %0.1f %0.1f C %0.1f %0.1f, %0.1f 56, %0.1f 69 C %0.1f 71, 108 72, 118 72 L 12 72 Z",
            $peak_x - 3.5, $peak_y + 10.0, $peak_x, $peak_y,
            $peak_x + 5.0, $peak_y, $peak_x + 18.0, $tail_x,
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

    public static function generateRbcCurve($rbc_count, $mcv, $rdw_cv = 13.5, $rdw_sd = 42.0)
    {
        $rbc_count = !empty($rbc_count) ? (float)$rbc_count : 5.0;
        $mcv       = !empty($mcv)       ? (float)$mcv : 85.0;
        $rdw_cv    = !empty($rdw_cv)    ? (float)$rdw_cv : 13.5;

        // Shift center based on MCV
        $center_x = max(42.0, min(86.0, 64.0 + ($mcv - 85.0) * 1.15));
        $half_width = max(13.0, min(26.0, 17.0 + ($rdw_cv - 13.0) * 1.5));

        $rbc_scale = max(0.65, min(1.30, $rbc_count / 5.2));
        $h_rbc = max(24.0, min(62.0, 54.0 * $rbc_scale));
        $peak_y = round(72.0 - $h_rbc, 1);

        $left_base  = max(10.0, round($center_x - $half_width * 1.6, 1));
        $right_base = min(120.0, round($center_x + $half_width * 1.6, 1));

        $path_d = sprintf(
            "M 10 72 L %0.1f 72 C %0.1f 72, %0.1f %0.1f, %0.1f %0.1f C %0.1f %0.1f, %0.1f 72, %0.1f 72 L 120 72 L 10 72 Z",
            $left_base,
            $left_base + 5.0, $center_x - $half_width * 0.5, $peak_y, $center_x, $peak_y,
            $center_x + $half_width * 0.5, $peak_y, $right_base - 5.0, $right_base
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
}

// Generate new curves and inspect
$svg_wbc = base64_decode(substr(CbcCurveGeneratorRefined::generateWbcCurve(10850, 52, 41, 4, 3, 0), 26));
$svg_plt = base64_decode(substr(CbcCurveGeneratorRefined::generatePltCurve(314000, 7.3, 13.7), 26));
$svg_rbc = base64_decode(substr(CbcCurveGeneratorRefined::generateRbcCurve(6.19, 72.9, 15.2), 26));

echo "Smooth WBC SVG:\n" . $svg_wbc . "\n\n";
echo "Smooth PLT SVG:\n" . $svg_plt . "\n\n";
echo "Smooth RBC SVG:\n" . $svg_rbc . "\n\n";
