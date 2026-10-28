"use strict";

function updateCommissionSummary() {
  var total_doc_comm = 0;
  var total_ref_comm = 0;
  var doctor_id = $("#add_doctor").val();
  var has_doctor = (doctor_id !== "" && doctor_id !== null && doctor_id !== undefined);
  var referrer_id = $("#add_referrer").val();
  var has_referrer = (referrer_id !== "" && referrer_id !== null && referrer_id !== undefined);

  // 1. Calculate Gross Item Subtotal and Gross Test Commissions
  var gross_subtotal = 0;
  var total_gross_doc_comm = 0;
  var total_gross_ref_comm = 0;

  $.each($("select.multi-select option:selected"), function () {
    var id1 = $(this).data("idd");
    var qty_elem = $("#idinput-" + id1);
    var qty = qty_elem.length ? (parseFloat(qty_elem.val()) || 0) : 1;
    var unit_price = parseFloat($(this).data("id")) || 0;
    var item_subtotal = qty * unit_price;
    gross_subtotal += item_subtotal;

    var d_rate = parseFloat($(this).data("d_commission")) || 0;
    var r_rate = parseFloat($(this).data("r_commission")) || 0;

    if (has_doctor) {
      total_gross_doc_comm += (item_subtotal * d_rate) / 100;
    }
    if (has_referrer) {
      total_gross_ref_comm += (item_subtotal * r_rate) / 100;
    }
  });

  // 2. Calculate Discount Amount
  var flat_discount = 0;
  var entered_flat = parseFloat($("#dis_id").val()) || 0;
  var entered_percent = parseFloat($("#dis_id_percent").val()) || 0;

  if (typeof discount_type !== "undefined" && discount_type === "percentage") {
    flat_discount = (gross_subtotal * entered_percent) / 100;
  } else if (entered_flat > 0) {
    flat_discount = entered_flat;
  } else if (entered_percent > 0) {
    flat_discount = (gross_subtotal * entered_percent) / 100;
  }
  flat_discount = Math.max(0, Math.min(flat_discount, gross_subtotal));

  var net_invoice = Math.max(0, gross_subtotal - flat_discount);

  // 3. Referral Commission Rules:
  // Rule 1: Calculate referral commission using each test's configured rate.
  // Rule 2: If a referral is selected, deduct invoice discount from referrer's total commission, not hospital's share.
  //         If no referral is selected, deduct discount from hospital's share.
  if (has_referrer) {
    total_ref_comm = Math.max(0, total_gross_ref_comm - flat_discount);
  } else {
    total_ref_comm = 0;
  }

  // Preserve existing doctor commission rules
  total_doc_comm = total_gross_doc_comm;

  var gross_val = parseFloat($("#gross").val()) || net_invoice;
  var net_revenue = Math.max(0, gross_val - total_doc_comm - total_ref_comm);

  // 4. Update UI Displays
  if ($("#comm_gross_display").length) {
    $("#comm_gross_display").text(currency + " " + gross_subtotal.toFixed(2));
  }
  if ($("#comm_discount_display").length) {
    $("#comm_discount_display").text("-" + currency + " " + flat_discount.toFixed(2));
  }
  if ($("#comm_net_invoice_display").length) {
    $("#comm_net_invoice_display").text(currency + " " + net_invoice.toFixed(2));
  }
  if ($("#comm_base_display").length) {
    $("#comm_base_display").text(currency + " " + (has_referrer ? total_gross_ref_comm : gross_subtotal).toFixed(2));
  }
  if ($("#comm_doctor_display").length) {
    $("#comm_doctor_display").text(currency + " " + total_doc_comm.toFixed(2));
  }
  if ($("#comm_referral_display").length) {
    $("#comm_referral_display").text(currency + " " + total_ref_comm.toFixed(2));
  }
  if ($("#comm_net_display").length) {
    $("#comm_net_display").text(currency + " " + net_revenue.toFixed(2));
  }
}

