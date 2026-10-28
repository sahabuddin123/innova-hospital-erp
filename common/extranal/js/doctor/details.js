"use strict";


$(document).ready(function () {
  "use strict";

  $(document).on("click", ".doctoreditbutton", function () {
    "use strict";

    var iid = $(this).attr("data-id");
    $("#img").attr("src", "uploads/cardiology-patient-icon-vector-6244713.jpg");
    $("#editDoctorForm").trigger("reset");

    $.ajax({
      url: "doctor/editDoctorByJason?id=" + iid,
      method: "GET",
      data: "",
      dataType: "json",
      success: function (response) {
        "use strict";

        // Populate the form fields with the data returned from server
        $("#editDoctorForm").find('[name="id"]').val(response.doctor.id).end();
        $("#editDoctorForm")
          .find('[name="name"]')
          .val(response.doctor.name)
          .end();
        $("#editDoctorForm")
          .find('[name="password"]')
          .val(response.doctor.password)
          .end();
        $("#editDoctorForm")
          .find('[name="email"]')
          .val(response.doctor.email)
          .end();
        $("#editDoctorForm")
          .find('[name="address"]')
          .val(response.doctor.address)
          .end();
        $("#editDoctorForm")
          .find('[name="phone"]')
          .val(response.doctor.phone)
          .end();
        $("#editDoctorForm")
          .find('[name="department"]')
          .val(response.doctor.department)
          .end();

        // myEditor3.setData(response.doctor.profile);
        //$("#profile1").val(response.doctor.profile);
        tinymce.remove('#editor3');
        tinymce.init({
          selector: '#editor3',
          plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help quickbars emoticons',
          menubar: 'file edit view insert format tools table help',
          toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | ltr rtl',
          branding: false,
          promotion: false,
          init_instance_callback: function (editor) {
            // editor.setContent();
            editor.setContent(response.doctor.profile);
          }
        });


        if (
          typeof response.doctor.img_url !== "undefined" &&
          response.doctor.img_url !== ""
        ) {
          $("#img").attr("src", response.doctor.img_url);
        }
        if (
          typeof response.doctor.signature !== "undefined" &&
          response.doctor.signature !== ""
        ) {
          $("#signature").attr("src", response.doctor.signature);
        }

        $(".js-example-basic-single.department")
          .val(response.doctor.department)
          .trigger("change");

        $("#myModalEdit").modal("show");
      },
    });
  });
});


$(document).ready(function () {
  "use strict";
  $(".table").on("click", ".editScheduleButton", function () {
    "use strict";
    $("#loader").show();
    // Get the record's ID via attribute  
    var iid = $(this).attr('data-id');
    $('#editTimeSlotForm').trigger("reset");
    $('#editScheduleModal').modal('show');
    $.ajax({
      url: 'schedule/editScheduleByJason?id=' + iid,
      method: 'GET',
      data: '',
      dataType: 'json',
      success: function (response) {
        "use strict";
        // Populate the form fields with the data returned from server
        $('#editTimeSlotForm').find('[name="id"]').val(response.schedule.id).end();
        $('#editTimeSlotForm').find('[name="s_time"]').val(response.schedule.s_time).end();
        $('#editTimeSlotForm').find('[name="e_time"]').val(response.schedule.e_time).end();
        $('#editTimeSlotForm').find('[name="weekday"]').val(response.schedule.weekday).end();
      },
      complete: function () {
        $("#loader").hide();
      },
    })
  });
});
$(document).ready(function () {
  "use strict";
  $(".table").on("click", ".editbutton", function () {
    "use strict";
    $("#loader").show();
    // Get the record's ID via attribute  
    var iid = $(this).attr('data-id');
    $('#myModal2').modal('show');
    $.ajax({
      url: 'patient/editMedicalHistoryByJason?id=' + iid,
      method: 'GET',
      data: '',
      dataType: 'json',
      success: function (response) {
        "use strict";
        // Populate the form fields with the data returned from server
        $('#medical_historyEditForm').find('[name="id"]').val(response.medical_history.id).end()
        $('#medical_historyEditForm').find('[name="date"]').val(response.medical_history.date).end()
        myEditor1.setData(response.medical_history.description)
      },
      complete: function () {
        $("#loader").hide();
      },
    })
  });
});

