"use strict";
tinymce.remove('#editor1');
tinymce.init({
  selector: '#editor1',
  min_height: 100,
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
    $("#editSymptomForm").trigger("reset");

    $.ajax({
      url: "symptom/editSymptomByJason?id=" + iid,
      method: "GET",
      data: "",
      dataType: "json",
      success: function (response) {
        "use strict";

        // Populate the form fields with the data returned from server
        $("#editSymptomForm").find('[name="id"]').val(response.symptom.id).end();
        $("#editSymptomForm")
          .find('[name="name"]')
          .val(response.symptom.name)
          .end();


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
            editor.setContent(response.symptom.description);
          }
        });


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

    processing: true,
    serverSide: true,
    searchable: true,
    bScrollCollapse: true,
    ajax: {
      url: "symptom/getSymptomList",
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