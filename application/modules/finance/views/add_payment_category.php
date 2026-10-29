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
                        if (!empty($category->id))
                            echo lang('edit_invoice_items_lab_tests');
                        else
                            echo lang('create_invoice_items_lab_tests');
                        ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item active">
                                <?php
                                if (!empty($category->id))
                                    echo lang('edit_invoice_items_lab_tests');
                                else
                                    echo lang('create_invoice_items_lab_tests');
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
                <div class="col-md-8">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-gradient-primary py-4">
                            <h2 class="card-title mb-0 text-white"><?php echo lang('items_created_here_will_be_appeared_at_the_time_of_creating_invoice'); ?></h2>
                        </div>
                        <div class="card-body p-5">
                            <?php echo validation_errors(); ?>
                            <form role="form" action="finance/addPaymentCategory" method="post" enctype="multipart/form-data">

                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm"><?php echo lang('item_lab_test'); ?> <?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" name="category" value='<?php
                                                                                                                    if (!empty($setval)) {
                                                                                                                        echo set_value('category');
                                                                                                                    }
                                                                                                                    if (!empty($category->category)) {
                                                                                                                        echo $category->category;
                                                                                                                    }
                                                                                                                    ?>' required="">
                                </div>

                                <div class="form-group mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="text-uppercase text-sm mb-0"><?php echo lang('category'); ?> / Investigation Department <span class="text-danger">*</span></label>
                                        <a href="finance/addCategoryView" target="_blank" class="small text-primary font-weight-bold" title="Add a new department/category">+ <?php echo lang('add_new'); ?> <?php echo lang('category'); ?></a>
                                    </div>
                                    <select class="form-control form-control-lg" name="payment_category" id="payment_category" required="">
                                        <option value=""><?php echo lang('select'); ?> <?php echo lang('category'); ?></option>
                                        <?php if (!empty($paycategories)) {
                                            foreach ($paycategories as $paycategory) { ?>
                                                <option value="<?php echo $paycategory->id; ?>" <?php
                                                    if (!empty($setval) && set_value('payment_category') == $paycategory->id) {
                                                        echo 'selected';
                                                    } elseif (!empty($category->payment_category) && $category->payment_category == $paycategory->id) {
                                                        echo 'selected';
                                                    } elseif (!empty($category->payment_category_name) && strcasecmp(trim($category->payment_category_name), trim($paycategory->category)) === 0) {
                                                        echo 'selected';
                                                    }
                                                ?>>
                                                    <?php echo $paycategory->category; ?>
                                                </option>
                                            <?php }
                                        } ?>
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm"><?php echo lang('item'); ?> <?php echo lang('code'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" name="code" value='<?php
                                                                                                                if (!empty($setval)) {
                                                                                                                    echo set_value('code');
                                                                                                                }
                                                                                                                if (!empty($category->code)) {
                                                                                                                    echo $category->code;
                                                                                                                }
                                                                                                                ?>' required="">
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm"><?php echo lang('service_point'); ?> <span class="text-muted">(<?php echo lang('if_applicable'); ?>)</span></label>
                                    <input type="text" class="form-control form-control-lg" name="description" value='<?php
                                                                                                                        if (!empty($setval)) {
                                                                                                                            echo set_value('description');
                                                                                                                        }
                                                                                                                        if (!empty($category->description)) {
                                                                                                                            echo $category->description;
                                                                                                                        }
                                                                                                                        ?>'>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm"><?php echo lang('price'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" name="c_price" value='<?php
                                                                                                                    if (!empty($setval)) {
                                                                                                                        echo set_value('c_price');
                                                                                                                    }
                                                                                                                    if (!empty($category->c_price)) {
                                                                                                                        echo $category->c_price;
                                                                                                                    }
                                                                                                                    ?>' required="">
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm"><?php echo lang('doctors_commission'); ?> <?php echo lang('rate'); ?> (%) <span class="text-muted">(<?php echo lang('if_applicable'); ?>)</span></label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control form-control-lg" name="d_commission" value='<?php
                                                                                                                        if (!empty($setval)) {
                                                                                                                            echo set_value('d_commission');
                                                                                                                        }
                                                                                                                        if (isset($category->d_commission)) {
                                                                                                                            echo $category->d_commission;
                                                                                                                        }
                                                                                                                        ?>'>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm"><?php echo lang('referral_commission'); ?> <?php echo lang('rate'); ?> (%) <span class="text-muted">(<?php echo lang('if_applicable'); ?>)</span></label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control form-control-lg" name="r_commission" value='<?php
                                                                                                                        if (!empty($setval)) {
                                                                                                                            echo set_value('r_commission');
                                                                                                                        }
                                                                                                                        if (isset($category->r_commission)) {
                                                                                                                            echo $category->r_commission;
                                                                                                                        }
                                                                                                                        ?>' placeholder="0">
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm"><?php echo lang('type'); ?> <span title="For lab tests that require reporting, choose 'Lab Test'. For all others, select 'Other'" data-toggle="tooltip"><i class="fa fa-question-circle"></i></span></label>
                                    <select class="form-control form-control-lg" name="type">
                                        <option value="diagnostic" <?php
                                                                    if (!empty($setval)) {
                                                                        if (set_value('type') == 'diagnostic') {
                                                                            echo 'selected';
                                                                        }
                                                                    }
                                                                    if (!empty($category->type)) {
                                                                        if ($category->type == 'diagnostic') {
                                                                            echo 'selected';
                                                                        }
                                                                    }
                                                                    ?>><?php echo lang('lab_test'); ?></option>
                                        <option value="others" <?php
                                                                if (!empty($setval)) {
                                                                    if (set_value('type') == 'others') {
                                                                        echo 'selected';
                                                                    }
                                                                }
                                                                if (!empty($category->type)) {
                                                                    if ($category->type == 'others') {
                                                                        echo 'selected';
                                                                    }
                                                                }
                                                                ?>><?php echo lang('others'); ?></option>
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm">Delivery Turnaround (Days) <span class="text-muted">(0 = Same Day, 1 = Next Day, etc.)</span></label>
                                    <?php
                                    $current_days = null;
                                    if (isset($setval)) {
                                        $current_days = set_value('delivery_days');
                                    } elseif (isset($category->delivery_days)) {
                                        $current_days = $category->delivery_days;
                                    }
                                    $standard_days = [0, 1, 2, 3, 4, 5, 7, 10, 15];
                                    $is_custom = ($current_days !== null && $current_days !== '' && !in_array(intval($current_days), $standard_days));
                                    ?>
                                    <select class="form-control form-control-lg mb-2" id="delivery_days_preset" onchange="handleDeliveryDaysPreset(this.value)">
                                        <option value="0" <?php echo ($current_days !== null && intval($current_days) == 0 && !$is_custom) ? 'selected' : ''; ?>>0 Days (Same Day / Normal Test)</option>
                                        <option value="1" <?php echo ($current_days !== null && intval($current_days) == 1 && !$is_custom) ? 'selected' : ''; ?>>1 Day (Next Day Delivery)</option>
                                        <option value="2" <?php echo ($current_days !== null && intval($current_days) == 2 && !$is_custom) ? 'selected' : ''; ?>>2 Days</option>
                                        <option value="3" <?php echo ($current_days !== null && intval($current_days) == 3 && !$is_custom) ? 'selected' : ''; ?>>3 Days (Hormonal / Special Turnaround)</option>
                                        <option value="4" <?php echo ($current_days !== null && intval($current_days) == 4 && !$is_custom) ? 'selected' : ''; ?>>4 Days</option>
                                        <option value="5" <?php echo ($current_days !== null && intval($current_days) == 5 && !$is_custom) ? 'selected' : ''; ?>>5 Days (Extended Turnaround)</option>
                                        <option value="7" <?php echo ($current_days !== null && intval($current_days) == 7 && !$is_custom) ? 'selected' : ''; ?>>7 Days (1 Week)</option>
                                        <option value="10" <?php echo ($current_days !== null && intval($current_days) == 10 && !$is_custom) ? 'selected' : ''; ?>>10 Days</option>
                                        <option value="15" <?php echo ($current_days !== null && intval($current_days) == 15 && !$is_custom) ? 'selected' : ''; ?>>15 Days (Biopsy / Culture / Special Investigation)</option>
                                        <option value="custom" <?php echo $is_custom ? 'selected' : ''; ?>>Custom Days (Enter Manually)...</option>
                                    </select>
                                    <div id="custom_days_wrapper" style="<?php echo $is_custom ? '' : 'display:none;'; ?>">
                                        <div class="input-group">
                                            <input type="number" min="0" max="365" class="form-control form-control-lg" id="delivery_days_input" name="delivery_days" value="<?php echo ($current_days !== null) ? html_escape($current_days) : '0'; ?>" placeholder="Enter number of days (e.g. 0, 1, 2, 7...)">
                                            <div class="input-group-append">
                                                <span class="input-group-text font-weight-bold">Days</span>
                                            </div>
                                        </div>
                                        <small class="text-muted">Enter any custom number of delivery turnaround days.</small>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm">Is Hormonal Test?</label>
                                    <select class="form-control form-control-lg" name="is_hormone">
                                        <option value="0" <?php echo (empty($category->is_hormone)) ? 'selected' : ''; ?>>No (Normal Diagnostic Test)</option>
                                        <option value="1" <?php echo (!empty($category->is_hormone)) ? 'selected' : ''; ?>>Yes (Hormonal Investigation)</option>
                                    </select>
                                </div>

                                <input type="hidden" name="id" value='<?php
                                                                        if (!empty($category->id)) {
                                                                            echo $category->id;
                                                                        }
                                                                        ?>'>

                                <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block">
                                    <i class="fas fa-check-circle mr-3"></i><?php echo lang('submit'); ?>
                                </button>

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
<script src="common/extranal/js/finance/payment_category.js"></script>
<script>
function handleDeliveryDaysPreset(val) {
    var wrapper = document.getElementById('custom_days_wrapper');
    var input = document.getElementById('delivery_days_input');
    if (val === 'custom') {
        wrapper.style.display = 'block';
        input.focus();
    } else {
        wrapper.style.display = 'none';
        input.value = val;
    }
}
</script>