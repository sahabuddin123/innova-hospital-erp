"use strict";
// var myEditor;
// var myEditor1;
// $(document).ready(function () {
//     ClassicEditor
//         .create(document.querySelector('#editor'))
//         .then(editor => {
//             editor.model.document.on('change:data', () => {
//                 $("#editor").html(editor.getData());;
//             });
//             editor.ui.view.editable.element.style.height = '200px';
//             myEditor = editor;
//         })
//         .catch(error => {
//             console.error(error);
//         });
//     ClassicEditor
//         .create(document.querySelector('#editor1'))
//         .then(editor1 => {
//             editor1.model.document.on('change:data', () => {
//                 $("#editor1").html(editor1.getData());;
//             });
//             editor1.ui.view.editable.element.style.height = '200px';
//             myEditor1 = editor1;

//         })
//         .catch(error => {
//             console.error(error);
//         });
// });
tinymce.init({
    selector: '#editor',
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | ltr rtl',
    branding: false,
    promotion: false
});
$(document).ready(function () {
    "use strict";
    var table = $("#editable-sample").DataTable({
        responsive: true,
        //   dom: 'lfrBtip',

        processing: true,
        serverSide: true,
        searchable: true,
        ajax: {
            url: "macro/getMacro",
            type: "POST",
        },
        scroller: {
            loadingIndicator: true,
        },
        dom:
            "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",

        buttons: [
            { extend: "copyHtml5", exportOptions: { columns: [0, 1, 2, 3] } },
            { extend: "excelHtml5", exportOptions: { columns: [0, 1, 2, 3] } },
            { extend: "csvHtml5", exportOptions: { columns: [0, 1, 2, 3] } },
            { extend: "pdfHtml5", exportOptions: { columns: [0, 1, 2, 3] } },
            { extend: "print", exportOptions: { columns: [0, 1, 2, 3] } },
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





$(".table").on("click", ".editbutton", function () {
    "use strict";
    $("#loader").show();
    var iid = $(this).attr("data-id");

    $("#editMacroForm").trigger("reset");
    $.ajax({
        url: "macro/editMacroByJason?id=" + iid,
        method: "GET",
        data: "",
        dataType: "json",
        success: function (response) {
            "use strict";
            $("#editMacroForm").find('[name="id"]').val(response.macro.id).end();
            $("#editMacroForm")
                .find('[name="short_name"]')
                .val(response.macro.short_name)
                .end();

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
                    editor.setContent(response.macro.description);
                }
            });
            // myEditor1.setData(response.macro.description)
            //        $("#editMacroForm") 
            //          .find('[name="description"]')
            //          .val(response.macro.description)
            //          .end();

            $("#myModal2").modal("show");
        },
        complete: function () {
            $("#loader").hide();
        },
    });
});


"use strict";
$(".flashmessage").delay(3000).fadeOut(100);


