var productId;
var imageValue;

$(".picture").click(function(){
    productId=$(this).attr("id").split("_")[1];
      $("#image").attr("src",$(this).attr("src"));
      $("#pictureModal").foundation("open");
      defineCropper();

      return false;
  });

  $("#save").click(function(){
    var opcije = { "width": 350, "height": 350 };
    var result = $image.cropper("getCroppedCanvas", opcije, opcije);
    imageValue= result.toDataURL();

    //ako želimo jpg https://github.com/fengyuanchen/cropperjs

    $.ajax({
        type: "POST",
        url:  "/product/savePicture",
        data: "id=" + productId + "&image=" + result.toDataURL(),
        success: function(vratioServer){
          if(vratioServer==="OK"){
        // $("#imageInput").attr("value",result.toDataURL());
            $("#p_"+productId).attr("src",result.toDataURL());
            $("#pictureModal").foundation("close");
          }else{
            alert(vratioServer);
          }
        }
      });


    return false;
  });

//   $("#submitForm").click(function(){
//     $.ajax({
//             type: "POST",
//             url:  "/product/savePicture",
//             data: "id=" + productId + "&image=" + imageValue,
//   });
// })



  var $image;

  function defineCropper(){


    var URL = window.URL || window.webkitURL;
    $image = $('#image');
    var options = {aspectRatio: 1 / 1 };

    // Cropper
    $image.on({}).cropper(options);

    var uploadedImageURL;


    // Import image
    var $inputImage = $('#inputImage');

    if (URL) {
      $inputImage.change(function () {
        var files = this.files;
        var file;

        if (!$image.data('cropper')) {
          return;
        }

        if (files && files.length) {
          file = files[0];

          if (/^image\/\w+$/.test(file.type)) {


            if (uploadedImageURL) {
              URL.revokeObjectURL(uploadedImageURL);
            }

            uploadedImageURL = URL.createObjectURL(file);
            $image.cropper('destroy').attr('src', uploadedImageURL).cropper(options);
            $inputImage.val('');
          } else {
            window.alert('Datoteka nije u formatu slike');
          }
        }
      });
    } else {
      $inputImage.prop('disabled', true).parent().addClass('disabled');
    }

    } 