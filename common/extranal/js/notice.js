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
//             ClassicEditor
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
        var iid = $(this).attr('data-id');
        $('#editNoticeForm').trigger("reset");
        $('#myModal2').modal('show');
        $.ajax({
            url: 'notice/editNoticeByJason?id=' + iid,
            method: 'GET',
            data: '',
            dataType: 'json',
            success: function (response) {
                "use strict";

                var de = response.notice.date * 1000;
                var d = new Date(de);
                var date = (d.getDate() + 1) + '-' + (d.getMonth() + 1) + '-' + d.getFullYear();

                $('#editNoticeForm').find('[name="id"]').val(response.notice.id).end();
                $('#editNoticeForm').find('[name="title"]').val(response.notice.title).end();
                $('#editNoticeForm').find('[name="date"]').val(date).end();
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
                        editor.setContent(response.notice.description);
                    }
                });

                // myEditor2.setData(response.notice.description);
            },
            complete: function () {
                $("#loader").hide();
            }
        })
    });
});



$(document).ready(function () {
    "use strict";
    var table = $('#editable-sample').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",

        buttons: [
            { extend: 'copyHtml5', exportOptions: { columns: [1, 2, 3], } },
            { extend: 'excelHtml5', exportOptions: { columns: [1, 2, 3], } },
            { extend: 'csvHtml5', exportOptions: { columns: [1, 2, 3], } },
            { extend: 'pdfHtml5', exportOptions: { columns: [1, 2, 3], } },
            { extend: 'print', exportOptions: { columns: [1, 2, 3], } },
        ],
        aLengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        iDisplayLength: -1,
        "order": [[3, "desc"]],
        "language": {
            "lengthMenu": "_MENU_",
            search: "_INPUT_",
            "url": "common/assets/DataTables/languages/" + language + ".json"
        },

    });

    table.buttons().container()
        .appendTo('.custom_buttons');
});
$(document).ready(function () {
    "use strict";
    $(".flashmessage").delay(3000).fadeOut(100);
});
