<link href="common/extranal/css/lab/add_template.css" rel="stylesheet">

<div class="content-wrapper bg-light">
    <!-- Content Header -->
    <section class="content-header py-3 bg-white shadow-sm mb-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="h3 font-weight-bold text-dark mb-0">
                        <i class="fas fa-file-medical-alt text-primary mr-2"></i>
                        <?php echo !empty($template->id) ? lang('edit_lab_report') . ' ' . lang('template') : lang('add_lab_report') . ' ' . lang('template'); ?>
                    </h1>
                </div>
                <div class="col-sm-6 text-right">
                    <ol class="breadcrumb bg-transparent p-0 m-0 justify-content-end">
                        <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                        <li class="breadcrumb-item"><a href="lab/template"><?php echo lang('template'); ?></a></li>
                        <li class="breadcrumb-item active"><?php echo !empty($template->id) ? lang('edit') : lang('add'); ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form role="form" id="templateForm" action="lab/addTemplate" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo !empty($template->id) ? $template->id : ''; ?>">

                <div class="row">
                    <!-- Left Column: Form & Editor -->
                    <div class="col-lg-9 col-md-12">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h5 class="card-title font-weight-bold text-primary mb-0">
                                    <i class="fas fa-sliders-h mr-2"></i>Template Configuration
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    <!-- Template Name -->
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted text-uppercase small">
                                                <?php echo lang('template'); ?> <?php echo lang('name'); ?> <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control form-control-lg shadow-sm" name="name" 
                                                   value="<?php echo !empty($template->name) ? htmlspecialchars($template->name) : ''; ?>" 
                                                   placeholder="e.g. Complete Blood Count (CBC) Report" required>
                                        </div>
                                    </div>

                                    <!-- Linked Diagnostic Test -->
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted text-uppercase small">
                                                Linked Diagnostic Test <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control form-control-lg shadow-sm js-example-basic-single" name="category_id" required>
                                                <option value="">-- Select Diagnostic Test --</option>
                                                <?php if (!empty($payment_categories)) { ?>
                                                    <?php foreach ($payment_categories as $pcat) { 
                                                        $selected = (!empty($template->category_id) && $template->category_id == $pcat->id) ? 'selected="selected"' : '';
                                                    ?>
                                                        <option value="<?php echo $pcat->id; ?>" <?php echo $selected; ?>>
                                                            <?php echo htmlspecialchars($pcat->category); ?> <?php echo !empty($pcat->code) ? '(' . htmlspecialchars($pcat->code) . ')' : ''; ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic Placeholder Insertion Toolbar -->
                                <div class="card bg-light border-0 mb-3 shadow-none">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="font-weight-bold text-dark small text-uppercase">
                                                <i class="fas fa-magic text-warning mr-1"></i> Quick Insert Dynamic Placeholders (Click to Insert)
                                            </span>
                                            <span class="text-muted small">Auto-populated on report creation</span>
                                        </div>
                                        <div class="d-flex flex-wrap" style="gap: 6px;">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertPlaceholder('{{patient_name}}')">+ Patient Name</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertPlaceholder('{{patient_id}}')">+ Patient ID</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertPlaceholder('{{patient_age}}')">+ Age</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertPlaceholder('{{patient_gender}}')">+ Gender</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertPlaceholder('{{patient_phone}}')">+ Phone</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertPlaceholder('{{doctor_name}}')">+ Doctor Name</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertPlaceholder('{{referring_doctor}}')">+ Doctor w/ Degrees</button>
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="insertPlaceholder('{{test_name}}')">+ Test Name</button>
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="insertPlaceholder('{{report_id}}')">+ Report #</button>
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="insertPlaceholder('{{report_date}}')">+ Report Date</button>
                                            <button type="button" class="btn btn-sm btn-outline-dark" onclick="insertPlaceholder('{{hospital_name}}')">+ Hospital Name</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Rich Text Editor -->
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-muted text-uppercase small d-flex justify-content-between">
                                        <span>Report Template Body &ast;</span>
                                        <span class="text-muted font-weight-normal">Formatted HTML with tables, styling and placeholders</span>
                                    </label>
                                    <textarea class="form-control" id="editor" name="template" rows="18" required><?php
                                        if (!empty($setval)) {
                                            echo set_value('template');
                                        } elseif (!empty($template->template)) {
                                            echo $template->template;
                                        }
                                    ?></textarea>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                    <div>
                                        <a href="lab/template" class="btn btn-light border px-4 py-2 mr-2">
                                            <i class="fas fa-times mr-1"></i> <?php echo lang('cancel'); ?>
                                        </a>
                                        <button type="button" class="btn btn-secondary px-4 py-2" onclick="previewTemplate()">
                                            <i class="fas fa-eye mr-1"></i> Preview Template
                                        </button>
                                    </div>
                                    <div>
                                        <button type="submit" name="submit" class="btn btn-primary btn-lg px-5 py-2 font-weight-bold shadow-sm">
                                            <i class="fas fa-save mr-2"></i> <?php echo lang('submit'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Quick Guidance -->
                    <div class="col-lg-3 col-md-12">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="card-title font-weight-bold text-dark mb-0">
                                    <i class="fas fa-info-circle text-info mr-2"></i>Clinical Report Standards
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <p class="text-muted small">
                                    Every diagnostic test template must follow professional clinical presentation standards:
                                </p>
                                <ul class="small text-muted pl-3 mb-3">
                                    <li class="mb-2"><strong>Quantitative Tests:</strong> Use structured 4-column tables (Test Parameter, Result, Reference Interval, Unit).</li>
                                    <li class="mb-2"><strong>Qualitative Tests:</strong> Clearly state interpretation, method (e.g. Immunochromatography, ELISA), and cut-off indices.</li>
                                    <li class="mb-2"><strong>Imaging / USG:</strong> Include sections for Indication, Findings, and Impression.</li>
                                    <li class="mb-2"><strong>Microbiology:</strong> Include specimen, findings, colony count, and antibiotic susceptibility table.</li>
                                </ul>
                                <div class="alert alert-info py-2 px-3 small mb-0">
                                    <i class="fas fa-shield-alt mr-1"></i> <strong>Safe Snapshot:</strong> Editing this template updates future reports. Previously finalized patient reports remain safely unchanged.
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="card-title font-weight-bold text-dark mb-0">
                                    <i class="fas fa-code text-secondary mr-2"></i>Supported Placeholders
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <table class="table table-sm table-borderless small mb-0">
                                    <tbody>
                                        <tr><td class="font-weight-bold text-primary"><code>{{patient_name}}</code></td><td class="text-muted">Patient Full Name</td></tr>
                                        <tr><td class="font-weight-bold text-primary"><code>{{patient_id}}</code></td><td class="text-muted">Hospital Patient ID</td></tr>
                                        <tr><td class="font-weight-bold text-primary"><code>{{patient_age}}</code></td><td class="text-muted">Calculated Age</td></tr>
                                        <tr><td class="font-weight-bold text-primary"><code>{{patient_gender}}</code></td><td class="text-muted">Gender / Sex</td></tr>
                                        <tr><td class="font-weight-bold text-primary"><code>{{doctor_name}}</code></td><td class="text-muted">Referring Doctor</td></tr>
                                        <tr><td class="font-weight-bold text-primary"><code>{{test_name}}</code></td><td class="text-muted">Investigation Name</td></tr>
                                        <tr><td class="font-weight-bold text-primary"><code>{{report_date}}</code></td><td class="text-muted">Report Date</td></tr>
                                        <tr><td class="font-weight-bold text-primary"><code>{{hospital_name}}</code></td><td class="text-muted">Lifecare Diagnostic</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<!-- Template Preview Modal -->
<div class="modal fade" id="templatePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-gradient-primary text-white py-3">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-eye mr-2"></i>Live Template Preview
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light" style="min-height: 400px;">
                <div class="bg-white p-4 shadow-sm rounded border" id="previewModalBody">
                    <!-- Dynamic preview injected here -->
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script src="common/assets/tinymce/tinymce.min.js"></script>
<script src="common/extranal/js/lab/add_template_view.js"></script>
<script>
function insertPlaceholder(code) {
    if (typeof tinymce !== 'undefined' && tinymce.get('editor')) {
        tinymce.get('editor').insertContent(code);
    } else {
        var el = document.getElementById('editor');
        if (el) {
            el.value += code;
        }
    }
}

function previewTemplate() {
    var content = '';
    if (typeof tinymce !== 'undefined' && tinymce.get('editor')) {
        content = tinymce.get('editor').getContent();
    } else {
        content = document.getElementById('editor').value;
    }
    document.getElementById('previewModalBody').innerHTML = content;
    $('#templatePreviewModal').modal('show');
}
</script>