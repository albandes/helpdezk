var global_idspool, all_ative = false, all_recip = false, dropzonefiles = 0, filesended = 0, flgerror = 0, errorname=[], upname=[];
$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    var objEmailData = {
        changeSections: function(idtypesend,operation) {

            if(operation === 'append'){
                var token = $("#_token").val();

                $.post(path + '/emq/emqEmail/sectionsList',
                    {idtypesend: idtypesend,_token: token},
                    function(valor){
                        var obj = jQuery.parseJSON(JSON.stringify(valor));

                        if(obj.tabList && obj.tabList != ""){
                            $("#bodySections").append(obj.tabList);
                            $(".sectionsLine").removeClass('hide');
                        }
                    },'json');

            }else{
                var name2remove = '.section_'+idtypesend;
                $("#bodySections "+name2remove).remove();

                if($("#bodySections tr").length == 0){
                    $(".sectionsLine").addClass('hide');
                    all_ative = false;
                    $("#btnSelAllSections").html(makeSmartyLabel('emq_select_all'));
                }
                return viewRecipients();
            }

        },
        viewRecipients: function() {
            var sectionId = $("input[name=section]:checked"), arrSection = [];

            sectionId.each(function(){
                arrSection.push($(this).val());
            });

            console.log(arrSection);

            /*$.post(path + '/emq/emqEmail/recipientsList',
                {idtypesend: idtypesend,_token: token},
                function(valor){
                    var obj = jQuery.parseJSON(JSON.stringify(valor));

                    if(obj.tabList && obj.tabList != ""){
                        $("#bodySections").append(obj.tabList);
                    }
                },'json');*/

        }
    }


    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /*
     *  Chosen
     */
    $("#cmbTypeSend").chosen({  width: "100%",    no_results_text: makeSmartyLabel('API_No_result'),    disable_search_threshold: 10});
    $("#cmbNetUser").chosen({   width: "100%",    no_results_text: makeSmartyLabel('API_No_result'),    disable_search_threshold: 10});
    $("#cmbUpBand").chosen({    width: "100%",    no_results_text: makeSmartyLabel('API_No_result'),    disable_search_threshold: 10});
    $("#cmbDownBand").chosen({  width: "100%",    no_results_text: makeSmartyLabel('API_No_result'),    disable_search_threshold: 10});


    $(".checkSend").on('ifChecked ifUnchecked',function(e){
        var idtypesend = e.target.attributes.value.nodeValue, op = '';

        if(e.type == 'ifChecked'){
            op = 'append';
        }else{
            op = 'remove';
        }
        objEmailData.changeSections(idtypesend,op);
    });


    $("#cmbTypeSend").change(function(){
        objEmailData.changeSections();
    });

    /*
     * Datepicker
     */
    $('.input-group.date').datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR",
        autoclose: true,
        startDate: '0'
    });

    /*
     * Clockpicker
     */
    $('.clockpicker').clockpicker({
        autoclose: true,
        default: 'now',
        fromnow: 6000
    });

    /*
     * Dropzone for files upload
     */
    Dropzone.autoDiscover = false;
    
    var myDropzone = new Dropzone("#myDropzone", {
        url: path + "/emq/emqEmail/saveAttachments/",
        method: "post",
        dictDefaultMessage: "<br><i class='fa fa-file fa-2x' aria-hidden='true'></i><br>" + makeSmartyLabel('Tckt_drop_file'),
        createImageThumbnails: true,
        maxFiles: noteAttMaxFiles,
        maxFilesize: hdkMaxSize,
        acceptedFiles: noteAcceptedFiles,
        parallelUploads: ticketAttMaxFiles,                         // https://github.com/enyo/dropzone/issues/253
        autoProcessQueue: false,
        dictFileTooBig: makeSmartyLabel('hdk_exceed_max_file_size'),
        addRemoveLinks:true,
        dictRemoveFile: makeSmartyLabel('hdk_remove_file')
    });

    myDropzone.on("maxfilesexceeded", function(file) {
        this.removeFile(file);
    });

    myDropzone.on("complete", function(file) {
        
        if(file.status === "canceled" || file.status === "error"){
            errorname.push(file.name);
            flgerror = 1;
        }else if((file.xhr)){
            var obj = JSON.parse(file.xhr.response);
        
            if(obj.success) {
                filesended = filesended + 1;
                upname.push(file.name);
            } else {
                errorname.push(file.name);
                flgerror = 1;
            }
        }
        
    });

    myDropzone.on("queuecomplete", function (file) {
        var msg,typeMsg;

        if(errorname.length == 0 && (filesended == dropzonefiles)){
            saveEmail(upname,myDropzone);                           
        }else{
            modalAlertMultiple("danger",makeSmartyLabel('EMQ_FILE_UPL_ERROR'),'alert-email');
            setTimeout(function(){
                $('#email-form').trigger('reset');
                // clear summernote and dropzone
                $('#emailMessage').summernote('code','');
                myDropzone.removeAllFiles(true);
                location.href = path + "/emq/emqEmail/index" ;
                $('.dz-remove').remove();
            },3000);
        }        
        
        dropzonefiles = 0; 
        filesended = 0;
        flgerror = 0;
    });
 
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/emq/emqEmail/index');

    $("#btnCreateMsg").click(function(){
        var sectionId = $("input[name=recipient]"), recipNotEmpty = 0;

        if($("#btnSaveMsg").hasClass('disabled')){
            $("#btnSaveMsg").removeClass('disabled');
        }

        sectionId.each(function(){
            if(this.checked){
                recipNotEmpty = recipNotEmpty + 1;
            }
            
        });

        if(recipNotEmpty > 0){
            $("#modal-form-email").modal('show');
        }else{
            modalAlertMultiple('danger',makeSmartyLabel('emq_alert_no_recipient'),'alert-send-email');
            return false;
        }

        
    });

    $("#btnSaveMsg").click(function(){

        if (!$("#email-form").valid()) {
            return false ;
        }
        
        if ($('#emailMessage').summernote('isEmpty')) {
            modalAlertMultiple('danger',makeSmartyLabel('emq_alert_empty_body'),'alert-email');
            return false;
        }

        if(!$("#btnSaveMsg").hasClass('disabled')){
            $("#btnSaveMsg").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            $("#btnCancel").addClass('disabled');
            
            if (myDropzone.getQueuedFiles().length > 0) {
                dropzonefiles = myDropzone.getQueuedFiles().length;
                myDropzone.processQueue();
            } else {
                saveEmail(upname,myDropzone);
            }
        }

    });

    $("#btnSelAllSections").click(function(){
        var sectionId = $("input[name=section]"), check, msg;

        if(all_ative){check = false; all_ative = false; msg = makeSmartyLabel('emq_select_all');}
        else{check = true; all_ative = true; msg = makeSmartyLabel('emq_unselect_all');}

        sectionId.each(function(){
            this.checked = check;
        });
        viewRecipients();
        $("#btnSelAllSections").html(msg);

    });

    $("#btnSelAllRecip").click(function(){
        var recipId = $("input[name=recipient]"), check, msg;

        if(all_recip){check = false; all_recip = false; msg = makeSmartyLabel('emq_select_all');}
        else{check = true; all_recip = true; msg = makeSmartyLabel('emq_unselect_all');}

        recipId.each(function(){
            this.checked = check;
        });

        $("#btnSelAllRecip").html(msg);

    });

    /*
     * Validate
     */
    $("#email-form").validate({
        ignore:[],
        rules: {
            emailtitle: {
                required: true,
                maxlength: 100
            }
        },
        messages: {
            emailtitle: {
                required: makeSmartyLabel('Alert_field_required'),
                maxlength: makeSmartyLabel('Alert_field_exceded_maxlength') + $('#emailtitle').attr('maxlength')
            }
        }
    });

    $('.tooltip-buttons').tooltip();

    $('#emailMessage').summernote(
        {
            toolbar:[
                ["style",["style"]],
                ["font",["bold","italic","underline","clear"]],
                ["fontname",["fontname"]],["color",["color"]],
                ["para",["ul","ol","paragraph"]],
                ["table",["table"]],
                ["insert",["link","picture"]],
                ["view",["codeview"]],
                ["help",["help"]]
            ],
            disableDragAndDrop: true,
            minHeight: null,  // set minimum height of editor
            maxHeight: 250,   // set maximum height of editor
            height: 250,      // set editor height
            focus: false,     // set focus to editable area after initializing summernote
            placeholder:  makeSmartyLabel('emq_plh_email_body'),
            dialogsInBody: true,
            lang: 'pt-BR'

        }
    );

    $('div.note-group-select-from-files').remove();

    $('#btnCancelMsg,.closeMsg').click(function(){
        $("#email-form").trigger('reset');
        $('#emailMessage').summernote('code','');
        myDropzone.removeAllFiles(true);
    }) ;

});

