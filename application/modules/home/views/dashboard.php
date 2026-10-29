<!DOCTYPE html>
<html lang="en" <?php
                if (!$this->ion_auth->in_group(array('superadmin'))) {

                  $this->db->where('hospital_id', $this->hospital_id);
                  $settings_lang = $this->db->get('settings')->row()->language;
                  if ($this->language == 'arabic') {
                ?> dir="rtl" <?php } else { ?> dir="ltr" <?php
                                                        }
                                                      } else {
                                                        $this->db->where('hospital_id', 'superadmin');
                                                        $settings_lang = $this->db->get('settings')->row()->language;
                                                        if ($this->language == 'arabic') {
                                                          ?> dir="rtl" <?php } else { ?> dir="ltr" <?php
                                                                                                  }
                                                                                                }
                                                                                                    ?>>

<head>
  <base href="<?php echo base_url(); ?>"> 
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php
  $class_name = $this->router->fetch_class();
  $class_name_lang = lang($class_name);
  if (empty($class_name_lang)) {
    $class_name_lang = $class_name;
  }
  ?>

  <title> <?php echo $class_name_lang; ?> | 
    <?php
    if ($this->ion_auth->in_group(array('superadmin'))) {
      $this->db->where('hospital_id', 'superadmin');
    } else {
      $this->db->where('hospital_id', $this->hospital_id);
    }
    ?>
    <?php
    $settings = $this->db->get('settings')->row();
    echo $settings->system_vendor;
    ?>
  </title>

  <!-- <link rel="stylesheet" href="common/css/bootstrap-select.min.css"> -->

  <!-- Google Fonts -->

  <!-- design the sidebar with more professional css  -->



  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="adminlte/plugins/jqvmap/jqvmap.min.css">
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>application/assets/css/enhanced-sidebar-styles.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>application/assets/css/enhanced-components-styles.css">
  <link rel="stylesheet" href="adminlte/plugins/daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="adminlte/plugins/summernote/summernote-bs4.min.css">


  <link rel="stylesheet" href="adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

  <link rel="stylesheet" href="adminlte/dist/css/changes.css?v=dt-compact">

  <link rel="stylesheet" href="adminlte/plugins/daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="adminlte/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
  <link rel="stylesheet" href="adminlte/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <link rel="stylesheet" href="adminlte/plugins/daterangepicker/daterangepicker.css">


  <link rel="stylesheet" href="adminlte/plugins/fullcalendar/main.css">
  <link rel="stylesheet" href="adminlte/plugins/flag-icon-css/css/flag-icon.min.css">

  <link rel="stylesheet" href="common/assets/bootstrap-datepicker/css/bootstrap-datepicker.css" />
  <link rel="stylesheet" type="text/css" href="common/assets/bootstrap-daterangepicker/daterangepicker-bs3.css" />
  <link rel="stylesheet" type="text/css" href="common/assets/bootstrap-datetimepicker/css/datetimepicker.css" />
  <link rel="stylesheet" href="common/assets/bootstrap-timepicker/compiled/timepicker.css" />
  <link rel="stylesheet" type="text/css" href="common/assets/jquery-multi-select/css/multi-select.css" />
  <link rel="stylesheet" type="text/css" href="common/css/lightbox.css" />


  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="adminlte/plugins/toastr/toastr.min.css">

  <!-- dropzonejs -->
  <link rel="stylesheet" href="adminlte/plugins/dropzone/min/dropzone.min.css">





  <?php

  if ($this->language == 'arabic') { ?>
    <link rel="stylesheet" href="adminlte/dist/css/rtl.css">
  <?php } ?>

  <!-- <link rel="stylesheet" href="common/css/bootstrap-select-country.min.css"> -->

  <link rel="stylesheet" href="common/assets/bootstrap-datepicker/css/bootstrap-datepicker.css" />

  <!-- Core jQuery Library loaded in head for all views and inline scripts -->
  <script src="common/js/jquery.js"></script>

</head>

