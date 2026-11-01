<!--sidebar end-->
<!--main content start-->
<div class="content-wrapper bg-gradient-light">
    <section class="content-header py-4 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-money-bill-wave text-primary mr-3"></i>
                        <?php
                        if (!empty($expense->id)) {
                            echo lang('edit_expense');
                        } else {
                            echo lang('add_expense');
                        }
                        ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item"><a href="finance/expense"><?php echo lang('expense'); ?></a></li>
                            <li class="breadcrumb-item active">
                                <?php
                                if (!empty($expense->id)) {
                                    echo lang('edit_expense');
                                } else {
                                    echo lang('add_expense');
                                }
                                ?>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <section class="content py-5">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-9">
                    <div class="card shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="card-title font-weight-bold mb-0 text-dark">
                                <i class="fas fa-receipt mr-2 text-primary"></i>
                                <?php echo !empty($expense->id) ? 'Update Expense Details' : 'New Expense Entry'; ?>
                            </h5>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            <?php echo validation_errors(); ?>
                            <form role="form" action="finance/addExpense" method="post" enctype="multipart/form-data">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="font-weight-bold text-uppercase text-xs text-muted mb-0"><?php echo lang('category'); ?> <span class="text-danger">*</span></label>
                                                <button type="button" class="btn btn-xs btn-outline-primary shadow-sm" data-toggle="modal" data-target="#modalAddCategory" style="font-size: 11px; padding: 2px 10px; font-weight: 700; border-radius: 4px; line-height: 1.5;">
                                                    <i class="fas fa-plus mr-1"></i> Add Category
                                                </button>
                                            </div>
                                            <select class="form-control form-control-lg select2" name="category" id="category_select" required style="width: 100%;">
                                                <option value="" disabled <?php echo (empty($expense->category) && empty(set_value('category'))) ? 'selected' : ''; ?>>-- Search or Select Category --</option>
                                                <?php foreach ($categories as $category) { ?>
                                                    <option value="<?php echo html_escape($category->category); ?>" <?php
                                                        if (!empty($setval) && $category->category == set_value('category')) {
                                                            echo 'selected';
                                                        } elseif (!empty($expense->category) && $category->category == $expense->category) {
                                                            echo 'selected';
                                                        }
                                                    ?>> <?php echo html_escape($category->category); ?> </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label class="font-weight-bold text-uppercase text-xs text-muted mb-2"><?php echo lang('amount'); ?> <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text font-weight-bold text-primary"><?php echo $settings->currency ?? '৳'; ?></span>
                                                </div>
                                                <input type="number" step="0.01" class="form-control form-control-lg font-weight-bold" name="amount" placeholder="0.00" value='<?php
                                                    if (!empty($setval)) {
                                                        echo set_value('amount');
                                                    } elseif (!empty($expense->amount)) {
                                                        echo $expense->amount;
                                                    }
                                                ?>' required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label class="font-weight-bold text-uppercase text-xs text-muted mb-2">
                                                <i class="far fa-calendar-alt mr-1 text-primary"></i> Bill Date <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="form-control form-control-lg" name="bill_date" value="<?php
                                                if (!empty($setval)) {
                                                    echo set_value('bill_date');
                                                } elseif (!empty($expense->bill_date)) {
                                                    echo date('Y-m-d', is_numeric($expense->bill_date) ? $expense->bill_date : strtotime($expense->bill_date));
                                                } elseif (!empty($expense->date)) {
                                                    echo date('Y-m-d', $expense->date);
                                                } else {
                                                    echo date('Y-m-d');
                                                }
                                            ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label class="font-weight-bold text-uppercase text-xs text-muted mb-2">
                                                <i class="fas fa-hand-holding-usd mr-1 text-success"></i> Pay To (Receiver / Vendor)
                                            </label>
                                            <input type="text" class="form-control form-control-lg" name="pay_to" placeholder="e.g. Nur Computer / Vendor Name" value="<?php
                                                if (!empty($setval)) {
                                                    echo set_value('pay_to');
                                                } elseif (!empty($expense->pay_to)) {
                                                    echo html_escape($expense->pay_to);
                                                }
                                            ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-4">
                                            <label class="font-weight-bold text-uppercase text-xs text-muted mb-2">
                                                <i class="fas fa-user-tag mr-1 text-info"></i> Payer Name (Purchaser)
                                            </label>
                                            <input type="text" class="form-control form-control-lg" name="payer_name" placeholder="e.g. Staff or Purchaser Name" value="<?php
                                                if (!empty($setval)) {
                                                    echo set_value('payer_name');
                                                } elseif (!empty($expense->payer_name)) {
                                                    echo html_escape($expense->payer_name);
                                                }
                                            ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-uppercase text-xs text-muted mb-2">
                                        <i class="fas fa-align-left mr-1 text-secondary"></i> <?php echo lang('remarks'); ?> / Description
                                    </label>
                                    <textarea class="form-control form-control-lg" name="note" rows="3" placeholder="Enter expense note, invoice reference or remarks..."><?php
                                        if (!empty($setval)) {
                                            echo set_value('note');
                                        } elseif (!empty($expense->note)) {
                                            echo html_escape($expense->note);
                                        }
                                    ?></textarea>
                                </div>

                                <input type="hidden" name="id" value='<?php
                                    if (!empty($expense->id)) {
                                        echo $expense->id;
                                    }
                                ?>'>

                                <div class="pt-2">
                                    <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block shadow-sm font-weight-bold py-3">
                                        <i class="fas fa-check-circle mr-2"></i> <?php echo lang('submit'); ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal: Add Expense Category Popup -->
    <div class="modal fade" id="modalAddCategory" tabindex="-1" role="dialog" aria-labelledby="modalAddCategoryTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-gradient-primary text-white py-3">
                    <h5 class="modal-title font-weight-bold" id="modalAddCategoryTitle">
                        <i class="fas fa-folder-plus mr-2"></i> Add Expense Category
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formAddCategoryAjax" action="finance/addExpenseCategory" method="post">
                    <div class="modal-body p-4">
                        <div id="category_modal_alert" style="display: none;"></div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark small text-uppercase">
                                Category Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" name="category" id="new_cat_name" placeholder="e.g. Office Stationery, Maintenance, USG Fee" required autocomplete="off">
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark small text-uppercase">
                                Description <small class="text-muted font-weight-normal">(Optional)</small>
                            </label>
                            <textarea class="form-control" name="description" id="new_cat_desc" rows="2" placeholder="Brief note about this category..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 font-weight-bold shadow-sm" id="btnSaveCategory">
                            <i class="fas fa-check mr-1"></i> Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Match select2 height and font with form-control-lg */
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(2.875rem + 2px) !important;
        padding: 0.5rem 1rem !important;
        font-size: 1rem !important;
        line-height: 1.5 !important;
        border-radius: 0.3rem !important;
        border: 1px solid #ced4da !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        padding-left: 0 !important;
        padding-right: 20px !important;
        color: #495057 !important;
        font-weight: 600 !important;
        line-height: normal !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        top: 50% !important;
        transform: translateY(-50%) !important;
        right: 12px !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear {
        margin-right: 25px !important;
        font-size: 18px !important;
        color: #adb5bd !important;
    }
    .select2-container--bootstrap4 .select2-dropdown {
        border-radius: 8px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12) !important;
        border: 1px solid #ced4da !important;
        overflow: hidden;
    }
    .select2-container--bootstrap4 .select2-search--dropdown {
        padding: 8px !important;
    }
    .select2-container--bootstrap4 .select2-search--dropdown .select2-search__field {
        border-radius: 6px !important;
        padding: 8px 12px !important;
        border: 1px solid #ced4da !important;
    }
    .select2-container--bootstrap4 .select2-results__option {
        padding: 8px 14px !important;
        font-size: 0.95rem !important;
    }
    .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
        background-color: #007bff !important;
        color: #fff !important;
    }
