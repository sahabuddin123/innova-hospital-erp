"use strict";
$(document).ready(function () {
  "use strict";

  function recalculateDueDeposit() {
    var originalDue = parseFloat($("#due_amount").data("original-due")) || 0;
    var prevDiscount = parseFloat($("#prev_discount_input").val()) || parseFloat($("#due_amount").data("prev-discount")) || 0;
    var addDiscount = parseFloat($("#additional_discount").val()) || 0;
    var deposit = parseFloat($("#deposited_amount").val()) || 0;

    if (addDiscount < 0) {
      addDiscount = 0;
      $("#additional_discount").val(0);
    }
    if (deposit < 0) {
      deposit = 0;
      $("#deposited_amount").val(0);
    }

    var totalDiscount = prevDiscount + addDiscount;
    $("#total_discount_display").text(totalDiscount.toFixed(2));

    var remainingDue = originalDue - addDiscount - deposit;
    if (remainingDue < 0) {
      remainingDue = 0;
    }
    $("#remaining_due_display").text(remainingDue.toFixed(2));

    if (addDiscount > 0 || deposit > 0) {
      $("#remaining_due_wrapper").show();
    } else {
      $("#remaining_due_wrapper").hide();
    }
  }

  $(document).on("input keyup change", "#additional_discount, #deposited_amount", function () {
    recalculateDueDeposit();
  });

  $(document).on("focus click", "#additional_discount, #deposited_amount", function () {
    var self = this;
    setTimeout(function () {
      self.select();
    }, 50);
  });

  $(document).off("click", ".depositButton").on("click", ".depositButton", function (e) {
    "use strict";
    e.preventDefault();
    $("#loader").show();
    var iid = $(this).attr("data-id");
    var from = $(this).attr("data-from");
    $("#due_amount").val("");
    $("#payment_id").val("");
    $("#patient_id").val("");
    $("#name").val("");
    $("#invoice_no").val("");
    $("#date").val("");
    $("#deposited_amount").val("");
    $("#additional_discount").val("0");
    $("#prev_discount_badge").text("0.00");
    $("#prev_discount_input").val("0.00");
    $("#total_discount_display").text("0.00");
    $("#remaining_due_display").text("0.00");
    $("#remaining_due_wrapper").hide();
    if ($("#deposit-form").length) {
      $("#deposit-form").trigger("reset");
    }
    $("#myModal").modal("show");
    $.ajax({
      url: "finance/getDepositByInvoiceIdForDeposit?id=" + iid,
      method: "GET",
      data: "",
      dataType: "json",
      success: function (response) {
        "use strict";
        var d = new Date();
        var strDate =
          d.getDate() + "-" + (d.getMonth() + 1) + "-" + d.getFullYear();
        var due = (response.response !== undefined && response.response !== null) ? parseFloat(response.response) : 0;
        var prev_discount = (response.previous_discount !== undefined && response.previous_discount !== null) ? parseFloat(response.previous_discount) : 0;

        $("#due_amount").val(due.toFixed(2));
        $("#due_amount").data("original-due", due);
        $("#due_amount").data("prev-discount", prev_discount);
        $("#prev_discount_input").val(prev_discount.toFixed(2));
        $("#prev_discount_badge").text(prev_discount.toFixed(2));
        $("#total_discount_display").text(prev_discount.toFixed(2));

        if (from == 'appointment') {
          $("#deposited_amount").val(due.toFixed(2));
          $('#deposited_amount').attr('readonly', true);
          $('#additional_discount').attr('readonly', true);
        } else {
          $('#deposited_amount').attr('readonly', false);
          $('#additional_discount').attr('readonly', false);
        }
        $("#payment_id").val(iid);
        $("#patient_id").val(response.patient ? response.patient.id : "");
        $("#name").val(response.patient ? response.patient.name : "");
        $("#invoice_no").val(iid);
        $("#date").val(strDate);
      },
      error: function (xhr, status, error) {
        console.error("Failed to load invoice details: ", error);
      },
      complete: function () {
        $("#loader").hide();
      },
    });
  });

  $("#deposit-form").on("submit", function (e) {
    var addDiscount = parseFloat($("#additional_discount").val()) || 0;
    var deposit = parseFloat($("#deposited_amount").val()) || 0;
    var originalDue = parseFloat($("#due_amount").data("original-due")) || 0;

    if (addDiscount <= 0 && deposit <= 0) {
      alert("Please enter a Deposit Amount or Discount Amount.");
      e.preventDefault();
      return false;
    }

    if ((addDiscount + deposit) > (originalDue + 0.05)) {
      if (!confirm("Total Deposit (৳" + deposit.toFixed(2) + ") and Additional Discount (৳" + addDiscount.toFixed(2) + ") is greater than Due Amount (৳" + originalDue.toFixed(2) + "). Do you want to proceed?")) {
        e.preventDefault();
        return false;
      }
    }
  });
});
$(document).ready(function () {
  "use strict";
  var date_to = $("#date_to").val();
  var date_from = $("#date_from").val();
  var table = $("#editable-sample4").DataTable({
    responsive: true,
    //   dom: 'lfrBtip',

    processing: true,
    serverSide: true,
    searchable: true,
    ajax: {
      url:
        "finance/getDuePayment?start_date=" +
        date_from +
        "&end_date=" +
        date_to,
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
        exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] },
      },
      {
        extend: "excelHtml5",
        exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] },
      },
      {
        extend: "csvHtml5",
        exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] },
      },
      {
        extend: "pdfHtml5",
        exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] },
      },
      {
        extend: "print",
        exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] },
      },
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
  "use strict";
  $(".flashmessage").delay(3000).fadeOut(100);
});
