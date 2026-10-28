"use strict";
$(document).ready(function () {
    "use strict";
    $(".table").on("click", ".editbutton", function () {
        "use strict";
        $("#loader").show();
        var iid = $(this).attr('data-id');
        $('#editHolidayForm').trigger("reset");
        $.ajax({
            url: 'schedule/editHolidayByJason?id=' + iid,
            method: 'GET',
            data: '',
            dataType: 'json',
            success: function (response) {
                "use strict";
                var hl = response.holiday;
                $('#editHolidayForm').find('[name="id"]').val(hl.id);
                $('#editHolidayForm').find('[name="date"]').val(response.start_date_formatted || "");
                $('#editHolidayForm').find('[name="end_date"]').val((response.end_date_formatted && response.end_date_formatted !== response.start_date_formatted) ? response.end_date_formatted : "");
                $('#editHolidayForm').find('[name="leave_type"]').val(hl.leave_type || "full_day");
                $('#editHolidayForm').find('[name="start_time"]').val(hl.start_time || "");
                $('#editHolidayForm').find('[name="end_time"]').val(hl.end_time || "");
                $('#editHolidayForm').find('[name="reason"]').val(hl.reason || "");
            },
            complete: function () {
                $('#myModal2').modal('show');
                $("#loader").hide();
            }
        });
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
            { extend: 'copyHtml5', exportOptions: { columns: [0, 1, 2, 3, 4] } },
            { extend: 'excelHtml5', exportOptions: { columns: [0, 1, 2, 3, 4] } },
            { extend: 'csvHtml5', exportOptions: { columns: [0, 1, 2, 3, 4] } },
            { extend: 'pdfHtml5', exportOptions: { columns: [0, 1, 2, 3, 4] } },
            { extend: 'print', exportOptions: { columns: [0, 1, 2, 3, 4] } },
        ],
        aLengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        iDisplayLength: 25,
        "order": [[0, "asc"]],
        "language": {
            "lengthMenu": "_MENU_",
            search: "_INPUT_",
            "url": (typeof language !== "undefined" && language !== "") ? "common/assets/DataTables/languages/" + language + ".json" : ""
        },
    });

    table.buttons().container().appendTo('.custom_buttons');
});

$(document).ready(function () {
    "use strict";
    $(".flashmessage").delay(3000).fadeOut(100);
});
