"use strict";
// var myEditor;
// var myEditor2;
// $(document).ready(function () {

//     ClassicEditor
//             .create(document.querySelector('#editor'))
//             .then(editor => {
//                 editor.ui.view.editable.element.style.height = '200px';
//                 myEditor = editor;
//             })
//             .catch(error => {
//                 console.error(error);
//             });
//     ClassicEditor
//             .create(document.querySelector('#editor1'))
//             .then(editor => {
//                 editor.ui.view.editable.element.style.height = '200px';
//                 myEditor2 = editor;
//             })
//             .catch(error => {
//                 console.error(error);
//             });

// });

tinymce.init({
    selector: '#editor',
    plugins: 'preview importcss searchreplace autolink autosave save directionality visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
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

        var iid = $(this).attr('data-id');
        $.ajax({
            url: 'department/editDepartmentByJason?id=' + iid,
            method: 'GET',
            data: '',
            dataType: 'json',
            success: function (response) {
                "use strict";
                $('#departmentEditForm').find('[name="id"]').val(response.department.id).end();
                $('#departmentEditForm').find('[name="name"]').val(response.department.name).end();
                //    myEditor2.setData(response.department.description);
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
                        editor.setContent(response.department.description);
                    }
                });
            },
            complete: function () {
                $('#myModal2').modal('show');
                $("#loader").hide();
            },
        })
    });
});

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
            "url": "common/assets/DataTables/languages/" + language + ".json"
        }
    });
    table.buttons().container().appendTo('.custom_buttons');
});

$(document).ready(function () {
    "use strict";
    $(".flashmessage").delay(3000).fadeOut(100);
});

