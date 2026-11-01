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
                                    <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('category'); ?> <span class="text-danger">*</span></label>
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
    });
</script>

<!--main content end-->
<!--footer start-->