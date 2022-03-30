// Using class selector since there are multiple add-to-cart on screen and getting id throught this data-id
$(".addToCart").on("click", function () {
  let productId = $(this).data("id");
  $.ajax({
    url: "/shoppingorder/addtocart/" + productId,
    success: function (result) {
      if (result == "OK") {
        alert("Proizvod uspjesno dodan u kosaricu!");
      } else {
        alert("Dogodila se greska. Pokusajte ponovo!");
      }
    },
  });
});
