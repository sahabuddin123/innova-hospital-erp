"use strict";

// var myEditor;
// var myEditor3;
// $(document).ready(function () {
//   const watchdog = new CKSource.EditorWatchdog();

//   window.watchdog = watchdog;

//   watchdog.setCreator((element, config) => {
//     return CKSource.Editor.create(element, config).then((editor) => {
//       return editor;
//     });
//   });

//   watchdog.setDestructor((editor) => {
//     return editor.destroy();
//   });

//   watchdog.on("error", handleError);

//   watchdog
//     .create(document.querySelector("#editor1"), {
//       licenseKey: "",
//     })
//     .catch(handleError);

//   function handleError(error) {
//     console.error("Oops, something went wrong!");
//     console.error(
//       "Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:"
//     );
//     console.warn("Build id: c4qhnvrcw0wv-nohdljl880ze");
//     console.error(error);
//   }
// ClassicEditor.create(document.querySelector("#editor1"))
//   .then((editor) => {
//     editor.ui.view.editable.element.style.height = "200px";
//     myEditor = editor;
// editor.model.document.on("change:data", (evt, data) => {
//   //let html = $('.ck-restricted-editing_mode_standard').text();
//   let html = $(".profile >.ck-restricted-editing_mode_standard").html();
//   $("#profile").val(html);
//   //    let text = editor.getData();
//   //    $('#report').val(text);
// });
// })
// .catch((error) => {
//   console.error(error);
// });

// ClassicEditor.create(document.querySelector("#editor3"))
//   .then((editor) => {
//     editor.ui.view.editable.element.style.height = "200px";
//     myEditor3 = editor;
// editor.model.document.on("change:data", (evt, data) => {
//   //let html = $('.ck-restricted-editing_mode_standard').text();
//   let html = $(".profile1 >.ck-restricted-editing_mode_standard").html();
//   $("#profile1").val(html);
//   //    let text = editor.getData();
//   //    $('#report').val(text);
// });
//     })
//     .catch((error) => {
//       console.error(error);
//     });
// });
tinymce.remove('#editor1');
tinymce.init({
  selector: '#editor1',
  min_height: 100,
  plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
  menubar: 'file edit view insert format tools table help',
  toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
  branding: false,
  promotion: false
});




$(document).ready(function () {
  "use strict";

  $(".table").on("click", ".editbutton", function () {
    "use strict";
    $("#loader").show();
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
        $("#editDoctorForm")
          .find('[name="degrees"]')
          .val(response.doctor.degrees || "")
          .end();

        // myEditor3.setData(response.doctor.profile);
        //$("#profile1").val(response.doctor.profile);
        tinymce.remove('#editor3');
        tinymce.init({
          selector: '#editor3',
          plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
          menubar: 'file edit view insert format tools table help',
          toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
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

        $("#myModal2").modal("show");
      },
      complete: function () {
        $("#loader").hide();
      },
    });
  });
});

$(document).ready(function () {
  "use strict";

  $(".table").on("click", ".inffo", function () {
    "use strict";
    $("#loader").show();
    var iid = $(this).attr("data-id");

    $(".nameClass").html("").end();
    $(".emailClass").html("").end();
    $(".addressClass").html("").end();
    $(".phoneClass").html("").end();
    $(".departmentClass").html("").end();
    $(".degreesClass").html("").end();
    $(".profileClass").html("").end();
    $.ajax({
      url: "doctor/editDoctorByJason?id=" + iid,
      method: "GET",
      data: "",
      dataType: "json",
      success: function (response) {
        "use strict";

        $("#editDoctorForm1").find('[name="id"]').val(response.doctor.id).end();
        $(".nameClass").append(response.doctor.name).end();
        $(".emailClass").append(response.doctor.email).end();
        $(".addressClass").append(response.doctor.address).end();
        $(".phoneClass").append(response.doctor.phone).end();
        $(".departmentClass").append(response.doctor.department_name).end();
        $(".degreesClass").append(response.doctor.degrees || "N/A").end();
        $(".profileClass").append(response.doctor.profile).end();

        $("#img1").attr(
          "src",
          "uploads/cardiology-patient-icon-vector-6244713.jpg"
        );

        if (
          typeof response.doctor.img_url !== "undefined" &&
          response.doctor.img_url !== ""
        ) {
          $("#img1").attr("src", response.doctor.img_url);
        }

        $("#infoModal").modal("show");
      },
      complete: function () {
        $("#loader").hide();
      },
    });
  });
});

$(document).ready(function () {
  "use strict";

  var table = $("#editable-sample").DataTable({
    responsive: true,

    processing: true,
    serverSide: true,
    searchable: true,
    bScrollCollapse: true,
    ajax: {
      url: "doctor/getDoctor",
      type: "POST",
    },
    scroller: {
      loadingIndicator: true,
    },

    dom:
      "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-5'i><'col-sm-7'p>>",

    buttons: [
      { extend: "copyHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
      { extend: "excelHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
      { extend: "csvHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
      { extend: "pdfHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
      { extend: "print", exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
    ],
    aLengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "All"],
    ],
    iDisplayLength: 100,
    order: [[0, "desc"]],

    language: {
      lengthMenu: "_MENU_",
      search: "_INPUT_",
      url: "common/assets/DataTables/languages/" + language + ".json",
    },
  });
  table.buttons().container().appendTo(".custom_buttons");
});

$(document).ready(function () {
  "use strict";

  $(".flashmessage").delay(3000).fadeOut(100);
  $(".signature_class").on(
    "click",
    "#remove_button_doctor_signature",
    function (e) {
      e.preventDefault();
      $("#signature").attr("src", "");
      var id = $("#id_value").val();
      $.ajax({
        url: "doctor/deleteDoctorImage?id=" + id,
        method: "GET",
        data: "",
        dataType: "json",
        success: function (response) {
          $("#signature").attr("src", "");
          console.log("deleted");
          // $("#signature").removeAttribute("src", "");
          // $("#signature").style.display = "none";
        },
      });
    }
  );
});
