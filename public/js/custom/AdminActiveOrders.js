$("#search")
  .autocomplete({
    source: function (req, res) {
      $.ajax({
        url: "/order/searchactive/" + req.term,
        success: function (response) {
          //   Below res is the one from line 2
          res(response);
        },
      });
    },
    minLength: 1,
    select: function (event, ui) {
      submitForm(ui.item);
    },
  })
  .autocomplete("instance")._renderItem = function (ul, item) {
  return $("<li>")
    .append("<div>" + item.name + "</div>")
    .appendTo(ul);
};

function submitForm(item) {
    location.replace('/order/active?search='+item.name)
}