<body class="hold-transition sidebar-mini <?php
                                          if ($this->ion_auth->user()->row()->sidebar != 1) {
                                            echo 'sidebar-collapse';
                                          }

                                          if ($this->session->userdata('darkMode') == 1) {
                                            echo 'dark-mode ';
                                          } else {
                                          }

                                          ?> ">



  <div id="loader" class="loader" style="display:none;"></div>
  <style>
    .loader {
      border: 16px solid #f3f3f3;
      /* Light grey */
      border-top: 16px solid #3498db;
      /* Blue */
      border-radius: 50%;
      width: 80px;
      height: 80px;
      animation: spin 1s linear infinite;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 9999;
    }

    /* Loader animation */
    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }
  </style>



  <div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header header-tight navbar navbar-expand navbar-transparent py-1 <?php
                                                                          if ($this->session->userdata('darkMode') == 1) {
                                                                            echo 'navbar-dark';
                                                                          } else {
                                                                            echo 'navbar-light';
                                                                          }
                                                                          ?>">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link collapse-server nav-link-enhanced" data-widget="pushmenu" href="#" role="button" title="<?php echo lang('toggle_sidebar'); ?>">
            <i class="fas fa-bars nav-icon"></i>
          </a>
        </li>


        <!-- <li class="nav-item d-none d-sm-inline-block">
          <a href="#" class="nav-link">Contact</a>
        </li> -->
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto align-items-center">

        <!-- Header Quick Action Buttons: New Invoice & Due Collection -->
        <?php if ($this->ion_auth->logged_in() && !$this->ion_auth->in_group(array('Patient', 'superadmin'))) { ?>
          <li class="nav-item d-none d-sm-flex align-items-center mr-2">
            <a href="finance/addPaymentView" class="nav-quick-btn nav-btn-invoice" title="<?php echo lang('new_invoice'); ?>">
              <i class="fas fa-plus mr-1"></i> New Invoice
            </a>
          </li>
          <li class="nav-item d-none d-sm-flex align-items-center mr-2">
            <a href="finance/dueCollection" class="nav-quick-btn nav-btn-due" title="Due Collection">
              <i class="fas fa-hand-holding-usd mr-1"></i> Due Collection
            </a>
          </li>
        <?php } ?>

        <!-- Search Section -->
        <li class="nav-item search-item">
          <div class="form-inline">
            <div class="input-group search-container" data-widget="sidebar-search">
              <input class="form-control form-control-sidebar search-input mr-2" style="height: 40px !important" type="search" placeholder="<?php echo lang('search'); ?>" aria-label="Search">
            </div>
          </div>
        </li>

        <!-- Timezone Section -->
        <?php if (!$this->ion_auth->in_group(array('superadmin'))) { ?>
        <li class="nav-item">
          <a href="#" id="timezone-display" class="nav-link nav-link-enhanced timezone-link" data-toggle="modal" data-target="#timezoneModal" title="<?php echo lang('timezone'); ?>">
            <i class="fas fa-clock nav-icon"></i>
            <span id="current-timezone" class="timezone-text"><?php echo $settings->timezone ?? 'UTC'; ?></span>
            <i class="fas fa-edit timezone-edit-icon"></i>
          </a>
        </li>
        <?php } ?>

        <?php $this->load->view('available'); ?>

        <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
          <li class="nav-item dropdown d-none d-md-block">
            <a class="nav-link nav-link-enhanced quick-actions-link" data-toggle="dropdown" href="#" title="<?php echo lang('quick_actions'); ?>">
              <i class="fas fa-bolt nav-icon"></i>
              <span class="badge badge-info navbar-badge quick-actions-badge"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

              <?php if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) { ?>
                <?php if (in_array('finance', $this->modules)) { ?>
                  <a href="finance/addPaymentView" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                      <div class="media-body">
                        <h3 class="dropdown-item-title">
                          <?php echo lang('add_payment'); ?>
                          <span class="float-right text-sm text-danger"><i class="fas fa-money-check"></i></span>
                        </h3>
                      </div>
                    </div>
                    <!-- Message End -->
                  </a>
                  <div class="dropdown-divider"></div>
                <?php } ?>
              <?php } ?>



              <?php if ($this->ion_auth->in_group(array('admin', 'Nurse', 'Accountant', 'Pharmacist', 'Doctor', 'Laboratorist', 'Receptionist'))) { ?>
                <?php if (in_array('appointment', $this->modules)) { ?>
                  <a href="appointment/addNewView" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                      <div class="media-body">
                        <h3 class="dropdown-item-title">
                          <?php echo lang('add_appointment'); ?>
                          <span class="float-right text-sm text-danger"><i class="fas fa-calendar-check"></i></span>
                        </h3>
                      </div>
                    </div>
                    <!-- Message End -->
                  </a>
                  <div class="dropdown-divider"></div>
                <?php } ?>
              <?php } ?>



              <?php if ($this->ion_auth->in_group(array('admin', 'Nurse', 'Accountant', 'Pharmacist', 'Doctor', 'Laboratorist', 'Receptionist'))) { ?>
                <?php if (in_array('patient', $this->modules)) { ?>
                  <a href="patient/addNewView" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                      <div class="media-body">
                        <h3 class="dropdown-item-title">
                          <?php echo lang('add_patient'); ?>
                          <span class="float-right text-sm text-danger"><i class="fas fa-user"></i></span>
                        </h3>
                      </div>
                    </div>
                    <!-- Message End -->
                  </a>
                  <div class="dropdown-divider"></div>
                <?php } ?>
              <?php } ?>



              <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                <?php if (in_array('doctor', $this->modules)) { ?>
                  <a href="doctor/addNewView" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                      <div class="media-body">
                        <h3 class="dropdown-item-title">
                          <?php echo lang('add_doctor'); ?>
                          <span class="float-right text-sm text-danger"><i class="fas fa-user"></i></span>
                        </h3>
                      </div>
                    </div>
                    <!-- Message End -->
                  </a>
                  <div class="dropdown-divider"></div>
                <?php } ?>
              <?php } ?>



              <?php if ($this->ion_auth->in_group(array('Doctor'))) { ?>
                <?php if (in_array('prescription', $this->modules)) { ?>
                  <a href="prescription/addPrescriptionView" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                      <div class="media-body">
                        <h3 class="dropdown-item-title">
                          <?php echo lang('add_prescription'); ?>
                          <span class="float-right text-sm text-danger"><i class="fas fa-user"></i></span>
                        </h3>
                      </div>
                    </div>
                    <!-- Message End -->
                  </a>
                  <div class="dropdown-divider"></div>
                <?php } ?>
              <?php } ?>

              <?php if ($this->ion_auth->in_group(array('admin', 'Nurse', 'Accountant', 'Pharmacist', 'Doctor', 'Laboratorist', 'Receptionist'))) { ?>
                <?php if (in_array('lab', $this->modules)) { ?>
                  <a href="lab" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                      <div class="media-body">
                        <h3 class="dropdown-item-title">
                          <?php echo lang('lab'); ?> <?php echo lang('reports'); ?>
                          <span class="float-right text-sm text-danger"><i class="fa fa-flask"></i></span>
                        </h3>
                      </div>
                    </div>
                    <!-- Message End -->
                  </a>
                  <div class="dropdown-divider"></div>
                <?php } ?>
              <?php } ?>

              <?php if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) { ?>
                <?php if (in_array('finance', $this->modules)) { ?>
                  <a href="finance/dueCollection" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                      <div class="media-body">
                        <h3 class="dropdown-item-title">
                          <?php echo lang('due_collection'); ?>
                          <span class="float-right text-sm text-danger"><i class="fas fa-money-check"></i></span>
                        </h3>
                      </div>
                    </div>
                    <!-- Message End -->
                  </a>
                  <div class="dropdown-divider"></div>
                <?php } ?>
              <?php } ?>




            </div>
          </li>
        <?php } ?>



        <!-- Messages Dropdown Menu -->

        <?php if ($this->ion_auth->in_group(array('admin', 'Nurse', 'Accountant', 'Pharmacist', 'Doctor', 'Laboratorist', 'Receptionist'))) { ?>
          <?php if (in_array('chat', $this->modules)) { ?>
            <li class="nav-item dropdown d-none d-md-block">
              <a class="nav-link nav-link-enhanced chat-link" href="chat" title="<?php echo lang('chat'); ?>">
                <i class="far fa-comments nav-icon"></i>
              </a>
            </li>
          <?php } ?>
        <?php } ?>
        <!-- Notifications Dropdown Menu -->





        <?php if ($this->ion_auth->in_group(array('admin', 'Accountant'))) : ?>
          <?php if (in_array('finance', $this->modules)) : ?>
            <li class="nav-item dropdown">
              <a class="nav-link nav-link-enhanced payment-link" data-toggle="dropdown" href="#" title="<?php echo lang('payment'); ?>">
                <i class="fas fa-credit-card nav-icon"></i>
                <?php
                $this->db->where('hospital_id', $this->hospital_id);
                $query = $this->db->get('payment');
                $query = $query->result();
                $payment_number = 0;
                foreach ($query as $payment) {
                  $payment_date = date('y/m/d', $payment->date);
                  if ($payment_date == date('y/m/d')) {
                    $payment_number++;
                  }
                }
                ?>
              </a>
              <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header"><?= $payment_number; ?>
                  <?php if ($payment_number <= 1) {
                    echo lang('payment_today');
                  } else {
                    echo lang('payments_today');
                  } ?>
                </span>
                <div class="dropdown-divider"></div>
                <a href="finance/payment" class="dropdown-item">
                  <?= lang('see_all_payments'); ?>
                  <span class="float-right text-muted text-sm"><?= ($payment_number > 0) ? 'Available' : 'Not Available'; ?></span>
                </a>
                <!-- Add more notifications or a footer link similar to "See All Notifications" if needed -->
              </div>
            </li>
          <?php endif; ?>
        <?php endif; ?>

        <?php if ($this->ion_auth->in_group(['admin', 'Accountant', 'Doctor', 'Nurse', 'Laboratorist'])) : ?>
          <?php if (in_array('patient', $this->modules)) : ?>
            <li class="nav-item dropdown">
              <a class="nav-link nav-link-enhanced patient-link" data-toggle="dropdown" href="#" title="<?php echo lang('patient'); ?>">
                <i class="fas fa-user-plus nav-icon"></i>
                <?php
                $this->db->where('hospital_id', $this->hospital_id);
                $this->db->where('add_date', date('m/d/y'));
                $query = $this->db->get('patient');
                $query = $query->result();
                $patient_number = count($query);
                ?>
              </a>
              <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">
                  <?= $patient_number; ?>
                  <?php if ($patient_number <= 1) : ?>
                    <?= lang('patient_registerred_today'); ?>
                  <?php else : ?>
                    <?= lang('patients_registerred_today'); ?>
                  <?php endif; ?>
                </span>
                <div class="dropdown-divider"></div>
                <a href="patient" class="dropdown-item">
                  <?= lang('see_all_patients'); ?>
                  <span class="float-right text-muted text-sm"><?= ($patient_number > 0) ? 'Available' : 'Not Available'; ?></span>
                </a>
                <!-- Add more notifications or a footer link similar to "See All Notifications" if needed -->
              </div>
            </li>
          <?php endif; ?>
        <?php endif; ?>

        <?php


        $languages = $this->db->get('language')->result();

        foreach ($languages as $language) {
          if ($this->language == $language->language) {
            $flagIcon = $language->flag_icon;
          }
        }

        ?>



        <!-- Language Dropdown Menu -->
        <?php if ($this->ion_auth->in_group(array('admin', 'superadmin'))) { ?>
          <li class="nav-item dropdown">
            <a class="nav-link nav-link-enhanced language-link" data-toggle="dropdown" href="#" title="<?php echo lang('language'); ?>">
              <i class="flag-icon flag-icon-<?php echo $flagIcon; ?> language-flag"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-0">

              <?php

              foreach ($languages as $language) {

              ?>
                <a href="settings/changeLanguageFlag?lang=<?php echo $language->language; ?>" class="dropdown-item <?php if ($this->language ==  $language->language) {
                                                                                                                      echo 'active';
                                                                                                                    } ?>">
                  <i class="flag-icon flag-icon-<?php echo $language->flag_icon; ?> mr-2"></i> <?php echo $language->language; ?>
                </a>
              <?php } ?>


            </div>
          </li>
        <?php } ?>

        <?php if ($this->ion_auth->in_group(array('Patient', 'Doctor'))) { ?>
          <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="">
              <i class="flag-icon flag-icon-<?php echo $flagIcon; ?>"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-0">


              <?php
              $languages = $this->db->get('language')->result();
              foreach ($languages as $language) {

              ?>
                <a href="profile/changeLanguageFlag?lang=<?php echo $language->language; ?>" class="dropdown-item <?php if ($this->language == $language->language) {
                                                                                                                    echo 'active';
                                                                                                                  } ?>">
                  <i class="flag-icon flag-icon-<?php echo $language->flag_icon; ?> mr-2"></i> <?php echo $language->language; ?>
                </a>


              <?php } ?>


            </div>
          </li>
        <?php } ?>

        <li class="nav-item d-none d-md-block">
          <a class="nav-link nav-link-enhanced fullscreen-link" title="<?php echo lang('full_screen'); ?>" data-widget="fullscreen" role="button">
            <i class="fas fa-expand-arrows-alt nav-icon"></i>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link nav-link-enhanced logout-link" title="<?php echo lang('log_out'); ?>" href="auth/logout" role="button">
            <i class="fas fa-sign-out-alt nav-icon"></i>
          </a>
        </li>
        <!-- <li class="nav-item d-flex align-items-center">
          <div class="custom-control custom-switch d-flex align-items-center">
            <input type="checkbox" class="custom-control-input me-2" id="darkModeToggle" <?php if ($this->session->userdata('darkMode') == 1) echo 'checked'; ?>>
            <label class="custom-control-label d-flex align-items-center" for="darkModeToggle">
              <i class="fas fa-moon text-secondary"></i>
            </label>
          </div>
        </li> -->



      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-1 sidebar-tight">
      <!-- Brand Logo -->
      <a href="home" class="brand-link sidebar-brand-compact bg-gradient-to-r from-indigo-900 to-purple-900 text-left">
        <?php
        $sidebar_brand_logo = file_exists('uploads/lifecare_logo_circle.png') ? 'uploads/lifecare_logo_circle.png' : (!empty($settings->logo_title) ? $settings->logo_title : 'uploads/logo.png');
        if (!$this->ion_auth->in_group(array('superadmin'))) { ?>
          <div class="d-flex align-items-center justify-content-start py-0 w-100">
            <img height="40" src="<?php echo $sidebar_brand_logo; ?>?v=<?php echo time(); ?>" alt="HMS" class="brand-image rounded-circle border border-white shadow-sm">
            <span class="brand-text text-white font-weight-bold text-uppercase sidebar-brand-title text-left"><?php echo $settings->title; ?></span>
          </div>
        <?php } else { ?>
          <div class="d-flex align-items-center justify-content-start py-0 w-100">
            <img height="40" src="<?php echo $sidebar_brand_logo; ?>?v=<?php echo time(); ?>" alt="HMS" class="brand-image rounded-circle border border-white shadow-sm">
            <span class="brand-text text-white font-weight-bold sidebar-brand-title-super text-left"><?php echo $settings->title; ?></span>
          </div>
        <?php } ?>
      </a>

      <!-- <div class="user-panel d-flex border-bottom-0">
        <div class="mt-2 ml-2">
          <?php
          $user_id = $this->ion_auth->get_user_id();
          $user_group = $this->ion_auth->get_users_groups($user_id)->row();
          $group_name = strtolower($user_group->name);

          if ($group_name === 'admin') {
            $table = 'users';
            $id_field = 'id';
          } elseif ($group_name === 'superadmin') {
            $table = 'superadmin';
            $id_field = 'ion_user_id';
          } else {
            $table = $group_name;
            $id_field = 'ion_user_id';
          }

          $user = $this->db->get_where($table, array($id_field => $user_id))->row();
          ?>
        </div>
        <div class="info mt-2">
          <a href="profile" class="d-block text-sm text-gray">
            <i class="fas fa-user-circle mr-2"></i>
            <?php
            $username = $this->ion_auth->user()->row()->username;
            echo !empty($username) ? $username : '';
            ?>
            - <small class="text-gray-300">
              <?php echo ucfirst($group_name); ?>
            </small>
          </a>
        </div>
      </div> -->

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->


        <!-- SidebarSearch Form -->
        <!-- <div class="form-inline">
          <div class="input-group" data-widget="sidebar-search">
            <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
              <button class="btn btn-sidebar">
                <i class="fas fa-search fa-fw"></i>
              </button>
            </div>
          </div>
        </div> -->

        <!-- Sidebar Menu -->
        <nav class="">
          <ul class="nav nav-pills nav-sidebar flex-column nav-compact" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->



            <?php $this->load->view('menu'); ?>

            <?php
            //  $this->load->view('menu_demo');
            ?>

          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>



    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark elevation-4">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->



  <style>
    .main-sidebar.sidebar-tight .nav-compact .nav-item > .nav-link {
      padding: 0.45rem 0.55rem;
      font-size: 0.875rem;
    }

    label {
      display: inline-block;
      margin-bottom: 0px;
      font-weight: 500 !important;
      padding-top: 5px;
    }
  </style>






  <style>
    .loader {
      border: 16px solid #f3f3f3;
      border-top: 16px solid #3498db;
      border-radius: 50%;
      width: 120px;
      height: 120px;
      animation: spin 1s linear infinite;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 9999;
      box-shadow: 0 0 20px rgba(52, 152, 219, 0.5);
    }

    @keyframes spin {
      0% {
        transform: translate(-50%, -50%) rotate(0deg);
      }

      100% {
        transform: translate(-50%, -50%) rotate(360deg);
      }
    }
  </style>


  <style>
    .main-sidebar.sidebar-tight .nav-sidebar .nav-item > .nav-link {
      padding: 0.45rem 0.55rem;
      font-size: 0.875rem;
      font-weight: 500;
    }

    label {
      display: inline-block;
      margin-bottom: 0.5rem;
      font-weight: 600 !important;
    }

    <?php if ($this->session->userdata('mode') != 'dark'): ?>label {
      font-weight: 400 !important;
    }

    <?php endif; ?>.content-wrapper {
      background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
    }

    .card {
      backdrop-filter: blur(10px);
      background-color: rgba(255, 255, 255, 0.8);
    }

    .btn-primary {
      border: none;
    }



    .table-striped tbody tr:nth-of-type(odd) {
      background-color: rgba(0, 0, 0, 0.05);
    }

    .navbar-nav .nav-link {
      position: relative;
      overflow: hidden;
    }

    .navbar-nav .nav-link::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 2px;
      background-color: var(--accent-color);
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }

    .navbar-nav .nav-link:hover::after {
      transform: scaleX(1);
    }

    /* Add more extreme styles as needed */
  </style>

  <style>
    :root {
      --primary-color: #3498db;
      --secondary-color: #2ecc71;
      --accent-color: #e74c3c;
      --text-color: #34495e;
      --background-color: #ecf0f1;
    }

    body {
      background: var(--background-color);
      color: var(--text-color);
      font-family: 'Source Sans Pro', sans-serif;
      transition: all 0.3s ease;
    }



    .main-sidebar {
      background: #2c3e50;
      box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    }

    .nav-sidebar .nav-item .nav-link {
      color: #ecf0f1;
      transition: all 0.3s ease;
    }

    .nav-sidebar .nav-item .nav-link:hover {
      background: var(--accent-color);
      transform: translateX(2px);
    }

    .content-wrapper {
      background: #fff;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }

    .card {
      border: none;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }

    .card:hover {
      box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
    }

    .btn {
      border-radius: 5px;
      font-weight: bold;
      letter-spacing: 1px;
      transition: all 0.3s ease;
      padding: 10px 20px;
      font-size: 12px;
    }

    table .btn {
      font-size: 12px;
    }

    .btn:hover {
      transform: scale(1.05);
    }

    .table {
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .table thead th {
      background: var(--primary-color);
      color: #fff;
    }

    .buttons-html5 {
      background: transparent !important;
      border: 1px solid #a1a1a1 !important;
      color: #ccc !important;
    }

    @media print {
      .table thead th {
        color: #333;
      }
    }



    /* Excel-like styling */
    .table {
      border-collapse: collapse;
      width: 100%;
      background: white;
    }

    .table th {
      background-color: #f3f3f3;
      border: 1px solid #ddd;
      font-weight: bold;
      padding: 8px;
      position: sticky;
      top: 0;
      z-index: 1;
      vertical-align: middle;
    }

    .table td {
      border: 1px solid #ddd;
      padding: 6px 8px;
      vertical-align: middle;
    }

    .table tbody tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .table tbody tr:hover {
      background-color: #f5f5f5;
    }

    /* Excel-like grid lines */
    .table-bordered {
      border: 1px solid #ddd;
    }

    .table-bordered th,
    .table-bordered td {
      border: 1px solid #ddd;
      vertical-align: middle;
    }

    /* Excel-like header */
    .card-header {
      background-color: #f3f3f3;
      border-bottom: 1px solid #ddd;
    }

    /* Excel-like cell focus */
    .table td:focus {
      outline: 2px solid #1a73e8;
      outline-offset: -2px;
    }



    /* Keep header visible while scrolling */
    thead {
      position: sticky;
      top: 0;
      background: #f3f3f3;
    }
  </style>

  <!-- Enhanced Timezone Selector Styles -->
  <style>
    /* Enhanced Timezone - Consistent with Other Elements */
    .timezone-link {
      display: flex;
      align-items: center;
      gap: 6px;
      text-decoration: none !important;
      color: inherit !important;
    }

    .timezone-text {
      font-size: 0.9rem;
      font-weight: 500;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 100px;
    }

    .timezone-edit-icon {
      font-size: 0.7rem;
      opacity: 0.7;
      transition: all 0.3s ease;
    }

    .timezone-link:hover .timezone-edit-icon {
      opacity: 1;
      transform: scale(1.1);
    }

    /* Modal Enhancements */
    .timezone-modal .modal-content {
      border: none;
      border-radius: 15px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
      overflow: hidden;
    }

    .timezone-modal .modal-header {
      background: linear-gradient(135deg, #3b82f6, #8b5cf6);
      color: white;
      border: none;
      padding: 20px 25px;
    }

    .timezone-modal .modal-title {
      font-weight: 600;
      font-size: 1.2rem;
    }

    .timezone-modal .close {
      color: white;
      opacity: 0.8;
      font-size: 1.5rem;
    }

    .timezone-modal .close:hover {
      opacity: 1;
    }

    .timezone-modal .modal-body {
      padding: 25px;
    }

    .timezone-search-container {
      position: relative;
      margin-bottom: 20px;
    }

    .timezone-search {
      width: 100%;
      padding: 12px 45px 12px 15px;
      border: 2px solid #e5e7eb;
      border-radius: 10px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .timezone-search:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
      outline: none;
    }

    .timezone-search-icon {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
      font-size: 1.1rem;
    }

    .timezone-select {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid #e5e7eb;
      border-radius: 10px;
      font-size: 1rem;
      background: white;
      transition: all 0.3s ease;
      max-height: 200px;
      overflow-y: auto;
    }

    .timezone-select:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
      outline: none;
    }

    .timezone-preview {
      background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
      border: 1px solid #bae6fd;
      border-radius: 10px;
      padding: 15px;
      margin-top: 15px;
    }

    .timezone-preview-title {
      font-weight: 600;
      color: #0369a1;
      margin-bottom: 8px;
      font-size: 0.9rem;
    }

    .timezone-preview-time {
      font-size: 1.1rem;
      color: #0c4a6e;
      font-weight: 500;
      font-family: 'Courier New', monospace;
    }

    .timezone-preview-location {
      font-size: 0.85rem;
      color: #0369a1;
      margin-top: 5px;
    }

    .timezone-modal .modal-footer {
      background: #f8fafc;
      border: none;
      padding: 20px 25px;
    }

    .timezone-btn {
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 500;
      transition: all 0.3s ease;
      border: none;
    }

    .timezone-btn-primary {
      background: linear-gradient(135deg, #3b82f6, #1d4ed8);
      color: white;
    }

    .timezone-btn-primary:hover {
      background: linear-gradient(135deg, #1d4ed8, #1e40af);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    .timezone-btn-secondary {
      background: #f1f5f9;
      color: #64748b;
      border: 1px solid #e2e8f0;
    }

    .timezone-btn-secondary:hover {
      background: #e2e8f0;
      color: #475569;
    }

    /* Loading Animation */
    .timezone-loading {
      display: inline-block;
      width: 16px;
      height: 16px;
      border: 2px solid #ffffff;
      border-radius: 50%;
      border-top-color: transparent;
      animation: timezone-spin 1s ease-in-out infinite;
    }

    @keyframes timezone-spin {
      to { transform: rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .timezone-value {
        max-width: 80px;
        font-size: 0.8rem;
      }
      
      .timezone-label {
        font-size: 0.7rem;
      }
      
      .timezone-modal .modal-dialog {
        margin: 10px;
      }
    }

    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
      .timezone-link {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(147, 51, 234, 0.2));
        border-color: rgba(59, 130, 246, 0.3);
      }
      
      .timezone-value {
        color: #f9fafb;
      }
      
      .timezone-label {
        color: #d1d5db;
      }
    }

    /* Enhanced Header Elements - Individual Improvements */
    .main-header {
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }

    .main-header .navbar-nav {
      align-items: center;
    }

    .main-header .navbar-nav .nav-item {
      margin: 0 2px;
    }

    /* Enhanced Navigation Links - Subtle Improvements */
    .nav-link-enhanced {
      position: relative;
      transition: all 0.3s ease;
      border-radius: 6px;
      margin: 0 1px;
      padding: 8px 12px !important;
      text-decoration: none !important;
      font-weight: 500;
    }

    .nav-link-enhanced:hover {
      background-color: rgba(0, 0, 0, 0.05);
      transform: translateY(-1px);
      text-decoration: none !important;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .nav-link-enhanced:active {
      transform: translateY(0);
    }

    .nav-icon {
      font-size: 1.1rem;
      transition: all 0.3s ease;
      position: relative;
    }

    .nav-link-enhanced:hover .nav-icon {
      transform: scale(1.1);
    }

    /* Enhanced Search Container - Subtle Improvements */
    .search-item {
      margin-right: 15px;
    }

    .search-container {
      position: relative;
      display: flex;
      align-items: center;
    }

    .search-input:focus {
      border-color: #007bff;
      box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
      outline: none;
      transform: scale(1.02);
    }

    .search-input::placeholder {
      color: #999;
      font-weight: 400;
    }

    .search-btn {
      border-radius: 0 20px 20px 0;
      border: 1px solid #ddd;
      background-color: #f8f9fa;
      color: #333;
      transition: all 0.3s ease;
      padding: 8px 12px;
      border-left: none;
    }

    .search-btn:hover {
      background-color: #e9ecef;
      transform: scale(1.05);
      color: #333;
      border-color: #007bff;
    }

    .search-icon {
      font-size: 0.9rem;
    }

    /* Top Header Quick Action Buttons */
    .nav-quick-btn {
      height: 38px;
      padding: 0 16px;
      font-size: 13px;
      font-weight: 600;
      letter-spacing: 0.2px;
      border-radius: 6px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: all 0.25s ease;
      white-space: nowrap;
      text-decoration: none !important;
      border: none;
      cursor: pointer;
    }
    .nav-btn-invoice {
      background: linear-gradient(135deg, #2563eb, #1d4ed8);
      color: #ffffff !important;
      box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
    }
    .nav-btn-invoice:hover {
      background: linear-gradient(135deg, #1d4ed8, #1e40af);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
      color: #ffffff !important;
    }
    .nav-btn-due {
      background: linear-gradient(135deg, #f59e0b, #d97706);
      color: #ffffff !important;
      box-shadow: 0 2px 6px rgba(217, 119, 6, 0.25);
    }
    .nav-btn-due:hover {
      background: linear-gradient(135deg, #d97706, #b45309);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(217, 119, 6, 0.35);
      color: #ffffff !important;
    }
    .nav-quick-btn i {
      font-size: 13px;
    }

    /* Enhanced Notification Elements - Subtle Improvements */
    .quick-actions-link {
      background: rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(0, 0, 0, 0.1);
      color: inherit !important;
    }

    .quick-actions-link:hover {
      background: rgba(0, 0, 0, 0.1);
      border-color: rgba(0, 0, 0, 0.2);
      color: inherit !important;
    }

    .quick-actions-badge {
      background: linear-gradient(135deg, #10b981, #059669) !important;
      animation: pulse 2s infinite;
      box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
    }

    /* Enhanced Notification Links - Subtle Improvements */
    .chat-link {
      background: rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(0, 0, 0, 0.1);
      color: inherit !important;
    }

    .chat-link:hover {
      background: rgba(0, 0, 0, 0.1);
      border-color: rgba(0, 0, 0, 0.2);
      color: inherit !important;
    }

    .payment-link {
      background: rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(0, 0, 0, 0.1);
      color: inherit !important;
    }

    .payment-link:hover {
      background: rgba(0, 0, 0, 0.1);
      border-color: rgba(0, 0, 0, 0.2);
      color: inherit !important;
    }

    .patient-link {
      background: rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(0, 0, 0, 0.1);
      color: inherit !important;
    }

    .patient-link:hover {
      background: rgba(0, 0, 0, 0.1);
      border-color: rgba(0, 0, 0, 0.2);
      color: inherit !important;
    }

    .language-link {
      background: rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(0, 0, 0, 0.1);
      color: inherit !important;
    }

    .language-link:hover {
      background: rgba(0, 0, 0, 0.1);
      border-color: rgba(0, 0, 0, 0.2);
      color: inherit !important;
    }

    .language-flag {
      font-size: 1.2rem;
      border-radius: 3px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .fullscreen-link {
      background: rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(0, 0, 0, 0.1);
      color: inherit !important;
    }

    .fullscreen-link:hover {
      background: rgba(0, 0, 0, 0.1);
      border-color: rgba(0, 0, 0, 0.2);
      color: inherit !important;
    }

    .logout-link {
      background: rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(0, 0, 0, 0.1);
      color: inherit !important;
    }

    .logout-link:hover {
      background: rgba(0, 0, 0, 0.1);
      border-color: rgba(0, 0, 0, 0.2);
      color: inherit !important;
    }

    /* Badge Animations */
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .nav-link-enhanced {
        padding: 6px 8px !important;
        margin: 0 1px;
      }
      
      .nav-icon {
        font-size: 1rem;
      }
      
      .search-container {
        margin-right: 10px;
      }

      .search-input {
        width: 180px;
        font-size: 0.85rem;
      }

      .timezone-text {
        max-width: 80px;
        font-size: 0.8rem;
      }
    }

    @media (max-width: 576px) {
      .search-input {
        width: 150px;
      }

      .timezone-text {
        max-width: 60px;
        font-size: 0.75rem;
      }

      .timezone-link {
        gap: 4px;
      }
    }

    /* Dark mode adjustments */
    .navbar-dark .nav-link-enhanced {
      color: inherit !important;
    }

    .navbar-dark .nav-link-enhanced:hover {
      color: inherit !important;
    }

    .navbar-dark .timezone-text {
      color: rgba(255, 255, 255, 0.9);
    }

    .navbar-dark .timezone-edit-icon {
      color: rgba(255, 255, 255, 0.7);
    }
  </style>

  <!-- Include Timezone Component -->
  <?php 
  // Ensure language is loaded for timezone component
  $this->load->helper('language');
  
  // Load timezone data if not already loaded
  if (!isset($timezones)) {
    $this->load->model('settings_model');
    $settings = $this->settings_model->getSettings();
    
    // Load timezone list - simplified
    $timezones = array(
      'UTC' => "(GMT+00:00) UTC",
      'GMT' => "(GMT+00:00) GMT",
      'America/New_York' => "(GMT-05:00) New York",
      'America/Chicago' => "(GMT-06:00) Chicago",
      'America/Denver' => "(GMT-07:00) Denver",
      'America/Los_Angeles' => "(GMT-08:00) Los Angeles",
      'Europe/London' => "(GMT+00:00) London",
      'Europe/Paris' => "(GMT+01:00) Paris",
      'Europe/Berlin' => "(GMT+01:00) Berlin",
      'Europe/Rome' => "(GMT+01:00) Rome",
      'Europe/Madrid' => "(GMT+01:00) Madrid",
      'Asia/Tokyo' => "(GMT+09:00) Tokyo",
      'Asia/Shanghai' => "(GMT+08:00) Shanghai",
      'Asia/Kolkata' => "(GMT+05:30) Mumbai, New Delhi",
      'Asia/Dubai' => "(GMT+04:00) Dubai",
      'Asia/Karachi' => "(GMT+05:00) Karachi",
      'Australia/Sydney' => "(GMT+10:00) Sydney",
      'Australia/Melbourne' => "(GMT+10:00) Melbourne",
      'Pacific/Auckland' => "(GMT+12:00) Auckland"
    );
  }
  ?>

  <!-- Timezone Modal -->
  <div class="modal fade timezone-modal" id="timezoneModal" tabindex="-1" role="dialog" aria-labelledby="timezoneModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="timezoneModalLabel">
            <i class="fas fa-clock mr-2"></i><?php echo lang('timezone'); ?> <?php echo lang('settings'); ?>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="timezone-search-container">
            <input type="text" id="timezoneSearch" class="timezone-search" placeholder="<?php echo lang('search'); ?> <?php echo lang('timezone'); ?>...">
            <i class="fas fa-search timezone-search-icon"></i>
          </div>
          
          <select id="timezoneSelect" class="timezone-select" size="10">
            <?php foreach ($timezones as $value => $label): ?>
              <option value="<?php echo $value; ?>" <?php echo ($settings->timezone == $value) ? 'selected' : ''; ?>>
                <?php echo $label; ?>
              </option>
            <?php endforeach; ?>
          </select>
          
          <div class="timezone-preview" id="timezonePreview">
            <div class="timezone-preview-title"><?php echo lang('current_time'); ?>:</div>
            <div class="timezone-preview-time" id="previewTime"></div>
            <div class="timezone-preview-location" id="previewLocation"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn timezone-btn timezone-btn-secondary" data-dismiss="modal">
            <i class="fas fa-times mr-2"></i><?php echo lang('cancel'); ?>
          </button>
          <button type="button" class="btn timezone-btn timezone-btn-primary" id="saveTimezone">
            <i class="fas fa-save mr-2"></i><?php echo lang('save'); ?> <?php echo lang('timezone'); ?>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Timezone JavaScript -->
  <script>
  $(document).ready(function() {
    // Timezone functionality
    let currentTimezone = '<?php echo $settings->timezone ?? "UTC"; ?>';
    
    // Update preview when timezone changes
    function updatePreview() {
      const selectedOption = $('#timezoneSelect option:selected');
      const timezoneValue = selectedOption.val();
      const timezoneLabel = selectedOption.text();
      
      if (timezoneValue) {
        // Create a temporary date to show current time in selected timezone
        const now = new Date();
        const timeString = now.toLocaleString('en-US', {
          timeZone: timezoneValue,
          year: 'numeric',
          month: '2-digit',
          day: '2-digit',
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit',
          hour12: true
        });
        
        $('#previewTime').text(timeString);
        $('#previewLocation').text(timezoneLabel);
      }
    }
    
    // Search functionality
    $('#timezoneSearch').on('input', function() {
      const searchTerm = $(this).val().toLowerCase();
      $('#timezoneSelect option').each(function() {
        const optionText = $(this).text().toLowerCase();
        if (optionText.includes(searchTerm)) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });
    });
    
    // Update preview when selection changes
    $('#timezoneSelect').on('change', updatePreview);
    
    // Initial preview update
    updatePreview();
    
    // Save timezone
    $('#saveTimezone').on('click', function() {
      const selectedTimezone = $('#timezoneSelect').val();
      
      if (!selectedTimezone) {
        alert('<?php echo lang('please_select_timezone'); ?>');
        return;
      }
      
      // Show loading state
      const $btn = $(this);
      const originalText = $btn.html();
      $btn.html('<span class="timezone-loading"></span> <?php echo lang('saving'); ?>...');
      $btn.prop('disabled', true);
      
      // Send AJAX request to save timezone
      $.ajax({
        url: '<?php echo base_url(); ?>home/updateTimezone',
        method: 'POST',
        data: {
          timezone: selectedTimezone
        },
        dataType: 'json',
        success: function(response) {
          console.log('Timezone update response:', response);
          
          if (response && response.success) {
            // Update the display
            $('#current-timezone').text(selectedTimezone);
            currentTimezone = selectedTimezone;
            
            // Show success message using SweetAlert2
            Swal.fire({
              icon: 'success',
              title: '<?php echo lang('success'); ?>',
              text: '<?php echo lang('timezone_updated_successfully'); ?>',
              timer: 2000,
              showConfirmButton: false
            });
            
            // Close modal
            $('#timezoneModal').modal('hide');
          } else {
            const errorMsg = response && response.message ? response.message : '<?php echo lang('error_updating_timezone'); ?>';
            Swal.fire({
              icon: 'error',
              title: '<?php echo lang('error'); ?>',
              text: errorMsg
            });
          }
        },
        error: function(xhr, status, error) {
          console.error('AJAX Error:', xhr, status, error);
          console.error('Response Text:', xhr.responseText);
          
          Swal.fire({
            icon: 'error',
            title: '<?php echo lang('error'); ?>',
            text: '<?php echo lang('error_updating_timezone'); ?>: ' + error
          });
        },
        complete: function() {
          // Restore button state
          $btn.html(originalText);
          $btn.prop('disabled', false);
        }
      });
    });
    
    // Reset search when modal is closed
    $('#timezoneModal').on('hidden.bs.modal', function() {
      $('#timezoneSearch').val('');
      $('#timezoneSelect option').show();
    });
  });
  </script>
