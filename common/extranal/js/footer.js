"use strict";
$(".multi-select").multiSelect({
  selectableHeader:
    "<div class='ms-search-wrap'><input type='text' class='search-input' autocomplete='off' placeholder='Search tests (e.g. CBC, X-Ray, ECG)...'><button type='button' class='ms-search-clear' style='display:none;' title='Clear search'>&times;</button></div>",
  selectionHeader:
    "<input type='text' class='search-input col-md-12' autocomplete='off' placeholder=''>",
  afterInit: function (ms) {
    "use strict";
    var that = this,
      $selectableSearch = that.$selectableContainer.find('.search-input'),
      $selectionSearch = that.$selectionContainer.find('.search-input'),
      selectableSearchString =
        "#" +
        that.$container.attr("id") +
        " .ms-elem-selectable:not(.ms-selected)",
      selectionSearchString =
        "#" + that.$container.attr("id") + " .ms-elem-selection.ms-selected";

    that.qs1 = $selectableSearch
      .quicksearch(selectableSearchString)
      .on("keydown", function (e) {
        "use strict";
        if (e.which === 40) {
          that.$selectableUl.focus();
          return false;
        }
      });

    that.qs2 = $selectionSearch
      .quicksearch(selectionSearchString)
      .on("keydown", function (e) {
        "use strict";
        if (e.which === 40) {
          that.$selectionUl.focus();
          return false;
        }
      });
  },
  afterSelect: function () {
    "use strict";
    var that = this;
    var $search = that.$selectableContainer.find('.search-input');
    if ($search.length) {
      $search.val('');
      that.$selectableContainer.find('.ms-search-clear').hide();
      that.$selectableUl.find('.ms-elem-selectable').show();
      that.$selectableUl.find('.ms-optgroup-container').show();
      that.$selectableUl.find('.ms-optgroup-label').show();
    }
    if (this.qs1 && typeof this.qs1.cache === 'function') {
      this.qs1.cache();
    }
    if (this.qs2 && typeof this.qs2.cache === 'function') {
      this.qs2.cache();
    }
  },
  afterDeselect: function () {
    "use strict";
    if (this.qs1 && typeof this.qs1.cache === 'function') {
      this.qs1.cache();
    }
    if (this.qs2 && typeof this.qs2.cache === 'function') {
      this.qs2.cache();
    }
  },
});
$(document).ready(function () {
  $(document.body).on("click", ".remove_attr", function () {
    var tot = 0;
    var fullid = " ";
    fullid = $(this).attr("id");
    console.log(fullid);
    var idd3 = fullid.split("-");
    var idd = idd3[2];
    ("use strict");
    //var idd = $(this).data("idd");
    $("#id-div" + idd).remove();
    $("#idinput-" + idd).remove();
    $("#categoryinput-" + idd).remove();
    $.each($("select.multi-select option:selected"), function () {
      var idd1 = $(this).data("idd");
      if (idd1 == idd) {
        $(this).prop("selected", false);
        $("#my_multi_select3").multiSelect("deselect", idd);
      }
    });

    $("#my_multi_select3").trigger("change");

    $.each($("select.multi-select option:selected"), function () {
      "use strict";
      var idd = $(this).data("idd");
      var qtity = $(this).data("qtity");
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
          var discount = $("#dis_id").val();
          var vat_amount = $("#vat_amount").val();
          var gross = tot - discount + vat_amount;

          $("#editPaymentForm").find('[name="subtotal"]').val(tot).end();
          //  $("#editPaymentForm").find('[name="vat"]').val(vat).end();
          $("#editPaymentForm").find('[name="grsss"]').val(gross);

          $("#editPaymentForm").find('[name="discount"]').val(discount).end();
          $("#editPaymentForm").find('[name="vat_amount"]').val(vat_amount).end();

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
              },
            });
          } else {
            $("#due").val($("#gross").val() - amount_received);
          }
        });
      });
      ("use strict");
      var sub_total = $(this).data("id") * $("#idinput-" + idd).val();
      tot = tot + sub_total;
    });
    ("use strict");
    var discount = $("#dis_id").val();

    if (discount_type === "flat") {
      var gross = tot - discount + parseFloat(vat_amount.value);
    } else {
      var gross = parseFloat(vat_amount.value) + tot - (tot * discount) / 100;
    }

    $("#editPaymentForm").find('[name="subtotal"]').val(tot).end();

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
        },
      });
    } else {
      $("#due").val($("#gross").val() - amount_received);
    }
  });
});
var color = "white";
let reply_click = (clicked_id) => {
  //var function = reply_click(clicked_id) {
  var c = document.getElementById(clicked_id).getAttribute("fill");

  if (c != "white") {
    document.getElementById(clicked_id).setAttribute("fill", "white");
  } else {
    document.getElementById(clicked_id).setAttribute("fill", color);
  }
  //alert(color);
  var c = document.getElementById(clicked_id).getAttribute("fill");
  var fill = c;
  if (clicked_id == "Tooth32") {
    if (document.getElementById("t32").value == fill) {
      document.getElementById("t32").value = "white";
    } else {
      document.getElementById("t32").value = fill;
    }
  } else if (clicked_id == "Tooth31") {
    if (document.getElementById("t31").value == fill) {
      document.getElementById("t31").value = "white";
    } else {
      document.getElementById("t31").value = fill;
    }
  } else if (clicked_id == "Tooth30") {
    if (document.getElementById("t30").value == fill) {
      document.getElementById("t30").value = "white";
    } else {
      document.getElementById("t30").value = fill;
    }
  } else if (clicked_id == "Tooth29") {
    if (document.getElementById("t29").value == fill) {
      document.getElementById("t29").value = "white";
    } else {
      document.getElementById("t29").value = fill;
    }
  } else if (clicked_id == "Tooth28") {
    if (document.getElementById("t28").value == fill) {
      document.getElementById("t28").value = "white";
    } else {
      document.getElementById("t28").value = fill;
    }
  } else if (clicked_id == "Tooth27") {
    if (document.getElementById("t27").value == fill) {
      document.getElementById("t27").value = "white";
    } else {
      document.getElementById("t27").value = fill;
    }
  } else if (clicked_id == "Tooth26") {
    if (document.getElementById("t26").value == fill) {
      document.getElementById("t26").value = "white";
    } else {
      document.getElementById("t26").value = fill;
    }
  } else if (clicked_id == "Tooth25") {
    if (document.getElementById("t25").value == fill) {
      document.getElementById("t25").value = "white";
    } else {
      document.getElementById("t25").value = fill;
    }
  } else if (clicked_id == "Tooth24") {
    if (document.getElementById("t24").value == fill) {
      document.getElementById("t24").value = "white";
    } else {
      document.getElementById("t24").value = fill;
    }
  } else if (clicked_id == "Tooth23") {
    if (document.getElementById("t23").value == fill) {
      document.getElementById("t23").value = "white";
    } else {
      document.getElementById("t23").value = fill;
    }
  } else if (clicked_id == "Tooth22") {
    if (document.getElementById("t22").value == fill) {
      document.getElementById("t22").value = "white";
    } else {
      document.getElementById("t22").value = fill;
    }
  } else if (clicked_id == "Tooth21") {
    if (document.getElementById("t21").value == fill) {
      document.getElementById("t21").value = "white";
    } else {
      document.getElementById("t21").value = fill;
    }
  } else if (clicked_id == "Tooth20") {
    if (document.getElementById("t20").value == fill) {
      document.getElementById("t20").value = "white";
    } else {
      document.getElementById("t20").value = fill;
    }
  } else if (clicked_id == "Tooth19") {
    if (document.getElementById("t19").value == fill) {
      document.getElementById("t19").value = "white";
    } else {
      document.getElementById("t19").value = fill;
    }
  } else if (clicked_id == "Tooth18") {
    if (document.getElementById("t18").value == fill) {
      document.getElementById("t18").value = "white";
    } else {
      document.getElementById("t18").value = fill;
    }
  } else if (clicked_id == "Tooth17") {
    if (document.getElementById("t17").value == fill) {
      document.getElementById("t17").value = "white";
    } else {
      document.getElementById("t17").value = fill;
    }
  } else if (clicked_id == "Tooth16") {
    if (document.getElementById("t16").value == fill) {
      document.getElementById("t16").value = "white";
    } else {
      document.getElementById("t16").value = fill;
    }
  } else if (clicked_id == "Tooth15") {
    if (document.getElementById("t15").value == fill) {
      document.getElementById("t15").value = "white";
    } else {
      document.getElementById("t15").value = fill;
    }
  } else if (clicked_id == "Tooth14") {
    if (document.getElementById("t14").value == fill) {
      document.getElementById("t14").value = "white";
    } else {
      document.getElementById("t14").value = fill;
    }
  } else if (clicked_id == "Tooth13") {
    if (document.getElementById("t13").value == fill) {
      document.getElementById("t13").value = "white";
    } else {
      document.getElementById("t13").value = fill;
    }
  } else if (clicked_id == "Tooth12") {
    if (document.getElementById("t12").value == fill) {
      document.getElementById("t12").value = "white";
    } else {
      document.getElementById("t12").value = fill;
    }
  } else if (clicked_id == "Tooth11") {
    if (document.getElementById("t11").value == fill) {
      document.getElementById("t11").value = "white";
    } else {
      document.getElementById("t11").value = fill;
    }
  } else if (clicked_id == "Tooth10") {
    if (document.getElementById("t10").value == fill) {
      document.getElementById("t10").value = "white";
    } else {
      document.getElementById("t10").value = fill;
    }
  } else if (clicked_id == "Tooth9") {
    if (document.getElementById("t9").value == fill) {
      document.getElementById("t9").value = "white";
    } else {
      document.getElementById("t9").value = fill;
    }
  } else if (clicked_id == "Tooth8") {
    if (document.getElementById("t8").value == fill) {
      document.getElementById("t8").value = "white";
    } else {
      document.getElementById("t8").value = fill;
    }
  } else if (clicked_id == "Tooth7") {
    if (document.getElementById("t7").value == fill) {
      document.getElementById("t7").value = "white";
    } else {
      document.getElementById("t7").value = fill;
    }
  } else if (clicked_id == "Tooth6") {
    if (document.getElementById("t6").value == fill) {
      document.getElementById("t6").value = "white";
    } else {
      document.getElementById("t6").value = fill;
    }
  } else if (clicked_id == "Tooth5") {
    if (document.getElementById("t6").value == fill) {
      document.getElementById("t6").value = "white";
    } else {
      document.getElementById("t6").value = fill;
    }
  } else if (clicked_id == "Tooth4") {
    if (document.getElementById("t4").value == fill) {
      document.getElementById("t4").value = "white";
    } else {
      document.getElementById("t4").value = fill;
    }
  } else if (clicked_id == "Tooth3") {
    if (document.getElementById("t3").value == fill) {
      document.getElementById("t3").value = "white";
    } else {
      document.getElementById("t3").value = fill;
    }
  } else if (clicked_id == "Tooth2") {
    if (document.getElementById("t2").value == fill) {
      document.getElementById("t2").value = "white";
    } else {
      document.getElementById("t2").value = fill;
    }
  } else if (clicked_id == "Tooth1") {
    if (document.getElementById("t1").value == fill) {
      document.getElementById("t1").value = "white";
    } else {
      document.getElementById("t1").value = fill;
    }
  }
};
let cause = (cause_id) => {
  //function cause(cause_id) {
  if (cause_id == 1) {
    color = "#00ba72";
  } else if (cause_id == 2) {
    color = "#004eff";
  } else if (cause_id == 3) {
    color = "#ff0000";
  } else if (cause_id == 4) {
    color = "#ff9000";
  } else if (cause_id == 5) {
    color = "#9c00ff";
  } else if (cause_id == 6) {
    color = "#8e0101";
  } else if (cause_id == 7) {
    color = "#006666";
  } else if (cause_id == 8) {
    color = "#00c0ff";
  }
};