$(document).ready(function () {
  "use strict";
  $(".table").on("click", ".editHoliday", function () {
    "use strict";
    $("#loader").show();
    // Get the record's ID via attribute  
    var iid = $(this).attr('data-id');
    $('#editHolidayForm').trigger("reset");
    $('#editHolidayModal').modal('show');
    $.ajax({
      url: 'schedule/editHolidayByJason?id=' + iid,
      method: 'GET',
      data: '',
      dataType: 'json',
      success: function (response) {
        "use strict";
        // Populate the form fields with the data returned from server
        var date = new Date(response.holiday.date * 1000);
        $('#editHolidayForm').find('[name="id"]').val(response.holiday.id).end()
        $('#editHolidayForm').find('[name="date"]').val(date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear()).end()
      },
      complete: function () {
        $("#loader").hide();
      },
    })
  });
});

$(document).ready(function () {
  "use strict";
  $('.pos_client').hide();
  $(document.body).on('change', '#pos_select', function () {
    "use strict";
    var v = $("select.pos_select option:selected").val()
    if (v == 'add_new') {
      $('.pos_client').show();
    } else {
      $('.pos_client').hide();
    }
  });
  $('.pos_client').hide();
  $(document.body).on('change', '#pos_select1', function () {
    "use strict";
    var v = $("select.pos_select1 option:selected").val()
    if (v == 'add_new') {
      $('.pos_client').show();
    } else {
      $('.pos_client').hide();
    }
  });

});





