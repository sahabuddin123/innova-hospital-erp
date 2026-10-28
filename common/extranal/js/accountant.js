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
  "use strict";
  $(".table").on("click", ".editbutton", function () {
    $("#loader").show();
    "use strict";
    var iid = $(this).attr("data-id");
    $("#editAccountantForm").trigger("reset");
    $.ajax({
      url: "accountant/editAccountantByJason?id=" + iid,
      method: "GET",
      data: "",
      dataType: "json",
      success: function (response) {
        "use strict";
        $("#editAccountantForm")
          .find('[name="id"]')
          .val(response.accountant.id)
          .end();
        $("#editAccountantForm")
          .find('[name="name"]')
          .val(response.accountant.name)
          .end();
        $("#editAccountantForm")
          .find('[name="password"]')
          .val(response.accountant.password)
          .end();
        $("#editAccountantForm")
          .find('[name="email"]')
          .val(response.accountant.email)
          .end();
        $("#editAccountantForm")
          .find('[name="address"]')
          .val(response.accountant.address)
          .end();
        $("#editAccountantForm")
          .find('[name="phone"]')
          .val(response.accountant.phone)
          .end();
        if (response.accountant.profile !== null) {
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
              editor.setContent(response.accountant.profile);
            }
          });
          // myEditor3.setData(response.accountant.profile);
          //$("#profile1").val(response.accountant.profile);
        }
        if (
          typeof response.accountant.img_url !== "undefined" &&
          response.accountant.img_url !== ""
        ) {
          $("#img").attr("src", response.accountant.img_url);
        }
        if (
          typeof response.accountant.signature !== "undefined" &&
          response.accountant.signature !== ""
        ) {
          $("#signature").attr("src", response.accountant.signature);
        }
        $("#myModal2").modal("show");
      },
      complete: function () {
        $("#loader").hide();
      }
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
      { extend: "csvHtml5", exportOptions: { columns: [0, 1, 2, 3, 4] } },
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
    "#remove_button_accountant_signature",
    function (e) {
      e.preventDefault();
      var id = $("#id_value").val();
      $("#signature").attr("src", "");
      $.ajax({
        url: "accountant/deleteAccountantImage?id=" + id,
        method: "GET",
        data: "",
        dataType: "json",
        success: function (response) {
          $("#signature").attr("src", "");
          // // $(".signature_class").find('[class="address"]').empty();
          // var img = $("#img_url1").find("img"), // select images inside .container
          //   len = img.length; // check if they exist
          // if (len > 0) {
          //   // images found, get id
          //   var attrID = img.first().attr("src"); // get id of first image
          // }
          // if (attrID !== null || attrID !== "unknown" || attrID !== "") {
          //   $("#img_url1 img").attr("src", "");
          //   $(".fileupload-exists >.fileupload-new").css("display", "");
          //   $(".fileupload-new > .fileupload-exists").css("display", "none");
          // }
          console.log("deleted");
          // $("#signature").removeAttribute("src", "");
          // $("#signature").style.display = "none";
        },
      });
    }
  );
});
