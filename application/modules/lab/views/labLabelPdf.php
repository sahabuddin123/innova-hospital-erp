<?php
/**
 * PDF Label export (mPDF compatible)
 * Formats: 50mm x 30mm for Specimen Barcodes OR 100mm x 60mm for Radiology Envelopes
 */

// Format Patient Age
$patient_age_compact = '';
$patient_age_full = '';
if (!empty($patient->age)) {
    $age_parts = explode('-', $patient->age);
    if (count($age_parts) === 3) {
        $y = (int)$age_parts[0];
        $m = (int)$age_parts[1];
        $d = (int)$age_parts[2];
        if ($y > 0) {
            $patient_age_compact = $y . 'Y' . ($m > 0 ? " {$m}M" : '');
        } elseif ($m > 0) {
            $patient_age_compact = $m . 'M' . ($d > 0 ? " {$d}D" : '');
        } else {
            $patient_age_compact = $d . 'D';
        }
        $patient_age_full = "{$y} Y {$m} M {$d} D";
    } else {
        $patient_age_compact = $patient->age . (is_numeric($patient->age) ? 'Y' : '');
        $patient_age_full = $patient->age;
    }
}

$gender_display = !empty($patient->sex) ? ucfirst($patient->sex) : 'N/A';
$gender_short = !empty($patient->sex) ? strtoupper(substr($patient->sex, 0, 1)) : '-';

$ref_doctor_name = 'SELF';
if (!empty($doctor) && !empty($doctor->name)) {
    $ref_doctor_name = $doctor->name;
} elseif (!empty($lab->doctor_name)) {
    $ref_doctor_name = $lab->doctor_name;
}

$test_names_str = !empty($test_names) ? implode(', ', $test_names) : 'Laboratory Test';
$barcode_inv = '000000000' . $lab->invoice_id;
if (empty($specimen_type)) {
    $specimen_type = !empty($is_imaging) ? 'IMAGING' : 'BLOOD';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            margin: 0;
            padding: 0;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
    </style>
</head>
<body>
<?php if (empty($is_imaging)): ?>
    <!-- Specimen Barcode Label: 50mm x 30mm -->
    <div style="font-size: 6.5pt; line-height: 1.1;">
        <table style="border-bottom: 0.5px solid #000; padding-bottom: 1px;">
            <tr>
                <td style="font-weight: bold; font-size: 7pt; text-align: left;">LIFE CARE DIAGNOSTIC</td>
                <td style="text-align: right; font-size: 6pt;"><?php echo date('d/m/y', $lab->date); ?></td>
            </tr>
        </table>

        <table style="margin-top: 1px;">
            <tr>
                <td style="font-weight: bold; font-size: 8pt; text-align: left;"><?php echo strtoupper($patient->name); ?></td>
                <td style="text-align: right; font-weight: bold; font-size: 7.5pt;">PID: <?php echo $patient->id; ?></td>
            </tr>
        </table>

        <table>
            <tr>
                <td style="font-size: 6pt; text-align: left;"><?php echo $patient_age_compact; ?> / <?php echo $gender_short; ?></td>
                <td style="font-size: 6pt; text-align: center;">Inv: #<?php echo $lab->invoice_id; ?></td>
                <td style="font-size: 5.5pt; font-weight: bold; text-align: right;">[<?php echo $specimen_type; ?>]</td>
            </tr>
        </table>

        <div style="text-align: center; margin: 1px 0;">
            <img src="<?php echo site_url('lab/barcode'); ?>?text=<?php echo $barcode_inv; ?>&size=20&sizefactor=1&print=false" style="height: 7mm; max-width: 44mm; margin: 0 auto;" />
            <div style="font-family: monospace; font-size: 6pt; font-weight: bold; letter-spacing: 1px;">*<?php echo $barcode_inv; ?>*</div>
        </div>

        <div style="font-size: 5.5pt; font-weight: bold; border-top: 0.5px solid #000; padding-top: 1px; white-space: nowrap; overflow: hidden;">
            T: <?php echo $test_names_str; ?>
        </div>
    </div>
<?php else: ?>
    <!-- Radiology Film Envelope Label: 100mm x 60mm -->
    <div style="font-size: 9pt; line-height: 1.4; padding: 2mm;">
        <table style="border-bottom: 1px solid #000; padding-bottom: 3px;">
            <tr>
                <td style="font-size: 11pt; font-weight: bold;">LIFE CARE DIAGNOSTIC CENTER</td>
                <td style="text-align: right; font-weight: bold;">Date: <?php echo date('d/m/Y', $lab->date); ?></td>
            </tr>
        </table>

        <table style="margin-top: 3px;">
            <tr>
                <td style="width: 50%;"><strong>PID:</strong> <?php echo $patient->id; ?></td>
                <td style="width: 50%;"><strong>Invoice ID:</strong> <?php echo $lab->invoice_id; ?></td>
            </tr>
            <tr>
                <td colspan="2"><strong>Patient:</strong> <?php echo strtoupper($patient->name); ?></td>
            </tr>
            <tr>
                <td><strong>Age:</strong> <?php echo $patient_age_full; ?></td>
                <td><strong>Gender:</strong> <?php echo $gender_display; ?></td>
            </tr>
            <tr>
                <td colspan="2"><strong>Ref By:</strong> <?php echo $ref_doctor_name; ?></td>
            </tr>
            <tr>
                <td colspan="2"><strong>Test:</strong> <?php echo $test_names_str; ?></td>
            </tr>
            <tr>
                <td>Film: [ _______ ]</td>
                <td>Paper Print: [ _______ ]</td>
            </tr>
        </table>

        <div style="text-align: center; margin-top: 5px; border-top: 1px dashed #000; padding-top: 3px;">
            <img src="<?php echo site_url('lab/barcode'); ?>?text=<?php echo $barcode_inv; ?>&size=24&sizefactor=1&print=false" style="height: 9mm; margin: 0 auto;" />
            <div style="font-family: monospace; font-size: 7.5pt; font-weight: bold;">*<?php echo $barcode_inv; ?>*</div>
        </div>
    </div>
<?php endif; ?>
</body>
</html>