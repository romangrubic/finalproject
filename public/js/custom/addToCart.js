// Using class selector since there are multiple add-to-cart on screen and getting id throught this data-id
$(".addToCart").on("click", function () {
  let productId = $(this).data("id");
  $.ajax({
    url: "/shoppingorder/addtocart/" + productId,
    success: function (result) {
      if (result == "OK") {
        // Updates badge icon without refreshing window :D
        $("#shopping-icon").load(location.href + " #shopping-icon>*", "");
        // alert("Proizvod uspješno dodan u košaricu!");
      } else {
        alert(result);
      }
    },
  });
});


