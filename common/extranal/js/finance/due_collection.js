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

  function fillDepositModalFields(response, from, iid) {
    var d = new Date();
    var strDate = d.getDate() + "-" + (d.getMonth() + 1) + "-" + d.getFullYear();
    var due = (response.response !== undefined && response.response !== null) ? parseFloat(response.response) : (parseFloat(response.due) || 0);
    var prev_discount = (response.previous_discount !== undefined && response.previous_discount !== null) ? parseFloat(response.previous_discount) : (parseFloat(response.discount) || 0);
    var pName = response.patient ? (response.patient.name || response.patient_name || "") : (response.patient_name || "");
    var pId = response.patient ? (response.patient.id || response.patient_id || "") : (response.patient_id || "");

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
    $("#patient_id").val(pId);
    $("#name").val(pName);
    $("#invoice_no").val(iid);
    $("#date").val(strDate);
  }

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

    if (!navigator.onLine) {
      if (window.OfflineDB && window.OfflineDB.getCatalog) {
        window.OfflineDB.getCatalog('due_invoices_cache').then(function(cached) {
          var found = cached ? cached.find(function(c) { return String(c.id) === String(iid); }) : null;
          if (found) {
            fillDepositModalFields(found, from, iid);
          } else {
            var d = new Date();
            var strDate = d.getDate() + "-" + (d.getMonth() + 1) + "-" + d.getFullYear();
            $("#payment_id").val(iid);
            $("#invoice_no").val(iid);
            $("#date").val(strDate);
            $("#name").attr('readonly', false);
            $("#due_amount").attr('readonly', false);
          }
          $("#loader").hide();
        }).catch(function() {
          $("#loader").hide();
        });
      } else {
        $("#loader").hide();
      }
    } else {
      $.ajax({
        url: "finance/getDepositByInvoiceIdForDeposit?id=" + iid,
        method: "GET",
        data: "",
        dataType: "json",
        success: function (response) {
          fillDepositModalFields(response, from, iid);
        },
        error: function (xhr, status, error) {
          console.warn("Could not fetch online due info, checking offline cache for invoice " + iid);
          if (window.OfflineDB && window.OfflineDB.getCatalog) {
            window.OfflineDB.getCatalog('due_invoices_cache').then(function(cached) {
              var found = cached ? cached.find(function(c) { return String(c.id) === String(iid); }) : null;
              if (found) {
                fillDepositModalFields(found, from, iid);
              }
            });
          }
        },
        complete: function () {
          $("#loader").hide();
        },
      });
    }
  });

  // Quick Due Search & Collect Listener
  $(document).on("click", "#btn_quick_due_lookup", function (e) {
    e.preventDefault();
    var val = $.trim($("#quick_due_search_input").val());
    if (!val) {
      alert("Please enter an Invoice Number.");
      $("#quick_due_search_input").focus();
      return;
    }
    $("<button class='depositButton' data-id='" + val + "' data-from='payment'></button>").trigger("click");
  });

  $("#quick_due_search_input").on("keypress", function (e) {
    if (e.which === 13) {
      e.preventDefault();
      $("#btn_quick_due_lookup").trigger("click");
    }
  });

  // Offline banner toggle
  function updateDueOfflineBanner() {
    if (!navigator.onLine) {
      $("#due_offline_banner").slideDown();
    } else {
      $("#due_offline_banner").slideUp();
    }
  }
  updateDueOfflineBanner();
  window.addEventListener("online", updateDueOfflineBanner);
  window.addEventListener("offline", updateDueOfflineBanner);

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
      dataSrc: function (json) {
        if (json && json.data && window.OfflineDB && window.OfflineDB.saveCatalog) {
          try {
            var cachedRows = json.data.map(function (r) {
              var tempDiv = document.createElement("div");
              tempDiv.innerHTML = r[1] || "";
              var pName = (tempDiv.firstChild ? tempDiv.firstChild.textContent : "").trim() || (tempDiv.innerText || "").split("\n")[0].trim();
              var phoneMatch = (tempDiv.innerText || "").match(/(?:\+?880|0)?1[3-9]\d{8}/);
              var pPhone = phoneMatch ? phoneMatch[0] : "";
              var subTotal = parseFloat(String(r[5]).replace(/[^\d.]/g, "")) || 0;
              var discount = parseFloat(String(r[6]).replace(/[^\d.]/g, "")) || 0;
              var grandTotal = parseFloat(String(r[7]).replace(/[^\d.]/g, "")) || 0;
              var paid = parseFloat(String(r[8]).replace(/[^\d.]/g, "")) || 0;
              var due = parseFloat(String(r[9]).replace(/[^\d.]/g, "")) || 0;
              return {
                id: String(r[0]),
                patient_name: pName,
                patient_phone: pPhone,
                patient_raw: r[1],
                doctor: r[2],
                date: r[3],
                from: r[4],
                sub_total: subTotal,
                discount: discount,
                grand_total: grandTotal,
                paid_amount: paid,
                due: due,
                raw: r
              };
            });
            window.OfflineDB.saveCatalog("due_invoices_cache", cachedRows);
          } catch (e) {
            console.warn("[PWA] Error caching due invoices:", e);
          }
        }
        return json.data || [];
      },
      error: function (xhr, error, thrown) {
        console.warn("[PWA] Due Payment AJAX error or offline, attempting local catalog fallback...", error);
        if (window.OfflineDB && window.OfflineDB.getCatalog) {
          window.OfflineDB.getCatalog("due_invoices_cache").then(function (cached) {
            if (cached && cached.length) {
              var fallbackData = cached.map(function (c) { return c.raw; });
              var dt = $("#editable-sample4").DataTable();
              dt.clear();
              dt.rows.add(fallbackData);
              dt.draw();
              if (typeof toastr !== "undefined") {
                toastr.info("Loaded " + cached.length + " due invoices from offline cache.", "Offline Mode");
              }
            }
          });
        }
      }
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