$(document).ready(function () {
  "use strict";
  $(".appointment_edit").on(
    "click",
    ".editAppointmentButton",
    function () {
      "use strict";

      $("#adoctors1").html("");

      var iid = $(this).attr("data-id");
      var id = $(this).attr("data-id");

      $("#editAppointmentForm").trigger("reset");
      $(".consultant_fee_div").addClass("d-none");
      $(".pay_now").addClass("d-none");
      $(".payment_status").addClass("d-none");
      $(".deposit_type1").addClass("d-none");
      $("#editAppointmentModal").modal("show");
      $.ajax({
        url: "appointment/editAppointmentByJason?id=" + iid,
        method: "GET",
        data: "",
        dataType: "json",
        success: function (response) {
          "use strict";
          var de = response.appointment.date * 1000;
          var d = new Date(de);
          var da =
            d.getDate() + "-" + (d.getMonth() + 1) + "-" + d.getFullYear();

          $("#editAppointmentForm")
            .find('[name="id"]')
            .val(response.appointment.id)
            .end();
          $("#editAppointmentForm")
            .find('[name="patient"]')
            .val(response.appointment.patient)
            .end();

          $("#editAppointmentForm").find('[name="date"]').val(da).end();
          $("#editAppointmentForm")
            .find('[name="status"]')
            .val(response.appointment.status)
            .end();
          $("#editAppointmentForm")
            .find('[name="remarks"]')
            .val(response.appointment.remarks)
            .end();
          var option1 = new Option(
            response.doctor.name + "-" + response.doctor.id,
            response.doctor.id,
            true,
            true
          );
          $("#editAppointmentForm")
            .find('[name="doctor"]')
            .append(option1)
            .trigger("change");

          var option = new Option(
            response.patient.name + "-" + response.patient.id,
            response.patient.id,
            true,
            true
          );
          $("#editAppointmentForm")
            .find('[name="patient"]')
            .append(option)
            .trigger("change");
          $("#visit_description1").html("");
          $.ajax({
            url:
              "doctor/getDoctorVisitForEdit?id=" +
              response.doctor.id +
              "&description=" +
              response.appointment.visit_description,
            method: "GET",
            data: "",
            dataType: "json",
            success: function (response1) {
              $("#visit_description1").html(response1.response).end();
              // $('#editAppointmentForm').find('[name="visit_description"]').val(response.appointment.visit_description).trigger('change').end();
            },
          });

          if (response.appointment.payment_status == "unpaid") {
            $(".consultant_fee_div").removeClass("d-none");
            $(".pay_now").removeClass("d-none");
            $(".payment_status").addClass("d-none");
            $('#visit_description1').css('pointer-events', "auto");
            // $('.deposit_type1').removeClass('hidden');
            $("#editAppointmentForm")
              .find('[name="visit_charges"]')
              .val(response.appointment.visit_charges)
              .end();
            $("#editAppointmentForm")
              .find('[name="discount"]')
              .val(response.appointment.discount)
              .end();
            $("#editAppointmentForm")
              .find('[name="grand_total"]')
              .val(response.appointment.grand_total)
              .end();
          } else {
            $(".payment_status").removeClass("d-none");
            $(".pay_now").addClass("d-none");
            $(".consultant_fee_div").addClass("d-none");
            //  $('.deposit_type1').addClass('hidden');
            // $('.status_paid_hidden_section').addClass('hidden');
            $("#visit_description1").css("pointer-events", "none");
            $("#editAppointmentForm")
              .find('[id="adoctors1"]')
              .select2([
                {
                  id: response.doctor.id,
                  text: response.doctor.name + "-" + response.doctor.id,
                  locked: true,
                },
              ]);
            $("#editAppointmentForm")
              .find('[id="pos_select1"]')
              .select2([
                {
                  id: response.patient.id,
                  text: response.patient.name + "-" + response.patient.id,
                  locked: true,
                },
              ]);
          }
          var date = $("#date1").val();
          var doctorr = $("#adoctors1").val();
          var appointment_id = $("#appointment_id").val();

          $.ajax({
            url:
              "schedule/getAvailableSlotByDoctorByDateByAppointmentIdByJason?date=" +
              date +
              "&doctor=" +
              doctorr +
              "&appointment_id=" +
              appointment_id,
            method: "GET",
            data: "",
            dataType: "json",
            success: function (response) {
              "use strict";
              $("#aslots1").find("option").remove();
              var slots = response.aslots;
              $.each(slots, function (key, value) {
                $("#aslots1")
                  .append($("<option>").text(value).val(value))
                  .end();
              });

              $("#aslots1").val(response.current_value).trigger("change");

              if ($("#aslots1").has("option").length == 0) {
                $("#aslots1")
                  .append(
                    $("<option>")
                      .text(no_available_timeslots)
                      .val("Not Selected")
                  )
                  .end();
              }
            },
          });
        },
      });
    }
  );
});
$(document).ready(function () {
  "use strict";

  $(".doctor_div").on("change", "#adoctors", function () {
    "use strict";

    var iid = $('#date').val();
    var doctorr = $('#adoctors').val();
    $('#aslots').find('option').remove();

    $.ajax({
      url: 'schedule/getAvailableSlotByDoctorByDateByJason?date=' + iid + '&doctor=' + doctorr,
      method: 'GET',
      data: '',
      dataType: 'json',
      success: function (response) {
        "use strict";

        var slots = response.aslots;
        $.each(slots, function (key, value) {
          "use strict";
          $('#aslots').append($('<option>').text(value).val(value)).end();
        });

        if ($('#aslots').has('option').length == 0) {                    //if it is blank. 
          $('#aslots').append($('<option>').text('No Further Time Slots').val('Not Selected')).end();
        }
      }
    })

    $("#visit_description").html(" ");
    $("#visit_charges").val(" ");
    $.ajax({
      url: "doctor/getDoctorVisit?id=" + doctorr,
      method: "GET",
      data: "",
      dataType: "json",
      success: function (response1) {
        $("#visit_description").html(response1.response).end();
      },
    });


  });

});

