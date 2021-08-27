$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#sort").on('change', function(){
        var sort = $(this).val();
        var fabric = get_filter('fabric');
        var sleeve = get_filter('sleeve');
        var pattern = get_filter('pattern');
        var fit = get_filter('fit');
        var occasion = get_filter('occasion');
        var url = $("#url").val();
        $.ajax({
            url:url,
            method:"post",
            data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
            success:function(data){
                $('.filter_products').html(data);
            }
        });
    });

    $(".fabric").on('click', function(){
        var fabric = get_filter('fabric');
        var sleeve = get_filter('sleeve');
        var pattern = get_filter('pattern');
        var fit = get_filter('fit');
        var occasion = get_filter('occasion');
        var sort = $("#sort option:selected").text();
        var url = $("#url").val();

        $.ajax({
            url:url,
            method:"post",
            data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
            success:function(data){
                $('.filter_products').html(data);
            }
        });

    })

    $(".sleeve").on('click', function(){
        var fabric = get_filter('fabric');
        var sleeve = get_filter('sleeve');
        var pattern = get_filter('pattern');
        var fit = get_filter('fit');
        var occasion = get_filter('occasion');
        var sort = $("#sort option:selected").text();
        var url = $("#url").val();

        $.ajax({
            url:url,
            method:"post",
            data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
            success:function(data){
                $('.filter_products').html(data);
            }
        });

    })

    $(".pattern").on('click', function(){
        var fabric = get_filter('fabric');
        var sleeve = get_filter('sleeve');
        var pattern = get_filter('pattern');
        var fit = get_filter('fit');
        var occasion = get_filter('occasion');
        var sort = $("#sort option:selected").text();
        var url = $("#url").val();

        $.ajax({
            url:url,
            method:"post",
            data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
            success:function(data){
                $('.filter_products').html(data);
            }
        });

    })

    $(".fit").on('click', function(){
        var fabric = get_filter('fabric');
        var sleeve = get_filter('sleeve');
        var pattern = get_filter('pattern');
        var fit = get_filter('fit');
        var occasion = get_filter('occasion');
        var sort = $("#sort option:selected").text();
        var url = $("#url").val();

        $.ajax({
            url:url,
            method:"post",
            data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
            success:function(data){
                $('.filter_products').html(data);
            }
        });

    })

    $(".occasion").on('click', function(){
        var fabric = get_filter('fabric');
        var sleeve = get_filter('sleeve');
        var pattern = get_filter('pattern');
        var fit = get_filter('fit');
        var occasion = get_filter('occasion');
        var sort = $("#sort option:selected").text();
        var url = $("#url").val();

        $.ajax({
            url:url,
            method:"post",
            data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
            success:function(data){
                $('.filter_products').html(data);
            }
        });

    })


    function get_filter (class_name){
        var filter = [];
        $('.'+class_name+':checked').each(function(){
            filter.push($(this).val());
        });
        return filter;
    }

    $("#getPrice").change(function(){
        var size = $(this).val();
        if(size==""){
            alert("Please select Size");
            return false;
        }
        var product_id = $(this).attr("product-id");
        $.ajax({
            url:'/get-product-price',
            data:{size:size,product_id:product_id},
            type:'post',
            success:function(resp){
                if(resp['discount']>0){
                    $(".getAttrPrice").html("<del>Rp."+resp['product_price']+"</del> Rp."+resp['final_price']);
                }else{
                    $(".getAttrPrice").html("Rp."+resp['product_price']);
                }
            },error:function(){
                alert("Error");
            }
        });
    });

    // Update Cart Items
    $(document).on('click','.btnItemUpdate',function(){
        if($(this).hasClass('qtyMinus')){
            var quantity = $(this).prev().val();
            // alert(quantity);
            // if qtyMinus button gets clicked by User
            if(quantity<=1){
                alert("Item quantity must be 1 or greater!");
                return false;
            }else{
                new_qty = parseInt(quantity)-1;
            }
        }

        if($(this).hasClass('qtyPlus')){
            var quantity = $(this).prev().prev().val();
            // alert(quantity);
            new_qty = parseInt(quantity)+1;

        }
        var cartid = $(this).data('cartid');
        $.ajax({
            data:{"cartid":cartid,"qty":new_qty},
            url:'/update-cart-item-qty',
            type:'post',
            success:function(resp){
                if(resp.status==false){
                    alert(resp.message);
                }
                $(".totalCartItems").html(resp.totalCartItems);
                $("#AppendCartItems").html(resp.view);
            },error:function(){
                alert("Error");
            }
        });
    });

     // Delete Cart Items
     $(document).on('click','.btnItemDelete',function(){
        var cartid = $(this).data('cartid');
        var result = confirm("Want to delete this Cart Item");
        if(result){
            $.ajax({
                data:{"cartid":cartid},
                url:'/delete-cart-item',
                type:'post',
                success:function(resp){
                    $(".totalCartItems").html(resp.totalCartItems);
                    $("#AppendCartItems").html(resp.view);
                },error:function(){
                    alert("Error");
                }
            });
        }
    });

    $("#registerForm").validate({
        rules: {
            name: "required",
            mobile: {
                required: true,
                minlength: 10,
                maxlength: 15,
                digits: true
            },
            email: {
                required: true,
                email: true,
                remote: "check-email"
            },
            password: {
                required: true,
                minlength: 6
            }
        },
        messages: {
            name: "Please enter your name",
            mobile: {
                required: "Please provide a password",
                minlength: "Your mobile must consist of 10 digits",
                maxlength: "Your mobile must consist of 15 digits",
                digits: "Please enter your valid Mobile",
            },
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 6 characters long"
            },
            email: {
                required: "Please enter you email",
                email: "Please enter a valid email address",
                remote: "Email already exists",
            }
        }
    });

    $("#loginForm").validate({
        rules: {
            email: {
                required: true,
                email: true,
            },
            password: {
                required: true,
                minlength: 6
            }
        },
        messages: {
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 6 characters long"
            },
            email: {
                required: "Please enter you email",
                email: "Please enter a valid email address",
            }
        }
    });

    $("#accountForm").validate({
        rules: {
            name: {
                required: true
            },
            mobile: {
                required: true,
                minlength: 10,
                maxlength: 15,
                digits: true
            }
        },
        messages: {
            name: {
                require: "Please enter your Name",
            },
            mobile: {
                required: "Please provide a password",
                minlength: "Your mobile must consist of 10 digits",
                maxlength: "Your mobile must consist of 15 digits",
                digits: "Please enter your valid Mobile",
            }
        }
    });

    $("#current_pwd").keyup(function(){
        var current_pwd = $(this).val();
        $.ajax({
            type:'post',
            url:"/check-user-pwd",
            data:{current_pwd:current_pwd},
            success:function(resp){
                if(resp=="false"){
                    $("#chkPwd").html("<font color='red'>Current Password is Incorrect</font>")
                }else if(resp=="true"){
                    $("#chkPwd").html("<font color='green'>Current Password is Correct</font>")
                }
            },error:function(){
                alert("Error");
            }
        });
    });

    $("#passwordForm").validate({
        rules: {
            current_pwd: {
                required: true,
                minlength: 6,
                maxlength: 20,
            },
            new_pwd: {
                required: true,
                minlength: 6,
                maxlength: 20,
            },
            confirm_pwd: {
                required: true,
                minlength: 6,
                maxlength: 20,
                equalTo:"#new_pwd"
            }
        }
    });

    // Apply Coupon
    $("#ApplyCoupon").submit(function(){
        var user = $(this).attr("user");
        if(user==1){
            // do nothing
        }else{
            alert("Please login to apply Coupon");
            return false;
        }

        var code = $("#code").val();
        // alert(code);
        $.ajax({
            type:'post',
            data:{code:code},
            url:'/apply-coupon',
            success:function(resp){
                if(resp.message!=""){
                    alert(resp.message);
                }
                $(".totalCartItems").html(resp.totalCartItems);
                $("#AppendCartItems").html(resp.view);

                if(resp.couponAmount>=0){
                    $(".couponAmount").text("Rp."+resp.couponAmount);
                }else{
                    $(".couponAmount").text("Rp.0");
                }

                if(resp.couponAmount>=0){
                    $(".grand_total").text("Rp."+resp.grand_total);
                }

            },error:function(){
                alert("Error");
            }
        })
    });

    // Delete Delivery Address
    $(document).on('click','.addressDelete', function(){
        var result = confirm("Want to delete this Address?");
        if(!result){
            return false;
        }
    });

    $("input[name=address_id]").bind('change',function(){
        var shipping_charges = $(this).attr("shipping_charges");
        var total_price = $(this).attr("total_price");
        var coupon_amount = $(this).attr("coupon_amount");
        var codpincodeCount = $(this).attr("codpincodeCount");
        var prepaidpincodeCount = $(this).attr("prepaidpincodeCount");

        if(codpincodeCount>0){
            $(".codMethod").show();
        }else{
            $(".codMethod").hide();
        }

        if(prepaidpincodeCount>0){
            $(".prepaidMethod").show();
        }else{
            $(".prepaidMethod").hide();
        }

        if(coupon_amount==""){
            coupon_amount = 0;
        }
        $(".shipping_charges").html("Rp."+shipping_charges);
        var grand_total =parseInt(total_price) + parseInt(shipping_charges) - parseInt(coupon_amount);
        $(".grand_total").html("Rp."+grand_total);
    });

    $("#checkPincode").click(function(){
        var pincode = $("#pincode").val();
        if(pincode==""){
            alert("Please enter delivery pincode"); return false;
        }
        $.ajax({
            type:'post',
            data:{pincode:pincode},
            url:'/check-pincode',
            success:function(resp){
                alert(resp);
            },error:function(){
                alert("Error");
            }
        });
    });

});
