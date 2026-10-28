"use strict";

tinymce.init({
    selector: '#editor',
    language: 'fr_FR',
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
        $.ajax({
            url: 'insurance/editInsuranceByJason?id=' + iid,
            method: 'GET',
            data: '',
            dataType: 'json',
            success: function (response) {
                "use strict";
                $('#insuranceEditForm').find('[name="id"]').val(response.insurance.id).end();
                $('#insuranceEditForm').find('[name="name"]').val(response.insurance.name).end();
                tinymce.remove('#editor1');
                tinymce.init({
                    selector: '#editor1',
                    language: 'fr_FR',
                    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
                    menubar: 'file edit view insert format tools table help',
                    toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media template link anchor codesample | ltr rtl',
                    branding: false,
                    promotion: false,
                    init_instance_callback: function (editor) {
                        if (response.insurance.description !== null) {
                            editor.setContent(response.insurance.description);
                        }

                    }
                });
                // myEditor2.setData(response.insurance.description);
                $('#myModal2').modal('show');
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
        processing: true,
        serverSide: true,
        searchable: true,
        bScrollCollapse: true,
        ajax: {
            url: "insurance/getInsuranceCompany",
            type: "POST",
        },
        scroller: {
            loadingIndicator: true,
        },
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