$(document).ready(function () {
  "use strict";
  var iid = $('#date').val();
  var doctorr = $('#adoctors').val();
  $('#aslots').find('option').remove();

  $.ajax({
    url: 'schedule/getAvailableSlotByDoctorByDateByJason?date=' + iid + '&doctor=' + doctorr,
    method: 'GET',
    data: '',
    dataType: 'json',
    success: function (response) {
      "use strict";
      var slots = response.aslots;
      $.each(slots, function (key, value) {
        "use strict";
        $('#aslots').append($('<option>').text(value).val(value)).end();
      });

      if ($('#aslots').has('option').length == 0) {                    //if it is blank. 
        $('#aslots').append($('<option>').text('No Further Time Slots').val('Not Selected')).end();
      }
    }
  })

});




$(document).ready(function () {
  "use strict";
  $('#date').datepicker({
    format: "dd-mm-yyyy",
    autoclose: true,
  })
    //Listen for the change even on the input
    // .change(dateChanged)
    .on('changeDate', dateChanged);
});

function dateChanged() {
  "use strict";
  // Get the record's ID via attribute  
  var iid = $('#date').val();
  var doctorr = $('#adoctors').val();
  $('#aslots').find('option').remove();

  $.ajax({
    url: 'schedule/getAvailableSlotByDoctorByDateByJason?date=' + iid + '&doctor=' + doctorr,
    method: 'GET',
    data: '',
    dataType: 'json',
    success: function (response) {
      "use strict";
      var slots = response.aslots;
      $.each(slots, function (key, value) {
        "use strict";
        $('#aslots').append($('<option>').text(value).val(value)).end();
      });

      if ($('#aslots').has('option').length == 0) {                    //if it is blank. 
        $('#aslots').append($('<option>').text('No Further Time Slots').val('Not Selected')).end();
      }
    }
  })

}

$(document).ready(function () {
  "use strict";
  $(".doctor_div1").on("change", "#adoctors1", function () {
    "use strict";
    var id = $('#appointment_id').val();
    var date = $('#date1').val();
    var doctorr = $('#adoctors1').val();
    $('#aslots1').find('option').remove();

    $.ajax({
      url: 'schedule/getAvailableSlotByDoctorByDateByAppointmentIdByJason?date=' + date + '&doctor=' + doctorr + '&appointment_id=' + id,
      method: 'GET',
      data: '',
      dataType: 'json',
      success: function (response) {
        "use strict";
        var slots = response.aslots;
        $.each(slots, function (key, value) {
          $('#aslots1').append($('<option>').text(value).val(value)).end();
        });

        if ($('#aslots1').has('option').length == 0) {                    //if it is blank. 
          $('#aslots1').append($('<option>').text('No Further Time Slots').val('Not Selected')).end();
        }

      }
    })

    $("#visit_description1").html(" ");
    $("#visit_charges1").val(" ");
    $.ajax({
      url: "doctor/getDoctorVisit?id=" + doctorr,
      method: "GET",
      data: "",
      dataType: "json",
      success: function (response1) {
        $("#visit_description1").html(response1.response).end();
      },
    });
  });
});

$(document).ready(function () {
  "use strict";
  var id = $('#appointment_id').val();
  var date = $('#date1').val();
  var doctorr = $('#adoctors1').val();
  $('#aslots1').find('option').remove();

  $.ajax({
    url: 'schedule/getAvailableSlotByDoctorByDateByAppointmentIdByJason?date=' + date + '&doctor=' + doctorr + '&appointment_id=' + id,
    method: 'GET',
    data: '',
    dataType: 'json',
    success: function (response) {
      "use strict";
      var slots = response.aslots;
      $.each(slots, function (key, value) {
        "use strict";
        $('#aslots1').append($('<option>').text(value).val(value)).end();
      });

      if ($('#aslots1').has('option').length == 0) {                    //if it is blank. 
        $('#aslots1').append($('<option>').text('No Further Time Slots').val('Not Selected')).end();
      }
    }
  })

});
$(document).ready(function () {
  "use strict";
  $('#date1').datepicker({
    format: "dd-mm-yyyy",
    autoclose: true,
  })

    // .change(dateChanged1)
    .on('changeDate', dateChanged1);
});

