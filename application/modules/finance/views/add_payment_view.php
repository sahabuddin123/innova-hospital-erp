<?php
$payment = isset($payment) ? $payment : null;
$draft = isset($draft) ? $draft : null;
$can_create_patient = $this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant', 'Receptionist', 'Doctor', 'Nurse', 'Laboratorist'));
$can_create_doctor = $this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant', 'Receptionist'));
$can_create_referrer = $this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant', 'Receptionist'));
?>
<link href="common/css/bootstrap-reset.css" rel="stylesheet">
<link href="common/extranal/css/finance/add_payment_view.css?v=pos-2026-v12" rel="stylesheet">

<div class="content-wrapper page-add-payment">
    <!-- Top Command Header Bar -->
    <header class="pos-header-banner mb-3">
        <div class="container-fluid">
            <div class="pos-header-row">
                <!-- Left: Title, Badge, Breadcrumb -->
                <div class="pos-header-left">
                    <div class="pos-header-icon mr-3">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center">
                            <h1 class="h5 font-weight-bold mb-0 text-dark mr-2">
                                <?php
                                if (!empty($payment->id)) {
                                    echo lang('edit_invoice') . ' <span class="badge badge-light border ml-1">#' . $payment->id . '</span>';
                                } elseif (!empty($draft->id)) {
                                    echo lang('edit_draft_invoice') . ' <span class="badge badge-warning ml-1">Draft</span>';
                                } else {
                                    echo lang('add_new_invoice');
                                }
                                ?>
                            </h1>
                            <span class="pos-title-badge"><i class="fas fa-bolt mr-1"></i> Billing POS</span>
                        </div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb bg-transparent p-0 mb-0 small">
                                <li class="breadcrumb-item"><a href="home" class="text-secondary"><?php echo lang('home'); ?></a></li>
                                <li class="breadcrumb-item"><a href="finance/payment" class="text-secondary"><?php echo lang('all') ?> <?php echo lang('invoices') ?></a></li>
                                <li class="breadcrumb-item active text-primary font-weight-bold">
                                    <?php
                                    if (!empty($payment->id)) {
                                        echo lang('edit_invoice');
                                    } elseif (!empty($draft->id)) {
                                        echo lang('edit_draft_invoice');
                                    } else {
                                        echo lang('add_new_invoice');
                                    }
                                    ?>
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Center: Inline Keyboard Shortcuts -->
                <div class="pos-header-shortcuts d-none d-lg-flex">
                    <span class="pos-shortcut-item"><span class="pos-kbd">F2</span> Focus Test Search</span>
                    <span class="pos-shortcut-item"><span class="pos-kbd">Alt+N</span> Add Patient</span>
                    <span class="pos-shortcut-item"><span class="pos-kbd">Alt+F</span> Full Paid</span>
                    <span class="pos-shortcut-item"><span class="pos-kbd">Alt+D</span> Full Due</span>
                    <span class="pos-shortcut-item"><span class="pos-kbd">Ctrl+Enter</span> Save &amp; Print</span>
                </div>

                <!-- Right: Clock, Invoices Button, POS Terminal Badge -->
                <div class="pos-header-right">
                    <div class="pos-live-pill mr-2">
                        <span class="pos-live-dot"></span>
                        <span id="pos_live_clock">--:--:--</span>
                    </div>
                    <a href="finance/payment" class="btn btn-pos-invoices mr-2">
                        <i class="fas fa-list mr-1"></i> <?php echo lang('invoices'); ?>
                    </a>
                    <span class="badge pos-terminal-badge d-none d-sm-inline-flex align-items-center">
                        <i class="fas fa-desktop mr-1"></i> POS Billing Terminal
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Grid -->
    <section class="content px-2 px-md-3">
        <div class="container-fluid">
            <form role="form" id="editPaymentForm" class="clearfix form-row" action="finance/addPayment" method="post" enctype="multipart/form-data">

                <!-- ==========================================
                     TOP ROW: Patient, Doctor & Referrer (Full Width)
                     ========================================== -->
                <div class="col-12 pos-top-section mb-2">
                    <div class="row align-items-start">
                        <!-- Patient Selection -->
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="pos-top-label mb-0">
                                    <span><?php echo lang('patient'); ?> <span class="text-danger">*</span></span>
                                </label>
                                <?php if ($can_create_patient) { ?>
                                    <button type="button" class="btn-top-add-new" id="btn_open_patient_modal" title="Add New Patient">
                                        + <?php echo lang('add_new'); ?>
                                    </button>
                                <?php } ?>
                            </div>
                            <select class="form-control pos_select" id="pos_select" name="patient" value='' required="">
                                <?php if (!empty($payment)) {
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
                                    <option value="<?php echo $patients->id; ?>" selected="selected">
                                        <?php echo $patients->name; ?> ( <?php echo lang('id'); ?>:
                                        <?php echo $patients->id; ?> - <?php echo lang('phone'); ?>:
                                        <?php echo $patients->phone; ?> - <?php echo lang('age'); ?>:
                                        <?php echo $age[0]; ?> ) </option>
                                <?php } elseif (!empty($draft->patient)) {
                                    if ($draft->patient == 'add_new') {
                                    ?>
                                        <option value="<?php echo 'add_new'; ?>" selected="selected">
                                            <?php echo lang('add_new'); ?></option>
                                    <?php } else {
                                        $patients = $this->patient_model->getPatientById($draft->patient);
                                        $age = explode('-', $patients->age);
                                    ?>
                                        <option value="<?php echo $patients->id; ?>" selected="selected">
                                            <?php echo $patients->name; ?> ( <?php echo lang('id'); ?>:
                                            <?php echo $patients->id; ?> - <?php echo lang('phone'); ?>:
                                            <?php echo $patients->phone; ?> - <?php echo lang('age'); ?>:
                                            <?php echo $age[0]; ?> ) </option>
                                    <?php  }
                                } else { ?>
                                    <option value="" selected="selected"><?php echo lang('select'); ?> <?php echo lang('patient'); ?></option>
                                    <option value="<?php echo 'add_new'; ?>"><?php echo lang('add_new'); ?></option>
                                <?php } ?>
                            </select>

                            <!-- Patient Information Fields (Collapsible for Quick Inline Add) -->
                            <?php
                            $patient_display_name = '';
                            $patient_display_phone = '';
                            $patient_display_age = '';
                            $patient_display_sex = '';
                            $patient_display_weight = '';
                            $patient_display_address = '';

                            if (!empty($payment) && !empty($patients)) {
                                $patient_display_name = $patients->name;
                                $patient_display_phone = $patients->phone;
                                if (!empty($patients->age)) {
                                    $age_parts = explode('-', $patients->age);
                                    $patient_display_age = $age_parts[0];
                                }
                                $patient_display_sex = !empty($patients->sex) ? $patients->sex : (!empty($patients->patient_gender) ? $patients->patient_gender : '');
                                $patient_display_weight = !empty($patients->weight) ? $patients->weight : '';
                                $patient_display_address = !empty($patients->address) ? $patients->address : '';
                            } elseif (!empty($draft)) {
                                $patient_display_name = !empty($draft->patient_name) ? $draft->patient_name : '';
                                $patient_display_phone = !empty($draft->patient_phone) ? $draft->patient_phone : '';
                                if (!empty($draft->age)) {
                                    $age_parts = explode('-', $draft->age);
                                    $patient_display_age = $age_parts[0];
                                }
                                $patient_display_sex = !empty($draft->patient_gender) ? $draft->patient_gender : '';
                                if (!empty($draft->patient) && $draft->patient != 'add_new') {
                                    $dp = $this->patient_model->getPatientById($draft->patient);
                                    if (!empty($dp)) {
                                        $patient_display_weight = !empty($dp->weight) ? $dp->weight : '';
                                        $patient_display_address = !empty($dp->address) ? $dp->address : '';
                                    }
                                }
                            }
                            $show_patient_card = false;
                            ?>
                            <div class="pos_client mt-2" id="pos_client" style="display: none;">
                                <div class="form-row">
                                    <div class="col-md-6 mb-2">
                                        <label class="pos-label"><?php echo lang('patient'); ?> <?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="p_name" id="p_name" value="<?php echo html_escape($patient_display_name); ?>" placeholder="Full name">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="pos-label"><?php echo lang('phone'); ?> <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" name="p_phone" id="p_phone" value="<?php echo html_escape($patient_display_phone); ?>" placeholder="01XXXXXXXXX">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-4 mb-2">
                                        <label class="pos-label"><?php echo lang('age'); ?></label>
                                        <input type="number" min="0" max="150" class="form-control" name="p_age" id="p_age" value="<?php echo html_escape($patient_display_age); ?>" placeholder="Age">
                                    </div>
                                    <div class="col-4 mb-2">
                                        <label class="pos-label"><?php echo lang('gender'); ?></label>
                                        <select class="form-control" id="p_gender" name="p_gender">
                                            <option value=""><?php echo lang('select_gender'); ?></option>
                                            <option value="Male" <?php if ($patient_display_sex == 'Male') { echo 'selected'; } ?>><?php echo lang('male'); ?></option>
                                            <option value="Female" <?php if ($patient_display_sex == 'Female') { echo 'selected'; } ?>><?php echo lang('female'); ?></option>
                                            <option value="Other" <?php if ($patient_display_sex == 'Other') { echo 'selected'; } ?>><?php echo lang('other'); ?></option>
                                        </select>
                                    </div>
                                    <div class="col-4 mb-2">
                                        <label class="pos-label"><?php echo lang('weight'); ?> (kg)</label>
                                        <input type="number" step="0.1" min="0" max="500" class="form-control" name="p_weight" id="p_weight" value="<?php echo html_escape($patient_display_weight); ?>" placeholder="kg">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-12 mb-1">
                                        <label class="pos-label"><?php echo lang('contact'); ?> <?php echo lang('address'); ?></label>
                                        <input type="text" class="form-control" name="p_address" id="p_address" value="<?php echo html_escape($patient_display_address); ?>" placeholder="Address">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Doctor Selection -->
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="pos-top-label mb-0">
                                    <span><?php echo lang('doctor'); ?> <span class="text-danger">*</span></span>
                                </label>
                                <?php if ($can_create_doctor) { ?>
                                    <button type="button" class="btn-top-add-new" id="btn_open_doctor_modal" title="Add New Doctor">
                                        + <?php echo lang('add_new'); ?>
                                    </button>
                                <?php } ?>
                            </div>
                            <select class="form-control add_doctor" id="add_doctor" name="doctor" value='' required>
                                <?php if (!empty($payment)) { ?>
                                    <option value="<?php echo $doctors->id; ?>" selected="selected">
                                        <?php echo $doctors->name; ?> - <?php echo $doctors->id; ?>
                                    </option>
                                <?php } elseif (!empty($draft->doctor)) {
                                    if ($draft->doctor == 'add_new') {
                                    ?>
                                        <option value="<?php echo 'add_new'; ?>" selected="selected">
                                            <?php echo lang('add_new'); ?></option>
                                    <?php } else {
                                        $doctor_name = $this->doctor_model->getDoctorById($draft->doctor)->name;
                                    ?>
                                        <option value="<?php echo $draft->doctor; ?>" selected="selected">
                                            <?php echo $doctor_name . '(' . lang('id') . ': ' . $draft->id . ')'; ?>
                                        </option>
                                    <?php }
                                } ?>
                            </select>
                        </div>

                        <!-- Referrer Selection -->
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="pos-top-label mb-0">
                                    <span><?php echo lang('referrer'); ?> <span class="text-muted font-weight-normal lowercase">(<?php echo lang('optional'); ?>)</span></span>
                                </label>
                                <?php if ($can_create_referrer) { ?>
                                    <button type="button" class="btn-top-add-new" id="btn_open_referrer_modal" title="Add New Referrer">
                                        + <?php echo lang('add_new'); ?>
                                    </button>
                                <?php } ?>
                            </div>
                            <select class="form-control add_referrer js-example-basic-single" id="add_referrer" name="referrer">
                                <option value=""><?php echo lang('select'); ?> <?php echo lang('referrer'); ?></option>
                                <?php if ($can_create_referrer) { ?>
                                    <option value="add_new">+ <?php echo lang('add_new'); ?> <?php echo lang('referrer'); ?></option>
                                <?php } ?>
                                <?php if (!empty($referrers)) {
                                    foreach ($referrers as $ref) { ?>
                                        <option value="<?php echo $ref->id; ?>" <?php
                                            if (!empty($payment->referrer) && $payment->referrer == $ref->id) {
                                                echo 'selected="selected"';
                                            } elseif (!empty($draft->referrer) && $draft->referrer == $ref->id) {
                                                echo 'selected="selected"';
                                            }
                                        ?>>
                                            <?php echo html_escape($ref->name); ?> (<?php echo html_escape($ref->type); ?> - <?php echo html_escape($ref->phone); ?>)
                                        </option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>

                    <!-- Dashed Divider -->
                    <div class="pos-top-divider"></div>
                </div>

                <!-- ==========================================
                     COLUMN 1: Diagnostic & Service Catalog
                     ========================================== -->
                <div class="col-12 col-lg-4 col-xl-4 mb-3">
                    <div class="pos-card h-100 d-flex flex-column">
                        <div class="pos-card-header pb-0 border-0 bg-white">
                            <div class="pos-tab-nav">
                                <span class="pos-tab-item active">
                                    <i class="fas fa-stethoscope mr-2 text-primary"></i><?php echo lang('diagnostic'); ?> &amp; <?php echo lang('services'); ?>
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-3 flex-grow-1 d-flex flex-column">

                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="pos-label mb-0">
                                    <span><?php echo lang('item'); ?> / <?php echo lang('test'); ?> <span class="text-danger">*</span></span>
                                </label>
                                <span class="text-muted font-weight-normal small">[F2 TO SEARCH]</span>
                            </div>
                            
                            <!-- Multi-select Items Box -->
                            <select name="category_name[]" class="form-control multi-select option_select" multiple="" id="my_multi_select3" required>
                                <?php
                                $current_group = null;
                                foreach ($categories as $category) {
                                    $item_group = !empty($category->payment_category_name) ? $category->payment_category_name : 'General Tests';
                                    if ($current_group !== $item_group) {
                                        if ($current_group !== null) {
                                            echo '</optgroup>';
                                        }
                                        $current_group = $item_group;
                                        echo '<optgroup label="' . html_escape($current_group) . '">';
                                    }
                                ?>
                                    <option class="ooppttiioonn" data-id="<?php echo $category->c_price; ?>" data-idd="<?php echo $category->id; ?>" data-cat_name="<?php echo $category->category; ?>" data-d_commission="<?php echo isset($category->d_commission) ? floatval($category->d_commission) : 0; ?>" data-r_commission="<?php echo isset($category->r_commission) ? floatval($category->r_commission) : 0; ?>" value="<?php echo $category->id; ?>" <?php
                                        if (!empty($payment->category_name)) {
                                            $category_name = $payment->category_name;
                                            $category_name1 = explode(',', $category_name);
                                            foreach ($category_name1 as $category_name2) {
                                                $category_name3 = explode('*', $category_name2);
                                                if ($category_name3[0] == $category->id) {
                                                    echo 'data-qtity=' . $category_name3[3];
                                                }
                                            }
                                        } elseif (!empty($draft->category_name)) {
                                            $category_name = $draft->category_name;
                                            $category_name1 = explode(',', $category_name);
                                            foreach ($category_name1 as $category_name2) {
                                                $category_name3 = explode('*', $category_name2);
                                                if ($category_name3[0] == $category->id) {
                                                    echo 'data-qtity=' . $category_name3[3];
                                                }
                                            }
                                        }
                                        ?> <?php
                                        if (!empty($payment->category_name)) {
                                            $category_name = $payment->category_name;
                                            $category_name1 = explode(',', $category_name);
                                            foreach ($category_name1 as $category_name2) {
                                                $category_name3 = explode('*', $category_name2);
                                                if ($category_name3[0] == $category->id) {
                                                    echo 'selected';
                                                }
                                            }
                                        } elseif (!empty($draft->category_name)) {
                                            $category_name = $draft->category_name;
                                            $category_name1 = explode(',', $category_name);
                                            foreach ($category_name1 as $category_name2) {
                                                $category_name3 = explode('*', $category_name2);
                                                if ($category_name3[0] == $category->id) {
                                                    echo 'selected';
                                                }
                                            }
                                        }
                                        ?>>
                                        <?php echo $category->category . ' - ' . $settings->currency . '' . $category->c_price; ?>
                                    </option>
                                <?php }
                                if ($current_group !== null) {
                                    echo '</optgroup>';
                                }
                                ?>
                            </select>

                            <div class="mt-2 pt-2">
                                <a target="_blank" href="finance/addPaymentCategoryView" class="btn btn-block btn-add-test-catalog">
                                    <i class="far fa-plus-circle mr-1"></i><?php echo lang('add_new') ?> <?php echo lang('item') ?> / <?php echo lang('test'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==========================================
                     COLUMN 2: Selected Items (Cart Panel)
                     ========================================== -->
                <div class="col-12 col-lg-4 col-xl-4 mb-3">
                    <div class="pos-card h-100 d-flex flex-column">
                        <div class="pos-card-header">
                            <h2 class="pos-card-title">
                                <i class="fas fa-shopping-cart text-primary"></i><?php echo lang('items'); ?> (Cart)
                            </h2>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-xs btn-outline-danger font-weight-bold py-0 px-2 mr-2 d-none" id="btn_clear_cart" title="Clear all items in cart">
                                    <i class="fas fa-trash-alt mr-1"></i>Clear
                                </button>
                                <span class="badge badge-pill badge-cart-count" id="cart_badge">0 items</span>
                            </div>
                        </div>

                        <!-- Table Sub-header -->
                        <div class="cart-header-strip d-flex justify-content-between align-items-center">
                            <div class="cart-col-title">
                                <i class="fas fa-list-ul mr-1 text-primary"></i> <?php echo lang('items'); ?> &amp; <?php echo lang('price'); ?>
                            </div>
                            <div class="cart-col-qty text-right pr-3">
                                <?php echo lang('qty'); ?>
                            </div>
                        </div>

                        <!-- Cart Items Body -->
                        <div class="card-body p-2 flex-grow-1" style="min-height: 480px;">
                            <!-- Empty Cart Notice -->
                            <div id="empty_cart_placeholder" class="empty-cart-state">
                                <div class="empty-cart-icon-box">
                                    <i class="fas fa-shopping-basket"></i>
                                </div>
                                <h6 class="font-weight-bold text-dark mb-1">Invoice Cart is Empty</h6>
                                <p class="text-muted small mb-0">Select diagnostic tests or services from the left catalog to add them to this invoice.</p>
                            </div>

                            <!-- Dynamic Selected Items Container (.qfloww) -->
                            <div class="qfloww"></div>
                        </div>

                        <!-- Cart Footer Helper -->
                        <div class="card-footer bg-light py-2 px-3 border-top d-flex justify-content-between align-items-center" style="border-radius: 0 0 12px 12px;">
                            <span class="text-muted small"><i class="fas fa-info-circle mr-1 text-info"></i> Click red ✕ icon to remove</span>
                            <span class="font-weight-bold text-dark small" id="cart_summary_text">0 items selected</span>
                        </div>
                    </div>
                </div>

                <!-- ==========================================
                     COLUMN 3: Financial Settlement & Checkout
                     ========================================== -->
                <div class="col-12 col-lg-4 col-xl-4 mb-3">
                    <!-- Top Action Buttons (Above Payment & Checkout Card) -->
                    <div class="pos-top-actions mb-3">
                        <!-- Save and Print (Primary Action) -->
                        <div class="cashsubmit2 mb-2">
                            <button type="submit" name="form_submit" value="saveandprint" id="submit2" class="btn btn-pos-save-print btn-block">
                                <i class="fas fa-print mr-2"></i><?php echo lang('save_and_print'); ?> <small class="ml-1 opacity-75 font-weight-normal">[Ctrl+Enter]</small>
                            </button>
                        </div>
                        <div class="cardsubmit3 d-none mb-2">
                            <button type="submit" name="form_submit" value="saveandprint" id="submit-btn2" class="btn btn-pos-save-print btn-block" <?php if ($settings->payment_gateway == 'Stripe') { ?>onClick="stripePay(event);" <?php } ?>>
                                <i class="fas fa-print mr-2"></i><?php echo lang('save_and_print'); ?> <small class="ml-1 opacity-75 font-weight-normal">[Ctrl+Enter]</small>
                            </button>
                        </div>

                        <div class="row no-gutters">
                            <!-- Save Only -->
                            <div class="col-6 pr-1">
                                <div class="cashsubmit mb-2">
                                    <button type="submit" name="form_submit" value="save" id="submit1" class="btn btn-pos-save btn-block">
                                        <i class="fas fa-check mr-1 text-success"></i><?php echo lang('save'); ?>
                                    </button>
                                </div>
                                <div class="cardsubmit d-none mb-2">
                                    <button type="submit" name="form_submit" value="save" id="submit-btn" class="btn btn-pos-save btn-block" <?php if ($settings->payment_gateway == 'Stripe') { ?>onClick="stripePay(event);" <?php } ?>>
                                        <i class="fas fa-check mr-1 text-success"></i><?php echo lang('save'); ?>
                                    </button>
                                </div>
                            </div>

                            <!-- Save as Draft -->
                            <div class="col-6 pl-1">
                                <div class="mb-2">
                                    <button type="submit" name="form_submit" value="save_as_draft" id="save_as_draft" class="btn btn-pos-draft btn-block">
                                        <i class="fas fa-file-alt mr-1"></i><?php echo lang('save_as_draft'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment & Checkout Card -->
                    <div class="pos-card pos-checkout-card">
                        <div class="pos-card-header">
                            <h2 class="pos-card-title">
                                <i class="far fa-file-alt text-success mr-2" style="background: none; font-size: 16px;"></i><?php echo lang('payment'); ?> &amp; <?php echo lang('checkout'); ?>
                            </h2>
                            <span class="pos-status-badge badge-due" id="badge_due_status">Full Due: <?php echo $settings->currency; ?> 0.00</span>
                        </div>

                        <div class="card-body p-3">
                            <div class="checkout-table-rows">
                                <!-- 1. Sub Total -->
                                <div class="checkout-row">
                                    <div class="checkout-label"><?php echo lang('sub_total'); ?></div>
                                    <div class="checkout-field">
                                        <input type="text" class="form-control pos-input-readonly text-right font-weight-bold" name="subtotal" id="subtotal" value='<?php
                                            if (!empty($payment->amount)) {
                                                echo $payment->amount;
                                            } elseif (!empty($draft->amount)) {
                                                echo $draft->amount;
                                            }
                                        ?>' placeholder="0" readonly style="font-family: var(--pos-font-mono);">
                                    </div>
                                </div>

                                <!-- 2. Discount -->
                                <div class="checkout-row">
                                    <div class="checkout-label"><?php echo lang('discount'); ?></div>
                                    <div class="checkout-field d-flex align-items-center">
                                        <input type="number" step="0.01" class="form-control text-right mr-1" name="discount" id="dis_id" value='<?php
                                            if (!empty($payment->discount)) {
                                                $discount = explode('*', $payment->discount);
                                                echo $discount[0];
                                            } elseif (!empty($draft->discount)) {
                                                $discount = explode('*', $draft->discount);
                                                echo $discount[0];
                                            } else {
                                                echo '0';
                                            }
                                        ?>' placeholder="0" style="font-family: var(--pos-font-mono); width: 50%;">
                                        <div class="input-group input-group-sm pos-percent-group">
                                            <input type="number" min="0" max="100" step="0.01" class="form-control text-right" name="percent_discount" id="dis_id_percent" value='<?php
                                                if (!empty($payment->percent_discount)) {
                                                    $percent_discount = explode('*', $payment->percent_discount);
                                                    echo $percent_discount[0];
                                                } elseif (!empty($draft->percent_discount)) {
                                                    $percent_discount = explode('*', $draft->percent_discount);
                                                    echo $percent_discount[0];
                                                } else {
                                                    echo $settings->discount_percent;
                                                }
                                            ?>' placeholder="0.00" style="font-family: var(--pos-font-mono);">
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-transparent border-left-0 text-muted">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3. VAT / Tax -->
                                <div class="checkout-row">
                                    <div class="checkout-label"><?php echo lang('vat'); ?></div>
                                    <div class="checkout-field d-flex align-items-center">
                                        <input type="number" step="0.01" class="form-control text-right mr-1" name="vat_amount" id="vat_amount" value='<?php
                                            if (!empty($payment->vat)) {
                                                echo $payment->vat;
                                            } elseif (!empty($draft->vat)) {
                                                echo $draft->vat;
                                            } else {
                                                echo '0';
                                            }
                                        ?>' placeholder="0" style="font-family: var(--pos-font-mono); width: 50%;">
                                        <div class="input-group input-group-sm pos-percent-group">
                                            <input type="number" min="0" max="100" step="0.01" class="form-control text-right" name="vat" id="vat" value='<?php
                                                if (!empty($payment->vat_amount_percent)) {
                                                    echo $payment->vat_amount_percent;
                                                } elseif (!empty($draft->vat_amount_percent)) {
                                                    echo $draft->vat_amount_percent;
                                                } else {
                                                    echo $settings->vat;
                                                }
                                            ?>' placeholder="0.00" style="font-family: var(--pos-font-mono);">
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-transparent border-left-0 text-muted">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 4. Grand Total / Gross Total -->
                                <div class="checkout-row">
                                    <div class="checkout-label"><?php echo lang('gross_total'); ?></div>
                                    <div class="checkout-field">
                                        <input type="text" class="form-control pos-input-readonly text-right font-weight-bold" id="gross_display_field" value='<?php
                                            if (!empty($payment->gross_total)) {
                                                echo $payment->gross_total;
                                            } elseif (!empty($draft->gross_total)) {
                                                echo $draft->gross_total;
                                            } else {
                                                echo '0';
                                            }
                                        ?>' placeholder="0" readonly style="font-family: var(--pos-font-mono); font-size: 14px;">
                                        <input type="hidden" name="grsss" id="gross" value='<?php
                                            if (!empty($payment->gross_total)) {
                                                echo $payment->gross_total;
                                            } elseif (!empty($draft->gross_total)) {
                                                echo $draft->gross_total;
                                            }
                                        ?>'>
                                    </div>
                                </div>

                                <!-- 5. Note / Remarks -->
                                <div class="checkout-row">
                                    <div class="checkout-label"><?php echo lang('note'); ?></div>
                                    <div class="checkout-field">
                                        <textarea class="form-control pos-note-input" name="remarks" id="remarks" rows="1" placeholder=""><?php
                                            if (!empty($payment->remarks)) {
                                                echo trim($payment->remarks);
                                            } elseif (!empty($draft->remarks)) {
                                                echo trim($draft->remarks);
                                            }
                                        ?></textarea>
                                    </div>
                                </div>

                                <!-- 6. Deposited Amount -->
                                <div class="checkout-row">
                                    <div class="checkout-label">
                                        <span><?php
                                            if (empty($payment)) {
                                                echo lang('deposited_amount');
                                            } else {
                                                echo lang('deposit') . ' 1 (' . date('d/m/Y', $payment->date) . ')';
                                            };
                                        ?></span>
                                    </div>
                                    <div class="checkout-field">
                                        <input type="text" class="form-control text-right font-weight-bold" name="amount_received" id="amount_received" value='<?php
                                            if (!empty($payment->amount_received)) {
                                                echo $payment->amount_received;
                                            }
                                        ?>' placeholder="0" style="font-family: var(--pos-font-mono); font-size: 14px;" <?php
                                            if (!empty($payment->deposit_type)) {
                                                if ($payment->deposit_type == 'Card') {
                                                    echo 'readonly';
                                                }
                                            }
                                        ?>>
                                    </div>
                                </div>

                                <!-- 7. Due Balance Indicator -->
                                <div class="checkout-row">
                                    <div class="checkout-label"><?php echo lang('due'); ?></div>
                                    <div class="checkout-field">
                                        <input type="text" class="form-control pos-input-readonly text-right font-weight-bold" name="due" id="due" value='<?php
                                            if (!empty($payment)) {
                                                $deposit = $this->finance_model->getDepositByInvoiceId($payment->id);
                                                if (!empty($deposit)) {
                                                    foreach ($deposit as $depos) {
                                                        $deposits[] = $depos->deposited_amount;
                                                    }
                                                    $depos_amount = array_sum($deposits);
                                                } else {
                                                    $depos_amount = 0;
                                                }
                                                echo $depos_amount;
                                            } elseif (!empty($draft->gross_total)) {
                                                if (!empty($draft->amount_received)) {
                                                    echo ($draft->gross_total - $draft->amount_received);
                                                } else {
                                                    echo $draft->gross_total;
                                                }
                                            } else {
                                                echo '0';
                                            }
                                        ?>' placeholder="0" readonly style="font-family: var(--pos-font-mono); font-size: 14px;">
                                    </div>
                                </div>

                                <!-- 8. Payment Method -->
                                <div class="checkout-row">
                                    <div class="checkout-label"><?php echo lang('type'); ?></div>
                                    <div class="checkout-field">
                                        <select class="form-control js-example-basic-single selecttype" id="selecttype" name="deposit_type" value=''>
                                            <?php if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) { ?>
                                                <option value="Cash" <?php if (!empty($payment->deposit_type) && $payment->deposit_type == 'Cash') { echo 'selected'; } ?>><?php echo lang('cash'); ?></option>
                                                <option value="Card" <?php if (!empty($payment->deposit_type) && $payment->deposit_type == 'Card') { echo 'selected'; } ?>><?php echo lang('card'); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Preset Discount Chips & Full-Paid Shortcut Bar -->
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                <div class="d-flex align-items-center" id="discount_presets">
                                    <span class="text-muted small mr-1 font-weight-bold">Disc:</span>
                                    <span class="discount-chip mr-1" data-percent="0">0%</span>
                                    <span class="discount-chip mr-1" data-percent="5">5%</span>
                                    <span class="discount-chip mr-1" data-percent="10">10%</span>
                                    <span class="discount-chip mr-1" data-percent="15">15%</span>
                                    <span class="discount-chip" data-percent="20">20%</span>
                                </div>
                                <div class="d-flex">
                                    <button type="button" class="btn-quick-full-pay mr-1" id="btn_quick_full_paid" title="Alt+F">
                                        Full Paid
                                    </button>
                                    <button type="button" class="btn-quick-zero-pay" id="btn_quick_zero_paid" title="Alt+D">
                                        Full Due
                                    </button>
                                </div>
                            </div>

                            <?php $payment_gateway = $settings->payment_gateway; ?>

                            <!-- Insurance Div -->
                            <div class="my-3 <?php if (empty($payment) || empty($payment->deposit_type) || $payment->deposit_type != 'Insurance') { echo 'hidden'; } ?> insurance_div">
                                <div class="form-group mb-2">
                                    <label class="pos-label mb-1"><?php echo lang('insurance'); ?> Company</label>
                                    <div class="company_div">
                                        <select class="form-control js-example-basic-single" name="insurance_company" id="insurance_company" value=''>
                                            <option value="">Company name</option>
                                            <?php foreach ($insurance_companys as $insurance_company) { ?>
                                                <option value="<?php echo $insurance_company->id; ?>" <?php
                                                    if (!empty($setval)) {
                                                        if ($insurance_company->id == set_value('insurance_company')) {
                                                            echo 'selected';
                                                        }
                                                    }
                                                    if (!empty($payment->insurance_company)) {
                                                        if ($insurance_company->id == $payment->insurance_company) {
                                                            echo 'selected';
                                                        }
                                                    }
                                                ?>>
                                                    <?php echo $insurance_company->name; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-2">
                                    <label class="pos-label mb-1"><?php echo lang('insurance_details'); ?></label>
                                    <textarea class="form-control" name="insurance_details" rows="2"><?php
                                        if (!empty($payment->insurance_details)) {
                                            echo trim($payment->insurance_details);
                                        } elseif (!empty($draft->insurance_details)) {
                                            echo trim($draft->insurance_details);
                                        }
                                    ?></textarea>
                                </div>
                            </div>

                            <!-- Card Payment Div -->
                            <div class="cardPayment p-3 my-3 bg-light rounded border" style="display: none;">
                                <div class="mb-2 text-center">
                                    <label class="pos-label justify-content-center mb-1"><?php echo lang('accepted'); ?> <?php echo lang('cards'); ?></label>
                                    <img src="uploads/card.png" class="img-fluid" style="max-height: 28px;">
                                </div>

                                <?php if ($payment_gateway == 'PayPal') { ?>
                                    <div class="form-group mb-2">
                                        <label class="pos-label small"><?php echo lang('card'); ?> <?php echo lang('type'); ?></label>
                                        <select class="form-control form-control-sm" name="card_type">
                                            <option value="Mastercard"><?php echo lang('mastercard'); ?></option>
                                            <option value="Visa"><?php echo lang('visa'); ?></option>
                                            <option value="American Express"><?php echo lang('american_express'); ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="pos-label small"><?php echo lang('name'); ?></label>
                                        <input type="text" id="cardholder" class="form-control form-control-sm pay_in" name="cardholder" value='' placeholder="Name on card">
                                    </div>
                                <?php } ?>

                                <?php if ($payment_gateway != 'Pay U Money' && $payment_gateway != 'Paystack' && $payment_gateway != 'SSLCOMMERZ' && $payment_gateway != 'Paytm') { ?>
                                    <div class="form-group mb-2">
                                        <label class="pos-label small"><?php echo lang('card'); ?> <?php echo lang('number'); ?></label>
                                        <input type="text" id="card" class="form-control form-control-sm pay_in" name="card_number" value='' placeholder="Card number">
                                    </div>
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label class="pos-label small"><?php echo lang('expire'); ?> <?php echo lang('date'); ?></label>
                                            <input type="text" class="form-control form-control-sm pay_in" id="expire" data-date-format="MM YY" placeholder="MM/YY" name="expire_date" maxlength="7">
                                        </div>
                                        <div class="col-6">
                                            <label class="pos-label small"><?php echo lang('cvv'); ?></label>
                                            <input type="text" class="form-control form-control-sm pay_in" id="cvv" maxlength="3" name="cvv" value='' placeholder="CVV">
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- Existing Deposits (For Invoice Edit View) -->
                            <?php
                            if (!empty($payment)) {
                                $deposits = $this->finance_model->getDepositByPaymentId($payment->id);
                                $i = 1;
                                foreach ($deposits as $deposit) {
                                    if (empty($deposit->amount_received_id)) {
                                        $i = $i + 1; ?>
                                        <div class="form-group mb-2 row align-items-center">
                                            <label class="col-sm-4 col-form-label pos-label small mb-0">
                                                <?php echo lang('deposit') . ' ' . $i; ?><br>
                                                <span class="text-muted font-weight-normal"><?php echo date('d/m/Y', $deposit->date); ?></span>
                                            </label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control form-control-sm pay_in" name="deposit_edit_amount[]" id="amount_received" value='<?php echo $deposit->deposited_amount; ?>' <?php
                                                    if ($deposit->deposit_type == 'Card') {
                                                        echo 'readonly';
                                                    } ?>>
                                                <input type="hidden" name="deposit_edit_id[]" value='<?php echo $deposit->id; ?>'>
                                            </div>
                                        </div>
                            <?php
                                    }
                                }
                            }
                            ?>

                            <input type="hidden" name="id" id="id_pay" value='<?php
                                if (!empty($payment->id)) {
                                    echo $payment->id;
                                }
                            ?>'>
                            <input type="hidden" name="draft_id" id="draft_id" value='<?php
                                if (!empty($draft->id)) {
                                    echo $draft->id;
                                }
                            ?>'>

                            <!-- Internal Commission Summary (Hospital Accounting) -->
                            <div class="commission-collapse-wrapper mt-3" id="internal_commission_card">
                                <div class="card border shadow-none">
                                    <div class="card-header bg-transparent p-2 border-0 d-flex justify-content-between align-items-center" role="button" data-toggle="collapse" data-target="#commSummaryContent" aria-expanded="false" style="cursor: pointer;">
                                        <span class="font-weight-bold text-dark small text-uppercase">
                                            <i class="fas fa-chart-pie text-primary mr-1"></i> <?php echo lang('internal_commission_summary'); ?>
                                        </span>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-secondary mr-2" style="font-size: 0.65rem;"><?php echo lang('hospital_accounting'); ?></span>
                                            <i class="fas fa-chevron-down text-muted small chevron-indicator"></i>
                                        </div>
                                    </div>
                                    <div class="collapse" id="commSummaryContent">
                                        <div class="card-body p-2 pt-0">
                                            <div class="d-flex justify-content-between py-1 small text-muted">
                                                <span>Gross Amount:</span>
                                                <span class="font-weight-bold text-dark" id="comm_gross_display"><?php echo $settings->currency; ?> 0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-1 small text-danger">
                                                <span>Discount (-):</span>
                                                <span class="font-weight-bold" id="comm_discount_display">-<?php echo $settings->currency; ?> 0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-1 small border-top font-weight-bold text-dark">
                                                <span>Final Net Amount:</span>
                                                <span id="comm_net_invoice_display"><?php echo $settings->currency; ?> 0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-1 small bg-white px-2 rounded border my-1 text-primary">
                                                <span class="font-weight-bold"><i class="fas fa-bullseye mr-1"></i> Commission Base:</span>
                                                <span class="font-weight-bold" id="comm_base_display"><?php echo $settings->currency; ?> 0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-1 small border-bottom text-info">
                                                <span><?php echo lang('doctor_commission'); ?> (-):</span>
                                                <span class="font-weight-bold" id="comm_doctor_display"><?php echo $settings->currency; ?> 0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-1 small border-bottom text-warning">
                                                <span><?php echo lang('referral_commission'); ?> (-):</span>
                                                <span class="font-weight-bold" id="comm_referral_display"><?php echo $settings->currency; ?> 0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-1 small pt-2 font-weight-bold text-success">
                                                <span><?php echo lang('net_hospital_revenue'); ?>:</span>
                                                <span id="comm_net_display"><?php echo $settings->currency; ?> 0.00</span>
                                            </div>
                                            <div class="text-muted mt-1" style="font-size: 0.68rem; font-style: italic;">
                                                * Commissions calculated proportionally on Final Net Invoice Amount after discount.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </form>
        </div>
    </section>



    <!-- Modals for Inline Master Data Creation -->
    <?php if ($can_create_patient) { $this->load->view('modal_add_patient'); } ?>
    <?php if ($can_create_doctor) { $this->load->view('modal_add_doctor'); } ?>
    <?php if ($can_create_referrer) { $this->load->view('modal_add_referrer'); } ?>

</div>

<style>
    #my_multi_select3 {
        display: none;
    }
</style>

<?php
if (!empty($draft->doctor)) {
    if ($draft->doctor == 'add_new') {
        $add_doctor = 'yes';
    } else {
        $add_doctor = 'no';
    }
} else {
    $add_doctor = 'no';
}

if (!empty($gateway->publish)) {
    $gateway_stripe = $gateway->publish;
} else {
    $gateway_stripe = '';
}
?>

<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">
    var select_doctor = "<?php echo lang('select_doctor'); ?>";
</script>
<script type="text/javascript">
    var select_patient = "<?php echo lang('select_patient'); ?>";
</script>
<script type="text/javascript">
    var discount_type = "<?php echo $discount_type; ?>";
</script>
<script type="text/javascript">
    var add_doctor = "<?php echo $add_doctor; ?>";
</script>
<script type="text/javascript">
    var currency = "<?php echo $settings->currency; ?>";
</script>
<script type="text/javascript">
    var publish = "<?php echo $gateway_stripe; ?>";
</script>
<script src="common/js/moment.min.js"></script>
<script type="text/javascript">
    var payment_gateway = "<?php echo $settings->payment_gateway; ?>";
</script>
<script src="common/extranal/js/finance/add_payment_view.js?v=pos-2026-v12"></script>

<!-- POS UI Reactive Helper Script -->
<script type="text/javascript">
$(document).ready(function() {
    // 1. Live Clock
    function updateClock() {
        var now = new Date();
        var timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        $("#pos_live_clock").text(timeStr);
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 2. Sync Cart State & Totals
    function updatePOSState() {
        var itemCount = $(".qfloww .remove1").length;
        if (itemCount > 0) {
            $("#empty_cart_placeholder").hide();
            $("#cart_badge").text(itemCount + (itemCount === 1 ? " item" : " items"));
            $("#cart_summary_text").text(itemCount + (itemCount === 1 ? " item selected" : " items selected"));
            $("#btn_clear_cart").removeClass("d-none");
        } else {
            $("#empty_cart_placeholder").show();
            $("#cart_badge").text("0 items");
            $("#cart_summary_text").text("0 items selected");
            $("#btn_clear_cart").addClass("d-none");
        }

        // Grand Total Display
        var grossVal = parseFloat($("#gross").val()) || 0;
        $("#gross_display_field").val(grossVal.toFixed(2));
        $("#grand_total_display").text(currency + " " + grossVal.toFixed(2));

        // Due Status Badge
        var recVal = parseFloat($("#amount_received").val()) || 0;
        var dueVal = parseFloat($("#due").val());
        
        if (grossVal === 0 && itemCount === 0) {
            $("#badge_due_status").removeClass("badge-paid").addClass("badge-due").text("Full Due: " + currency + " 0.00");
        } else if (grossVal > 0 && recVal >= grossVal) {
            $("#badge_due_status").removeClass("badge-due").addClass("badge-paid").text("✓ Paid in Full");
        } else if (grossVal > 0 && recVal === 0) {
            $("#badge_due_status").removeClass("badge-paid").addClass("badge-due").text("Full Due: " + currency + " " + grossVal.toFixed(2));
        } else if (grossVal > 0 && recVal < grossVal) {
            var actualDue = grossVal - recVal;
            $("#badge_due_status").removeClass("badge-paid").addClass("badge-due").text("Due: " + currency + " " + actualDue.toFixed(2));
        } else {
            $("#badge_due_status").removeClass("badge-paid").addClass("badge-due").text("Full Due: " + currency + " 0.00");
        }
    }

    // Stepper Plus Button
    $(document).on("click", ".btn-qty-plus", function(e) {
        e.preventDefault();
        e.stopPropagation();
        var idd = $(this).data("idd");
        var input = $("#idinput-" + idd);
        var currentVal = parseInt(input.val(), 10) || 0;
        input.val(currentVal + 1).trigger("keyup");
    });

    // Stepper Minus Button
    $(document).on("click", ".btn-qty-minus", function(e) {
        e.preventDefault();
        e.stopPropagation();
        var idd = $(this).data("idd");
        var input = $("#idinput-" + idd);
        var currentVal = parseInt(input.val(), 10) || 1;
        if (currentVal > 1) {
            input.val(currentVal - 1).trigger("keyup");
        }
    });

    // Quick Discount Preset Chips
    $(document).on("click", ".discount-chip", function() {
        var pct = $(this).data("percent");
        $(".discount-chip").removeClass("active");
        $(this).addClass("active");
        $("#dis_id_percent").val(pct).trigger("keyup");
        updatePOSState();
    });

    // Quick Full Paid Button
    $("#btn_quick_full_paid").on("click", function() {
        var grossVal = parseFloat($("#gross").val()) || 0;
        $("#amount_received").val(grossVal.toFixed(2)).trigger("keyup");
        updatePOSState();
    });

    // Quick Zero Paid (Full Due) Button
    $("#btn_quick_zero_paid").on("click", function() {
        $("#amount_received").val("0").trigger("keyup");
        updatePOSState();
    });

    // Clear Cart Button
    $("#btn_clear_cart").on("click", function() {
        if (confirm("Are you sure you want to clear all items in the cart?")) {
            clearPOSCartStorage();
            $(".qfloww .remove_attr").each(function() {
                $(this).trigger("click");
            });
            updatePOSState();
        }
    });

    // Automatic Search Reset & Smart Optgroup Filtering Helper
    function resetPOSSearch() {
        var $search = $(".ms-container .search-input");
        if ($search.length) {
            $search.val("");
            $(".ms-search-clear").hide();
            $(".ms-selectable .ms-elem-selectable").show();
            $(".ms-selectable .ms-optgroup-container").show();
            $(".ms-selectable .ms-optgroup-label").show();
        }
    }

    // Filter Items & Hide Empty Category Folders
    function applyPOSSearchFilter(query) {
        query = $.trim(query).toLowerCase();
        var $clearBtn = $(".ms-search-clear");
        
        if (query === "") {
            $clearBtn.hide();
            $(".ms-selectable .ms-elem-selectable").show();
            $(".ms-selectable .ms-optgroup-container").show();
            $(".ms-selectable .ms-optgroup-label").show();
            return;
        }
        
        $clearBtn.show();
        
        $(".ms-selectable .ms-optgroup-container").each(function() {
            var $grp = $(this);
            var hasMatch = false;
            $grp.find(".ms-elem-selectable").each(function() {
                var itemText = $(this).text().toLowerCase();
                if (itemText.indexOf(query) !== -1) {
                    $(this).show();
                    hasMatch = true;
                } else {
                    $(this).hide();
                }
            });
            
            if (hasMatch) {
                $grp.show();
                $grp.find(".ms-optgroup-label").show();
            } else {
                $grp.hide();
            }
        });

        // Top level items not in optgroups
        $(".ms-selectable > ul.ms-list > .ms-elem-selectable").each(function() {
            var itemText = $(this).text().toLowerCase();
            if (itemText.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // CRITICAL USER FEATURE: Automatically Clear and Reset Search when an item is selected
    $(document).on("click", ".ms-selectable .ms-elem-selectable", function() {
        setTimeout(function() {
            resetPOSSearch();
        }, 50);
    });

    $("#my_multi_select3").on("change", function() {
        setTimeout(function() {
            resetPOSSearch();
        }, 50);
    });

    // Clear Search Button (✕)
    $(document).on("click", ".ms-search-clear", function(e) {
        e.preventDefault();
        e.stopPropagation();
        resetPOSSearch();
        var $search = $(".ms-container .search-input");
        if ($search.length) {
            $search.focus();
        }
    });

    // Live search input listener
    $(document).on("keyup input", ".ms-container .search-input", function(e) {
        if (e.key === "Escape") {
            resetPOSSearch();
            return;
        }
        applyPOSSearchFilter($(this).val());
    });


    // Keyboard Shortcuts
    $(document).on("keydown", function(e) {
        // F2: Focus Test Search
        if (e.key === "F2") {
            e.preventDefault();
            var searchInput = $(".ms-container .search-input");
            if (searchInput.length) {
                searchInput.focus().select();
            }
        }
        // Alt + N: New Patient Modal
        else if (e.altKey && (e.key === "n" || e.key === "N")) {
            e.preventDefault();
            $("#btn_open_patient_modal").trigger("click");
        }
        // Alt + F: Full Paid
        else if (e.altKey && (e.key === "f" || e.key === "F")) {
            e.preventDefault();
            $("#btn_quick_full_paid").trigger("click");
        }
        // Alt + D: Full Due
        else if (e.altKey && (e.key === "d" || e.key === "D")) {
            e.preventDefault();
            $("#btn_quick_zero_paid").trigger("click");
        }
        // Ctrl + Enter: Save and Print
        else if (e.ctrlKey && e.key === "Enter") {
            e.preventDefault();
            $("#submit2").trigger("click");
        }
    });

    // =========================================================================
    // 3. Persistent Cart Storage across Page Reloads (Auto-Save & Restore)
    // =========================================================================
    var isNewInvoice = <?php echo (empty($payment->id) && empty($draft->id)) ? 'true' : 'false'; ?>;
    var STORAGE_KEY = "lifecare_pos_active_cart_state";
    var isRestoringCart = false;

    function clearPOSCartStorage() {
        if (isNewInvoice) {
            try {
                localStorage.removeItem(STORAGE_KEY);
            } catch (e) {}
        }
    }

    function savePOSCartToStorage() {
        if (!isNewInvoice || isRestoringCart) return;
        try {
            var items = [];
            $(".qfloww .remove1").each(function() {
                var divId = $(this).attr("id"); // "id-div" + idd
                if (divId) {
                    var idd = divId.replace("id-div", "");
                    var qty = parseInt($("#idinput-" + idd).val(), 10) || 1;
                    items.push({ id: String(idd), qty: qty });
                }
            });

            if (items.length === 0) {
                localStorage.removeItem(STORAGE_KEY);
                return;
            }

            var patientText = "";
            var patientData = (typeof $ !== 'undefined' && $("#pos_select").hasClass("select2-hidden-accessible")) ? $("#pos_select").select2('data') : null;
            if (patientData && patientData.length && patientData[0].text) {
                patientText = patientData[0].text;
            }

            var doctorText = "";
            var docData = (typeof $ !== 'undefined' && $("#add_doctor").hasClass("select2-hidden-accessible")) ? $("#add_doctor").select2('data') : null;
            if (docData && docData.length && docData[0].text) {
                doctorText = docData[0].text;
            }

            var cartData = {
                timestamp: new Date().getTime(),
                patient_id: $("#pos_select").val() || "",
                patient_text: patientText,
                doctor_id: $("#add_doctor").val() || "",
                doctor_text: doctorText,
                referrer_id: $("#add_referrer").val() || "",
                discount_percent: $("#dis_id_percent").val() || "0",
                discount: $("#dis_id").val() || "0",
                amount_received: $("#amount_received").val() || "0",
                deposit_type: $("#selecttype").val() || "Cash",
                remarks: $("#remarks").val() || "",
                items: items
            };

            localStorage.setItem(STORAGE_KEY, JSON.stringify(cartData));
        } catch (e) {
            console.error("Failed to save POS cart to storage:", e);
        }
    }

    function restorePOSCartFromStorage() {
        if (!isNewInvoice) return;
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return;

            var cartData = JSON.parse(raw);
            if (!cartData || !cartData.items || !cartData.items.length) return;

            // Expire after 24 hours
            var now = new Date().getTime();
            if (cartData.timestamp && (now - cartData.timestamp > 86400000)) {
                localStorage.removeItem(STORAGE_KEY);
                return;
            }

            isRestoringCart = true;

            // 1. Restore Patient
            if (cartData.patient_id && $("#pos_select").length) {
                if (!$('#pos_select').find("option[value='" + cartData.patient_id + "']").length && cartData.patient_text) {
                    var newOption = new Option(cartData.patient_text, cartData.patient_id, true, true);
                    $('#pos_select').append(newOption).trigger('change');
                } else {
                    $('#pos_select').val(cartData.patient_id).trigger('change');
                }
            }

            // 2. Restore Doctor
            if (cartData.doctor_id && $("#add_doctor").length) {
                if (!$('#add_doctor').find("option[value='" + cartData.doctor_id + "']").length && cartData.doctor_text) {
                    var docOption = new Option(cartData.doctor_text, cartData.doctor_id, true, true);
                    $('#add_doctor').append(docOption).trigger('change');
                } else {
                    $("#add_doctor").val(cartData.doctor_id).trigger("change");
                }
            }

            // 3. Restore Referrer
            if (cartData.referrer_id && $("#add_referrer").length) {
                $("#add_referrer").val(cartData.referrer_id).trigger("change");
            }

            // 4. Restore Items in MultiSelect
            var itemIds = [];
            var qtyMap = {};
            for (var i = 0; i < cartData.items.length; i++) {
                var it = cartData.items[i];
                itemIds.push(String(it.id));
                qtyMap[String(it.id)] = it.qty || 1;
            }

            if ($("#my_multi_select3").length && itemIds.length) {
                for (var j = 0; j < itemIds.length; j++) {
                    $("#my_multi_select3 option[value='" + itemIds[j] + "']").prop("selected", true);
                    $("#my_multi_select3").multiSelect("select", itemIds[j]);
                }
                $("#my_multi_select3").trigger("change");

                setTimeout(function() {
                    for (var k in qtyMap) {
                        var $input = $("#idinput-" + k);
                        if ($input.length && qtyMap[k] > 1) {
                            $input.val(qtyMap[k]);
                        }
                    }

                    // Restore Discount
                    if (cartData.discount_percent && cartData.discount_percent !== "0") {
                        $("#dis_id_percent").val(cartData.discount_percent);
                        $(".discount-chip").removeClass("active");
                        $(".discount-chip[data-percent='" + cartData.discount_percent + "']").addClass("active");
                    } else if (cartData.discount && cartData.discount !== "0") {
                        $("#dis_id").val(cartData.discount);
                    }

                    // Restore Amount Received
                    if (cartData.amount_received && cartData.amount_received !== "0") {
                        $("#amount_received").val(cartData.amount_received);
                    }

                    // Restore Deposit Type
                    if (cartData.deposit_type && $("#selecttype").length) {
                        $("#selecttype").val(cartData.deposit_type).trigger("change");
                    }

                    // Restore Remarks / Note
                    if (cartData.remarks) {
                        $("#remarks").val(cartData.remarks);
                    }

                    // Re-trigger calculation on first item input to refresh all totals
                    var firstId = itemIds[0];
                    if ($("#idinput-" + firstId).length) {
                        $("#idinput-" + firstId).trigger("keyup");
                    } else if ($("#dis_id_percent").length) {
                        $("#dis_id_percent").trigger("keyup");
                    }

                    updatePOSState();
                    isRestoringCart = false;
                    savePOSCartToStorage();
                }, 250);
            } else {
                isRestoringCart = false;
            }
        } catch (e) {
            isRestoringCart = false;
            console.error("Failed to restore POS cart from storage:", e);
        }
    }

    // Watch for Cart DOM Mutations & Auto-Save
    var cartObserver = new MutationObserver(function() {
        updatePOSState();
        savePOSCartToStorage();
    });

    var qflowwElem = document.querySelector(".qfloww");
    if (qflowwElem) {
        cartObserver.observe(qflowwElem, { childList: true, subtree: true });
    }

    // Input listeners for live sync & Auto-Save
    $(document).on("keyup change", "#dis_id, #dis_id_percent, #vat, #vat_amount, #amount_received, input.remove, #pos_select, #add_doctor, #add_referrer, #selecttype, #remarks, textarea[name='remarks']", function() {
        setTimeout(function() {
            updatePOSState();
            savePOSCartToStorage();
        }, 50);
    });

    // Clear saved cart on invoice submission
    $(document).on("submit", "#editPaymentForm", function() {
        clearPOSCartStorage();
    });
    $(document).on("click", "#submit2, #submit, #save_as_draft, #submit-btn2", function() {
        clearPOSCartStorage();
    });

    // Initialize state & restore persisted cart on reload
    setTimeout(function() {
        updatePOSState();
        restorePOSCartFromStorage();
    }, 350);
});
</script>