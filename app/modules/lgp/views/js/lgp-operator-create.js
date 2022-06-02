var global_newsID = '', dropzonefiles = 0,filesended = 0, flgerror = 0, errorname=[];

$(document).ready(function () {

    countdown.start(timesession);

    /*
     * Demo version
     */
    if (demoVersion == 1){
        if ($('#logindemo').val() == 'user' || $('#logindemo').val() == 'operator' ) {
            $('#btnUpdatePerson').prop('disabled', true);
        }
    }

    new gnMenu( document.getElementById( 'gn-menu' ) );

    if(access[1] != "Y"){
        $("#btnCreateRequest").addClass('hide');
    }

    if(access[2] != "Y"){
        $("#btnUpdateRequest").addClass('hide');
    }

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /*
     * Mask
     */
    
    $('#operatorPhone').mask('(00) 0000-0000');
    $('#operatorMobile').mask('(00) 00000-0000');        

    /*
     *  Chosen
     */
    $("#cmbUF").chosen({width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    /*
     * Datepicker
     */
    $('.input-group.date').datepicker(datepickerOpts);

    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/lgp/lgpOperator/index');
    

    $("#btnSave").click(function(){
        if (!$("#create-operator-form").valid()) {
           // console.log('teste');
            return false ;
        }
        if(!$("#btnSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpOperator/createOperator',
                dataType: 'json',
                data: $("#create-operator-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-operator-create');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/lgp/lgpOperator/index');                        
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-operator-create');
                    }
                },
                beforeSend: function(){
                    $("#btnSave").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnSave").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }
        
    });
    
    

    $("#btnSaveUpdate").click(function(){
        if (!$("#update-operator-form").valid()) {
            return false ;
        }

        if(!$("#btnSaveUpdate").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpOperator/updateOperator',
                dataType: 'json',
                data: $("#update-operator-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-operator-update');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/lgp/lgpOperator/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-operator-update');
                    }
                },
                beforeSend: function(){
                    $("#btnSaveUpdate").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnSaveUpdate").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }       
    });

   
    
    /*
     * Validate
     */
    $("#create-operator-form").validate({
        ignore:[],
        rules: {
            operatorName:  {
                required:true,
                remote:{
                    url: path+"/lgp/lgpOperator/existOperator",
                    type: 'post',
                    dataType:'json',
                    async: false,
                },
                minlength: '3',
            },
                operatorPhone:{required: true, minlength: '10'},
                operatorMobile:{required: true, minlength: '11'},          
                operatorContact:{
                    required: function(element) {
                      return $("input:radio[name='categoryOperator']:checked").val() == '2';
                    },
                    minlength: '3'
                }

             },        
        messages: {
            operatorName: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minlength')},
            operatorPhone: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minlengthPhone')},
            operatorMobile: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minlengthMobile')},
            operatorContact: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minlength')}
        }
    
    });

    $("#update-operator-form").validate({
        ignore:[],
        rules: {
            operatorName:  {
                required:true,
                remote:{
                    url: path+"/lgp/lgpOperator/existOperator",
                    type: 'post',
                    dataType:'json',
                    async: false,
                   data:{operatorID:$('#operatorID').val()}
                },
                minlength: '3'
            },
            operatorPhone:{required: true, minlength: '10'},
                operatorMobile:{required: true, minlength: '11'},              
            operatorContact:{required:function(element){return $('#categoryOperator').val() == '2';}
                },
                minlength: '3'
            },
    
        messages: {
            operatorName: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minlength')},
            operatorPhone: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minlengthPhone')},
            operatorMobile: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minlengthMobile')},
            operatorContact: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minlength')}
        }
    });

    $("input[name='categoryOperator']").on('ifClicked', function() { // bind a function to the change event
        var typeFlg = $(this).val();
        $('#btnCreate').removeClass('hide');

        if(typeFlg == '2'){
            $('.juridicalView').removeClass('hide');
            $('.commonView').removeClass('hide');
            $('.naturalView').addClass('hide');
            $(".userView").addClass('hide');
            $(".operatorView").addClass('hide');
        }else{
            $('.juridicalView').addClass('hide');
            $('.commonView').removeClass('hide');
            $('.naturalView').removeClass('hide');
        }
    });
});

/*function showAlert(msg,typeAlert,btnOk)
{
    $('#modal-notification').html(msg);
    $("#btn-modal-ok").attr("href", btnOk);
    $("#tipo-alert").attr('class', 'alert alert-'+typeAlert);
    $('#modal-alert').modal('show');

    return false;
}*/