</style>

<script>
    $(document).ready(function() {
        function initCategorySelect2() {
            if (typeof $.fn.select2 !== 'undefined') {
                $('#category_select').select2({
                    theme: 'bootstrap4',
                    placeholder: '-- Search or Select Category --',
                    allowClear: true,
                    width: '100%'
                });
            } else {
                setTimeout(initCategorySelect2, 100);
            }
        }
        initCategorySelect2();

        $('#modalAddCategory').on('shown.bs.modal', function () {
            $('#new_cat_name').focus();
        });

        $('#formAddCategoryAjax').on('submit', function(e) {
            e.preventDefault();
            var catName = $.trim($('#new_cat_name').val());
            var catDesc = $.trim($('#new_cat_desc').val());
            if (!catName) {
                $('#category_modal_alert').html('<div class="alert alert-danger py-2 small mb-3">Please enter a category name.</div>').show();
                $('#new_cat_name').focus();
                return false;
            }

            var submitBtn = $('#btnSaveCategory');
            var originalBtnHtml = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
            $('#category_modal_alert').hide().empty();

            $.ajax({
                url: 'finance/addExpenseCategory',
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
                        $('#category_select option').each(function() {
                            if ($(this).val().toLowerCase() === finalCat.toLowerCase()) {
                                exists = true;
                                finalCat = $(this).val();
                            }
                        });

                        if (!exists) {
                            var newOption = new Option(finalCat, finalCat, true, true);
                            $('#category_select').append(newOption);
                        }

                        $('#category_select').val(finalCat).trigger('change');

                        $('#modalAddCategory').modal('hide');
                        $('#formAddCategoryAjax')[0].reset();
                        $('#category_modal_alert').hide().empty();

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: response.message || 'Category saved and selected!',
                                showConfirmButton: false,
                                timer: 2500
                            });
                        }
                    } else {
                        var err = (response && response.message) ? response.message : 'Failed to save category.';
                        $('#category_modal_alert').html('<div class="alert alert-danger py-2 small mb-3">' + err + '</div>').show();
                    }
                },
                error: function(xhr, status, error) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    $('#category_modal_alert').html('<div class="alert alert-danger py-2 small mb-3">An error occurred while saving category.</div>').show();
                }
            });
        });
    });
</script>

<!--main content end-->
<!--footer start-->
