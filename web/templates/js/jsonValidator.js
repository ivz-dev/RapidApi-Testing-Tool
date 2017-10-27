$(document).ready(function () {
    $("#textArea").on("input paste", function() {
        $(this).validateJSON({
            compress: false,
            reformat: false,
            onSuccess: function (json) {
                $("#validStatus").addClass("label-success");
                $("#validStatus").removeClass("label-danger");
                $("#validStatus").text("Valid JSON");
                $("#submit").css("display","block");
            },
            onError: function (error) {
                $("#validStatus").addClass("label-danger");
                $("#validStatus").removeClass("label-success");
                $("#validStatus").text("JSON Error");
                $("#submit").css("display","none");
            }
        });
    });
});
