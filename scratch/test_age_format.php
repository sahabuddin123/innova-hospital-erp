<?php
function format_age_compact($age_str) {
    if (empty($age_str)) return '';
    $parts = explode('-', $age_str);
    if (count($parts) === 3) {
        $y = (int)$parts[0];
        $m = (int)$parts[1];
        $d = (int)$parts[2];
        if ($y > 0) return $y . 'Y' . ($m > 0 ? " {$m}M" : '');
        if ($m > 0) return $m . 'M' . ($d > 0 ? " {$d}D" : '');
        if ($d > 0) return $d . 'D';
        return '0D';
    }
    return $age_str . (is_numeric($age_str) ? 'Y' : '');
}

echo "20-0-0 -> " . format_age_compact('20-0-0') . "\n";
echo "32-5-10 -> " . format_age_compact('32-5-10') . "\n";
echo "0-6-0 -> " . format_age_compact('0-6-0') . "\n";
echo "0-0-15 -> " . format_age_compact('0-0-15') . "\n";
echo "45 -> " . format_age_compact('45') . "\n";