function viewRecipients() {
    var sectionId = $("input[name=section]:checked"), arrSection = [], token = $("#_token").val();

    sectionId.each(function(){
        arrSection.push($(this).val());
    });

    $.ajax({
        type: "POST",
        url: path + '/emq/emqEmail/recipientsList',
        data:{idsection:arrSection,_token:token},
        dataType: 'json',
        error: function (ret) {
            modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
        },
        success: function(ret) {
            
            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if(obj) {

                if(obj.tabList != ''){
                    if($(".emailsList").hasClass('hide')){
                        $(".emailsList").removeClass('hide')
                    }

                    $("#bodyRecipients").html(obj.tabList);
                }else{
                    if(!$(".emailsList").hasClass('hide')){
                        $(".emailsList").addClass('hide')
                    }
                    $("#bodyRecipients").html('');
                    all_recip = false;
                    $("#btnSelAllRecip").html(makeSmartyLabel('emq_select_all'));
                }

            } else {
                modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
            }
        },
        beforeSend: function(){
            if($(".emailsList").hasClass('hide')){
                $(".emailsList").removeClass('hide')
            }
            $("#loaderList").html("<span style='color:#1ab394;'><i class='fa fa-spinner fa-spin fa-5x'></i></span>");
            $("#bodyRecipients").html('');
        },
        complete: function(){
            $("#loaderList").html('');
        }
    });

}

