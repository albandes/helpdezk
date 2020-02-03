var global_idproduto = '';
$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /*
     *  Chosen
     */
    $("#cmbCompany").chosen({ width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/helpdezk/hdkDepartment/index');

    $("#btnCreateDepartment").click(function(e){

        e.preventDefault();
        if (!$("#create-department-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkDepartment/createDepartment',
            dataType: 'json',
            data: $("#create-department-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-department');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == "Ok") {
                    $('#modal-notification').html(makeSmartyLabel('Alert_inserted'));
                    $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkDepartment/index');
                    $("#tipo-alert").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');
                } else {
                    if(obj.message){
                        modalAlertMultiple('danger',obj.message,'alert-create-department');
                    }else{
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-department');
                    }

                }
            },
            beforeSend: function(){
                $("#btnCancel").addClass('disabled').prop('disabled',true);
                $("#btnCreateDepartment").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled').prop('disabled',true);
            },
            complete: function(){
                $("#btnCancel").removeClass('disabled').prop('disabled',false);
                $("#btnCreateDepartment").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled').prop('disabled',false);
            }
        });

        return false ;
    });

    $("#btnUpdateDepartment").click(function(e){

        e.preventDefault();
        if (!$("#update-department-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkDepartment/updateDepartment',
            dataType: 'json',
            data: $("#update-department-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-update-department');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'Ok' ) {
                    $('#modal-notification').html(makeSmartyLabel('Edit_sucess'));
                    $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkDepartment/index');
                    $("#tipo-alert").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {
                    if(obj.message){
                        modalAlertMultiple('danger',obj.message,'alert-update-department');
                    }else{
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-update-department');
                    }


                }

            },
            beforeSend: function(){
                $("#btnCancel").addClass('disabled');
                $("#btnUpdateDepartment").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled').prop('disabled',true);
            },
            complete: function(){
                $("#btnCancel").removeClass('disabled').prop('disabled',false);
                $("#btnUpdateDepartment").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled').prop('disabled',false);
            }

        });


    });

    /*
     * Validate
     */
    $("#create-department-form").validate({
        ignore:[],
        rules: {
            cmbCompany: "required",
            txtDepartment:  {
                required:true,
                remote:{
                    url: path+"/helpdezk/hdkDepartment/checkDepartment",
                    type: 'post',
                    data: {
                        companyId:function(){return $('#cmbCompany').val();}
                    }
                }
            }
        },
        messages: {
            cmbCompany: makeSmartyLabel('Alert_field_required'),
            txtDepartment:  {required:makeSmartyLabel('Alert_field_required')}
        }
    });

    $("#update-department-form").validate({
        ignore:[],
        rules: {
            cmbCompany: "required",
            txtDepartment:  {
                required:true,
                remote:{
                    url: path+"/helpdezk/hdkDepartment/checkDepartment",
                    type: 'post',
                    data: {
                        companyId:function(){return $('#idcompany').val();},
                        departmentId:function(){return $('#iddepartment').val();}
                    }
                }
            }
        },
        messages: {
            cmbCompany: makeSmartyLabel('Alert_field_required'),
            txtDepartment:  {required:makeSmartyLabel('Alert_field_required')}
        }
    });

});