$("#my_multi_select3").multiSelect();

$(".default-date-picker").datepicker({
  format: "dd-mm-yyyy",
  autoclose: true,
  todayHighlight: true,
  startDate: "01-01-1900",
  clearBtn: true,
  language: langdate,
});

$("#date").on("changeDate", function () {
  "use strict";

  $("#date").datepicker("hide", {
    format: "dd-mm-yyyy",
    autoclose: true,
    todayHighlight: true,
    startDate: "01-01-1900",
    language: langdate,
  });
});

$("#date1").on("changeDate", function () {
  "use strict";
  $("#date1").datepicker("hide", {
    format: "dd-mm-yyyy",
    autoclose: true,
    todayHighlight: true,
    startDate: "01-01-1900",
    language: langdate,
  });
});




$(document).ready(function () {
  if (time_format == 12) {
    time_format = "hh:mm p";
    var showMeridian = true;
  } else {
    time_format = "HH:mm";
    var showMeridian = false;
  }
  $(".timepicker-default").timepicker({ defaultTime: "value" });
  $(".timepicker-default1").timepicker({
    defaultTime: "current",
    minuteStep: 15,
    timeFormat: time_format,
    showMeridian: showMeridian
  });
});

$(document).ready(function () {
  "use strict";
  $(".js-example-basic-single").select2();

  $(".js-example-basic-multiple").select2();
});

