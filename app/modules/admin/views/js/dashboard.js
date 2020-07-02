

$(document).ready(function () {

    countdown.start(timesession);
    console.log('adm: main.js');
    new gnMenu( document.getElementById( 'gn-menu' ) );

    $("#btnUpdateVocabulary").click(function(){

        //alert('inside');




        $.ajax({
            type: "POST",
            url: path + '/admin/home/updateLanguageFile',
            dataType: 'json',
             data: { action: 'write'},
            error: function (ret) {
                showAlert('erroooooo','danger',path + '/admin/home/index');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.success) {
                    //showAlert(makeSmartyLabel('Language file was successfully generated .'),'success',path + '/admin/home/index');
                    showAlert('Language file was successfully generated .','success',path + '/admin/home/index');
                } else {
                    //showAlert(makeSmartyLabel('Error generating the language file: ')+': '+obj.message,'danger',path + '/admin/home/index');
                    showAlert('Error generating the language file '+': '+obj.message,'danger',path + '/admin/home/index');
                }

            }
            /*
            ,
            beforeSend: function(){
                $("#btnCreateModule").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                $("#btnCancel").addClass('disabled');
            },
            complete: function(){
                $("#btnCreateModule").html("<i class='fa fa-check-circle'></i> "+ makeSmartyLabel('Yes')).removeClass('disabled');
                $("#btnCancel").removeClass('disabled');
            }
            */
        });


    });

    $("#btnClearSmartyCash").click(function(){
        $.ajax({
            type: "POST",
            url: path + '/admin/home/clearSmartycache',
            dataType: 'json',
            data: { action: 'doit'},
            error: function (ret) {
                showAlert('erroooooo','danger',path + '/admin/home/index');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if(obj.success) {
                    showAlert(obj.message,'success',path + '/admin/home/index');
                } else {
                    showAlert('Error clearing Smarty Cache '+': '+obj.message,'danger',path + '/admin/home/index');
                }
            }
        });
    });


});