function dateChanged1() {
  "use strict";
  // Get the record's ID via attribute  
  var id = $('#appointment_id').val();
  var iid = $('#date1').val();
  var doctorr = $('#adoctors1').val();
  $('#aslots1').find('option').remove();

  $.ajax({
    url: 'schedule/getAvailableSlotByDoctorByDateByAppointmentIdByJason?date=' + iid + '&doctor=' + doctorr + '&appointment_id=' + id,
    method: 'GET',
    data: '',
    dataType: 'json',
    success: function (response) {
      "use strict";
      var slots = response.aslots;
      $.each(slots, function (key, value) {
        "use strict";
        $('#aslots1').append($('<option>').text(value).val(value)).end();
      });

      if ($('#aslots1').has('option').length == 0) {                    //if it is blank. 
        $('#aslots1').append($('<option>').text('No Further Time Slots').val('Not Selected')).end();
      }
    }
  })

}









$(document).ready(function () {
  "use strict";
  var table = $('#editable-sample').DataTable({
    responsive: true,
    dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-5'i><'col-sm-7'p>>",

    buttons: [
      { extend: 'copyHtml5', exportOptions: { columns: [0, 1], } },
      { extend: 'excelHtml5', exportOptions: { columns: [0, 1], } },
      { extend: 'csvHtml5', exportOptions: { columns: [0, 1], } },
      { extend: 'pdfHtml5', exportOptions: { columns: [0, 1], } },
      { extend: 'print', exportOptions: { columns: [0, 1], } },
    ],
    aLengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "All"]
    ],
    iDisplayLength: -1,
    "order": [[0, "desc"]],
    "language": {
      "lengthMenu": "_MENU_",
      search: "_INPUT_",
      searchPlaceholder: "Search..."
    },
  });

  table.buttons().container()
    .appendTo('.custom_buttons');
});




$(document).ready(function () {
  "use strict";
  $("#pos_select").select2({
    placeholder: select_patient,
    allowClear: true,
    ajax: {
      url: 'patient/getPatientinfoWithAddNewOption',
      type: "post",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          searchTerm: params.term // search term
        };
      },
      processResults: function (response) {
        return {
          results: response
        };
      },
      cache: true
    }

  });
  $("#pos_select1").select2({
    placeholder: select_patient,
    allowClear: true,
    ajax: {
      url: 'patient/getPatientinfoWithAddNewOption',
      type: "post",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          searchTerm: params.term // search term
        };
      },
      processResults: function (response) {
        return {
          results: response
        };
      },
      cache: true
    }

  });
  $(".patient").select2({
    placeholder: select_patient,
    allowClear: true,
    ajax: {
      url: 'patient/getPatientinfo',
      type: "post",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          searchTerm: params.term // search term
        };
      },
      processResults: function (response) {
        return {
          results: response
        };
      },
      cache: true
    }

  });
  $("#add_doctor").select2({
    placeholder: select_doctor,
    allowClear: true,
    ajax: {
      url: 'doctor/getDoctorWithAddNewOption',
      type: "post",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          searchTerm: params.term // search term
        };
      },
      processResults: function (response) {
        return {
          results: response
        };
      },
      cache: true
    }

  });

});

$(document).ready(function () {
  "use strict";
  $(".flashmessage").delay(3000).fadeOut(100);
});


var myEditor1;

$(document).ready(function () {
  ClassicEditor
    .create(document.querySelector('#editor'))
    .then(editor => {
      editor.ui.view.editable.element.style.height = '200px';
      myEditor1 = editor;
    })
    .catch(error => {
      console.error(error);
    });



});






