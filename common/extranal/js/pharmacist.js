"use strict";
// var myEditor;
// var myEditor3;
// $(document).ready(function () {
//   ClassicEditor.create(document.querySelector("#editor1"))
//     .then((editor) => {
//       editor.ui.view.editable.element.style.height = "200px";
//       myEditor = editor;
// editor.model.document.on("change:data", (evt, data) => {
//   //let html = $('.ck-restricted-editing_mode_standard').text();
//   let html = $(".ck-restricted-editing_mode_standard").html();
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
//   let html = $(".ck-restricted-editing_mode_standard").html();
//   $("#profile1").val(html);
//   //    let text = editor.getData();
//   //    $('#report').val(text);
// });
//     })
//     .catch((error) => {
//       console.error(error);
//     });
// });
tinymce.init({
  selector: '#editor1',
  plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
  menubar: 'file edit view insert format tools table help',
  toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | ltr rtl',
  branding: false,
  promotion: false
});
$(document).ready(function () {
  $(".table").on("click", ".editbutton", function () {
    "use strict";
    $("#loader").show();
    var iid = $(this).attr("data-id");
    $("#editPharmacistForm").trigger("reset");
    $.ajax({
      url: "pharmacist/editPharmacistByJason?id=" + iid,
      method: "GET",
      data: "",
      dataType: "json",
      success: function (response) {
        "use strict";
        $("#editPharmacistForm")
          .find('[name="id"]')
          .val(response.pharmacist.id)
          .end();
        $("#editPharmacistForm")
          .find('[name="name"]')
          .val(response.pharmacist.name)
          .end();
        $("#editPharmacistForm")
          .find('[name="password"]')
          .val(response.pharmacist.password)
          .end();
        $("#editPharmacistForm")
          .find('[name="email"]')
          .val(response.pharmacist.email)
          .end();
        $("#editPharmacistForm")
          .find('[name="address"]')
          .val(response.pharmacist.address)
          .end();
        $("#editPharmacistForm")
          .find('[name="phone"]')
          .val(response.pharmacist.phone)
          .end();
        if (response.pharmacist.profile !== null) {
          tinymce.remove('#editor3');
          tinymce.init({
            selector: '#editor3',
            plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | ltr rtl',
            branding: false,
            promotion: false,
            init_instance_callback: function (editor) {
              // editor.setContent();
              editor.setContent(response.pharmacist.profile);
            }
          });
          // myEditor3.setData(response.pharmacist.profile);
          //  $("#profile1").val(response.pharmacist.profile);
        }

        if (
          typeof response.pharmacist.img_url !== "undefined" &&
          response.pharmacist.img_url !== ""
        ) {
          $("#img").attr("src", response.pharmacist.img_url);
        }
        if (
          typeof response.pharmacist.signature !== "undefined" &&
          response.pharmacist.signature !== ""
        ) {
          $("#signature").attr("src", response.pharmacist.signature);
        }
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
  var table = $("#editable-sample").DataTable({
    responsive: true,

    dom:
      "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-5'i><'col-sm-7'p>>",

    buttons: [
      { extend: "copyHtml5", exportOptions: { columns: [1, 2, 3, 4] } },
      { extend: "excelHtml5", exportOptions: { columns: [1, 2, 3, 4] } },
      { extend: "csvHtml5", exportOptions: { columns: [1, 2, 3, 4] } },
      { extend: "pdfHtml5", exportOptions: { columns: [1, 2, 3, 4] } },
      { extend: "print", exportOptions: { columns: [1, 2, 3, 4] } },
    ],
    aLengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "All"],
    ],
    iDisplayLength: -1,
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
    "#remove_button_pharmacist_signature",
    function (e) {
      e.preventDefault();
      $("#signature").attr("src", "");
      var id = $("#id_value").val();
      $.ajax({
        url: "pharmacist/deletePharmacistImage?id=" + id,
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
