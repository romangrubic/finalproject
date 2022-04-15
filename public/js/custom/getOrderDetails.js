$(".modal").on("click", function () {
  let orderId = $(this).data("id");
  console.log(orderId);
  $.ajax({
    url: "/order/getDetails/" + orderId,
    success: function (response, result) {
      if (result == "success") {
        $("#modal" + orderId).foundation("open");
        let array = JSON.parse(response);
        let sum = 0;
        for (var i = 0; i < array.length; i++) {
        //   console.log(array[i]);
          sum += parseFloat(array[i].price);
          $("<tr>")
            .append("<td>" + array[i].name + "</td>")
            .append("<td class=\"text-center\">" + array[i].quantity + "</td>")
            .append("<td>" + array[i].productPrice + " kn</td>")
            .append("<td>" + array[i].price + " kn</td>")
            .appendTo("#order");
        }
        let total = 'Sveukupno: '+sum.toFixed(2) + ' kn';
        $("<span>").append(total).appendTo("#sum");
      } else {
        alert("Dogodila se greška. Pokusajte ponovo!");
      }
    },
  });
});

// Cleaning values from modal when closing
(function ($, window, undefined) {
    'use strict';
  
    $('[data-reveal]').on('closed.zf.reveal', function () {
      var modal = $(this);
      $("#order").empty();
      $("#sum").empty();
    });
  
    $(document).foundation();
  
  
  })(jQuery, this);