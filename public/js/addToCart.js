$("#addToCart").click(function () {
  $.ajax({
    url: "/shoppingorder/addtocart/" + product,
  });
  alert("Proizvod uspješno dodan u košaricu!");
});
