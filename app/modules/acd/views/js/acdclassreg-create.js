
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
    
    var objRecordData = {
        loadSerie: function() {        
            $.post(path+"/acd/acdClassreg/ajaxcomboSerie",
                {"courseID":$('#cmbClassCourse').val()},
                function(valor) {
                    $("#cmbClassSerie").html(valor);
                    $("#cmbClassSerie").trigger("chosen:updated");
                    return false;
                });
            return false ;
        }
    }

    //Sempre que o item selecionado no filtro "Curso" alterar
    $("#cmbClassCourse").change(function(){
        objRecordData.loadSerie();
    });

    //Dinâmica de filtros considerando o valor do "tipo de relatório"
    $("#cmbClassCourse").change(function(){

        if($(this).val() == ''){

            $(".serie_combo").addClass('hide');

        }else{

            $(".serie_combo").removeClass('hide');

        }
        
    });

    /*
     *  Chosen
     */
    $("#cmbClassCourse").chosen({width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbClassSerie").chosen({width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/acd/acdClassreg/index');

    $("#btnSave").click(function(){
        if (!$("#create-classreg-form").valid()) {
            return false ;
        }

        if(!$("#btnSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/acd/acdClassreg/createClass',
                dataType: 'json',
                data: $("#create-classreg-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-classreg-create');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/acd/acdClassreg/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-classreg-create');
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
        if (!$("#update-classreg-form").valid()) {
            return false ;
        }

        if(!$("#btnSaveUpdate").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/acd/acdClassreg/updateClass',
                dataType: 'json',
                data: $("#update-classreg-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-classreg-update');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/acd/acdClassreg/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-classreg-update');
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
    $("#create-classreg-form").validate({
        ignore:[],
        rules: {
            className: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+"/acd/acdClassreg/existClass",
                    data: 
                    {SerieID: function(element){return $("#cmbClassSerie").val()}},
                    type: 'post',
                    dataType:'json',
                    async: false

                }
            },
            classNameAbrev:{
                required:true,
                minlength: 2,
                remote:{
                    url: path+"/acd/acdClassreg/existClass",
                    type: 'post',
                    dataType:'json',
                    async: false

                }
            },
            cmbClassCourse: "required",
            cmbClassSerie: "required",
        },
        messages: {
            className: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_word_min_letters')},
            classNameAbrev: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_abbrev_min_letters')},
            cmbClassCourse: {required:makeSmartyLabel('Alert_field_required')},
            cmbClassSerie: {required:makeSmartyLabel('Alert_field_required')},
        }
    });

    $("#update-classreg-form").validate({
        ignore:[],
        rules: {
            className: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+"/acd/acdClassreg/existClass",
                    data: 
                    {SerieID: function(element){return $("#cmbClassSerie").val()},
                    classID: function(element){return $("#classID").val()}},
                    type: 'post',
                    dataType:'json',
                    async: false
                }
            },
            classNameAbrev:{
                required:true,
                minlength: 2,
                remote:{
                    url: path+"/acd/acdClassreg/existClass",
                    type: 'post',
                    dataType:'json',
                    async: false

                }
            },
            cmbClassCourse: "required",
            cmbClassSerie: "required",
        },
        messages: {
            className: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_word_min_letters')},
            classNameAbrev: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_abbrev_min_letters')},
            cmbClassCourse: {required:makeSmartyLabel('Alert_field_required')},
            cmbClassSerie: {required:makeSmartyLabel('Alert_field_required')},
        }

    });

});

function showAlert(msg,typeAlert,btnOk)
{
    $('#modal-notification').html(msg);
    $("#btn-modal-ok").attr("href", btnOk);
    $("#tipo-alert").attr('class', 'alert alert-'+typeAlert);
    $('#modal-alert').modal('show');

    return false;
}