$(document).ready(function (e) {
  "use strict";
  $("#add_referrer").on("change", function () {
    var v = $(this).val();
    if (v === "add_new") {
      $("#add_referrer").val("").trigger("change.select2");
      $("#referrer_modal_error").addClass("d-none").text("");
      if ($("#formAddNewReferrerModal").length) {
        $("#formAddNewReferrerModal")[0].reset();
        $("#modal_ref_type").val("Doctor");
      }
      $("#referrerModal").modal("show");
      return;
    }
    updateCommissionSummary();
  });
  $("#add_doctor").on("change", function () {
    updateCommissionSummary();
  });

  $(document).on("click", ".remove_attr", function () {
    var idd = $(this).attr("id").replace("id-remove-", "");
    $("#id-div" + idd).remove();
    $("#idinput-" + idd).remove();
    $("#categoryinput-" + idd).remove();
    $("select.multi-select option[value='" + idd + "']").prop("selected", false);
    $(".ms-list li[data-idd='" + idd + "']").removeClass("ms-selected selected");
    $("select.multi-select").trigger("change");
    updateCommissionSummary();
  });

  $("#save_as_draft").click(function () {
    $("input[name='type']").removeAttr("required");
    $("#pos_select").removeAttr("required");
    $("#add_doctor").removeAttr("required");
    $(".multi-select").removeAttr("required");
    $("#p_name").prop("required", false);
    $("#p_phone").prop("required", false);

    e.preventDefault;
  });
  var tot = 0;
  $(".ms-list").on("click", ".ms-selected", function () {
    "use strict";
    var idd = $(this).data("idd");
    $("#id-div" + idd).remove();
    $("#idinput-" + idd).remove();
    $("#categoryinput-" + idd).remove();
    updateCommissionSummary();
  });
  $.each($("select.multi-select option:selected"), function () {
    "use strict";
    var idd = $(this).data("idd");
    var qtity = $(this).data("qtity");
    var d_comm = $(this).data("d_commission");
    var r_comm = $(this).data("r_commission");
    if ($("#idinput-" + idd).length) {
    } else {
      if ($("#id-div" + idd).length) {
      } else {
        $("#editPaymentForm .qfloww").append(
          '<div class="cart-item-row remove1" id="id-div' + idd + '">' +
            '<div class="cart-item-left">' +
              '<button type="button" class="btn-cart-del remove_attr" id="id-remove-' + idd + '" title="Remove item"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="pointer-events:none;"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg><i class="fas fa-times d-none"></i></button>' +
              '<span class="cart-item-title">' + $(this).data("cat_name") + '</span>' +
              ' <span class="cart-item-price">- ' + currency + $(this).data("id") + '</span>' +
            '</div>' +
            '<div class="cart-item-right">' +
              '<div class="cart-stepper">' +
                '<button type="button" class="btn-stepper btn-qty-plus" data-idd="' + idd + '">+</button>' +
                '<input type="text" class="remove cart-qty-num" id="idinput-' + idd + '" name="quantity[]" value="' + (qtity ? qtity : 1) + '" autocomplete="off">' +
                '<button type="button" class="btn-stepper btn-qty-minus" data-idd="' + idd + '">-</button>' +
              '</div>' +
              '<input type="hidden" class="remove" id="categoryinput-' + idd + '" name="category_id[]" value="' + idd + '">' +
            '</div>' +
          '</div>'
        );
      }
    }
    $(document).ready(function () {
      "use strict";

      $("#idinput-" + idd).keyup(function () {
        "use strict";
        var qty = 0;
        var total = 0;
        $.each($("select.multi-select option:selected"), function () {
          var id1 = $(this).data("idd");
          qty = $("#idinput-" + id1).val();
          var ekokk = $(this).data("id");
          total = total + qty * ekokk;
        });
        tot = total;
        var discount = ($("#dis_id_percent").val() * tot) / 100;
        var vat_amount = $("#vat").val();
        var vat = (vat_amount * tot) / 100;
        var gross = tot - discount + vat;
        $("#editPaymentForm").find('[name="subtotal"]').val(tot).end();
        $("#editPaymentForm")
          .find('[name="discount"]')
          .val(discount.toFixed(2))
          .end();
        $("#editPaymentForm").find('[name="grsss"]').val(gross);
        $("#editPaymentForm").find('[name="vat_amount"]').val(vat).end();
        var amount_received = $("#amount_received").val();
        var change = amount_received - gross;
        $("#editPaymentForm").find('[name="change"]').val(change).end();
        var id = $("#id_pay").val() ? $("#id_pay").val() : null;
        if (id !== null) {
          $.ajax({
            url: "finance/getDepositByInvoiceId?id=" + id,
            method: "GET",
            data: "",
            dataType: "json",
            success: function (response) {
              var due = $("#gross").val() - response.response;
              $("#due").val(due);
          updateCommissionSummary();
            },
          });
        } else {
          $("#due").val($("#gross").val() - amount_received);
          updateCommissionSummary();
        }
      });
    });
    ("use strict");
    var sub_total = $(this).data("id") * $("#idinput-" + idd).val();
    tot = tot + sub_total;
  });
  ("use strict");
  var discount = ($("#dis_id_percent").val() * tot) / 100;
  // if (discount_type === "flat") {
  var vat_amount = $("#vat").val();
  var vat = (vat_amount * tot) / 100;
  var gross = tot - discount + vat;
  // } else {
  //   var vat = (vat_amount * tot) / 100;

  //   var gross = tot - (tot * discount) / 100 + vat;
  // }

  $("#editPaymentForm").find('[name="subtotal"]').val(tot).end();
  $("#editPaymentForm")
    .find('[name="discount"]')
    .val(discount.toFixed(2))
    .end();
  $("#editPaymentForm").find('[name="vat_amount"]').val(vat.toFixed(2)).end();
  $("#editPaymentForm").find('[name="grsss"]').val(gross);
  var amount_received = $("#amount_received").val();
  var change = gross - amount_received;
  $("#editPaymentForm").find('[name="change"]').val(change).end();
  var id = $("#id_pay").val() ? $("#id_pay").val() : null;

  if (id !== null) {
    $.ajax({
      url: "finance/getDepositByInvoiceId?id=" + id,
      method: "GET",
      data: "",
      dataType: "json",
      success: function (response) {
        var due = $("#gross").val() - response.response;
        $("#due").val(due);
          updateCommissionSummary();
      },
    });
  } else {
    $("#due").val($("#gross").val() - amount_received);
          updateCommissionSummary();
  }
});

$(document).ready(function () {
  "use strict";
  $("#dis_id").keyup(function () {
    "use strict";
    var val_dis = 0;
    var amount = 0;
    var ggggg = 0;

    amount = $("#subtotal").val();
    val_dis = this.value;
    var vat_amount = $("#vat").val();
    var vat = (vat_amount * amount) / 100;
    var discount = (val_dis * 100) / amount;
    ggggg = amount - val_dis + vat;

    $("#editPaymentForm").find('[name="grsss"]').val(ggggg);
    $("#editPaymentForm")
      .find('[name="percent_discount"]')
      .val(discount.toFixed(2));

    var amount_received = $("#amount_received").val();
    var change = amount_received - ggggg;
    $("#editPaymentForm").find('[name="change"]').val(change).end();
    var id = $("#id_pay").val() ? $("#id_pay").val() : null;
    if (id !== null) {
      $.ajax({
        url: "finance/getDepositByInvoiceId?id=" + id,
        method: "GET",
        data: "",
        dataType: "json",
        success: function (response) {
          var due = $("#gross").val() - response.response;
          $("#due").val(due);
          updateCommissionSummary();
        },
      });
    } else {
      $("#due").val($("#gross").val() - amount_received);
          updateCommissionSummary();
    }
  });
  $("#dis_id_percent").keyup(function () {
    "use strict";
    var val_dis = 0;
    var amount = 0;
    var ggggg = 0;
    amount = $("#subtotal").val();
    val_dis = this.value;
    var vat_amount = $("#vat").val();
    var vat = (vat_amount * amount) / 100;

    var discount = (amount * val_dis) / 100;
    ggggg = amount - (amount * val_dis) / 100 + vat;
    $("#editPaymentForm").find('[name="discount"]').val(discount);
    $("#editPaymentForm").find('[name="grsss"]').val(ggggg);
    // $("#editPaymentForm").find('[name="vat"]').val(vat);
    var amount_received = $("#amount_received").val();
    var change = amount_received - ggggg;
    $("#editPaymentForm").find('[name="change"]').val(change).end();
    var id = $("#id_pay").val() ? $("#id_pay").val() : null;
    if (id !== null) {
      $.ajax({
        url: "finance/getDepositByInvoiceId?id=" + id,
        method: "GET",
        data: "",
        dataType: "json",
        success: function (response) {
          var due = $("#gross").val() - response.response;
          $("#due").val(due);
          updateCommissionSummary();
        },
      });
    } else {
      $("#due").val($("#gross").val() - amount_received);
          updateCommissionSummary();
    }
  });
});

