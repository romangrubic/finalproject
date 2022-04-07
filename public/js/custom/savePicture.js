// Opens modal
$(".picture").click(function () {
  $("#image").attr("src", $(this).attr("src"));
  $("#pictureModal").foundation("open");
  defineCropper();

  return false;
});

// Saves changes done in modal
$("#save").click(function () {
  var size = { width: 350, height: 350 };
  var result = $image.cropper("getCroppedCanvas", size, size);

  $("#imageInput").attr("value", result.toDataURL());
  $("#photo").attr("src", result.toDataURL());
  $("#pictureModal").foundation("close");

  return false;
});

// CropperJS
var $image;

function defineCropper() {
  var URL = window.URL || window.webkitURL;
  $image = $("#image");
  var options = { aspectRatio: 1 / 1 };

  // Cropper
  $image.on({}).cropper(options);

  var uploadedImageURL;

  // Import image
  var $inputImage = $("#inputImage");

  if (URL) {
    $inputImage.change(function () {
      var files = this.files;
      var file;

      if (!$image.data("cropper")) {
        return;
      }

      if (files && files.length) {
        file = files[0];

        if (/^image\/\w+$/.test(file.type)) {
          if (uploadedImageURL) {
            URL.revokeObjectURL(uploadedImageURL);
          }

          uploadedImageURL = URL.createObjectURL(file);
          $image
            .cropper("destroy")
            .attr("src", uploadedImageURL)
            .cropper(options);
          $inputImage.val("");
        } else {
          window.alert("Datoteka nije u formatu slike");
        }
      }
    });
  } else {
    $inputImage.prop("disabled", true).parent().addClass("disabled");
  }
}