$(document).ready(function () {
  "use strict";
  var windowH = $(window).height();
  var wrapperH = $("#container").height();
  if (windowH > wrapperH) {
    $("#sidebar").css("height", windowH + "px");
  } else {
    $("#sidebar").css("height", wrapperH + "px");
  }
  var windowSize = window.innerWidth;
  if (windowSize < 768) {
    $("#sidebar").removeAttr("style");
  }
});
function onElementHeightChange(elm, callback) {
  "use strict";
  var newHeight;
  var lastHeight = elm.clientHeight,
    newHeight;
  (function run() {
    "use strict";
    newHeight = elm.clientHeight;
    if (lastHeight !== newHeight) callback();
    lastHeight = newHeight;
    if (elm.onElementHeightChangeTimer)
      clearTimeout(elm.onElementHeightChangeTimer);
    elm.onElementHeightChangeTimer = setTimeout(run, 200);
  })();
}

onElementHeightChange(document.body, function () {
  "use strict";
  var windowH = $(window).height();
  var wrapperH = $("#container").height();
  if (windowH > wrapperH) {
    $("#sidebar").css("height", windowH + "px");
  } else {
    $("#sidebar").css("height", wrapperH + "px");
  }

  var windowSize = $(window).width();
  if (windowSize < 768) {
    $("#sidebar").removeAttr("style");
  }
});



