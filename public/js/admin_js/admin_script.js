$(document).ready(function () {
    // Cek Admin Password Benar atau Salah
    $("#current_pwd").keyup(function () {
        var current_pwd = $("#current_pwd").val();
        // alert(current_pwd);
        $.ajax({
            type: 'post',
            url: '/admin/check-current-pwd',
            data: {
                current_pwd: current_pwd
            },
            success: function (resp) {
                if (resp == "false") {
                    $("#chkCurrentPwd").html("<font color=red>Current Password is incorrect</font>");
                } else if (resp == "true") {
                    $("#chkCurrentPwd").html("<font color=green>Current Password is correct</font>");
                }
            },
            error: function () {
                alert("Error");
            }
        })
    });

    $(".updateSectionStatus").click(function () {
        var status = $(this).text();
        var section_id = $(this).attr("section_id");
        // alert(status);
        // alert(section_id);
        $.ajax({
            type: 'post',
            url: '/admin/update-section-status',
            data: {
                status: status,
                section_id: section_id
            },
            success: function (resp) {
                // alert(resp['status']);
                // alert(resp['section_id']);
                if (resp['status'] == 0) {
                    $("#section-" + section_id).html("<i class='fas fa-times-circle' style='color:#ff6b6b;'></i><a class='updateSectionStatus' href='javascript:void(0)'> Inactive</a>");
                } else if (resp['status'] == 1) {
                    $("#section-" + section_id).html("<i class='fas fa-check-circle' style='color:#51cf66;'></i><a class='updateSectionStatus' href='javascript:void(0)'> Active</a>");
                }
            },
            error: function () {
                alert("Error");
            }
        });
    });

    $(".updateBrandStatus").click(function () {
        var status = $(this).children("i").attr("status");
        var brand_id = $(this).attr("brand_id");
        // alert(status);
        // alert(brand_id);
        $.ajax({
            type: 'post',
            url: '/admin/update-brand-status',
            data: {
                status: status,
                brand_id: brand_id
            },
            success: function (resp) {
                // alert(resp['status']);
                // alert(resp['brand_id']);
                if (resp['status'] == 0) {
                    $("#brand-" + brand_id).html("<i class='fas fa-toggle-off' aria-hidden='true' style='color:#ff6b6b;' status='Inactive'></i>");
                } else if (resp['status'] == 1) {
                    $("#brand-" + brand_id).html("<i class='fas fa-toggle-on' aria-hidden='true' style='color:#51cf66;' status='Active'></i>");
                }
            },
            error: function () {
                alert("Error");
            }
        });
    });

    // Update Categories Status
    $(".updateCategoryStatus").click(function () {
        var status = $(this).text();
        var category_id = $(this).attr("category_id");
        // alert(status);
        // alert(category_id);
        $.ajax({
            type: 'post',
            url: '/admin/update-category-status',
            data: {
                status: status,
                category_id: category_id
            },
            success: function (resp) {
                // alert(resp['status']);
                // alert(resp['category_id']);
                if (resp['status'] == 0) {
                    $("#category-" + category_id).html("<i class='fas fa-times-circle' style='color:#ff6b6b;'></i><a class='updateCategoryStatus' href='javascript:void(0)'> Inactive</a>");
                } else if (resp['status'] == 1) {
                    $("#category-" + category_id).html("<i class='fas fa-check-circle' style='color:#51cf66;'></i><a class='updateCategoryStatus' href='javascript:void(0)'> Active</a>");
                }
            },
            error: function () {
                alert("Error");
            }
        });
    });

    // Append Categories Level
    $('#section_id').change(function () {
        var section_id = $(this).val();
        // alert(section_id);
        $.ajax({
            type: 'post',
            url: '/admin/append-categories-level',
            data: {
                section_id: section_id
            },
            success: function (resp) {
                $("#appendCategoriesLevel").html(resp);
            },
            error: function () {
                alert("Error");
            }
        });
    });

    // Confirm Deletion of Record
    // $(".confirmDelete").click(function(){
    //     var name = $(this).attr('name');
    //     if(confirm("Apakah Kamu Yakin Menhapus Item "+name+"?")){
    //         return true;
    //     }
    //     return false;
    // });

    $(".confirmDelete").click(function () {
        var record = $(this).attr('record');
        var recordid = $(this).attr('recordid');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                Swal.fire(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                )
                window.location.href = "/admin/delete-" + record + "/" + recordid;
            }
        });
    });

    // Update Product Status
    $(".updateProductStatus").click(function () {
        var status = $(this).text();
        var product_id = $(this).attr("product_id");
        // alert(status);
        // alert(product_id);
        $.ajax({
            type: 'post',
            url: '/admin/update-product-status',
            data: {
                status: status,
                product_id: product_id
            },
            success: function (resp) {
                // alert(resp['status']);
                // alert(resp['product_id']);
                if (resp['status'] == 0) {
                    $("#product-" + product_id).html("<i class='fas fa-times-circle' style='color:#ff6b6b;'></i><a class='updateProductStatus' href='javascript:void(0)'> Inactive</a>");
                } else if (resp['status'] == 1) {
                    $("#product-" + product_id).html("<i class='fas fa-check-circle' style='color:#51cf66;'></i><a class='updateProductStatus' href='javascript:void(0)'> Active</a>");
                }
            },
            error: function () {
                alert("Error");
            }
        });
    });

    // Update Attribute Status
    $(".updateAttributeStatus").click(function () {
        var status = $(this).text();
        var attribute_id = $(this).attr("attribute_id");
        // alert(status);
        // alert(attribute_id);
        $.ajax({
            type: 'post',
            url: '/admin/update-attribute-status',
            data: {
                status: status,
                attribute_id: attribute_id
            },
            success: function (resp) {
                // alert(resp['status']);
                // alert(resp['attribute_id']);
                if (resp['status'] == 0) {
                    $("#attribute-" + attribute_id).html("Inactive");
                } else if (resp['status'] == 1) {
                    $("#attribute-" + attribute_id).html("Active");
                }
            },
            error: function () {
                alert("Error");
            }
        });
    });

    // Update Attribute Status
    $(".updateImageStatus").click(function () {
        var status = $(this).text();
        var image_id = $(this).attr("image_id");
        // alert(status);
        // alert(image_id);
        $.ajax({
            type: 'post',
            url: '/admin/update-image-status',
            data: {
                status: status,
                image_id: image_id
            },
            success: function (resp) {
                // alert(resp['status']);
                // alert(resp['image_id']);
                if (resp['status'] == 0) {
                    $("#image-" + image_id).html("Inactive");
                } else if (resp['status'] == 1) {
                    $("#image-" + image_id).html("Active");
                }
            },
            error: function () {
                alert("Error");
            }
        });
    });

    // Products Attributes Add/Remove Script
    $(document).ready(function () {
        var maxField = 10; //Input fields increment limitation
        var addButton = $('.add_button'); //Add button selector
        var wrapper = $('.field_wrapper'); //Input field wrapper
        var fieldHTML = '<div><div style="height:10px;"></div><input id="size" name="size[]" type="text" placeholder="Size" style="width: 120px;"/><input id="sku" name="sku[]" type="text" placeholder="SKU" style="width: 120px;"/><input id="price" name="price[]" type="text" placeholder="Price" style="width: 120px;"/><input id="stock" name="stock[]" type="text" placeholder="Stock" style="width: 120px;"/><a href="javascript:void(0);" class="remove_button">&nbsp&nbsp<i class="fas fa-times"></i></a></div>'; //New input field html
        var x = 1; //Initial field counter is 1

        //Once add button is clicked
        $(addButton).click(function () {
            //Check maximum number of input fields
            if (x < maxField) {
                x++; //Increment field counter
                $(wrapper).append(fieldHTML); //Add field html
            }
        });

        //Once remove button is clicked
        $(wrapper).on('click', '.remove_button', function (e) {
            e.preventDefault();
            $(this).parent('div').remove(); //Remove field html
            x--; //Decrement field counter
        });
    });

});
