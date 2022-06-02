//var global_idproduto = '';
$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    /*
     *  Chosen
     */
    $("#cmbCompany").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $("#cmbBank").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/fin/finBankRemittanceRep/index');

    $("#btnCheckFile").click(function(){
        var token = $("#_token").val(), idcompany = $("#cmbCompany").val(),
            idbank = $("#cmbBank").val();

        if (myDropzone.getQueuedFiles().length > 0) {
            console.log('tem '+ myDropzone.getQueuedFiles().length + ' arquivos');

            myDropzone.options.params = {_token:token, idcompany:idcompany, idbank:idbank };
            myDropzone.processQueue();
        } else {
            console.log('No files, no dropzone processing');
            //sendNotification('new-ticket-user',ticket,false);
        }

        return false;
    });


    /*
     * Dropzone
     */
    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone("#myDropzone", {  url: path + "/fin/finBankRemittanceRep/checkFile/",
        method: "post",
        dictDefaultMessage: "<br><i class='fa fa-file fa-2x' aria-hidden='true'></i><br>Arraste o arquivo ou clique aqui.",
        createImageThumbnails: true,
        maxFiles: 1,
        autoQueue: true,
        addRemoveLinks:true,
        acceptedFiles: '.crm,.txt,.rm2,.rm3,.rm4,.rm5,.rm6,.rm7,.rm8,.rm9,.CRM,.TXT,.RM2,.RM3,.RM4,.RM5,.RM6,.RM7,.RM8,.RM9',
        parallelUploads: 1,
        autoProcessQueue: false
    });

    myDropzone.on("maxfilesexceeded", function(file) {
        this.removeFile(file);
    });

    myDropzone.on("complete", function(file) {
        if(file.xhr.response){
            var res = file.xhr.response.split('|');            

            if(res[0] == 'msg'){
                modalAlertMultiple('danger',res[1],'alert-check-remittance');
                this.removeFile(file);
            }else{
                var fileUrl = res[0].split("="), numrows = res[1].split("="),
                    numprotest = res[2].split("="), numnoprotest = res[3].split("="),
                    total = res[4].split("="), filerem = res[5].split("="),
                    sequence = res[6].split("="), numsendbank = res[7].split("="),
                    numnosendbank = res[8].split("="), numprintbank = res[9].split("="),
                    numnoprintbank = res[10].split("="), numdiscount = res[11].split("="),
                    numnodiscount = res[12].split("="), typediscount = res[13].split("="),
                    valdiscount = res[14].split("=");

                $("#btn-modal-ok").attr("href", path+"/fin/finBankRemittanceRep/index");
                $("#btn-modal-print").attr("href", fileUrl[1]).attr("target","_blank");
                $('#filename').html(filerem[1]);
                $("#sequence").html(sequence[1]);
                $('#numrows').html(numrows[1]);
                $("#totalvalue").html('R$ '+ total[1]);
                $('#numprotest').html(numprotest[1]);
                $("#numnoprotest").html(numnoprotest[1]);
                $('#numsendbank').html(numsendbank[1]);
                $("#numnosendbank").html(numnosendbank[1]);
                $('#numprintbank').html(numprintbank[1]);
                $("#numnoprintbank").html(numnoprintbank[1]);
                $('#numdiscount').html(numdiscount[1]);
                $("#numnodiscount").html(numnodiscount[1]);
                $('#typediscount').html(typediscount[1]);
                $("#valdiscount").html(valdiscount[1]);
                $('#modal-alert').modal('show');

                /*
                 * I had to make changes to open the file in a new window
                 * because I could not use the jquery.download with the .pdf extension
                 */
                /*if (res[1].indexOf(".pdf") >= 0) {
                    window.open(res[1], '_blank');
                } else {
                    $.fileDownload(res[1]);
                }*/
            }

        }else{
            modalAlertMultiple('danger','Ocorreu um problema ao realizar a verificação','alert-check-remittance');
        }
    });

    myDropzone.on("queuecomplete", function (file) {

     //   console.log('Completed the dropzone queue');
      //  sendNotification('new-ticket-user',global_coderequest,true);
      //  console.log('Sent email, with attachments');
    });


    /*
     * Combos
     */
    var objRemittanceRepData = {
        changeBank: function() {
            var companyId = $("#cmbCompany").val();
            $.post(path+"/fin/finBankRemittanceRep/ajaxBank",{companyId: companyId},
                function(valor){
                    $("#cmbBank").html(valor);
                    $("#cmbBank").trigger("chosen:updated");
                })
        }
    }

    $("#cmbCompany").change(function(){
        objRemittanceRepData.changeBank();
    });

});
