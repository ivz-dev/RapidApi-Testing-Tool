$(document).ready(function () {

   $(".vendor-block .headers-button").click(function () {
       $(".vendor-block  .headers-block").toggle();
       if($(".vendor-block  .headers-button i").attr("class") == "fa fa-angle-down"){
           $(".vendor-block  .headers-button i").attr("class", "fa fa-angle-up");
       } else {
           $(".vendor-block  .headers-button i").attr("class", "fa fa-angle-down");
       }
   });

    $(".rapid-block .headers-button").click(function () {
        $(".rapid-block  .headers-block").toggle();
        if($(".rapid-block  .headers-button i").attr("class") == "fa fa-angle-down"){
            $(".rapid-block  .headers-button i").attr("class", "fa fa-angle-up");
        } else {
            $(".rapid-block  .headers-button i").attr("class", "fa fa-angle-down");
        }
    });

    $(".vendor-block .request-tab").click(function () {
        $(this).addClass("active");
        $(".vendor-block .response-tab").removeClass("active");
        $(".vendor-request-content").css("display", "block");
        $(".vendor-response-content").css("display", "none");
    });

    $(".vendor-block .response-tab").click(function () {
        $(this).addClass("active");
        $(".vendor-block .request-tab").removeClass("active");
        $(".vendor-request-content").css("display", "none");
        $(".vendor-response-content").css("display", "block");
    });

    $(".rapid-block .request-tab").click(function () {
        $(this).addClass("active");
        $(".rapid-block .response-tab").removeClass("active");
        $(".rapid-request-content").css("display", "block");
        $(".rapid-response-content").css("display", "none");
    });

    $(".rapid-block .response-tab").click(function () {
        $(this).addClass("active");
        $(".rapid-block .request-tab").removeClass("active");
        $(".rapid-request-content").css("display", "none");
        $(".rapid-response-content").css("display", "block");
    });


    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    $(".run-button").click(function () {
        vendorRequestFunction();
        rapidRequestFunction();

        $('.params-block .form-control').each(function(i,elem) {
            if($(this).val()!='' && $(this).attr("id"))
            {
                var value = $(this).val();
                if(isJson($(this).val())){
                    console.log($(this).val());
                    value = JSON.parse($(this).val());
                }

                localStorage.setItem($(this).attr("id"), value);
            }
        });

        var text1 = $(".rapid-response-content .prettyprint").text();
        var text2 = $(".vendor-response-content .prettyprint").text();
        $(".rapid-response-content .prettyprint").html(textDiff(text1, text2).text1);
        $(".vendor-response-content .prettyprint").html(textDiff(text1, text2).text2);
    });

    function vendorRequestFunction()
    {
        $.ajax({
            url:"/ajax",
            data: {
                url: "https://rapid-run-tools.herokuapp.com/vendor",
                params: $("#vendorRequestParams").val(),
                request: $("#vendorRequestBody").val()
            },
            type:"POST",
            beforeSend:function(){
                $('.vendor-block .preloader').css('display','block');
            },
            complete:function(){
                $('.vendor-block .preloader').css('display','none');
            },
            success: function (data) {
                var vendorRequest = JSON.stringify(JSON.parse(data.replace(/&quot;/g,'"')).contextWrites.to,null,2);
                var vendorStatus = JSON.parse(data.replace(/&quot;/g,'"')).status;
                var vendorMethod = JSON.parse($("#vendorRequestBody").val().replace(/&quot;/g,'"')).method;


                $(".vendor-response-content .prettyprint").text(vendorRequest);
                $(".vendor-response-content .status").text(vendorStatus);
                $(".vendor-response-content .method").text(vendorMethod);
                $(".vendor-response-content .method").addClass(vendorMethod);
                $(".vendor-block .response-tab").trigger('click');
                $(".vendor-block .response-tab").css("display","block");


                if($(".rapid-response-content .prettyprint").text()!=''){
                    var text1 = $(".rapid-response-content .prettyprint").text();
                    var text2 = $(".vendor-response-content .prettyprint").text();
                    $(".rapid-response-content .prettyprint").html(textDiff(text1, text2).text1);
                    $(".vendor-response-content .prettyprint").html(textDiff(text1, text2).text2);
                }
            }
        });
    }

    function rapidRequestFunction()
    {
        $.ajax({
            url:"/ajax",
            data: {
                url: "https://rapid-run-tools.herokuapp.com/rapid",
                params: $("#rapidRequestParams").val(),
                request: $("#rapidRequestBody").val()
            },
            type:"POST",
            beforeSend:function(){
                $('.rapid-block .preloader').css('display','block');
            },
            complete:function(){
                $('.rapid-block .preloader').css('display','none');
            },
            success: function (data) {
                var rapidRequest = JSON.stringify(JSON.parse(data.replace(/&quot;/g,'"')).content.payload,null,2);
                var rapidStatus = JSON.parse(data.replace(/&quot;/g,'"')).status;
                var rapidMethod = JSON.parse($("#rapidRequestBody").val().replace(/&quot;/g,'"')).method;
                $(".rapid-response-content .prettyprint").text(rapidRequest);
                $(".rapid-response-content .status").text(rapidStatus);
                $(".rapid-response-content .method").text(rapidMethod);
                $(".rapid-response-content .method").addClass(rapidMethod);
                $(".rapid-block .response-tab").trigger('click');
                $(".rapid-block .response-tab").css("display","block");

                if($(".vendor-response-content .prettyprint").text()!=''){
                    var text1 = $(".rapid-response-content .prettyprint").text();
                    var text2 = $(".vendor-response-content .prettyprint").text();
                    $(".rapid-response-content .prettyprint").html(textDiff(text1, text2).text1);
                    $(".vendor-response-content .prettyprint").html(textDiff(text1, text2).text2);
                }
            }
        });
    }

    $("#createParamsObj").click(function () {
        var vendorParam = {};
        var rapidParam = {};
        var rapVendObj = JSON.parse(rapidVendorField.replace(/&quot;/g,'"'));

        $('.params-block .list').each(function(i,elem) {
            var valueString = "";
            var valueArray = [];
            var paramName = $(this).attr("name");
            var listItems = $(this).find(".list-items");
            listItems.find('input').each(function (i, elem) {
                valueArray.push('"'+$(this).val()+'"');
            });
            $("input#"+paramName).val('['+valueArray.join()+']');
        });


        var vendorParamJSON = JSON.stringify(vendorParam,null,2);
        $("#vendorRequestParams").val(vendorParamJSON);

        var vendorRequestJSON = JSON.stringify(JSON.parse(vendorRequest.replace(/&quot;/g,'"')),null,2);
        $("#vendorRequestBody").val(vendorRequestJSON);

        var rapidParamJSON = JSON.stringify(rapidParam,null,2);
        $("#rapidRequestParams").val(rapidParamJSON);

        var rapidRequestJSON = JSON.stringify(JSON.parse(rapidRequest.replace(/&quot;/g,'"')),null,2);
        $("#rapidRequestBody").val(rapidRequestJSON);
    });

    function textDiff(text1, text2) {
        var obj1 = text1.split("\n");
        var obj2 = text2.split("\n");

        var validText1 = text1.split("\n");
        var validText2 = text2.split("\n");

        for(var i=0; i<obj1.length; i++){
            if(obj2[i]!=undefined){
                if(obj1[i] != obj2[i]){
                    validText1[i] = "<span class='diff'>" + obj1[i] + "</span>";
                } else {
                    validText1[i] = "<span>" + obj1[i] + "</span>";
                }
            } else{
                validText1[i] = "<span class='diff'>" + obj1[i] + "</span>";
            }
        }

        for(var i=0; i<obj2.length; i++){
            if(obj1[i]!=undefined){
                if(obj1[i] != obj2[i]){
                    validText2[i] = "<span class='diff'>" + obj2[i] + "</span>";
                } else {
                    validText2[i] = "<span>" + obj2[i] + "</span>";
                }
            } else{
                validText2[i] = "<span class='diff'>" + obj2[i] + "</span>";
            }
        }
        var diff = {};

        diff['text1'] = validText1.join("\n");
        diff['text2']  = validText2.join("\n");
        return diff;
    }

    $(".add-button").click(function () {
        var className = $(this).parent().attr("id");
        $(this).parent().find(".list-items").append('<input type="text" class="form-control '+className +'">');
    })
});