function deleteSpool(idspool)
{
    $.ajax({
        url : path + "/emq/emqEmail/deleteSpool/",
        type : 'POST',
        data : {
            id: idspool
        },
        success : function(data) {
            console.log('Delete success');
        },
        error : function(data)
        {
            console.log('Delete error');
        }
    });

    return false ;

}

function saveEmail(aAttachs,myDropzone)
{
    var recipientId = $("input[name=recipient]:checked"), arrRecipient = [];

    recipientId.each(function(){
        arrRecipient.push($(this).val());
    });
    
    $.ajax({
        type: "POST",
        url: path + '/emq/emqEmail/saveEmailMessage',
        dataType: 'json',
        data: {
            _token: $("#_token").val(),
            subject: $("#emailtitle").val(),
            body: $('#emailMessage').summernote('code'),
            recipient: arrRecipient,
            attachments: aAttachs
        },
        error: function (ret) {
            modalAlertMultiple('danger', makeSmartyLabel('TMSProvaSendMail_failure'),'alert-email');
        },
        success: function(ret){

            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if($.isNumeric(obj.idspool) && obj.status == 'ok') {
                modalAlertMultiple('success',makeSmartyLabel('TMSProvaSendMail_sucess'),'alert-email');

                setTimeout(function(){
                    $('#email-form').trigger('reset');
                    // clear summernote and dropzone
                    $('#emailMessage').summernote('code','');
                    myDropzone.removeAllFiles(true);
                    location.href = path + "/emq/emqEmail/index" ;
                    $('.dz-remove').remove();
                    errorname=[];
                    upname=[];
                },3000);

            } else {
                modalAlertMultiple('danger',makeSmartyLabel('TMSProvaSendMail_failure'),'alert-email');
            }
        },
        beforeSend: function(){
            /*$("#btnSaveMsg").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            $("#btnCancel").addClass('disabled');*/
        },
        complete: function(){
            $("#btnSaveMsg").html("<i class='fa fa-paper-plane'></i> "+ makeSmartyLabel('Send'));
            $("#btnCancel").removeClass('disabled');
        }
    });

    return false ;

}
