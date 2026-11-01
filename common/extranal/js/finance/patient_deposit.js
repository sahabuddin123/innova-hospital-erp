"use strict";
$(document).ready(function () {
  "use strict";
  $(".table").on("click", ".editbutton", function () {
    "use strict";
    $("#loader").show();
    var iid = $(this).attr("data-id");
    $("#editDepositform").trigger("reset");
    $.ajax({
      url: "finance/editDepositbyJason?id=" + iid,
      method: "GET",
      data: "",
      dataType: "json",
      success: function (response) {
        "use strict";
        if (response.deposit.deposit_type != "Card") {
          $("#editDepositform")
            .find('[name="id"]')
            .val(response.deposit.id)
            .end();
          $("#editDepositform")
            .find('[name="patient"]')
            .val(response.deposit.patient)
            .end();
          $("#payment_id2").html(
            "<option value=" +
            response.deposit.payment_id +
            ">" +
            response.deposit.payment_id +
            "</option>"
          );
          $("#editDepositform")
            .find('[name="due"]')
            .val(response.response)
            .end();
          $("#editDepositform")
            .find('[name="date"]')
            .val(response.deposit.date)
            .end();
          $("#editDepositform")
            .find('[name="deposited_amount"]')
            .val(response.deposit.deposited_amount)
            .end();
          $("#editDepositform")
            .find('[name="insurance_company"]')
            .val(response.deposit.insurance_company)
            .end();
          $("#editDepositform")
            .find('[name="insurance_details"]')
            .val(response.deposit.insurance_details)
            .end();

          if (response.deposit.deposit_type == "Cash") {
            $(".insurance_div1").addClass("hidden");

          } if (response.deposit.deposit_type == "Insurance") {
            $(".insurance_div1").removeClass("hidden");

          }

          $("#myModal2").modal("show");
        } else {
          alert("Payement Processed By Card can not be edited. Thanks.");
        }
      },
      complete: function () {
        $("#loader").hide();
      },
    });
  });
  $("#deposited_amount").keyup(function () {
    var deposit = $(this).val();
    var due = $("#due_amount").val();
    if (parseFloat(deposit) > parseFloat(due)) {
      $("#deposited_amount").val(due);
    }
  });
  $("#deposited_amount1").keyup(function () {
    var deposit = $(this).val();
    var due = $("#due_amount1").val();
    if (parseFloat(deposit) > parseFloat(due)) {
      $("#deposited_amount1").val(due);
    }
  });
  $(".payment_class").on("change", "#payment_id1", function () {
    "use strict";
    var iid = $(this).val();
    $("#due_amount").val("");

    // $("#deposit-form").trigger("reset");
    $("#myModal").modal("show");
    if (iid == "") {
      $("#due_amount").val("");
      $("#deposited_amount").val("");
    } else {
      $.ajax({
        url: "finance/getDepositByInvoiceIdForDeposit?id=" + iid,
        method: "GET",
        data: "",
        dataType: "json",
        success: function (response) {
          "use strict";
          $("#due_amount").val(response.response);
        },
      });
    }
  });
});

$(document).ready(function () {
  "use strict";
  $(".cardPayment").hide();
  $(document.body).on("change", "#selecttype", function () {
    "use strict";
    var v = $("select.selecttype option:selected").val();
    if (v == "Card") {
      $(".insurance_div").addClass("hidden");
      $(".cardPayment").show();
      $(".cardsubmit").removeClass("d-none");
      $(".cashsubmit").addClass("d-none");
    } if (v == "Insurance") {
      $(".insurance_div").removeClass("hidden");
      $(".cardPayment").hide();
      $(".cashsubmit").removeClass("d-none");
      $(".cardsubmit").addClass("d-none");
    }
    else {
      $(".insurance_div").addClass("hidden");
      $(".cardPayment").hide();
      $(".cashsubmit").removeClass("d-none");
      $(".cardsubmit").addClass("d-none");
    }
  });
});