$(document).ready(function () {
  "use strict";
  $(".timepickers_time").on("change", "#s_time", function () {
    var s_time = $(this).val();
    var e_time = $("#e_time").val();
    var weekday = $("#weekday").val();
    var doctor = $("#doctorchoose").val();
    $.ajax({
      url: "schedule/getAvailableScheduleStime",
      method: "GET",
      data: {
        s_time: s_time,
        e_time: e_time,
        weekday: weekday,
        doctor: doctor,
      },
      dataType: "json",
      success: function (response) {
        if (response.response == "no") {
          alert("You have Already Schedule on that time.Please Change It");
          $("#addSubmit").attr("disabled", true);
        } else {
          $("#addSubmit").attr("disabled", false);
        }
      },
    });
  });
  $(".timepickere_time").on("change", "#e_time", function () {
    var e_time = $(this).val();
    var s_time = $("#s_time").val();
    var weekday = $("#weekday").val();
    var doctor = $("#doctorchoose").val();
    $.ajax({
      url: "schedule/getAvailableScheduleEtime",
      method: "GET",
      data: {
        s_time: s_time,
        e_time: e_time,
        weekday: weekday,
        doctor: doctor,
      },
      dataType: "json",
      success: function (response) {
        if (response.response == "no") {
          alert("You have Already Schedule on that time.Please Change It");
          $("#addSubmit").attr("disabled", true);
        } else if (response.response == "upper_lower") {
          alert("Start time Should be Lower Than The End Time");
          $("#addSubmit").attr("disabled", true);
        } else {
          $("#addSubmit").attr("disabled", false);
        }
      },
    });
  });
  $(".doctor_div").on("change", "#doctorchoose", function () {
    var doctor = $(this).val();
    var e_time = $("#e_time").val();
    var s_time = $("#s_time").val();
    var weekday = $("#weekday").val();
    if (s_time === null || e_time !== null) {
      $.ajax({
        url: "schedule/getAvailableScheduleEtime",
        method: "GET",
        data: {
          s_time: s_time,
          e_time: e_time,
          weekday: weekday,
          doctor: doctor,
        },
        dataType: "json",
        success: function (response) {
          if (response.response == "no") {
            alert("You have Already Schedule on that time.Please Change It");
            $("#addSubmit").attr("disabled", true);
          } else if (response.response == "upper_lower") {
            alert("Start time Should be Lower Than The End Time");
            $("#addSubmit").attr("disabled", true);
          } else {
            $("#addSubmit").attr("disabled", false);
          }
        },
      });
    } else {
      $.ajax({
        url: "schedule/getAvailableScheduleStime",
        method: "GET",
        data: {
          s_time: s_time,
          e_time: e_time,
          weekday: weekday,
          doctor: doctor,
        },
        dataType: "json",
        success: function (response) {
          if (response.response == "no") {
            alert("You have Already Schedule on that time.Please Change It");
            $("#addSubmit").attr("disabled", true);
          } else if (response.response == "upper_lower") {
            alert("Start time Should be Lower Than The End Time");
            $("#addSubmit").attr("disabled", true);
          } else {
            $("#addSubmit").attr("disabled", false);
          }
        },
      });
    }
  });
  $(".weekday_div").on("change", "#weekday", function () {
    var doctor = $("#doctorchoose").val();
    var e_time = $("#e_time").val();
    var s_time = $("#s_time").val();
    var weekday = $(this).val();
    if (s_time === null || e_time !== null) {
      $.ajax({
        url: "schedule/getAvailableScheduleEtime",
        method: "GET",
        data: {
          s_time: s_time,
          e_time: e_time,
          weekday: weekday,
          doctor: doctor,
        },
        dataType: "json",
        success: function (response) {
          if (response.response == "no") {
            alert("You have Already Schedule on that time.Please Change It");
            $("#addSubmit").attr("disabled", true);
          } else if (response.response == "upper_lower") {
            alert("Start time Should be Lower Than The End Time");
            $("#addSubmit").attr("disabled", true);
          } else {
            $("#addSubmit").attr("disabled", false);
          }
        },
      });
    } else {
      $.ajax({
        url: "schedule/getAvailableScheduleStime",
        method: "GET",
        data: {
          s_time: s_time,
          e_time: e_time,
          weekday: weekday,
          doctor: doctor,
        },
        dataType: "json",
        success: function (response) {
          if (response.response == "no") {
            alert("You have Already Schedule on that time.Please Change It");
            $("#addSubmit").attr("disabled", true);
          } else if (response.response == "upper_lower") {
            alert("Start time Should be Lower Than The End Time");
            $("#addSubmit").attr("disabled", true);
          } else {
            $("#addSubmit").attr("disabled", false);
          }
        },
      });
    }
  });
});

