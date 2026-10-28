"use strict";

// Initialize TinyMCE for Doctor description
if (typeof tinymce !== "undefined") {
  tinymce.remove("#editor1");
  tinymce.init({
    selector: "#editor1",
    min_height: 140,
    plugins: "preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help quickbars emoticons",
    menubar: "file edit view insert format tools table help",
    toolbar: "undo redo | bold italic underline | formatselect | alignleft aligncenter alignright alignjustify | numlist bullist | forecolor backcolor removeformat | preview code",
    branding: false,
    promotion: false
  });
}

$(document).ready(function () {
  "use strict";

  // Initialize DataTable
  var table = $("#editable-sample").DataTable({
    responsive: true,
    processing: true,
    serverSide: true,
    searchable: true,
    bScrollCollapse: true,
    ajax: {
      url: "doctor/getDoctor",
      type: "POST",
      data: function (d) {
        d.department = $("#department_filter").val();
        d.status = $("#status_filter").val();
      }
    },
    scroller: {
      loadingIndicator: true
    },
    dom:
      "<'row mb-3'<'col-sm-3'l><'col-sm-5 text-center custom_buttons'B><'col-sm-4 text-right'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
    buttons: [
      { extend: "copyHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
      { extend: "excelHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
      { extend: "csvHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
      { extend: "pdfHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
      { extend: "print", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } }
    ],
    aLengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "All"]
    ],
    iDisplayLength: 25,
    order: [[0, "desc"]],
    columnDefs: [
      { targets: [8], orderable: false, searchable: false }
    ],
    language: {
      lengthMenu: "_MENU_",
      search: "_INPUT_",
      searchPlaceholder: "Search by doctor name, degrees, dept, phone...",
      url: (typeof language !== "undefined" && language !== "") ? "common/assets/DataTables/languages/" + language + ".json" : ""
    }
  });

  table.buttons().container().appendTo(".custom_buttons");

  // Filter Listeners
  $("#department_filter").on("change", function () {
    table.ajax.reload();
  });

  $("#status_filter").on("change", function () {
    table.ajax.reload();
  });

  $("#reset_filter_btn").on("click", function () {
    $("#department_filter").val("all").trigger("change");
    $("#status_filter").val("all");
    table.ajax.reload();
  });

  // Toggle Doctor Active / Inactive Status
  $(".table").on("click", ".toggle-status-btn", function (e) {
    e.preventDefault();
    var docId = $(this).attr("data-id");
    var btn = $(this);
    btn.prop("disabled", true);

    $.ajax({
      url: "doctor/toggleActive",
      type: "POST",
      data: { id: docId },
      dataType: "json",
      success: function (res) {
        if (res.status === "success") {
          if (typeof swal !== "undefined") {
            swal({
              title: "Updated",
              text: res.message,
              icon: "success",
              buttons: false,
              timer: 1500
            });
          }
          table.ajax.reload(null, false);
        } else {
          alert(res.message || "Unable to update status.");
        }
      },
      error: function () {
        alert("An error occurred while updating status.");
      },
      complete: function () {
        btn.prop("disabled", false);
      }
    });
  });

  // Edit Doctor Action
  $(".table").on("click", ".editbutton", function () {
    $("#loader").show();
    var iid = $(this).attr("data-id");
    $("#img").attr("src", "uploads/cardiology-patient-icon-vector-6244713.jpg");
    $("#signature").attr("src", "");
    $("#editDoctorForm").trigger("reset");

    $.ajax({
      url: "doctor/editDoctorByJason?id=" + iid,
      method: "GET",
      dataType: "json",
      success: function (response) {
        var doc = response.doctor;
        if (!doc) return;

        var form = $("#editDoctorForm");
        form.find('[name="id"]').val(doc.id);
        form.find('[name="name"]').val(doc.name);
        form.find('[name="designation"]').val(doc.designation || "");
        form.find('[name="degrees"]').val(doc.degrees || "");
        form.find('[name="specialization"]').val(doc.specialization || "");
        form.find('[name="experience"]').val(doc.experience || "");
        form.find('[name="email"]').val(doc.email || "");
        form.find('[name="phone"]').val(doc.phone || "");
        form.find('[name="phone_secondary"]').val(doc.phone_secondary || "");
        form.find('[name="chamber"]').val(doc.chamber || "");
        form.find('[name="address"]').val(doc.address || "");
        form.find('[name="consultation_fee"]').val(doc.consultation_fee || "700");
        form.find('[name="followup_fee"]').val(doc.followup_fee || "400");
        form.find('[name="active"]').val(doc.active || "1");

        if (doc.department) {
          form.find('[name="department"]').val(doc.department).trigger("change");
        }

        if (typeof tinymce !== "undefined") {
          tinymce.remove("#editor3");
          tinymce.init({
            selector: "#editor3",
            min_height: 140,
            plugins: "preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help quickbars emoticons",
            menubar: "file edit view insert format tools table help",
            toolbar: "undo redo | bold italic underline | formatselect | alignleft aligncenter alignright alignjustify | numlist bullist | forecolor backcolor removeformat | preview code",
            branding: false,
            promotion: false,
            init_instance_callback: function (editor) {
              editor.setContent(doc.profile || "");
            }
          });
        }

        if (doc.img_url) {
          $("#img").attr("src", doc.img_url);
        }
        if (doc.signature) {
          $("#signature").attr("src", doc.signature);
        }

        $("#myModal2").modal("show");
      },
      complete: function () {
        $("#loader").hide();
      }
    });
  });

  // Doctor Info Action
  $(".table").on("click", ".inffo", function () {
    $("#loader").show();
    var iid = $(this).attr("data-id");

    $.ajax({
      url: "doctor/editDoctorByJason?id=" + iid,
      method: "GET",
      dataType: "json",
      success: function (response) {
        var doc = response.doctor;
        if (!doc) return;

        $(".nameClass").text(doc.name || "N/A");
        $(".designationClass").text(doc.designation || "");
        $(".departmentClass").html('<i class="fas fa-hospital mr-1"></i> ' + (doc.department_name || "General"));
        $(".consultFeeClass").text("BDT " + (doc.consultation_fee ? Math.round(doc.consultation_fee) : 700));
        $(".followupFeeClass").text("BDT " + (doc.followup_fee ? Math.round(doc.followup_fee) : 400));
        $(".degreesClass").text(doc.degrees || "N/A");
        $(".specializationClass").text(doc.specialization || "N/A");
        $(".experienceClass").text(doc.experience || "N/A");
        $(".phoneClass").html('<a href="tel:' + doc.phone + '" class="text-primary">' + doc.phone + '</a>');
        $(".phoneSecondaryClass").text(doc.phone_secondary || "N/A");
        $(".emailClass").text(doc.email || "N/A");
        $(".chamberClass").text(doc.chamber || "Lifecare Diagnostic Center");
        $(".addressClass").text(doc.address || "N/A");

        if (doc.active == 1) {
          $(".statusBadgeClass").html('<span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size:12px;"><i class="fas fa-check-circle mr-1"></i> Active</span>');
        } else {
          $(".statusBadgeClass").html('<span class="badge badge-danger px-3 py-2 font-weight-bold" style="font-size:12px;"><i class="fas fa-times-circle mr-1"></i> Inactive</span>');
        }

        if (doc.schedule_flag) {
          $(".flagNoticeClass").html('<div class="alert alert-warning py-2 px-3 small font-weight-bold mb-0"><i class="fas fa-exclamation-triangle mr-2"></i>' + doc.schedule_flag + '</div>');
        } else {
          $(".flagNoticeClass").html("");
        }

        // Schedule breakdown
        if (response.schedules && response.schedules.length > 0) {
          var schedHtml = '<ul class="list-unstyled mb-0">';
          $.each(response.schedules, function (idx, sc) {
            schedHtml += '<li class="mb-1"><span class="badge badge-primary mr-2">' + sc.weekday + '</span> <strong class="text-dark">' + sc.s_time + ' - ' + sc.e_time + '</strong> <span class="text-muted small">(' + sc.duration + ' min slots)</span></li>';
          });
          schedHtml += '</ul>';
          $(".scheduleBreakdownClass").html(schedHtml);
        } else {
          $(".scheduleBreakdownClass").html('<span class="text-muted font-italic">No regular weekly schedule configured.</span>');
        }

        // Holiday & Leave breakdown
        if (response.holidays && response.holidays.length > 0) {
          var holHtml = '<ul class="list-unstyled mb-0">';
          $.each(response.holidays, function (idx, hl) {
            var stDate = new Date(hl.date * 1000);
            var dateStr = stDate.toLocaleDateString("en-GB");
            if (hl.end_date && hl.end_date != hl.date) {
              var enDate = new Date(hl.end_date * 1000);
              dateStr += " to " + enDate.toLocaleDateString("en-GB");
            }
            var typeBadge = '<span class="badge badge-secondary mr-2">' + (hl.leave_type || "Holiday") + '</span>';
            var timeDetail = (hl.start_time && hl.end_time) ? ' (' + hl.start_time + ' - ' + hl.end_time + ')' : '';
            var reasonDetail = hl.reason ? ' - <span class="text-muted">' + hl.reason + '</span>' : '';
            holHtml += '<li class="mb-1">' + typeBadge + '<strong>' + dateStr + '</strong>' + timeDetail + reasonDetail + '</li>';
          });
          holHtml += '</ul>';
          $(".holidayBreakdownClass").html(holHtml);
        } else {
          $(".holidayBreakdownClass").html('<span class="text-muted font-italic">No active holidays or leaves scheduled.</span>');
        }

        $(".profileClass").html(doc.profile || "<span class='text-muted font-italic'>No biography or professional summary provided.</span>");

        var photo = (doc.img_url && doc.img_url !== "") ? doc.img_url : "uploads/cardiology-patient-icon-vector-6244713.jpg";
        $("#img1").attr("src", photo);

        $("#infoModal").modal("show");
      },
      complete: function () {
        $("#loader").hide();
      }
    });
  });

  $(".flashmessage").delay(3000).fadeOut(100);
});
