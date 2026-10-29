<style>
    .d-none {
        display: none;
    }

    .d-flex {
        display: flex;
    }

    .align-center {
        align-items: center
    }

    .d-flex label {
        width: 200px;
    }
</style>
<!--main content start-->
<link href="common/extranal/css/description.css" rel="stylesheet">
<div class="content-wrapper bg-gradient-light" style="min-height: 2726.9px;">
    <section class="content-header py-4 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-flask text-primary mr-3"></i>
                        <?php
                        if (!empty($lab->id))
                            echo lang('edit_lab_report');
                        else
                            echo lang('add_lab_report');
                        ?>
                        (<?php echo lang('test') ?> <?php echo lang('name') ?>:
                        <?php
                        if (!empty($lab->id)) {
                            $test_name = $this->db->get_where('payment_category', array('id' => $lab->category_id))->row();
                            if (isset($test_name->category)) {
                                echo $test_name->category;
                            }
                        }
                        ?>)
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item active">
                                <?php
                                if (!empty($lab->id))
                                    echo lang('edit_lab_report');
                                else
                                    echo lang('add_lab_report');
                                ?>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <i class="fa fa-info-circle"></i> If you click "Save and ready to deliver", it will appear on the delivery report section.
    </div>

    <section class="content py-5">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-lg border-0">
                        <div class="card-body bg-light p-4">
                            <form role="form" class="labForm" id="editLabForm" action="lab/addLab" method="post" enctype="multipart/form-data">
                                <div class="row mb-5">
                                    <div class="col-12 mb-4">
                                        <h3 class="border-bottom border-primary pb-3 text-uppercase font-weight-900">
                                            <i class="fas fa-flask mr-3 text-primary"></i><?php echo lang('lab_report'); ?>
                                        </h3>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('type'); ?></label>
                                            <select class="form-control form-control-lg shadow-sm js-example-basic-multiple type" id="type" name="type">
                                                <option value="all"><?php echo lang('all'); ?></option>
                                                <option value="<?php echo $this->ion_auth->get_user_id(); ?>">Only Mine</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('template'); ?></label>
                                            <select class="form-control form-control-lg shadow-sm js-example-basic-multiple template" id="template1" name="template">
                                                <option value=""><?php echo lang('select'); ?></option>
                                                <?php foreach ($templates as $template) { 
                                                    $isSelected = (!empty($selected_template_id) && $selected_template_id == $template->id) ? 'selected="selected"' : '';
                                                    $t_user = !empty($template->user) ? $this->db->get_where('users', array("id" => $template->user))->row() : null;
                                                    $t_username = !empty($t_user->username) ? " (" . $t_user->username . ")" : "";
                                                ?>
                                                    <option value="<?php echo $template->id; ?>" <?php echo $isSelected; ?>><?php echo htmlspecialchars($template->name . $t_username); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('date'); ?> <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg shadow-sm pay_in default-date-picker readonly" name="date" value='<?php
                                                                                                                                                                        if (!empty($lab->date)) {
                                                                                                                                                                            echo date('d-m-Y', $lab->date);
                                                                                                                                                                        } else {
                                                                                                                                                                            echo date('d-m-Y');
                                                                                                                                                                        }
                                                                                                                                                                        ?>' required style="pointer-events: none;">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('patient'); ?> <span class="text-danger">*</span></label>
                                            <select class="form-control form-control-lg shadow-sm <?php if (empty($lab->patient)) { ?> pos_select <?php } ?>" id="<?php if (empty($lab->patient)) { ?> pos_select <?php } ?>" name="patient" required style="pointer-events: none;">
                                                <?php
                                                if (!empty($lab->patient)) {
                                                    if (empty($patients->age)) {
                                                        $dateOfBirth = $patients->birthdate;
                                                        if (empty($dateOfBirth)) {
                                                            $age[0] = '0';
                                                        } else {
                                                            $today = date("Y-m-d");
                                                            $diff = date_diff(date_create($dateOfBirth), date_create($today));
                                                            $age[0] = $diff->format('%y');
                                                        }
                                                    } else {
                                                        $age = explode('-', $patients->age);
                                                    }
                                                ?>
                                                    <option value="<?php echo $patients->id; ?>" selected="selected"><?php echo $patients->name; ?> ( <?php echo lang('id'); ?>: <?php echo $patients->id; ?> - <?php echo lang('phone'); ?>: <?php echo $patients->phone; ?> - <?php echo lang('age'); ?>: <?php echo $age[0]; ?> )</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('report'); ?></label>
                                            <textarea class="form-control form-control-lg shadow-sm" id="editor" name="report" rows="10"><?php
                                                                                                                                            if (!empty($setval)) {
                                                                                                                                                echo set_value('report');
                                                                                                                                            } elseif (!empty($lab->report)) {
                                                                                                                                                echo $lab->report;
                                                                                                                                            } elseif (!empty($report_template)) {
                                                                                                                                                echo $report_template;
                                                                                                                                            }
                                                                                                                                            ?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <button type="submit" name="submit" id="submit" onclick="document.querySelector('#submission_type').value = 'submit';" class="btn btn-primary btn-lg px-4 py-2 mr-2">
                                            <i class="fas fa-check-circle mr-2"></i><?php echo lang('save_and_ready_to_deliver'); ?>
                                        </button>
                                        <button type="submit" name="draft" id="draft" onclick="document.querySelector('#submission_type').value = 'draft';" class="btn btn-warning btn-lg px-4 py-2 mr-2">
                                            <i class="fas fa-save mr-2"></i><?php echo lang('save_as_draft'); ?>
                                        </button>
                                        <button type="" id="template2" onclick="document.querySelector('#submission_type').value = 'template';" class="btn btn-danger btn-lg px-4 py-2">
                                            <i class="fas fa-file-alt mr-2"></i><?php echo lang('save_as_template'); ?>
                                        </button>
                                    </div>
                                </div>

                                <input type="hidden" id="submission_type" name="submission_type" value="">
                                <input type="hidden" name="redirect" value="lab">
                                <input type="hidden" name="id" value='<?php if (!empty($lab->id)) {
                                                                            echo $lab->id;
                                                                        } ?>'>
                                <input type="hidden" name="draft" id="draft2" value="">

                                <div class="modal fade" id="templateModal">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-gradient-primary">
                                                <h5 class="modal-title text-white font-weight-bold"><?php echo lang('template_details'); ?></h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="form-group">
                                                    <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('name'); ?></label>
                                                    <input type="text" name="template_name" class="form-control form-control-lg shadow-sm">
                                                </div>

                                                <div class="form-group">
                                                    <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('category'); ?></label>
                                                    <select class="form-control form-control-lg shadow-sm" name="template_category">
                                                        <?php foreach ($categories as $category) { ?>
                                                            <option value="<?php echo $category->id; ?>"><?php echo $category->category; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="template" id="template3" onclick="document.querySelector('#submission_type').value = 'template';" class="btn btn-primary">
                                                    <i class="fas fa-save mr-2"></i><?php echo lang('save_as_template'); ?>
                                                </button>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    <i class="fas fa-times mr-2"></i><?php echo lang('close'); ?>
                                                </button>
                                            </div>
                                        </div>
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
<script src="common/extranal/js/description.js"></script>
<script src="common/assets/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
    var select_doctor = "<?php echo lang('select_doctor'); ?>";
</script>
<script type="text/javascript">
    var select_patient = "<?php echo lang('select_patient'); ?>";
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.26.1/axios.min.js"></script>
<script src="common/extranal/js/lab/add_lab_view.js"></script>
<script>
    $('.draftSubmit').on("click", function(e) {
        e.preventDefault();
        $('.pay_in').prop('required', false)
        $('.pos_select').prop('required', false)
        $('#draft').val('1');
        return true;
        let form = document.getElementById("editLabForm");
        document.querySelected("#editLabForm").submit();
    })

    $(document).ready(function() {
        $('#template2').on('click', function(e) {
            e.preventDefault();
            $('#templateModal').modal();
        })
        "use strict";
        $(document.body).on('change', '#macro', function() {
            "use strict";
            var iid = $("select.macro option:selected").val();
            $.ajax({
                url: 'macro/getMacroByIdByJason?id=' + iid,
                method: 'GET',
                data: '',
                dataType: 'json',
                success: function(response) {
                    "use strict";
                    var data = myEditor.getData();
                    if (response.macro.description != null) {
                        //var data1 = data + response.macro.description;
                        //myEditor.insertText("Hi");
                        myEditor.model.change(writer => {
                            const insertPosition = myEditor.model.document.selection.getFirstPosition();
                            writer.insertText(response.macro.description, insertPosition);
                        });
                        data = myEditor.getData();
                        myEditor.setData(data);
                        //ClassicEditor.instances('#editor').insertText("Hi");
                    } else {
                        //var data1 = data;
                    }
                    //myEditor.setData(data1)

                }
            })
        });

        $("#editor").keypress(function() {
            console.log("Hello");
        });

        $('#category').on("change", function() {
            let id = $('#category').val();
            let user_id = $('#type').val();
            axios.get('lab/getTemplateByCategory?category_id=' + id + "&user_id=" + user_id)
                .then(response => {
                    $('#template1').empty();
                    $("#template1").append(response.data);
                    $('#template1').trigger('change');
                })
        })

        $('#type').on("change", function() {
            let id = $('#type').val();
            let category_id = $('#category').val();
            axios.get('lab/getTemplateByUser?user_id=' + id + "&category_id=" + category_id)
                .then(response => {
                    $('#template1').empty();
                    $("#template1").append(response.data);
                    $('#template1').trigger('change');
                })
        })
    });
</script>