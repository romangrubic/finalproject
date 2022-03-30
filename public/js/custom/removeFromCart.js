$('.removeFromCart').on('click', function(){
    let productId = ($(this).data("id"));
    $.ajax({
        url: "/shoppingorder/removefromcart/" + productId,
        success: function (result) {
          if (result == "OK") {
            alert("Proizvod obrisan iz košarice!");
            $('#product'+productId).remove();
          } else {
            alert("Dogodila se greska. Pokusajte ponovo!");
          }
        },
      });
})