<!--sidebar end-->
<!--main content start-->

<div class="content-wrapper bg-light">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row my-2">
                <div class="col-sm-6">
                    <h1 class="font-weight-bold">
                        <i class="fas fa-plus-circle text-primary mr-3"></i>
                        <?php
                        if (!empty($expense->id))
                            echo lang('pharmacy') . ' ' . lang('edit_expense');
                        else
                            echo lang('pharmacy') . ' ' . lang('add_expense');
                        ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item"><a href="finance/pharmacy/home"><?php echo lang('pharmacy'); ?></a></li>
                            <li class="breadcrumb-item"><a href="finance/pharmacy/expense"><?php echo lang('expense'); ?></a></li>
                            <li class="breadcrumb-item active">
                                <?php
                                if (!empty($expense->id))
                                    echo lang('pharmacy') . ' ' . lang('edit_expense');
                                else
                                    echo lang('pharmacy') . ' ' . lang('add_expense');
                                ?>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content py-5">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-6">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-gradient-primary">
                            <h3 class="card-title text-white font-weight-800">Add expense for a pharmacy expense category</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body p-4">
                            <?php echo validation_errors(); ?>
                            <form role="form" action="finance/pharmacy/addExpense" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="text-uppercase font-weight-bold text-muted mb-0"><?php echo lang('category'); ?> <span class="text-danger">*</span></label>
                                        <button type="button" class="btn btn-xs btn-outline-primary shadow-sm" data-toggle="modal" data-target="#modalAddPharmacyCategory" style="font-size: 11px; padding: 2px 10px; font-weight: 700; border-radius: 4px; line-height: 1.5;">
                                            <i class="fas fa-plus mr-1"></i> Add Category
                                        </button>
                                    </div>
                                    <select class="form-control form-control-lg shadow-sm select2" name="category" id="pharmacy_category_select" required style="width: 100%;">
                                        <option value="" disabled <?php echo empty($expense->category) ? 'selected' : ''; ?>>-- Search or Select Category --</option>
                                        <?php foreach ($categories as $category) { ?>
                                            <option value="<?php echo $category->category; ?>" <?php
                                                                                                if (!empty($expense->category)) {
                                                                                                    if ($category->category == $expense->category) {
                                                                                                        echo 'selected';
                                                                                                    }
                                                                                                }
                                                                                                ?>> <?php echo $category->category; ?> </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('amount'); ?> <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control form-control-lg shadow-sm" name="amount" value='<?php
                                                                                                                                            if (!empty($expense->amount)) {
                                                                                                                                                echo $expense->amount;
                                                                                                                                            }
                                                                                                                                            ?>' placeholder="<?php echo $settings->currency; ?>" required="">
                                </div>
                                <input type="hidden" name="id" value='<?php
                                                                        if (!empty($expense->id)) {
                                                                            echo $expense->id;
                                                                        }
                                                                        ?>'>
                                <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block shadow-lg py-3">
                                    <i class="fas fa-check-circle mr-3"></i><?php echo lang('submit'); ?>
                                </button>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Modal: Add Pharmacy Category Popup -->
    <div class="modal fade" id="modalAddPharmacyCategory" tabindex="-1" role="dialog" aria-labelledby="modalAddPharmacyCatTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-gradient-primary text-white py-3">
                    <h5 class="modal-title font-weight-bold" id="modalAddPharmacyCatTitle">
                        <i class="fas fa-folder-plus mr-2"></i> Add Pharmacy Expense Category
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formAddPharmacyCategoryAjax" action="finance/pharmacy/addExpenseCategory" method="post">
                    <div class="modal-body p-4">
                        <div id="pharmacy_category_modal_alert" style="display: none;"></div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark small text-uppercase">
                                Category Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" name="category" id="new_pharm_cat_name" placeholder="e.g. Medicine Purchases, Packaging" required autocomplete="off">
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark small text-uppercase">
                                Description <small class="text-muted font-weight-normal">(Optional)</small>
                            </label>
                            <textarea class="form-control" name="description" id="new_pharm_cat_desc" rows="2" placeholder="Brief note about this category..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 font-weight-bold shadow-sm" id="btnSavePharmacyCategory">
                            <i class="fas fa-check mr-1"></i> Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /.content -->
</div>

<script>
    $(document).ready(function() {
        function initPharmacyCategorySelect2() {
            if (typeof $.fn.select2 !== 'undefined') {
                $('#pharmacy_category_select').select2({
                    theme: 'bootstrap4',
                    placeholder: '-- Search or Select Category --',
                    allowClear: true,
                    width: '100%'
                });
            } else {
                setTimeout(initPharmacyCategorySelect2, 100);
            }
        }
        initPharmacyCategorySelect2();

        $('#modalAddPharmacyCategory').on('shown.bs.modal', function () {
            $('#new_pharm_cat_name').focus();
        });

        $('#formAddPharmacyCategoryAjax').on('submit', function(e) {
            e.preventDefault();
            var catName = $.trim($('#new_pharm_cat_name').val());
            var catDesc = $.trim($('#new_pharm_cat_desc').val());
            if (!catName) {
                $('#pharmacy_category_modal_alert').html('<div class="alert alert-danger py-2 small mb-3">Please enter a category name.</div>').show();
                $('#new_pharm_cat_name').focus();
                return false;
            }

            var submitBtn = $('#btnSavePharmacyCategory');
            var originalBtnHtml = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
            $('#pharmacy_category_modal_alert').hide().empty();

            $.ajax({
                url: 'finance/pharmacy/addExpenseCategory',
                type: 'POST',
                data: {
                    category: catName,
                    description: catDesc || catName,
                    is_ajax: 1
                },
                dataType: 'json',
                success: function(response) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    if (response && response.status === 'success') {
                        var finalCat = response.category || catName;

                        var exists = false;
                        $('#pharmacy_category_select option').each(function() {
                            if ($(this).val().toLowerCase() === finalCat.toLowerCase()) {
                                exists = true;
                                finalCat = $(this).val();
                            }
                        });

                        if (!exists) {
                            var newOption = new Option(finalCat, finalCat, true, true);
                            $('#pharmacy_category_select').append(newOption);
                        }

                        $('#pharmacy_category_select').val(finalCat).trigger('change');

                        $('#modalAddPharmacyCategory').modal('hide');
                        $('#formAddPharmacyCategoryAjax')[0].reset();
                        $('#pharmacy_category_modal_alert').hide().empty();

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: response.message || 'Pharmacy category saved and selected!',
                                showConfirmButton: false,
                                timer: 2500
                            });
                        }
                    } else {
                        var err = (response && response.message) ? response.message : 'Failed to save category.';
                        $('#pharmacy_category_modal_alert').html('<div class="alert alert-danger py-2 small mb-3">' + err + '</div>').show();
                    }
                },
                error: function(xhr, status, error) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    $('#pharmacy_category_modal_alert').html('<div class="alert alert-danger py-2 small mb-3">An error occurred while saving category.</div>').show();
                }
            });
        });
    });
</script>

<!--main content end-->
<!--footer start-->