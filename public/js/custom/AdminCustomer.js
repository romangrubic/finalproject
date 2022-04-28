$("#search")
  .autocomplete({
    source: function (req, res) {
      $.ajax({
        url: "/customer/searchcustomer/" + req.term,
        success: function (response) {
          //   Below res is the one from line 2
          res(response);
        },
      });
    },
    minLength: 1,
    select: function (event, ui) {
      $(this).val(ui.item.name)
      return false;
    },
  })
  .autocomplete("instance")._renderItem = function (ul, item) {
  return $("<li>")
    .append("<div>" + item.name + "</div>")
    .appendTo(ul);
};


  /* CKEDIT */
  CKEDITOR.replace('message', {
    height: 300,
    baseFloatZIndex: 10005,
    removeButtons: 'PasteFromWord'
  });