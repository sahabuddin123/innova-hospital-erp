<!--sidebar end-->
<!--main content start-->

<div class="content-wrapper bg-gradient-light">
    <section class="content-header py-4 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-money-bill-wave mr-3 text-primary"></i>
                        <?php echo lang('expense') ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home" class="text-primary"><?php echo lang('home') ?></a></li>
                            <li class="breadcrumb-item active font-weight-bold"><?php echo lang('expense') ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="finance/addExpenseView" class="btn btn-primary btn-sm px-4 py-3">
                        <i class="fa fa-plus-circle"></i> <?php echo lang('add_expense'); ?>
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
                        <!-- <div class="card-header">
                            <h2 class="card-title mb-0 font-weight-800"><?php echo lang('All the expenses'); ?></h2>
                        </div> -->
                        <div class="card-body bg-light p-4">
                            <table class="table table-hover datatables" id="editable-sample" width="100%">
                                <thead>
                                    <tr class="bg-light">
                                        <th class="font-weight-bold text-uppercase" style="width: 65px;">#</th>
                                        <th class="font-weight-bold text-uppercase"><?php echo lang('category'); ?></th>
                                        <th class="font-weight-bold text-uppercase"><?php echo lang('date'); ?></th>
                                        <th class="font-weight-bold text-uppercase"><?php echo !empty(lang('payer')) ? lang('payer') : 'Payer'; ?></th>
                                        <th class="font-weight-bold text-uppercase"><?php echo !empty(lang('receiver')) ? lang('receiver') : 'Receiver'; ?></th>
                                        <th class="font-weight-bold text-uppercase"><?php echo lang('note'); ?></th>
                                        <th class="font-weight-bold text-uppercase"><?php echo lang('amount'); ?></th>
                                        <th class="font-weight-bold text-uppercase no-print" style="width: 95px;"><?php echo lang('options'); ?></th>
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

<!--main content end-->
<!--footer start-->

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = "<?php echo $this->language; ?>";
</script>
<script src="common/extranal/js/finance/expense.js"></script>