$(document).ready(function () {
  "use strict";
  $(".card1").hide();
  $(document.body).on("change", "#selecttype1", function () {
    "use strict";
    var v = $("select.selecttype1 option:selected").val();
    if (v == "Card") {
      $(".insurance_div1").addClass("hidden");
      $(".card1").show();
      $(".cardsubmit1").removeClass("hidden");
      $(".cashsubmit1").addClass("hidden");
    } if (v == "Insurance") {
      $(".insurance_div1").removeClass("hidden");
      $(".card1").hide();
      $(".cashsubmit1").removeClass("hidden");
      $(".cardsubmit1").addClass("hidden");
    }
    else {
      $(".insurance_div1").addClass("hidden");
      $(".card1").hide();
      $(".cashsubmit1").removeClass("hidden");
      $(".cardsubmit1").addClass("hidden");
    }
  });
});

$(document).ready(function () {
  "use strict";
  $(".flashmessage").delay(3000).fadeOut(100);
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

Stripe.setPublishableKey(publish);
function stripeResponseHandler(status, response) {
  "use strict";
  if (response.error) {
    $("#submit-btn").show();
    $("#loader").css("display", "none");

    $("#error-message").html(response.error.message).show();
    $("#submit-btn").attr("disabled", false);
    $("#error-message").html(response.error.message).show();
  } else {
    var token = response["id"];
    if (token != null) {
      $("#token").val(token);
      $("#deposit-form").append(
        "<input type='hidden' name='token' value='" + token + "' />"
      );

      $("#deposit-form").submit();
    } else {
      alert("Please Check Your Card details");
      $("#submit-btn").attr("disabled", false);
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

function cardValidation1() {
  "use strict";
  var valid = true;
  var cardNumber = $("#card1").val();
  var expire = $("#expire1").val();
  var cvc = $("#cvv1").val();

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

Stripe.setPublishableKey(publish);

function stripeResponseHandler1(status, response) {
  "use strict";
  if (response.error) {
    $("#submit-btn1").show();
    $("#loader").css("display", "none");

    $("#error-message").html(response.error.message).show();
  } else {
    var token = response["id"];

    $("#token").val(token);
    $("#editDepositform").append(
      "<input type='hidden' name='token' value='" + token + "' />"
    );

    $("#editDepositform").submit();
  }
}

function stripePay1(e) {
  "use strict";
  e.preventDefault();
  var valid = cardValidation1();

  if (valid == true) {
    $("#submit-btn").attr("disabled", true);
    $("#loader").css("display", "inline-block");
    var expire = $("#expire1").val();
    var arr = expire.split("/");
    Stripe.createToken(
      {
        number: $("#card1").val(),
        cvc: $("#cvv1").val(),
        exp_month: arr[0],
        exp_year: arr[1],
      },
      stripeResponseHandler1
    );

    return false;
  }
}


$(".down").on("click", "#download", function () {
  html2canvas($("#invoice")[0], {
    backgroundColor: '#FFFFFF',
    onrendered: function (canvas) {
      var imgData = canvas.toDataURL('image/png');
      var pdf = new jsPDF('p', 'pt', 'letter');

      // Adjusting page size access for compatibility
      var pdfWidth = pdf.internal.pageSize.width || pdf.internal.pageSize.getWidth();
      var canvasWidth = canvas.width;
      var canvasHeight = canvas.height;

      // Calculate the scale to fit the canvas width to the PDF width
      var scale = pdfWidth / canvasWidth;
      var scaledHeight = canvasHeight * scale;

      var adjustedWidth = pdfWidth * 0.8;
      var adjustedScale = adjustedWidth / canvas.width;
      var adjustedHeight = canvas.height * adjustedScale;

      // Center the image horizontally with the adjusted width
      var xOffset = (pdfWidth - adjustedWidth) / 2;

      pdf.addImage(imgData, 'PNG', xOffset, 0, adjustedWidth, adjustedHeight);
      pdf.save('invoice.pdf');
    }
  });
});
