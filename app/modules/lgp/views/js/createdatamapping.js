
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

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /*
     *  Chosen
     */
    $("#cmbHolderType").chosen({ width: "100%",           no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbType").chosen({ width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#purposegroups").chosen({ width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#formatgroups").chosen({ width: "100%",           no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#collectformats").chosen({ width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#legalgrounds").chosen({ width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#storage").chosen({ width: "100%",           no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#personaccesses").chosen({ width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#sharedwhith").chosen({ width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    $('#datashared').on('ifChecked ifUnchecked',function(e){
        if(e.type == 'ifChecked'){
            $('#sharedWithLine').removeClass('hide');            
        }else{
            $('#sharedWithLine').addClass('hide');
        }
    });

    /*
     * Validate
     */
    $("#add-data-map-form").validate({
        ignore:[],
        rules: {
            cmbHolderType:"required",
            description:{required:true,minlength:3},
            cmbType:"required",
            "purposegroups[]":"required",
            "formatgroups[]":"required",
            "collectformats[]":"required",
            "legalgrounds[]":"required",
            "storage[]":"required",
            "personaccesses[]":"required",
            "sharedwhith[]":{required:function(element){return $('input[name="datashared"]:checked').val() == 'S';}}
        },
        messages: {
            cmbHolderType:makeSmartyLabel('Alert_field_required'),
            description:{required:makeSmartyLabel('Alert_field_required'),minlength:makeSmartyLabel('Alert_minlength')},
            cmbType:makeSmartyLabel('Alert_field_required'),
            "purposegroups[]":makeSmartyLabel('Alert_field_required'),
            "formatgroups[]":makeSmartyLabel('Alert_field_required'),
            "collectformats[]":makeSmartyLabel('Alert_field_required'),
            "legalgrounds[]":makeSmartyLabel('Alert_field_required'),
            "storage[]":makeSmartyLabel('Alert_field_required'),
            "personaccesses[]":makeSmartyLabel('Alert_field_required'),
            "sharedwhith[]":{required:makeSmartyLabel('Alert_field_required')}
        }
    });

    $("#upd-data-map-form").validate({
        ignore:[],
        rules: {
            cmbHolderType:"required",
            description:{required:true,minlength:3},
            cmbType:"required",
            "purposegroups[]":"required",
            "formatgroups[]":"required",
            "collectformats[]":"required",
            "legalgrounds[]":"required",
            "storage[]":"required",
            "personaccesses[]":"required",
            "sharedwhith[]":{required:function(element){return $('input[name="datashared"]:checked').val() == 'S';}}
        },
        messages: {
            cmbHolderType:makeSmartyLabel('Alert_field_required'),
            description:{required:makeSmartyLabel('Alert_field_required'),minlength:makeSmartyLabel('Alert_minlength')},
            cmbType:makeSmartyLabel('Alert_field_required'),
            "purposegroups[]":makeSmartyLabel('Alert_field_required'),
            "formatgroups[]":makeSmartyLabel('Alert_field_required'),
            "collectformats[]":makeSmartyLabel('Alert_field_required'),
            "legalgrounds[]":makeSmartyLabel('Alert_field_required'),
            "storage[]":makeSmartyLabel('Alert_field_required'),
            "personaccesses[]":makeSmartyLabel('Alert_field_required'),
            "sharedwhith[]":{required:makeSmartyLabel('Alert_field_required')}
        }
    });

    
    $("#btnCreateDataMap").click(function(){

        if (!$("#add-data-map-form").valid()) {
            return false ;
        }

        if(!$("#btnCreateDataMap").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpDataMapping/createDataMap',
                dataType: 'json',
                data: $("#add-data-map-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger', makeSmartyLabel('Alert_failure'),'alert-add-data-mapping');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/lgp/lgpDataMapping/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-add-data-mapping');
                    }
                },
                beforeSend: function(){
                    $("#btnCreateDataMap").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnCreateDataMap").html("<i class='fa fa-check-circle'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }
        
    });

    $("#btnUpdateDataMap").click(function(){

        if (!$("#upd-data-map-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdateDataMap").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpDataMapping/updateDataMap',
                dataType: 'json',
                data: $("#upd-data-map-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger', makeSmartyLabel('Edit_failure'),'alert-upd-data-mapping');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/lgp/lgpDataMapping/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-upd-data-mapping');
                    }
                },
                beforeSend: function(){
                    $("#btnUpdateDataMap").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateDataMap").html("<i class='fa fa-check-circle'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }
        
    });

    

});