$(document).ready(function () {
  var width = $(window).width();
  if (width < 768) {
    $("#sidebar > ul").hide();
    $("#sidebar").removeAttr("style");
  } else {
    $("#sidebar > ul").show();
  }
});
$(document).ready(function () {
  $("#txtQuickFind").keyup(function () {
    $("#typeahead-menu").css("display", "none");
    var count = 0;
    var value = $('input[type="search"]').val();
    if (value == "" || value == null) {
      $(".menu-position").attr("aria-hidden", "false");
      $(".quickFindDiv").removeClass("open");
      $("#txtQuickFind").attr("aria-expanded", "false");
    } else {
      $(".quickFindDiv").addClass("open");
      $("#txtQuickFind").attr("aria-expanded", "true");

      $("li.site_map").each(function (element) {
        var id = $(this).attr("id");
        $("#" + id).addClass("hide-option");
        $("#" + id).removeClass("show-option");
        var name = $(this).attr("data-name");
        if (
          name.toLowerCase().match(value.toLowerCase()) == value.toLowerCase()
        ) {
          $("#typeahead-menu").css("display", "block");
          $("#" + id).removeClass("hide-option");
          $("#" + id).addClass("show-option");
          if ($("#" + id).hasClass("active")) {
            $("#" + id).removeClass("active");
          }

          if (count == 0) {
            $(".menu-position").attr("aria-hidden", "true");
            $("#" + id).addClass("active");
          }
          count++;
        }
      });
      // $("#" + id)
      //   .first()
      //   .addClass("active");
    }
  });
});
$(document).ready(function () {
  $(".site_map").hover(function () {
    $("li.site_map").each(function (element) {
      var id1 = $(this).attr("id");
      if ($("#" + id1).hasClass("active")) {
        $("#" + id1).removeClass("active");
      }
    });
    var id = $(this).attr("id");
    $("#" + id).addClass("active");
  });

});


/** add active class and stay opened when selected */
var url = window.location;

// for sidebar menu entirely but not cover treeview
$('ul.nav-sidebar a').filter(function () {
  return this.href == url;
}).addClass('active');

// for treeview
$('ul.nav-treeview a').filter(function () {
  return this.href == url;
}).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');








