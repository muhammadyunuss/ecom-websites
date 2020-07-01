$(document).ready(function(){
    // Cek Admin Password Benar atau Salah
    $("#current_pwd").keyup(function(){
        var current_pwd = $("#current_pwd").val();
        // alert(current_pwd);
        $.ajax({
            type:'post',
            url:'/admin/check-current-pwd',
            data:{current_pwd:current_pwd},
            success:function(resp){
                if(resp=="false"){
                    $("#chkCurrentPwd").html("<font color=red>Current Password is incorrect</font>");
                }else if(resp=="true"){
                    $("#chkCurrentPwd").html("<font color=green>Current Password is correct</font>");
                }
            },error:function(){
                alert("Error");
            }
        })
    });

    $(".updateSectionStatus").click(function(){
        var status =$(this).text();
        var section_id = $(this).attr("section_id");
        // alert(status);
        // alert(section_id);
        $.ajax({
            type:'post',
            url:'/admin/update-section-status',
            data:{status:status,section_id:section_id},
            success:function(resp){
                // alert(resp['status']);
                // alert(resp['section_id']);
                if(resp['status']==0){
                    $("#section-"+section_id).html("<i class='fas fa-times-circle' style='color:#ff6b6b;'></i><a class='updateSectionStatus' href='javascript:void(0)'> Inactive</a>");
                }else if(resp['status']==1){
                    $("#section-"+section_id).html("<i class='fas fa-check-circle' style='color:#51cf66;'></i><a class='updateSectionStatus' href='javascript:void(0)'> Active</a>");
                }
            },error:function(){
                alert("Error");
            }
        });
    });

    // Update Categories Status
    $(".updateCategoryStatus").click(function(){
        var status =$(this).text();
        var category_id = $(this).attr("category_id");
        // alert(status);
        // alert(category_id);
        $.ajax({
            type:'post',
            url:'/admin/update-category-status',
            data:{status:status,category_id:category_id},
            success:function(resp){
                // alert(resp['status']);
                // alert(resp['category_id']);
                if(resp['status']==0){
                    $("#category-"+category_id).html("<i class='fas fa-times-circle' style='color:#ff6b6b;'></i><a class='updateCategoryStatus' href='javascript:void(0)'> Inactive</a>");
                }else if(resp['status']==1){
                    $("#category-"+category_id).html("<i class='fas fa-check-circle' style='color:#51cf66;'></i><a class='updateCategoryStatus' href='javascript:void(0)'> Active</a>");
                }
            },error:function(){
                alert("Error");
            }
        });
    });

    // Append Categories Level
    $('#section_id').change(function(){
        var section_id = $(this).val();
        // alert(section_id);        
        $.ajax({
            type:'post',
            url:'/admin/append-categories-level',
            data:{section_id:section_id},
            success:function(resp){
                $("#appendCategoriesLevel").html(resp);
            },error:function(){
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

    $(".confirmDelete").click(function(){
        var record = $(this).attr('record');
        var recordid = $(this).attr('recordid');        
        Swal.fire({
            title: 'Apa anda yakin menghapus file?',
            text: "Anda tidak akan dapat mengembalikan file ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus File!'
          }).then((result) => {
            if (result.value) {
              Swal.fire(
                'Berhasil Dihapus!',
                'File Sudah Terhapus.',
                'success'
              )
              window.location.href="/admin/delete-"+record+"/"+recordid;
            }
          });
    });

    // Update Product Status
    $(".updateProductStatus").click(function(){
        var status =$(this).text();
        var product_id = $(this).attr("product_id");
        // alert(status);
        // alert(product_id);
        $.ajax({
            type:'post',
            url:'/admin/update-product-status',
            data:{status:status,product_id:product_id},
            success:function(resp){
                // alert(resp['status']);
                // alert(resp['product_id']);
                if(resp['status']==0){
                    $("#product-"+product_id).html("<i class='fas fa-times-circle' style='color:#ff6b6b;'></i><a class='updateProductStatus' href='javascript:void(0)'> Inactive</a>");
                }else if(resp['status']==1){
                    $("#product-"+product_id).html("<i class='fas fa-check-circle' style='color:#51cf66;'></i><a class='updateProductStatus' href='javascript:void(0)'> Active</a>");
                }
            },error:function(){
                alert("Error");
            }
        });
    });

});