$('.removeFromCart').on('click', function(){
    let productId = ($(this).data("id"));
    $.ajax({
        url: "/shoppingorder/removefromcart/" + productId,
        success: function (result) {
          if (result == "OK") {
            $.ajax({
              url: "/shoppingorder/numberofproducts/",
              success: function (result) {
                // If it's ) then refresh window because of PHP
                if(result == ''){
                  location.reload();
                }
              }
            })
            $( "#shopping-icon" ).load(location.href+" #shopping-icon>*","");
            $('#product'+productId).remove();
          } else {
            alert("Dogodila se greska. Pokusajte ponovo!");
          }
        },
      });
})