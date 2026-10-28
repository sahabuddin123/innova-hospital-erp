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
//   let html = $(".profile1")
//     .next(".ck-restricted-editing_mode_standard")
//     .html();
//   console.log(html);
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
  "use strict";
  $(".table").on("click", ".editbutton", function () {
    "use strict";
    $("#loader").show();
    var iid = $(this).attr("data-id");
    $("#editReceptionistForm").trigger("reset");
    $.ajax({
      url: "receptionist/editReceptionistByJason?id=" + iid,
      method: "GET",
      data: "",
      dataType: "json",
      success: function (response) {
        "use strict";
        $("#editReceptionistForm")
          .find('[name="id"]')
          .val(response.receptionist.id)
          .end();
        $("#editReceptionistForm")
          .find('[name="name"]')
          .val(response.receptionist.name)
          .end();
        $("#editReceptionistForm")
          .find('[name="password"]')
          .val(response.receptionist.password)
          .end();
        $("#editReceptionistForm")
          .find('[name="email"]')
          .val(response.receptionist.email)
          .end();
        $("#editReceptionistForm")
          .find('[name="address"]')
          .val(response.receptionist.address)
          .end();
        $("#editReceptionistForm")
          .find('[name="phone"]')
          .val(response.receptionist.phone)
          .end();
        if (response.receptionist.profile !== null) {
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
              editor.setContent(response.receptionist.profile);
            }
          });
          // myEditor3.setData(response.receptionist.profile);
          //  $("#profile1").val(response.receptionist.profile);
        }

        if (
          typeof response.receptionist.img_url !== "undefined" &&
          response.receptionist.img_url !== ""
        ) {
          $("#img").attr("src", response.receptionist.img_url);
        }
        if (
          typeof response.receptionist.signature !== "undefined" &&
          response.receptionist.signature !== ""
        ) {
          $("#signature").attr("src", response.receptionist.signature);
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
    "#remove_button_receptionist_signature",
    function (e) {
      e.preventDefault();
      $("#signature").attr("src", "");
      var id = $("#id_value").val();
      $.ajax({
        url: "receptionist/deleteReceptionistImage?id=" + id,
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
