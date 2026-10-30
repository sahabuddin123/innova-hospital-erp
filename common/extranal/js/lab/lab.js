"use strict";
$(document).ready(function () {
    "use strict";
    $(".flashmessage").delay(3000).fadeOut(100);
});

$(document).ready(function () {
    "use strict";
    var table = $('#editable-sample').DataTable({
        responsive: true,

        "processing": true,
        "serverSide": true,
        "searchable": true,
        "ajax": {
            url: "lab/getLab",
            type: 'POST',
        },
        scroller: {
            loadingIndicator: true
        },

        dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",

        buttons: [
            { extend: 'copyHtml5', exportOptions: { columns: [0, 1, 2], } },
            { extend: 'excelHtml5', exportOptions: { columns: [0, 1, 2], } },
            { extend: 'csvHtml5', exportOptions: { columns: [0, 1, 2], } },
            { extend: 'pdfHtml5', exportOptions: { columns: [0, 1, 2], } },
            { extend: 'print', exportOptions: { columns: [0, 1, 2], } },
        ],
        aLengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        iDisplayLength: 100,
        "order": [[0, "desc"]],

        "language": {
            "lengthMenu": "_MENU_",
            search: "_INPUT_",
            searchPlaceholder: "Search..."
        }
    });
    table.buttons().container().appendTo('.custom_buttons');
});

$(document).ready(function () {
    "use strict";
    $('.pos_client').hide();
    $(document.body).on('change', '#pos_select', function () {
        "use strict";
        var v = $("select.pos_select option:selected").val()
        if (v == 'add_new') {
            $('.pos_client').show();
        } else {
            $('.pos_client').hide();
        }
    });

});


$(document).ready(function () {
    "use strict";
    $('.pos_doctor').hide();
    $(document.body).on('change', '#add_doctor', function () {
        "use strict";
        var v = $("select.add_doctor option:selected").val()
        if (v == 'add_new') {
            $('.pos_doctor').show();
        } else {
            $('.pos_doctor').hide();
        }
    });

});



$(document).ready(function () {
    "use strict";
    $(document.body).on('change', '#template', function () {
        "use strict";
        var iid = $("select.template option:selected").val();
        $.ajax({
            url: 'lab/getTemplateByIdByJason?id=' + iid,
            method: 'GET',
            data: '',
            dataType: 'json',
            success: function (response) {
                "use strict";
                var data = myEditor.getData();
                if (response.template.template != null) {
                    var data1 = data + response.template.template;
                } else {
                    var data1 = data;
                }
                myEditor.setData(data1)
            }
        })
    });
});

$(document).ready(function () {
    "use strict";
    $("#pos_select").select2({
        placeholder: select_patient,
        allowClear: true,
        ajax: {
            url: 'patient/getPatientinfoWithAddNewOption',
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        }

    });

    $("#add_doctor").select2({
        placeholder: select_doctor,
        allowClear: true,
        ajax: {
            url: 'doctor/getDoctorWithAddNewOption',
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        }

    });

});
var myEditor;
$(document).ready(function () {

    ClassicEditor
        .create(document.querySelector('#editor'))
        .then(editor => {
            editor.ui.view.editable.element.style.height = '200px';

            myEditor = editor;
        })
        .catch(error => {
            console.error(error);
        });

});
$(document).ready(function () {
    "use strict";

    $('.other').hide();
    $(".radio_button").on("change", "input[type=radio][name=radio]", function () {
        if (this.value === 'other') {
            $('.other').show();
        } else {
            $('.other').hide();
        }
    });

});
$(document).ready(function () {
    "use strict";

    $('.single_patient').hide();
    $('input[type=radio][name=radio]').change(function () {
        if (this.value === 'single_patient') {
            $('.single_patient').show();
        } else {
            $('.single_patient').hide();
        }
    });

});

// Xprinter XP-350BM Silent Barcode Printing via Hidden Iframe
window.silentPrintBarcode = function (labId) {
    "use strict";
    var frameId = 'silent_print_frame_' + Date.now();
    var iframe = document.createElement('iframe');
    iframe.id = frameId;
    iframe.name = frameId;
    iframe.style.position = 'fixed';
    iframe.style.right = '0';
    iframe.style.bottom = '0';
    iframe.style.width = '10px';
    iframe.style.height = '10px';
    iframe.style.opacity = '0.01';
    iframe.style.border = '0';
    iframe.src = 'lab/printLabel?id=' + encodeURIComponent(labId) + '&silent=1&format=tube';

    document.body.appendChild(iframe);

    // Provide user feedback
    if (typeof $.notify === 'function') {
        $.notify('বারকোড প্রিন্টারে পাঠানো হচ্ছে (Xprinter XP-350BM)...', { className: 'info', autoHideDelay: 2500 });
    } else if (typeof toastr !== 'undefined') {
        toastr.info('বারকোড প্রিন্টারে পাঠানো হচ্ছে (Xprinter XP-350BM)...', 'Silent Print');
    }

    // Auto cleanup after print job
    setTimeout(function () {
        var el = document.getElementById(frameId);
        if (el && el.parentNode) {
            el.parentNode.removeChild(el);
        }
    }, 12000);
};