$(document).ready(function () {
  "use strict";
  $(".flashmessage").delay(3000).fadeOut(100);
});




$(document).ready(function () {
  $("#visit_description").change(function () {
    var id = $(this).val();
    $("#visit_charges").val(" ");
    $.ajax({
      url: "doctor/getDoctorVisitCharges?id=" + id,
      method: "GET",
      dataType: "json",
      success: function (response) {
        $("#visit_charges").val(response.response.visit_charges).end();
        var discount = $("#discount").val();
        $("#grand_total")
          .val(parseFloat(response.response.visit_charges - discount))
          .end();
      },
    });
  });
  $("#discount").keyup(function () {
    var discount = $(this).val();
    var price = $("#visit_charges").val();
    $("#grand_total")
      .val(parseFloat(price - discount))
      .end();
  });
});


$(document).ready(function () {
  $("#visit_description1").change(function () {
    var id = $(this).val();
    $("#visit_charges1").val(" ");
    $.ajax({
      url: "doctor/getDoctorVisitCharges?id=" + id,
      method: "GET",
      dataType: "json",
      success: function (response) {
        $("#visit_charges1").val(response.response.visit_charges).end();
        var discount = $("#discount1").val();
        $("#grand_total1")
          .val(parseFloat(response.response.visit_charges - discount))
          .end();
      },
    });
  });
  $("#discount1").keyup(function () {
    var discount = $(this).val();
    var price = $("#visit_charges1").val();
    $("#grand_total1")
      .val(parseFloat(price - discount))
      .end();
  });
});






function cardValidation() {
  "use strict";
  var valid = true;
  var cardNumber = $("#card").val();
  var expire = $("#expire").val();
  var cvc = $("#cvv").val();

  $("#error-message").html("").hide();

  if (cardNumber.trim() == "") {
    valid = false;
  }

  if (expire.trim() == "") {
    valid = false;
  }
  if (cvc.trim() == "") {
    valid = false;
  }

  if (valid == false) {
    $("#error-message").html("All Fields are required").show();
  }

  return valid;
}
//set your publishable key
Stripe.setPublishableKey(publish);

//callback to handle the response from stripe
function stripeResponseHandler(status, response) {
  "use strict";
  if (response.error) {
    $("#submit-btn").show();
    $("#loader").css("display", "none");

    $("#error-message").html(response.error.message).show();
    $("#submit-btn").attr("disabled", false);
    $("#error-message").html(response.error.message).show();
  } else {
    var token = response["id"];
    if (token != null) {
      $("#token").val(token);
      $("#addAppointmentForm").append(
        "<input type='hidden' name='token' value='" + token + "' />"
      );
      $("#addAppointmentForm").submit();
    } else {
      alert("Please Check Your Card details");
      $("#submit-btn").attr("disabled", false);
    }
  }
}

function stripePay(e) {
  "use strict";
  e.preventDefault();
  var valid = cardValidation();

  if (valid == true) {
    $("#submit-btn").attr("disabled", true);
    $("#loader").css("display", "inline-block");
    var expire = $("#expire").val();
    var arr = expire.split("/");
    Stripe.createToken(
      {
        number: $("#card").val(),
        cvc: $("#cvv").val(),
        exp_month: arr[0],
        exp_year: arr[1],
      },
      stripeResponseHandler
    );

    return false;
  }
}
function cardValidation1() {
  var valid = true;
  var cardNumber = $("#card1").val();
  var expire = $("#expire1").val();
  var cvc = $("#cvv1").val();

  $("#error-message").html("").hide();

  if (cardNumber.trim() == "") {
    valid = false;
  }

  if (expire.trim() == "") {
    valid = false;
  }
  if (cvc.trim() == "") {
    valid = false;
  }

  if (valid == false) {
    $("#error-message").html("All Fields are required").show();
  }

  return valid;
}
//set your publishable key
Stripe.setPublishableKey(publish);