$(document).ready(function () {
  "use strict";

  $(document.body).on("change", ".multi-select", function () {
    "use strict";
    var tot = 0;

    $(".ms-list").on("click", ".ms-selected", function () {
      "use strict";
      var idd = $(this).data("idd");
      $("#id-div" + idd).remove();
      $("#idinput-" + idd).remove();
      $("#categoryinput-" + idd).remove();
    });
    $.each($("select.multi-select option:selected"), function () {
      "use strict";
      var curr_val = $(this).data("id");
      var idd = $(this).data("idd");

      var cat_name = $(this).data("cat_name");
      if ($("#idinput-" + idd).length) {
      } else {
        var d_comm = $(this).data("d_commission");
        var r_comm = $(this).data("r_commission");
        if ($("#id-div" + idd).length) {
        } else {
          $("#editPaymentForm .qfloww").append(
            '<div class="cart-item-row remove1" id="id-div' + idd + '">' +
              '<div class="cart-item-left">' +
                '<button type="button" class="btn-cart-del remove_attr" id="id-remove-' + idd + '" title="Remove item"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="pointer-events:none;"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg><i class="fas fa-times d-none"></i></button>' +
                '<span class="cart-item-title">' + $(this).data("cat_name") + '</span>' +
                ' <span class="cart-item-price">- ' + currency + $(this).data("id") + '</span>' +
              '</div>' +
              '<div class="cart-item-right">' +
                '<div class="cart-stepper">' +
                  '<button type="button" class="btn-stepper btn-qty-plus" data-idd="' + idd + '">+</button>' +
                  '<input type="text" class="remove cart-qty-num" id="idinput-' + idd + '" name="quantity[]" value="1" autocomplete="off">' +
                  '<button type="button" class="btn-stepper btn-qty-minus" data-idd="' + idd + '">-</button>' +
                '</div>' +
                '<input type="hidden" class="remove" id="categoryinput-' + idd + '" name="category_id[]" value="' + idd + '">' +
              '</div>' +
            '</div>'
          );
        }
      }

      $(document).ready(function () {
        "use strict";
        $("#idinput-" + idd).keyup(function () {
          "use strict";

          var qty = 0;
          var total = 0;
          $.each($("select.multi-select option:selected"), function () {
            var id1 = $(this).data("idd");
            qty = $("#idinput-" + id1).val();
            var ekokk = $(this).data("id");
            total = total + qty * ekokk;
          });

          tot = total;

          // var discount = $("#dis_id").val();
          var discount = (tot * $("#dis_id_percent").val()) / 100;
          var vat_amount = $("#vat").val();
          var vat = (vat_amount * tot) / 100;
          var gross = tot - discount + vat;

          $("#editPaymentForm").find('[name="subtotal"]').val(tot).end();
          $("#editPaymentForm").find('[name="discount"]').val(discount).end();
          $("#editPaymentForm").find('[name="vat_amount"]').val(vat).end();
          $("#editPaymentForm").find('[name="grsss"]').val(gross);

          var amount_received = $("#amount_received").val();
          var change = amount_received - gross;
          $("#editPaymentForm").find('[name="change"]').val(change).end();
          var asdid = $("#id_pay").val() ? $("#id_pay").val() : null;
          if (asdid !== null) {
            $.ajax({
              url: "finance/getDepositByInvoiceId?id=" + asdid,
              method: "GET",
              data: "",
              dataType: "json",
              success: function (response) {
                var due = $("#gross").val() - response.response;
                $("#due").val(due);
          updateCommissionSummary();
              },
            });
          } else {
            $("#due").val($("#gross").val() - amount_received);
          updateCommissionSummary();
          }
        });
      });
      ("use strict");
      var sub_total = $(this).data("id") * $("#idinput-" + idd).val();
      tot = tot + sub_total;
    });
    ("use strict");
    var discount = ($("#dis_id_percent").val() * tot) / 100;

    // if (discount_type === "flat") {
    //   var vat = (vat_amount * tot) / 100;
    //   var gross = tot - discount + vat;
    // } else {
    var vat_amount = $("#vat").val();
    var vat = (vat_amount * tot) / 100;

    var gross = tot - discount + vat;
    //}
    $("#editPaymentForm").find('[name="subtotal"]').val(tot).end();
    $("#editPaymentForm").find('[name="discount"]').val(discount).end();
    $("#editPaymentForm").find('[name="vat_amount"]').val(vat);
    $("#editPaymentForm").find('[name="grsss"]').val(gross);

    var amount_received = $("#amount_received").val();
    var change = gross - amount_received;
    $("#editPaymentForm").find('[name="change"]').val(change).end();
    var asdid = $("#id_pay").val() ? $("#id_pay").val() : null;

    if (asdid !== null) {
      $.ajax({
        url: "finance/getDepositByInvoiceId?id=" + asdid,
        method: "GET",
        data: "",
        dataType: "json",
        success: function (response) {
          var due = $("#gross").val() - response.response;
          $("#due").val(due);
          updateCommissionSummary();
        },
      });
    } else {
      $("#due").val($("#gross").val() - amount_received);
          updateCommissionSummary();
    }

    if (typeof window.resetPOSSearch === "function") {
      window.resetPOSSearch();
    }
  });
});

