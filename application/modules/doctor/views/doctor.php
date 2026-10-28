<!--sidebar end-->
<!--main content start-->
<link href="common/extranal/css/doctor/doctor.css" rel="stylesheet">

<div class="content-wrapper bg-light">
    <section class="content-header py-4 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-user text-primary mr-3"></i>
                        <?php echo lang('doctor'); ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"> <?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item active"> <?php echo lang('doctor'); ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-sm-6 text-right">
                    <a data-toggle="modal" href="#myModal" class="btn btn-primary btn-sm px-4 py-3">
                        <i class="fa fa-plus"></i> <?php echo lang('add_new'); ?> <?php echo lang('doctor'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content py-5">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-lg border-0">
                        <div class="card-body bg-light p-4">
                            <table class="table table-bordered table-hover datatables" id="editable-sample" width="100%">
                                <thead>
                                    <tr>
                                        <th><?php echo lang('id'); ?></th>
                                        <th><?php echo lang('name'); ?></th>
                                        <th><?php echo lang('email'); ?></th>
                                        <th><?php echo lang('phone'); ?></th>
                                        <th><?php echo lang('department'); ?></th>
                                        <th><?php echo lang('profile'); ?></th>
                                        <th class="no-print"><?php echo lang('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Include Global Modal Styles -->
<link rel="stylesheet" href="<?php echo base_url(); ?>application/assets/css/global-modal-styles.css">

<!-- Add Doctor Modal-->
<div class="modal fade modal-enhanced" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-md mr-2"></i>
                    <?php echo lang('add_new_doctor'); ?>
                </h5>
                <a type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
</a>
            </div>
            <div class="modal-body">
                <form action="doctor/addNew" method="post" enctype="multipart/form-data">
                    <div class="row mb-5">
                        <div class="col-12 mb-4">
                            <h3 class="border-bottom border-primary pb-3 text-uppercase font-weight-900">
                                <i class="fas fa-user-circle mr-3 text-primary"></i><?php echo lang('add_new_doctor'); ?>
                            </h3>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="name" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('email'); ?> <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-lg shadow-sm" name="email" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('password'); ?> <span class="text-danger">*</span></label>
                                <input type="password" class="form-control form-control-lg shadow-sm" name="password" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('address'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="address" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('phone'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="phone" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('department'); ?></label>
                                <select class="form-control form-control-lg shadow-sm" name="department">
                                    <?php foreach ($departments as $department) { ?>
                                        <option value="<?php echo $department->id; ?>"> <?php echo $department->name; ?> </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted">Degrees / Qualifications (e.g. MBBS, FCPS, MS)</label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="degrees" placeholder="e.g. MBBS (RU), MS (Ortho)">
                            </div>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('doctor'); ?> <?php echo lang('description'); ?></label>
                                <textarea class="form-control shadow-sm" id="editor1" name="profile" rows="5"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('signature'); ?> <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="signature">
                                    <label class="custom-file-label shadow-sm"><?php echo lang('choose_signature_image'); ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('image'); ?></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="img_url">
                                    <label class="custom-file-label shadow-sm"><?php echo lang('choose_profile_picture'); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-lg btn-block shadow-lg py-3">
                                <i class="fas fa-user-plus mr-3"></i><?php echo lang('submit'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Doctor Modal-->
<div class="modal fade modal-enhanced" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit mr-2"></i>
                    <?php echo lang('edit_doctor'); ?>
                </h5>
                <a type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                                    </a>
            </div>
            <div class="modal-body">
                <form id="editDoctorForm" action="doctor/addNew" method="post" enctype="multipart/form-data">
                    <div class="row mb-5">
                        <div class="col-12 mb-4">
                            <h3 class="border-bottom border-primary pb-3 text-uppercase font-weight-900">
                                <i class="fas fa-user-circle mr-3 text-primary"></i><?php echo lang('update_doctor_details'); ?>
                            </h3>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="name" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('email'); ?> <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-lg shadow-sm" name="email" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('password'); ?></label>
                                <input type="password" class="form-control form-control-lg shadow-sm" name="password" placeholder="********">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('address'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="address" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('phone'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="phone" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('department'); ?></label>
                                <select class="form-control form-control-lg shadow-sm department" name="department">
                                    <?php foreach ($departments as $department) { ?>
                                        <option value="<?php echo $department->id; ?>" <?php
                                                                                        if (!empty($doctor->department)) {
                                                                                            if ($department->id == $doctor->department) {
                                                                                                echo 'selected';
                                                                                            }
                                                                                        }
                                                                                        ?>> <?php echo $department->name; ?> </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted">Degrees / Qualifications (e.g. MBBS, FCPS, MS)</label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="degrees" placeholder="e.g. MBBS (RU), MS (Ortho)">
                            </div>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('doctor'); ?> <?php echo lang('description'); ?></label>
                                <textarea class="form-control shadow-sm" id="editor3" name="profile" rows="5"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('signature'); ?> <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="signature">
                                    <label class="custom-file-label shadow-sm"><?php echo lang('choose_signature_image'); ?></label>
                                </div>
                                <div class="mt-2">
                                    <img src="" id="signature" height="100px" alt="" class="img-thumbnail" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('image'); ?></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="img_url">
                                    <label class="custom-file-label shadow-sm"><?php echo lang('choose_profile_picture'); ?></label>
                                </div>
                                <div class="mt-2">
                                    <img src="" id="img" height="100px" alt="" class="img-thumbnail" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="id" id="id_value">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-lg btn-block shadow-lg py-3">
                                <i class="fas fa-user-edit mr-3"></i><?php echo lang('submit'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Info Modal -->
<div class="modal fade modal-enhanced" id="infoModal" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-md mr-2"></i>
                    <?php echo lang('doctor'); ?> <?php echo lang('info'); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-md-12 text-center mb-4">
                        <img src="" id="img1" class="img-thumbnail" height="100px" alt="" />
                    </div>

                    <div class="col-md-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4 text-uppercase font-weight-bold text-muted"><?php echo lang('name'); ?></div>
                                    <div class="col-md-8 nameClass"></div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4 text-uppercase font-weight-bold text-muted"><?php echo lang('email'); ?></div>
                                    <div class="col-md-8 emailClass"></div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4 text-uppercase font-weight-bold text-muted"><?php echo lang('address'); ?></div>
                                    <div class="col-md-8 addressClass"></div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4 text-uppercase font-weight-bold text-muted"><?php echo lang('phone'); ?></div>
                                    <div class="col-md-8 phoneClass"></div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4 text-uppercase font-weight-bold text-muted"><?php echo lang('department'); ?></div>
                                    <div class="col-md-8 departmentClass"></div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4 text-uppercase font-weight-bold text-muted">Degrees</div>
                                    <div class="col-md-8 degreesClass"></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 text-uppercase font-weight-bold text-muted"><?php echo lang('profile'); ?></div>
                                    <div class="col-md-8 profileClass"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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