//callback to handle the response from stripe
function stripeResponseHandler1(status, response) {
  if (response.error) {
    //enable the submit button
    $("#submit-btn1").show();
    $("#loader").css("display", "none");
    //display the errors on the form
    $("#error-message").html(response.error.message).show();
  } else {
    //get token id
    var token = response["id"];
    //insert the token into the form
    $("#token").val(token);
    $("#editAppointmentForm").append(
      "<input type='hidden' name='token' value='" + token + "' />"
    );
    //submit form to the server
    $("#editAppointmentForm").submit();
  }
}

function stripePay1(e) {
  e.preventDefault();
  var valid = cardValidation1();

  if (valid == true) {
    $("#submit-btn1").attr("disabled", true);
    $("#loader").css("display", "inline-block");
    var expire = $("#expire1").val();
    var arr = expire.split("/");
    Stripe.createToken(
      {
        number: $("#card1").val(),
        cvc: $("#cvv1").val(),
        exp_month: arr[0],
        exp_year: arr[1],
      },
      stripeResponseHandler1
    );

    //submit from callback
    return false;
  }
}

if (payment_gateway == "2Checkout") {
  var successCallback = function (data) {
    "use strict";
    var myForm = document.getElementById("addAppointmentForm");

    $("#addAppointmentForm").append(
      "<input type='hidden' name='token' value='" +
      data.response.token.token +
      "' />"
    );

    myForm.submit();
  };
  // Called when token creation fails.
  var errorCallback = function (data) {
    "use strict";
    if (data.errorCode === 200) {
      tokenRequest();
    } else {
      alert(data.errorMsg);
    }
  };
  var tokenRequest = function () {
    "use strict";
    var expire = $("#expire").val();
    var expiresep = expire.split("/");
    var dateformat = moment(expiresep[1], "YY");
    var year = dateformat.format("YYYY");
    var args = {
      sellerId: merchant,
      publishableKey: publishable,
      ccNo: $("#card").val(),
      cvv: $("#cvv").val(),
      expMonth: expiresep[0],
      expYear: year,
    };
    console.log(
      $("#card").val() + "-" + $("#cvv").val() + expiresep[0] + year + merchant
    );

    TCO.requestToken(successCallback, errorCallback, args);
  };

  function twoCheckoutPay(e) {
    "use strict";
    e.preventDefault();

    TCO.loadPubKey("sandbox", function () {
      // for sandbox environment
      publishableKey = publishable; //your public key
      tokenRequest();
    });

    return false;
  }

  var successCallback1 = function (data) {
    "use strict";
    var myForm = document.getElementById("editAppointmentForm");

    $("#editAppointmentForm").append(
      "<input type='hidden' name='token' value='" +
      data.response.token.token +
      "' />"
    );

    myForm.submit();
  };
  // Called when token creation fails.
  var errorCallback1 = function (data) {
    "use strict";
    if (data.errorCode === 200) {
      tokenRequest1();
    } else {
      alert(data.errorMsg);
    }
  };
  var tokenRequest1 = function () {
    "use strict";
    var expire = $("#expire1").val();
    var expiresep = expire.split("/");
    var dateformat = moment(expiresep[1], "YY");
    var year = dateformat.format("YYYY");
    var args = {
      sellerId: merchant,
      publishableKey: publishable,
      ccNo: $("#card1").val(),
      cvv: $("#cvv1").val(),
      expMonth: expiresep[0],
      expYear: year,
    };
    console.log(
      $("#card1").val() +
      "-" +
      $("#cvv1").val() +
      expiresep[0] +
      year +
      merchant
    );

    TCO.requestToken(successCallback, errorCallback, args);
  };

  function twoCheckoutPay1(e) {
    "use strict";
    e.preventDefault();

    TCO.loadPubKey("sandbox", function () {
      // for sandbox environment
      publishableKey = publishable; //your public key
      tokenRequest1();
    });

    return false;
  }
}