$(document).ready(function () {
  "use strict";
  $("#dis_id").keyup(function () {
    "use strict";
    var val_dis = 0;
    var amount = 0;
    var ggggg = 0;
    amount = $("#subtotal").val();
    val_dis = this.value;
    var vat_amount = $("#vat").val();
    var vat = (vat_amount * amount) / 100;
    var discount = (val_dis * 100) / amount;
    ggggg = amount - val_dis + vat;

    $("#editPaymentForm").find('[name="grsss"]').val(ggggg);
    $("#editPaymentForm")
      .find('[name="percent_discount"]')
      .val(discount.toFixed(2));

    var amount_received = $("#amount_received").val();
    var change = amount_received - ggggg;
    $("#editPaymentForm").find('[name="change"]').val(change).end();
    var id = $("#id_pay").val() ? $("#id_pay").val() : null;
    if (id !== null) {
      $.ajax({
        url: "finance/getDepositByInvoiceId?id=" + id,
        method: "GET",
        data: "",
        dataType: "json",
        success: function (response) {
          var due = $("#gross").val() - response.response;
          $("#due").val(due);
          updateCommissionSummary();
        },
      });
    } else {
      $("#due").val($("#gross").val() - amount_received);
          updateCommissionSummary();
    }
  });
  $("#dis_id_percent").keyup(function () {
    "use strict";
    var val_dis = 0;
    var amount = 0;
    var ggggg = 0;
    amount = $("#subtotal").val();
    val_dis = this.value;
    var vat_amount = $("#vat").val();
    var vat = (vat_amount * amount) / 100;

    var discount = (amount * val_dis) / 100;
    ggggg = amount - discount + vat;
    $("#editPaymentForm").find('[name="discount"]').val(discount).toFixed(2);
    $("#editPaymentForm").find('[name="grsss"]').val(ggggg);

    var amount_received = $("#amount_received").val();

    var id = $("#id_pay").val() ? $("#id_pay").val() : null;
    if (id !== null) {
      $.ajax({
        url: "finance/getDepositByInvoiceId?id=" + id,
        method: "GET",
        data: "",
        dataType: "json",
        success: function (response) {
          var due = $("#gross").val() - response.response;
          $("#due").val(due);
          updateCommissionSummary();
        },
      });
    } else {
      $("#due").val($("#gross").val() - amount_received);
          updateCommissionSummary();
    }
  });
});
$(document).ready(function () {
  "use strict";
  $("#vat_amount").keyup(function () {
    "use strict";
    var val_dis = 0;
    var amount = 0;
    var ggggg = 0;

    amount = $("#subtotal").val();
    val_dis = $(this).val();

    var vat = (100 * val_dis) / amount;
    var discount = $("#dis_id").val();
    ggggg = amount - discount + parseFloat(val_dis);
    $("#vat").val("");
    $("#editPaymentForm").find('[name="grsss"]').val(ggggg);
    $("#editPaymentForm").find('[name="vat"]').val(vat.toFixed(2));

    var amount_received = $("#amount_received").val();
    var change = amount_received - ggggg;
    $("#editPaymentForm").find('[name="change"]').val(change).end();
    var id = $("#id_pay").val() ? $("#id_pay").val() : null;
    if (id !== null) {
      $.ajax({
        url: "finance/getDepositByInvoiceId?id=" + id,
        method: "GET",
        data: "",
        dataType: "json",
        success: function (response) {
          var due = $("#gross").val() - response.response;
          $("#due").val(due);
          updateCommissionSummary();
        },
      });
    } else {
      $("#due").val($("#gross").val() - amount_received);
          updateCommissionSummary();
    }
  });
  $("#vat").keyup(function () {
    "use strict";
    var val_dis = 0;
    var amount = 0;
    var ggggg = 0;
    amount = $("#subtotal").val();
    val_dis = this.value;
    var discount = $("#dis_id").val();
    var vat = (val_dis * amount) / 100;

    ggggg = amount - discount + vat;
    $("#vat").val("");
    $("#vat_amount").val("");
    $("#editPaymentForm").find('[name="grsss"]').val(ggggg);
    $("#editPaymentForm").find('[name="vat_amount"]').val(vat.toFixed(2));
    $("#editPaymentForm").find('[name="vat"]').val(val_dis);
    var amount_received = $("#amount_received").val();
    var change = amount_received - ggggg;
    $("#editPaymentForm").find('[name="change"]').val(change).end();
    var id = $("#id_pay").val() ? $("#id_pay").val() : null;
    if (id !== null) {
      $.ajax({
        url: "finance/getDepositByInvoiceId?id=" + id,
        method: "GET",
        data: "",
        dataType: "json",
        success: function (response) {
          var due = $("#gross").val() - response.response;
          $("#due").val(due);
          updateCommissionSummary();
        },
      });
    } else {
      $("#due").val($("#gross").val() - amount_received);
          updateCommissionSummary();
    }
  });
});
$(document).ready(function () {
  "use strict";
  // $("#due").keyup(function () {
  //   var id = $("#id_pay").val();
  //   if (id !== null) {
  //     $.ajax({
  //       url: "finance/getDepositByInvoiceId?id=" + id,
  //       method: "GET",
  //       data: "",
  //       dataType: "json",
  //       success: function (response) {
  //         var due = $("#gross").val() - response.response;
  //         $("#due").val(due);
          updateCommissionSummary();
  //       },
  //     });
  //   } else {
  //     $("#due").val($("#gross").val());
          updateCommissionSummary();
  //   }
  // });
  $("#amount_received").keyup(function () {
    var gross = $("#gross").val();

    var ammount_recived = $(this).val();

    $("#due").val(gross - ammount_recived);
          updateCommissionSummary();
  });
  if ($.trim($("#id_pay").val()) == "" && $("#pos_select").val() !== "add_new" && !$("#pos_select").val()) {
    $(".pos_client").hide();
  }

  // -------------------------------------------------------------
  // PATIENT MODAL & AUTO-SELECTION
  // -------------------------------------------------------------
  $(document.body).on("change", "#pos_select", function () {
    "use strict";
    var v = $("select.pos_select option:selected").val();
    if (!v) {
      $(".pos_client").hide();
      $(".pos_new_patient_actions").hide();
      $("#p_name").prop("required", false);
      $("#p_phone").prop("required", false);
      return;
    }

    if (v === "add_new") {
      $("#pos_select").val(null).trigger("change.select2");
      $("#patient_modal_error").addClass("d-none").text("");
      if ($("#formAddNewPatientModal").length) {
        $("#formAddNewPatientModal")[0].reset();
      }
      $("#patientModal").modal("show");
      return;
    } else {
      $(".pos_new_patient_actions").hide();
      $(".pos_client").hide();
      $("#p_name").prop("required", false);
      $("#p_phone").prop("required", false);

      $.ajax({
        url: "patient/getPatientById?patient_id=" + encodeURIComponent(v),
        method: "GET",
        dataType: "json",
        success: function (response) {
          if (response && (response.data || response.patient)) {
            var p = response.data || response.patient;
            $("#p_name").val(p.name || "");
            $("#p_phone").val(p.phone || "");
            var ageVal = "";
            if (p.age !== undefined && p.age !== null) {
              ageVal = (typeof p.age === "string" && p.age.indexOf("-") !== -1) ? p.age.split("-")[0] : p.age;
            }
            $("#p_age").val(ageVal);
            $("#p_gender").val(p.sex || "");
            $("#p_weight").val(p.weight || "");
            $("#p_address").val(p.address || "");
          }
        }
      });
    }
  });

  $(document.body).off("click.openPatientModal", "#btn_open_patient_modal")
                 .on("click.openPatientModal", "#btn_open_patient_modal", function (e) {
    "use strict";
    e.preventDefault();
    $("#patient_modal_error").addClass("d-none").text("");
    if ($("#formAddNewPatientModal").length) {
      $("#formAddNewPatientModal")[0].reset();
    }
    $("#patientModal").modal("show");
  });

  $("#patientModal").on("shown.bs.modal", function () {
    $("#modal_p_name").trigger("focus");
  });

  $(document.body).off("click.savePatientModal", "#btn_modal_save_patient")
                 .on("click.savePatientModal", "#btn_modal_save_patient", function (e) {
    "use strict";
    e.preventDefault();
    var name = $.trim($("#modal_p_name").val());
    var phone = $.trim($("#modal_p_phone").val());
    var age = $.trim($("#modal_p_age").val());
    var sex = $("#modal_p_gender").val();
    var weight = $.trim($("#modal_p_weight").val());
    var address = $.trim($("#modal_p_address").val());

    $("#patient_modal_error").addClass("d-none").text("");

    if (!name) {
      $("#patient_modal_error").removeClass("d-none").text("Patient Full Name is required.");
      $("#modal_p_name").focus();
      return false;
    }
    if (!phone) {
      $("#patient_modal_error").removeClass("d-none").text("Phone Number is required.");
      $("#modal_p_phone").focus();
      return false;
    }
    var cleanPhone = phone.replace(/[\s\-]/g, "");
    if (!/^(?:\+?880|880|0)?1[3-9]\d{8}$/.test(cleanPhone)) {
      $("#patient_modal_error").removeClass("d-none").text("Please enter a valid Bangladesh mobile number (e.g. 01XXXXXXXXX).");
      $("#modal_p_phone").focus();
      return false;
    }

    var $btn = $(this);
    var origHtml = $btn.html();
    $btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

    $.ajax({
      url: "patient/addNewWithAjax",
      type: "POST",
      dataType: "json",
      data: {
        name: name,
        phone: phone,
        age: age,
        sex: sex,
        weight: weight,
        address: address
      },
      success: function (res) {
        $btn.prop("disabled", false).html(origHtml);
        if (res && res.status === "success" && res.patient) {
          var p = res.patient;
          var newOption = new Option(p.text || (p.name + " (" + p.phone + ")"), p.id, true, true);
          $("#pos_select").append(newOption).trigger("change");

          $("#p_name").val(p.name || "");
          $("#p_phone").val(p.phone || "");
          $("#p_age").val(p.age || "");
          $("#p_gender").val(p.sex || "");
          $("#p_weight").val(p.weight || "");
          $("#p_address").val(p.address || "");
          $(".pos_client").hide();
          $(".pos_new_patient_actions").hide();

          $("#patientModal").modal("hide");
          if ($("#formAddNewPatientModal").length) {
            $("#formAddNewPatientModal")[0].reset();
          }

          if (typeof toastr !== "undefined") {
            toastr.success(res.message || "Patient created successfully.");
          } else if (typeof Swal !== "undefined") {
            Swal.fire({
              toast: true,
              position: "top-end",
              icon: "success",
              title: res.message || "Patient created successfully.",
              showConfirmButton: false,
              timer: 2500
            });
          }
        } else {
          var msg = (res && res.message) ? res.message : "Failed to create patient.";
          $("#patient_modal_error").removeClass("d-none").text(msg);
        }
      },
      error: function (xhr) {
        $btn.prop("disabled", false).html(origHtml);
        var msg = "Unable to create patient. Please check your network and try again.";
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        $("#patient_modal_error").removeClass("d-none").text(msg);
      }
    });
  });

  // Legacy inline save patient button compatibility
  $(document.body).on("click", "#btn_save_patient_ajax", function (e) {
    "use strict";
    e.preventDefault();
    var name = $.trim($("#p_name").val());
    var phone = $.trim($("#p_phone").val());
    var age = $.trim($("#p_age").val());
    var sex = $("#p_gender").val();
    var weight = $.trim($("#p_weight").val());
    var address = $.trim($("#p_address").val());

    if (!name) {
      alert("Patient Full Name is required.");
      $("#p_name").focus();
      return false;
    }
    if (!phone) {
      alert("Phone Number is required.");
      $("#p_phone").focus();
      return false;
    }

    var $btn = $(this);
    var origHtml = $btn.html();
    $btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

    $.ajax({
      url: "patient/addNewWithAjax",
      type: "POST",
      dataType: "json",
      data: {
        name: name,
        phone: phone,
        age: age,
        sex: sex,
        weight: weight,
        address: address
      },
      success: function (res) {
        $btn.prop("disabled", false).html(origHtml);
        if (res && res.status === "success" && res.patient) {
          var p = res.patient;
          var newOption = new Option(p.text || (p.name + " (" + p.phone + ")"), p.id, true, true);
          $("#pos_select").append(newOption).trigger("change");
          $(".pos_new_patient_actions").hide();
          if (typeof toastr !== "undefined") {
            toastr.success(res.message || "Patient created successfully.");
          }
        } else {
          alert((res && res.message) ? res.message : "Error creating patient.");
        }
      },
      error: function (xhr) {
        $btn.prop("disabled", false).html(origHtml);
        var msg = "Failed to save patient.";
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        alert(msg);
      }
    });
  });

  // -------------------------------------------------------------
  // DOCTOR MODAL & AUTO-SELECTION
  // -------------------------------------------------------------
  $(document.body).off("click.openDoctorModal", "#btn_open_doctor_modal")
                 .on("click.openDoctorModal", "#btn_open_doctor_modal", function (e) {
    "use strict";
    e.preventDefault();
    $("#doctor_modal_error").addClass("d-none").text("");
    if ($("#formAddNewDoctorModal").length) {
      $("#formAddNewDoctorModal")[0].reset();
    }
    $("#doctorModal").modal("show");
  });

  $("#doctorModal").on("shown.bs.modal", function () {
    $("#modal_doc_name").trigger("focus");
  });

  $(document.body).on("change", "#add_doctor", function () {
    "use strict";
    var v = $(this).val();
    if (v === "add_new") {
      $("#add_doctor").val(null).trigger("change.select2");
      $("#doctor_modal_error").addClass("d-none").text("");
      if ($("#formAddNewDoctorModal").length) {
        $("#formAddNewDoctorModal")[0].reset();
      }
      $("#doctorModal").modal("show");
      return;
    }
    updateCommissionSummary();
  });

  $(document.body).off("click.saveDoctorModal", "#btn_modal_save_doctor")
                 .on("click.saveDoctorModal", "#btn_modal_save_doctor", function (e) {
    "use strict";
    e.preventDefault();
    var name = $.trim($("#modal_doc_name").val());
    var name_en = $.trim($("#modal_doc_name_en").val());
    var phone = $.trim($("#modal_doc_phone").val());
    var department_name = $.trim($("#modal_doc_department").val());
    var profile = $.trim($("#modal_doc_profile").val());
    var address = $.trim($("#modal_doc_address").val());

    $("#doctor_modal_error").addClass("d-none").text("");

    if (!name) {
      $("#doctor_modal_error").removeClass("d-none").text("Doctor Name is required.");
      $("#modal_doc_name").focus();
      return false;
    }
    if (!phone) {
      $("#doctor_modal_error").removeClass("d-none").text("Phone Number is required.");
      $("#modal_doc_phone").focus();
      return false;
    }
    var cleanPhone = phone.replace(/[\s\-]/g, "");
    if (!/^(?:\+?880|880|0)?1[3-9]\d{8}$/.test(cleanPhone)) {
      $("#doctor_modal_error").removeClass("d-none").text("Please enter a valid Bangladesh mobile number (e.g. 01XXXXXXXXX).");
      $("#modal_doc_phone").focus();
      return false;
    }

    var $btn = $(this);
    var origHtml = $btn.html();
    $btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

    $.ajax({
      url: "doctor/addNewWithAjax",
      type: "POST",
      dataType: "json",
      data: {
        name: name,
        name_en: name_en,
        phone: phone,
        department_name: department_name,
        degrees: profile,
        profile: profile,
        address: address
      },
      success: function (res) {
        $btn.prop("disabled", false).html(origHtml);
        if (res && res.status === "success" && res.doctor) {
          var d = res.doctor;
          var newOption = new Option(d.text, d.id, true, true);
          $("#add_doctor").append(newOption).trigger("change");
          updateCommissionSummary();

          $("#doctorModal").modal("hide");
          if ($("#formAddNewDoctorModal").length) {
            $("#formAddNewDoctorModal")[0].reset();
          }

          if (typeof toastr !== "undefined") {
            toastr.success(res.message || "Doctor created successfully.");
          } else if (typeof Swal !== "undefined") {
            Swal.fire({
              toast: true,
              position: "top-end",
              icon: "success",
              title: res.message || "Doctor created successfully.",
              showConfirmButton: false,
              timer: 2500
            });
          }
        } else {
          var msg = (res && res.message) ? res.message : "Failed to create doctor.";
          $("#doctor_modal_error").removeClass("d-none").text(msg);
        }
      },
      error: function (xhr) {
        $btn.prop("disabled", false).html(origHtml);
        var msg = "Unable to create doctor. Please check your network and try again.";
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        $("#doctor_modal_error").removeClass("d-none").text(msg);
      }
    });
  });

  // -------------------------------------------------------------
  // REFERRER MODAL & AUTO-SELECTION
  // -------------------------------------------------------------
  $(document.body).off("click.openReferrerModal", "#btn_open_referrer_modal")
                 .on("click.openReferrerModal", "#btn_open_referrer_modal", function (e) {
    "use strict";
    e.preventDefault();
    $("#referrer_modal_error").addClass("d-none").text("");
    if ($("#formAddNewReferrerModal").length) {
      $("#formAddNewReferrerModal")[0].reset();
      $("#modal_ref_type").val("Doctor");
    }
    $("#referrerModal").modal("show");
  });

  $("#referrerModal").on("shown.bs.modal", function () {
    $("#modal_ref_name").trigger("focus");
  });

  $(document.body).off("click.saveReferrerModal", "#btn_modal_save_referrer")
                 .on("click.saveReferrerModal", "#btn_modal_save_referrer", function (e) {
    "use strict";
    e.preventDefault();
    var name = $.trim($("#modal_ref_name").val());
    var phone = $.trim($("#modal_ref_phone").val());
    var type = $("#modal_ref_type").val();
    var address = $.trim($("#modal_ref_address").val());
    var notes = $.trim($("#modal_ref_notes").val());

    $("#referrer_modal_error").addClass("d-none").text("");

    if (!name) {
      $("#referrer_modal_error").removeClass("d-none").text("Referrer Full Name is required.");
      $("#modal_ref_name").focus();
      return false;
    }
    if (!phone) {
      $("#referrer_modal_error").removeClass("d-none").text("Phone Number is required.");
      $("#modal_ref_phone").focus();
      return false;
    }
    var cleanPhone = phone.replace(/[\s\-]/g, "");
    if (!/^(?:\+?880|880|0)?1[3-9]\d{8}$/.test(cleanPhone)) {
      $("#referrer_modal_error").removeClass("d-none").text("Please enter a valid Bangladesh mobile number (e.g. 01XXXXXXXXX).");
      $("#modal_ref_phone").focus();
      return false;
    }

    var $btn = $(this);
    var origHtml = $btn.html();
    $btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

    $.ajax({
      url: "referral/addNewWithAjax",
      type: "POST",
      dataType: "json",
      data: {
        name: name,
        phone: phone,
        type: type,
        address: address,
        notes: notes
      },
      success: function (res) {
        $btn.prop("disabled", false).html(origHtml);
        if (res && res.status === "success" && res.referrer) {
          var r = res.referrer;
          var newOption = new Option(r.text, r.id, true, true);
          $("#add_referrer").append(newOption).trigger("change");
          updateCommissionSummary();

          $("#referrerModal").modal("hide");
          if ($("#formAddNewReferrerModal").length) {
            $("#formAddNewReferrerModal")[0].reset();
          }

          if (typeof toastr !== "undefined") {
            toastr.success(res.message || "Referrer created successfully.");
          } else if (typeof Swal !== "undefined") {
            Swal.fire({
              toast: true,
              position: "top-end",
              icon: "success",
              title: res.message || "Referrer created successfully.",
              showConfirmButton: false,
              timer: 2500
            });
          }
        } else {
          var msg = (res && res.message) ? res.message : "Failed to create referrer.";
          $("#referrer_modal_error").removeClass("d-none").text(msg);
        }
      },
      error: function (xhr) {
        $btn.prop("disabled", false).html(origHtml);
        var msg = "Unable to create referrer. Please check your network and try again.";
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        $("#referrer_modal_error").removeClass("d-none").text(msg);
      }
    });
  });
});

