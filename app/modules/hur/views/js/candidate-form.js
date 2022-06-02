var global_idcurriculum = '';
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
    $("#cmbGender").chosen({ width: "95%", no_results_text: "Nada encontrado!"});
    $("#cmbMaritalStatus").chosen({ width: "95%",   no_results_text: "Nada encontrado!"});
    $("#cmbMaritalStatus").chosen({ width: "95%",   no_results_text: "Nada encontrado!"});
    $("#cmbArea").chosen({ width: "95%",   no_results_text: "Nada encontrado!"});
    $("#cmbRole").chosen({ width: "95%",   no_results_text: "Nada encontrado!"});
    $("#cmbDeficiency").chosen({ width: "95%",   no_results_text: "Nada encontrado!"});

    /*
     * Mask
     */
    $('#birthdate').mask('00/00/0000');
    $('#home_phone').mask('(00) 0000-0000');
    $('#mobile_phone').mask('(00) 00000-0000');
    $('#scrap_phone').mask('(00) #0000-0000');
    $('#cep').mask('00000-000');
    $('#cpfinfo').mask('000.000.000-00');

    /*
     * Datepicker
     */
    $('.input-group.date').datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR",
        autoclose: true,
        orientation: "bottom auto"
    });

    /*
     * Combos
     */

    var objCurriculumData = {
        changeRole: function() {
            var areaId = $("#cmbArea").val();
            $.post(path+"/curriculum/home/ajaxRole",{areaId: areaId},
                function(valor){
                    $("#cmbRole").html(valor);
                    $("#cmbRole").trigger("chosen:updated");
                })
        }
    }

    $("#cmbArea").change(function(){
        objCurriculumData.changeRole();
    });



    /*
     * Buttons
     */
    $("#btnEmail").click(function () {

        $('#modal-form-email').modal('show');

    });

    $("#btnPDF").click(function () {
        var filename = $("#filename").val();
        console.log(filename);
        window.open(filename, '_blank');

    });

    $("#btnPrint").click(function () {
        $.ajax({
            type: "POST",
            url: path + "/hur/hurCandidate/printData",
            data: {id:$("#idcurriculum").val()},
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
            },
            success: function(fileName) {
                
                if(fileName){
                    /*
                     * I had to make changes to open the file in a new window
                     * because I could not use the jquery.download with the .pdf extension
                     */
                    if (fileName.indexOf(".pdf") >= 0) {
                        console.log(fileName)
                        window.open(fileName, '_blank'); //abre em nova janela o relatório
                    } else {
                        $.fileDownload(fileName );
                    }
                }
                else {
                }
            }
        });
        return false;

    });

    $(".btnCancel").attr("href", path + '/hur/hurCandidate/index');

    $("#btnSendEmail").click(function(){
        var itemlist = $("input[name='curriculumItem[]']"),
            tolist = $("input[name='toAddress[]']"),
            emailtitle = $("input[name='emailtitle").val(),
            ck = 0, txt, msg = $('#emailMessage').summernote('code');

        txt = validateRecipientList(tolist);
        if(txt != 'ok'){
            modalAlertMultiple('danger',txt,'alert-email');
            return false;
        }

        if(emailtitle == ''){
            modalAlertMultiple('danger','Favor inserir o Assunto do E-mail.','alert-email');
            return false;
        }

        for(var i = 0; i < itemlist.length; i++){
            if(itemlist[i].checked){
                ck = ck + 1;
            }
        }

        if(ck == 0){
            modalAlertMultiple('danger','Selecione pelo menos um anexo!','alert-email');
            return false;
        }

        $.ajax({
            type: "POST",
            url: path + '/hur/hurCandidate/sendEmail',
            dataType: 'json',
            data: $('#email-form').serialize() + "&msg=" + msg,
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel enviar !','alert-email');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if(obj.status === "OK") {
                    $("#alert-email").html('');
                    modalAlertMultiple('success','Email enviado com sucesso !','alert-email');
                    setTimeout(function(){
                        $('#modal-form-email').modal('hide');
                        $("#email-form").trigger('reset');
                        $('#emailMessage').summernote('code','');
                    },2000);
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel enviar !','alert-email');
                }
            },
            beforeSend: function(){
                $("#btnSendEmail").attr('disabled','disabled');
                $("#alert-email").html('<div class="alert alert-warning"><i class="fa fa-spinner fa-spin"></i>&nbsp;&nbsp;&nbsp;&nbsp;Enviando e-mail.</div>');
            },
            complete: function(){
                $("#btnSendEmail").removeAttr('disabled')
            }
        });

    });

    var i = $("#_totalto").val();
    $('#btnAddTo').click (function() {
        console.log(i);
        i++;
        $('.to').append('<div class="form-group" id="to_'+i+'"><label class="col-sm-2 control-label">&nbsp;</label><div class="col-sm-8"><input type="text" name="toAddress[]" id="toAddress_'+i+'" class="form-control input-sm" value=""  /></div><div class="col-sm-1"> <div class="btn-group"><button class="btn btn-danger btn-sm btnRemoveTo" data-to="'+i+'" type="button"><i class="fa fa-remove" aria-hidden="true"></i></button></div></div>');
    });

    $(document).on('click', '.btnRemoveTo', function() {
        $('#to_'+$(this).data('to')).remove();
    });

    $('#emailMessage').summernote(
        {
            toolbar:[
                ["style",["style"]],
                ["font",["bold","italic","underline","clear"]],
                ["fontname",["fontname"]],["color",["color"]],
                ["para",["ul","ol","paragraph"]]
            ],
            disableDragAndDrop: true,
            minHeight: null,  // set minimum height of editor
            maxHeight: 250,   // set maximum height of editor
            height: 250,      // set editor height
            width: 600,       // set editor width
            focus: false     // set focus to editable area after initializing summernote


        }
    );

    $('.modal').on('hidden.bs.modal', function(e){
        $("#email-form").trigger('reset');
        $('#emailMessage').summernote('code','');
    }) ;


});

function alertClose()
{
    $('#modal-alert').modal('hide');

}

function validateRecipientList($recipientlist)
{
    var empty = false, valid = true, msg,
        pattern = /^(?=.{1,254}$)(?=.{1,64}@)[-!#$%&'*+/0-9=?A-Z^_`a-z{|}~]+(\.[-!#$%&'*+/0-9=?A-Z^_`a-z{|}~]+)*@[A-Za-z0-9]([A-Za-z0-9-]{0,61}[A-Za-z0-9])?(\.[A-Za-z0-9]([A-Za-z0-9-]{0,61}[A-Za-z0-9])?)*$/;
    for(var i = 0; i < $recipientlist.length; i++){

        if($recipientlist[i].value == ''){
            empty = true;
        }else{
            if(!pattern.test($recipientlist[i].value)){
                valid = false;
            }
        }
    }

    if(empty){
        msg = "Um ou mais destinatários não possuem endereço de e-mail!";
    }else if(!empty && !valid){
        msg = "Um ou mais endereços de e-mail possuem formato incorreto!";
    }else{
        msg = "ok";
    }

    return msg

}


