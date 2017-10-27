$(document).ready(function () {
    $('.params-block input').each(function(i,elem) {
        var key = $(this).attr("id");
        if(localStorage.getItem(key)!=undefined)
        {
            $(this).val(localStorage.getItem(key));
        }
    });
});