$(document).ready(function () {
  "use strict";
  $(".cardPayment").hide();
  $(document.body).on("change", "#selecttype", function () {
    "use strict";
    var v = $("select.selecttype option:selected").val();
    if (v === "Card") {
      $(".insurance_div").addClass("hidden");
      $(".cardsubmit").removeClass("d-none");
      $(".cashsubmit").addClass("d-none");
      $(".cardsubmit3").removeClass("d-none");
      $(".cashsubmit2").addClass("d-none");
      $("#amount_received").prop("required", true);

      $(".cardPayment").show();
    } if (v === "Insurance") {
      $(".insurance_div").removeClass("hidden");
      $(".cardPayment").hide();
      $(".cashsubmit").removeClass("d-none");
      $(".cardsubmit").addClass("d-none");
      $(".cashsubmit2").removeClass("d-none");
      $(".cardsubmit3").addClass("d-none");
      $("#amount_received").prop("required", false);
    }
    if (v === "Cash") {
      $(".insurance_div").addClass("hidden");
      $(".cardPayment").hide();
      $(".cashsubmit").removeClass("d-none");
      $(".cardsubmit").addClass("d-none");
      $(".cashsubmit2").removeClass("d-none");
      $(".cardsubmit3").addClass("d-none");
      $("#amount_received").prop("required", false);
    }
  });
});

