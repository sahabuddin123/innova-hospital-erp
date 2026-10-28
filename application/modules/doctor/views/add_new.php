<!--sidebar end-->
<!--main content start-->
<link href="common/extranal/css/doctor/add_new.css" rel="stylesheet">

<div class="content-wrapper bg-light">
    <section class="content-header py-4 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-calendar-plus text-primary mr-3"></i>
                        <?php
                        if (!empty($doctor->id))
                            echo lang('edit_doctor');
                        else
                            echo lang('add_doctor');
                        ?>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb float-sm-right bg-transparent">
                            <li class="breadcrumb-item"><a href="home" class="text-primary"><?php echo lang('home') ?></a></li>
                            <li class="breadcrumb-item active font-weight-bold">
                                <?php
                                if (!empty($doctor->id))
                                    echo lang('edit_doctor');
                                else
                                    echo lang('add_doctor');
                                ?>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-gradient-primary py-4">
                            <h2 class="card-title mb-0 text-white display-6 font-weight-800"><?php echo lang('doctor_registration_form'); ?></h2>
                        </div>
                        <div class="card-body bg-light p-4">
                            <?php echo validation_errors(); ?>
                            <?php echo $this->session->flashdata('feedback'); ?>

                            <form role="form" action="doctor/addNew" method="post" enctype="multipart/form-data">

                                <!-- Personal Information -->
                                <div class="row mb-5">
                                    <div class="col-12 mb-4">
                                        <h3 class="border-bottom border-primary pb-3 text-uppercase font-weight-900">
                                            <i class="fas fa-user-circle mr-3 text-primary"></i><?php echo lang('personal_details'); ?>
                                        </h3>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg shadow-sm" name="name" value='<?php
                                                                                                                                    if (!empty($setval)) {
                                                                                                                                        echo set_value('name');
                                                                                                                                    }
                                                                                                                                    if (!empty($doctor->name)) {
                                                                                                                                        echo $doctor->name;
                                                                                                                                    }
                                                                                                                                    ?>' required="">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('email'); ?> <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control form-control-lg shadow-sm" name="email" value='<?php
                                                                                                                                    if (!empty($setval)) {
                                                                                                                                        echo set_value('email');
                                                                                                                                    }
                                                                                                                                    if (!empty($doctor->email)) {
                                                                                                                                        echo $doctor->email;
                                                                                                                                    }
                                                                                                                                    ?>' required="">
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
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('phone'); ?> <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg shadow-sm" name="phone" value='<?php
                                                                                                                                    if (!empty($setval)) {
                                                                                                                                        echo set_value('phone');
                                                                                                                                    }
                                                                                                                                    if (!empty($doctor->phone)) {
                                                                                                                                        echo $doctor->phone;
                                                                                                                                    }
                                                                                                                                    ?>' required>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('address'); ?> <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg shadow-sm" name="address" value='<?php
                                                                                                                                    if (!empty($setval)) {
                                                                                                                                        echo set_value('address');
                                                                                                                                    }
                                                                                                                                    if (!empty($doctor->address)) {
                                                                                                                                        echo $doctor->address;
                                                                                                                                    }
                                                                                                                                    ?>' required>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('department'); ?></label>
                                            <select class="form-control form-control-lg shadow-sm" name="department">
                                                <?php foreach ($departments as $department) { ?>
                                                    <option value="<?php echo $department->id; ?>" <?php
                                                                                                    if (!empty($setval)) {
                                                                                                        if ($department->id == set_value('department')) {
                                                                                                            echo 'selected';
                                                                                                        }
                                                                                                    }
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
                                            <input type="text" class="form-control form-control-lg shadow-sm" name="degrees" placeholder="e.g. MBBS (RU), MS (Ortho)" value='<?php
                                                if (!empty($setval)) {
                                                    echo set_value('degrees');
                                                }
                                                if (!empty($doctor->degrees)) {
                                                    echo html_escape($doctor->degrees);
                                                }
                                            ?>'>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('profile'); ?> <span class="text-danger">*</span></label>
                                            <textarea class="form-control ckeditor" id="editor1" name="profile" rows="6"><?php
                                                                                                                            if (!empty($setval)) {
                                                                                                                                echo set_value('profile');
                                                                                                                            }
                                                                                                                            if (!empty($doctor->profile)) {
                                                                                                                                echo $doctor->profile;
                                                                                                                            }
                                                                                                                            ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Images -->
                                <div class="row mb-5">
                                    <div class="col-12 mb-4">
                                        <h3 class="border-bottom border-info pb-3 text-uppercase font-weight-900">
                                            <i class="fas fa-images mr-3 text-info"></i>Images
                                        </h3>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('image'); ?></label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="img_url" id="customFile1">
                                                <label class="custom-file-label" for="customFile1">Choose profile image</label>
                                            </div>
                                            <?php if (!empty($doctor->img_url)) { ?>
                                                <div class="mt-3">
                                                    <img src="<?php echo $doctor->img_url; ?>" class="img-thumbnail" height="100px">
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('signature'); ?> <span class="text-danger">*</span></label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="signature" id="customFile2">
                                                <label class="custom-file-label" for="customFile2"><?php echo lang('choose_signature_image'); ?></label>
                                            </div>
                                            <?php if (!empty($doctor->signature)) { ?>
                                                <div class="mt-3">
                                                    <img src="<?php echo $doctor->signature; ?>" class="img-thumbnail" height="100px">
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="id" value='<?php
                                                                        if (!empty($doctor->id)) {
                                                                            echo $doctor->id;
                                                                        }
                                                                        ?>'>

                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block shadow-lg py-3">
                                            <i class="fas fa-save mr-3"></i><?php echo lang('submit'); ?>
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!--main content end-->
<!--footer start-->
<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = "<?php echo $this->language; ?>";
</script>
<script src="common/assets/tinymce/tinymce.min.js"></script>
<script src="common/extranal/js/doctor/doctor.js"></script>