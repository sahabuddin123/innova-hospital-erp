"use strict";

$(document).ready(function () {
  "use strict";
  var category = $(".category").val();

  var table = $("#editable-sample").DataTable({
    responsive: true,
    //   dom: 'lfrBtip',

    processing: true,
    serverSide: true,
    searchable: true,
    ajax: {
      url: "finance/getCategoryList?category=" + category,
      type: "POST",
    },
    scroller: {
      loadingIndicator: true,
    },
    dom:
      "<'row'<'col-md-3'l><'col-sm-5 text-center'B><'col-sm-4'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-5'i><'col-sm-7'p>>",

    buttons: [
      {
        extend: "copyHtml5",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] },
      },
      {
        extend: "excelHtml5",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] },
      },
      { extend: "csvHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
      { extend: "pdfHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
      { extend: "print", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
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
  $(".category").on("change", function () {
    var category = $(this).val();
    $("#editable-sample").DataTable().destroy().clear();
    var table = $("#editable-sample").DataTable({
      responsive: true,
      //   dom: 'lfrBtip',

      processing: true,
      serverSide: true,
      searchable: true,
      ajax: {
        url: "finance/getCategoryList?category=" + category,
        type: "POST",
      },
      scroller: {
        loadingIndicator: true,
      },
      dom:
        "<'row'<'col-md-3'l><'col-sm-5 text-center'B><'col-sm-4'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-5'i><'col-sm-7'p>>",

      buttons: [
        {
          extend: "copyHtml5",
          exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] },
        },
        {
          extend: "excelHtml5",
          exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] },
        },
        {
          extend: "csvHtml5",
          exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] },
        },
        {
          extend: "pdfHtml5",
          exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] },
        },
        { extend: "print", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
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
});
$(document).ready(function () {
  $("#category_name").keyup(function () {
    $("#notification").html("");
    var attr = $(this).val();
    var id = $("#id").val();
    $.ajax({
      url: "finance/getPaymentCategoryNameVerify?attr=" + attr + "&id=" + id,
      method: "GET",
      data: "",
      dataType: "json",
      success: function (response) {
        if (response.response == "yes") {
          $("#notification").html(" ");
          $("#submit_form").attr("disabled", false);
        } else {
          $("#notification").html("Already Existed Payment Item Name");
          $("#submit_form").attr("disabled", true);
        }
      },
    });
  });


//   var myEditor;

// $(document).ready(function () {
//   ClassicEditor.create(document.querySelector("#editor1"))
//     .then((editor) => {
//       //editor.ui.view.editable.element.style.height = "200px";
//       myEditor = editor;
//     })
//     .catch((error) => {
//       console.error(error);
//     });
// });

  $(".table").on("click", ".template", function () {
    "use strict";
    $("#loader").show();
    var iid = $(this).attr("data-id");
    $("#addTemplate").find('[name="id"]').val("");
    $("#editor1").val("");
    $("#addTemplate").trigger("reset");
    
    $.ajax({
      url: "finance/getPaymentProccedureTemplate?id=" + iid,
      method: "GET",
      data: "",
      dataType: "json",
      success: function (response) {
       
        tinymce.remove('#editor1');
        tinymce.init({
          selector: '#editor1',
          plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
          menubar: 'file edit view insert format tools table help',
          toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | ltr rtl',
          branding: false,
          promotion: false,
          init_instance_callback: function (editor) {
            // editor.setContent();
            editor.setContent(response.payment_proccedure.report);
          }
        });
        $("#addTemplate")
          .find('[name="id"]')
          .val(response.payment_proccedure.id)
          .end();

          $("#myModal").modal("show");
      },
      complete: function () {
        $("#loader").hide();
      },
    });
  });
});