function cardValidation() {
  "use strict";
  var valid = true;
  var cardNumber = $("#card").val();
  var expire = $("#expire").val();
  var cvc = $("#cvv").val();

  $("#error-message").html("").hide();

  if (cardNumber.trim() == "") {
    valid = false;
  }

  if (expire.trim() == "") {
    valid = false;
  }
  if (cvc.trim() == "") {
    valid = false;
  }

  if (valid == false) {
    $("#error-message").html("All Fields are required").show();
  }

  return valid;
}
//set your publishable key
Stripe.setPublishableKey(publish);

//callback to handle the response from stripe
function stripeResponseHandler(status, response) {
  "use strict";

  if (response.error) {
    alert(response.error.message);
    $("#submit-btn").show();
    $("#submit-btn2").show();
    $("#loader").css("display", "none");
    $("#submit-btn").attr("disabled", false);
    $("#submit-btn2").show();
    $("#error-message").html(response.error.message).show();
  } else {
    var token = response["id"];
    if (token != null) {
      $("#token").val(token);
      $("#editPaymentForm").append(
        "<input type='hidden' name='token' value='" + token + "' />"
      );
      $("#editPaymentForm").submit();
    } else {
      alert("Please Check Your Card details");
      $("#submit-btn").attr("disabled", false);
      $("#submit-btn2").attr("disabled", false);
    }
  }
}

