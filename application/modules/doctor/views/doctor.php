<!--main content start-->
<link href="common/extranal/css/doctor/doctor.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo base_url(); ?>application/assets/css/global-modal-styles.css">

<div class="content-wrapper bg-light">
    <!-- Section Header -->
    <section class="content-header py-4 bg-white shadow-sm border-bottom">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="h3 font-weight-bold mb-1 text-dark">
                        <i class="fas fa-user-md text-primary mr-2"></i> Doctor Management
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent p-0 mb-0 small">
                            <li class="breadcrumb-item"><a href="home" class="text-muted"><i class="fas fa-home mr-1"></i> Home</a></li>
                            <li class="breadcrumb-item active font-weight-bold text-primary">Doctor Management</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-sm-6 text-right">
                    <a data-toggle="modal" href="#myModal" class="btn btn-primary btn-sm px-4 py-2 shadow-sm font-weight-bold">
                        <i class="fa fa-plus mr-1"></i> Add New Doctor
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Content Body -->
    <section class="content py-4">
        <div class="container-fluid">

            <!-- Summary KPI Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card stat-card shadow-sm border-left border-primary" style="border-left-width: 4px !important;">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-uppercase small font-weight-bold text-muted mb-1">Total Doctors</div>
                                    <div class="h4 font-weight-bold text-dark mb-0"><?php echo isset($total_doctors) ? $total_doctors : count($doctors); ?></div>
                                </div>
                                <div class="stat-icon-wrapper bg-primary text-white">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card stat-card shadow-sm border-left border-success" style="border-left-width: 4px !important;">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-uppercase small font-weight-bold text-muted mb-1">Active Doctors</div>
                                    <div class="h4 font-weight-bold text-success mb-0"><?php echo isset($active_doctors) ? $active_doctors : count($doctors); ?></div>
                                </div>
                                <div class="stat-icon-wrapper bg-success text-white">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card stat-card shadow-sm border-left border-info" style="border-left-width: 4px !important;">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-uppercase small font-weight-bold text-muted mb-1">Departments</div>
                                    <div class="h4 font-weight-bold text-info mb-0"><?php echo isset($total_departments) ? $total_departments : count($departments); ?></div>
                                </div>
                                <div class="stat-icon-wrapper bg-info text-white">
                                    <i class="fas fa-hospital"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card stat-card shadow-sm border-left border-warning" style="border-left-width: 4px !important;">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-uppercase small font-weight-bold text-muted mb-1">Standard Consultation Fee</div>
                                    <div class="h4 font-weight-bold text-dark mb-0">BDT 700</div>
                                </div>
                                <div class="stat-icon-wrapper bg-warning text-white">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Toolbar -->
            <div class="filter-bar mb-4 shadow-sm">
                <div class="row align-items-center">
                    <div class="col-md-4 mb-2 mb-md-0">
                        <label class="small font-weight-bold text-muted mb-1"><i class="fas fa-filter text-primary mr-1"></i> Filter by Department</label>
                        <select class="form-control form-control-sm select2" id="department_filter">
                            <option value="all">All Departments</option>
                            <?php foreach ($departments as $department) { ?>
                                <option value="<?php echo $department->id; ?>"><?php echo html_escape($department->name); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label class="small font-weight-bold text-muted mb-1"><i class="fas fa-toggle-on text-primary mr-1"></i> Filter by Status</label>
                        <select class="form-control form-control-sm" id="status_filter">
                            <option value="all">All Status</option>
                            <option value="active" selected>Active Only</option>
                            <option value="inactive">Inactive Only</option>
                        </select>
                    </div>
                    <div class="col-md-5 text-md-right pt-md-4">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" id="reset_filter_btn">
                            <i class="fas fa-undo mr-1"></i> Reset Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Doctor List Table -->
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover datatables" id="editable-sample" width="100%">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">ID</th>
                                    <th>Doctor</th>
                                    <th>Qualifications & Specialization</th>
                                    <th>Department</th>
                                    <th>Contact</th>
                                    <th class="text-center" style="width: 110px;">Consultation Fee</th>
                                    <th>Schedule</th>
                                    <th class="text-center" style="width: 90px;">Status</th>
                                    <th class="no-print text-center" style="width: 140px;">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Add Doctor Modal -->
<div class="modal fade modal-enhanced" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-user-plus mr-2"></i> Add New Doctor Profile
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form action="doctor/addNew" method="post" enctype="multipart/form-data">
                    <!-- Professional Credentials Section -->
                    <div class="section-divider">
                        <i class="fas fa-id-card-alt mr-2"></i> Professional Credentials & Identification
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Full Name in English <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" placeholder="e.g. Dr. Md. Mahbubur Rahman" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Professional Designation</label>
                                <input type="text" class="form-control" name="designation" placeholder="e.g. Assistant Professor / Consultant">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Department <span class="text-danger">*</span></label>
                                <select class="form-control" name="department" required>
                                    <?php foreach ($departments as $department) { ?>
                                        <option value="<?php echo $department->id; ?>"><?php echo html_escape($department->name); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Medical Degrees & Qualifications</label>
                                <input type="text" class="form-control" name="degrees" placeholder="e.g. MBBS, MS (Orthopaedic Surgery)">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Clinical Specialization</label>
                                <input type="text" class="form-control" name="specialization" placeholder="e.g. Orthopaedics, Trauma, Spine Surgery">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Professional Experience</label>
                                <input type="text" class="form-control" name="experience" placeholder="e.g. 10+ Years Experience">
                            </div>
                        </div>
                    </div>

                    <!-- Consultation Fees & Availability -->
                    <div class="section-divider mt-2">
                        <i class="fas fa-money-check-alt mr-2"></i> Fees & Status
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Standard Consultation Fee (BDT) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text font-weight-bold">BDT</span></div>
                                    <input type="number" class="form-control font-weight-bold text-success" name="consultation_fee" value="700" readonly required>
                                </div>
                                <small class="text-muted">Standard mandatory fee: BDT 700</small>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Follow-up Fee (BDT)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">BDT</span></div>
                                    <input type="number" class="form-control" name="followup_fee" value="400">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Status</label>
                                <select class="form-control" name="active">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Contact & Chamber Section -->
                    <div class="section-divider mt-2">
                        <i class="fas fa-address-book mr-2"></i> Contact & Chamber Details
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Primary Mobile Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="phone" placeholder="e.g. 017XXXXXXXX" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Secondary Mobile / Chamber Phone</label>
                                <input type="text" class="form-control" name="phone_secondary" placeholder="e.g. 018XXXXXXXX">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" placeholder="e.g. doctor@lifecare.com" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Account Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password" placeholder="Create login password" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Chamber / Hospital Information</label>
                                <input type="text" class="form-control" name="chamber" placeholder="e.g. Lifecare Diagnostic Center, Room 204">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Address</label>
                                <input type="text" class="form-control" name="address" placeholder="e.g. Pabna, Bangladesh">
                            </div>
                        </div>
                    </div>

                    <!-- Bio & Media Section -->
                    <div class="section-divider mt-2">
                        <i class="fas fa-file-medical mr-2"></i> Professional Summary & Media
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Doctor Biography / Professional Summary</label>
                                <textarea class="form-control" id="editor1" name="profile" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Profile Headshot Photo (JPG/PNG, Max 2MB)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="img_url" accept="image/*">
                                    <label class="custom-file-label">Choose profile image</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Doctor Signature (JPG/PNG, Max 2MB)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="signature" accept="image/*">
                                    <label class="custom-file-label">Choose signature image</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right pt-3 border-top">
                        <button type="button" class="btn btn-secondary px-4 mr-2" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-2"></i> Save Doctor Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Doctor Modal -->
<div class="modal fade modal-enhanced" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-user-edit mr-2"></i> Update Doctor Profile
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form id="editDoctorForm" action="doctor/addNew" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="id_value">

                    <!-- Credentials Section -->
                    <div class="section-divider">
                        <i class="fas fa-id-card-alt mr-2"></i> Professional Credentials & Identification
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Full Name in English <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Professional Designation</label>
                                <input type="text" class="form-control" name="designation">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Department <span class="text-danger">*</span></label>
                                <select class="form-control department" name="department" required>
                                    <?php foreach ($departments as $department) { ?>
                                        <option value="<?php echo $department->id; ?>"><?php echo html_escape($department->name); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Medical Degrees & Qualifications</label>
                                <input type="text" class="form-control" name="degrees">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Clinical Specialization</label>
                                <input type="text" class="form-control" name="specialization">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Professional Experience</label>
                                <input type="text" class="form-control" name="experience">
                            </div>
                        </div>
                    </div>

                    <!-- Consultation Fees & Availability -->
                    <div class="section-divider mt-2">
                        <i class="fas fa-money-check-alt mr-2"></i> Fees & Status
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Standard Consultation Fee (BDT) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text font-weight-bold">BDT</span></div>
                                    <input type="number" class="form-control font-weight-bold text-success" name="consultation_fee" value="700" required>
                                </div>
                                <small class="text-muted">Standard mandatory fee: BDT 700</small>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Follow-up Fee (BDT)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">BDT</span></div>
                                    <input type="number" class="form-control" name="followup_fee" value="400">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Status</label>
                                <select class="form-control" name="active">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Contact & Chamber Section -->
                    <div class="section-divider mt-2">
                        <i class="fas fa-address-book mr-2"></i> Contact & Chamber Details
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Primary Mobile Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="phone" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Secondary Mobile / Chamber Phone</label>
                                <input type="text" class="form-control" name="phone_secondary">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Change Password (Leave blank to keep existing)</label>
                                <input type="password" class="form-control" name="password" placeholder="********">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Chamber / Hospital Information</label>
                                <input type="text" class="form-control" name="chamber">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Address</label>
                                <input type="text" class="form-control" name="address">
                            </div>
                        </div>
                    </div>

                    <!-- Bio & Media Section -->
                    <div class="section-divider mt-2">
                        <i class="fas fa-file-medical mr-2"></i> Professional Summary & Media
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Doctor Biography / Professional Summary</label>
                                <textarea class="form-control" id="editor3" name="profile" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Profile Headshot Photo</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="img_url" accept="image/*">
                                    <label class="custom-file-label">Choose new image</label>
                                </div>
                                <div class="mt-2 text-center">
                                    <img src="" id="img" height="90px" alt="Profile" class="img-thumbnail rounded shadow-sm" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="info-item-label">Doctor Signature</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="signature" accept="image/*">
                                    <label class="custom-file-label">Choose new signature</label>
                                </div>
                                <div class="mt-2 text-center signature_class">
                                    <img src="" id="signature" height="90px" alt="Signature" class="img-thumbnail rounded shadow-sm" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right pt-3 border-top">
                        <button type="button" class="btn btn-secondary px-4 mr-2" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-sm">
                            <i class="fas fa-check mr-2"></i> Update Doctor Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Info Profile Modal -->
<div class="modal fade modal-enhanced" id="infoModal" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-user-md mr-2"></i> Doctor Profile Information
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light">
                <!-- Profile Header Banner -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center mb-3 mb-md-0">
                                <img src="" id="img1" class="img-thumbnail rounded-circle shadow" style="width: 130px; height: 130px; object-fit: cover;" alt="Profile Picture" />
                            </div>
                            <div class="col-md-9">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h3 class="nameClass font-weight-bold text-dark mb-0"></h3>
                                    <div class="statusBadgeClass"></div>
                                </div>
                                <h6 class="designationClass text-primary font-weight-bold mb-2"></h6>
                                <div class="mb-3">
                                    <span class="badge badge-info p-2 departmentClass font-weight-bold mr-2" style="font-size: 12px;"></span>
                                    <span class="badge badge-success p-2 font-weight-bold" style="font-size: 12px;">
                                        <i class="fas fa-money-bill-wave mr-1"></i> Consultation Fee: <span class="consultFeeClass">BDT 700</span>
                                    </span>
                                </div>
                                <div class="flagNoticeClass"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Tabs / Cards -->
                <div class="row">
                    <!-- Left Column: Professional Details -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white font-weight-bold text-primary border-bottom">
                                <i class="fas fa-graduation-cap mr-2"></i> Medical Qualifications & Specialization
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="info-item-label">Degrees & Qualifications</div>
                                    <div class="info-item-value degreesClass text-dark"></div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-item-label">Clinical Specialization</div>
                                    <div class="info-item-value specializationClass text-dark"></div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-item-label">Professional Experience</div>
                                    <div class="info-item-value experienceClass text-dark"></div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-item-label">Follow-up Consultation Fee</div>
                                    <div class="info-item-value followupFeeClass text-dark"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Contact & Chamber -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white font-weight-bold text-primary border-bottom">
                                <i class="fas fa-address-card mr-2"></i> Contact & Chamber Details
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="info-item-label">Primary Mobile Phone</div>
                                    <div class="info-item-value phoneClass text-dark"></div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-item-label">Secondary Mobile / Chamber Phone</div>
                                    <div class="info-item-value phoneSecondaryClass text-dark"></div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-item-label">Email Address</div>
                                    <div class="info-item-value emailClass text-dark"></div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-item-label">Chamber / Hospital Location</div>
                                    <div class="info-item-value chamberClass text-dark"></div>
                                </div>
                                <div class="mb-0">
                                    <div class="info-item-label">Address</div>
                                    <div class="info-item-value addressClass text-dark"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule & Leave Card -->
                    <div class="col-md-12 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white font-weight-bold text-primary border-bottom">
                                <i class="fas fa-clock mr-2"></i> Weekly Appointment Schedule & Leave
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="info-item-label mb-2">Configured Consultation Hours</div>
                                        <div class="scheduleBreakdownClass bg-light p-3 rounded border"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-item-label mb-2">Active Holidays & Leave</div>
                                        <div class="holidayBreakdownClass bg-light p-3 rounded border"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Biography -->
                    <div class="col-md-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white font-weight-bold text-primary border-bottom">
                                <i class="fas fa-info-circle mr-2"></i> Professional Summary / Biography
                            </div>
                            <div class="card-body profileClass text-muted" style="line-height: 1.7;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = "<?php echo $this->language; ?>";
</script>
<script src="common/assets/tinymce/tinymce.min.js"></script>
<script src="common/extranal/js/doctor/doctor.js"></script>