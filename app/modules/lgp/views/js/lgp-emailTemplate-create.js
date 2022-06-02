var global_coderequest = '';
$(document).ready(function () {

    countdown.start(timesession);

    new gnMenu( document.getElementById( 'gn-menu' ) );

    $("#btnCancel").attr("href", path + '/lgp/lgpEmailTemplate/index');

    $('#description').summernote(
        {
            toolbar:[
                ["style",["style"]],
                ["font",["bold","italic","underline","clear"]],
                ["fontname",["fontname"]],["color",["color"]],
                ["para",["ul","ol","paragraph"]],
                ["table",["table"]],
                ["insert",["link"]],
                ["view",["codeview"]],
                ["help",["help"]]
            ],
            disableDragAndDrop: true,
            minHeight: null,  // set minimum height of editor
            maxHeight: 250,   // set maximum height of editor
            height: 250,      // set editor height
            //width: 750,       // set editor width
            focus: false,     // set focus to editable area after initializing summernote
            placeholder:  makeSmartyLabel('Editor_Placeholder_email_body')

        }
    );

    $("#btnSave").click(function(){
        if (!$("#create-emailtemplate-form").valid()) {
            return false ;
        }
        if(!$("#btnSave").hasClass('disabled')){ 
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpEmailTemplate/createEmailTemplate',
                dataType: 'json',
                data: $("#create-emailtemplate-form").serialize() + '&description=' + $('#description').summernote('code').replace(/&nbsp;/g, ""),
                error: function (ret) { 
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-emailtemplate-create');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/lgp/lgpEmailTemplate/index');                        
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-emailtemplate-create');
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

    
   
    $("#btnUpdate").click(function(){
        if ($("#update-emailtemplate-form").valid()) {
            
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpEmailTemplate/updateEmailTemplate',
                dataType: 'json',
                data: $("#update-emailtemplate-form").serialize() + '&description=' + $('#description').summernote('code').replace(/&nbsp;/g, ""),
                error: function (ret) { 
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-emailtemplate-update');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/lgp/lgpEmailTemplate/index');                        
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-emailtemplate-update');
                    }
                },
                beforeSend: function(){
                    $("#btnUpdate").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdate").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }        
    });
/*
     * Validate
     */
    $("#create-emailtemplate-form").validate({
        ignore:[],
        rules: {
            emailTemplateName:  {
                required:true,
                remote:{
                    url: path+"/lgp/lgpEmailTemplate/existEmailTemplate",
                    type: 'post',
                    dataType:'json',
                    async: false,
                },
                minlength: '3'
            },            
        },
    
        messages: {
            emailTemplateName: {required:makeSmartyLabel('Alert_field_required'), 
            minlength:makeSmartyLabel('Alert_minlength')},
           
        }
    });

    $("#update-emailtemplate-form").validate({
        ignore:[],
        rules: {
            emailTemplateName:  {
                required:true,
                remote:{
                    url: path+"/lgp/lgpEmailTemplate/existEmailTemplate",
                    type: 'post',
                    dataType:'json',
                    async: false,
                   data:{emailTemplateID:$('#emailTemplateID').val()}
                },
                minlength: '3'
            },            
        },
    
        messages: {
            emailTemplateName: {required:makeSmartyLabel('Alert_field_required'), 
            minlength:makeSmartyLabel('Alert_minlength')},
           
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