function stripePay(e) {
  "use strict";
  e.preventDefault();
  var valid = cardValidation();

  if (valid == true) {
    $("#submit-btn").attr("disabled", true);
    $("#loader").css("display", "inline-block");
    var expire = $("#expire").val();
    var arr = expire.split("/");
    Stripe.createToken(
      {
        number: $("#card").val(),
        cvc: $("#cvv").val(),
        exp_month: arr[0],
        exp_year: arr[1],
      },
      stripeResponseHandler
    );

    return false;
  }
}



$(document).ready(function () {
  "use strict";
  $("#pos_select").select2({
    placeholder: select_patient,
    allowClear: true,
    ajax: {
      url: "patient/getPatientinfoWithAddNewOption",
      type: "post",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          searchTerm: params.term, // search term
        };
      },
      processResults: function (response) {
        return {
          results: response,
        };
      },
      cache: true,
    },
  });

  $("#add_doctor").select2({
    placeholder: select_doctor,
    allowClear: true,
    ajax: {
      url: "doctor/getDoctorWithAddNewOption",
      type: "post",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          searchTerm: params.term, // search term
        };
      },
      processResults: function (response) {
        return {
          results: response,
        };
      },
      cache: true,
    },
  });
});











// $(document).ready(function() {



//     $('#selected_testpkz').on('change', function() {
//         var id = $(this).val();
//         var count = 0;
//         // var testpkz_id = $('#testpkz_id').val();
//         $.ajax({
//             url: 'testpkz/getTestpkzDetails?id=' + id,
//             method: 'GET',
//             data: '',
//             dataType: 'json',
//             success: function(response2) {
//                 var testpkz_item1 = response2.testpkz_item_list.split(",");

//                 $.each(testpkz_item1, function(index, value) {
//                     let testpkz_item_extended111 = [];
//                     testpkz_item_extended111 = value.split("****");
//                     var $select = $("#selected_testpkz");
//                     var idToRemove = testpkz_item_extended111[0] + '*' +
//                     testpkz_item_extended111[1];
//                     $("#selected_testpkz option[value='" +
//                     testpkz_item_extended111[0] + '*' +
//                     testpkz_item_extended111[1] + "']").remove();
//                     $('.select2 - selection__clear').find('[title="' +
//                     testpkz_item_extended111[1] + '"]').remove();
//                     var values = $select.val();
//                     if (values) {
//                         var i = values.indexOf(idToRemove);
//                         if (i >= 0) {
//                             values.splice(i);
//                             $select.val(values).change();
//                         }
//                     }
//                     $('#med_selected_section-' + testpkz_item_extended111[0])
//                         .remove();
//                 });
//             },
//         });
//         // $(".board_doc").find("option").remove();
//         $.ajax({
//             url: 'testpkz/getTestpkzDetails?id=' + id,
//             method: 'GET',
//             data: '',
//             dataType: 'json',
//             //  timeout: 5000
//             success: function(response) {
//                 // $('#team_id').val(id);
//                 // var lead_doctor = response.team.lead_doctor
//                 // $('#board_leader_id').val(lead_doctor);
//                 var testpkz_item = response.testpkz.payment_category.split(",");
//                 $.each(testpkz_item, function(index, value) {
//                     var testpkz_item_extended = [];
//                     testpkz_item_extended = value.split("***");
//                     var item_id = testpkz_item_extended[0];
//                     $.ajax({
//                         url: 'finance/getPaymentCategoryByJason?id=' + item_id,
//                         method: 'GET',
//                         data: '',
//                         dataType: 'json',
//                         success: function(response1) {
//                             var id = response1.testpkz.id;
//                             // var id = $(this).data('id');
//                             // var med_id = response1.testpkz.id;
//                             var med_name = response1.testpkz.category;
//                             var price = response1.testpkz.c_price;
//                             var selection = $('selection').text();
//                             // var option = new Option(med_name, true,
//                             //     true);
//                             // $('.ms-list').append(option).trigger(
//                             //     'change');
//                             $('.ms-list').append('<li class="ooppttiioonn ms-elem-selection ms-selected" data-id="' + price + '" data-iid="' + id + '" data-cat_name="' + med_name + '" id="' + id + '-selection' + '">' + med_name + '</li>');
//                             // $('.ms-list').append('<li class="ooppttiioonn ms-elem-selection ms-selected">' + med_name + '</li>');
//                         },




//                     });
//                 });
//             },

//         });



//     });
// });


$(document).ready(function () {
  // Listen for changes in the first dropdown
  $('#selected_testpkz').change(function () {
    // Get the selected option's value
    var selectedValue = $(this).val();

    // Click the first two <li> items in the second dropdown
    $('.ms-list li').removeClass('selected');
    $('.ms-list li[data-idd="' + selectedValue + '"]').addClass('selected');
    $('.ms-list li[data-idd="' + selectedValue + '"]').next().addClass('selected');
  });
});


$(document).ready(function () {
  // Add a change event listener to the country dropdown
  $('#selected_testpkz').change(function () {
    // Get the selected country
    const country = $(this).val();

    // Deselect all list items
    //$('li').removeClass('selected');
    // alert('hi');
    // Select the list items that match the selected country
    $('ms-list').click();
    // $('li[data-idd="66"]').click().addClass('ms-selected');
    // $('li').css("display", "none");
  });
});


