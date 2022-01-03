//Initial settings of Dropzone
Dropzone.autoDiscover = false;
var dropzonefiles = 0,filesended = 0, flgerror = 0, errorname=[], upname=[], flgDefault=0, btnClicked = 0;

$(document).ready(function () {
    countdown.start(timesession);

    /*
     * Datepicker
     */
    $('.input-group.date').datepicker({
        format: dtpFormat,
        language:  dtpLanguage,
        autoclose:  dtpAutoclose,
        orientation: dtpOrientation
    });
    
    /*
     * Select2
     */
    $('#cmbUF').select2({placeholder:translateLabel('Select'),allowClear:true});

    /*
     * iCheck - checkboxes/radios styling
     */
    $('#cityDefault').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /*
     * Dropzone
     */
    var myDropzone = new Dropzone("#myDropzone", {  url: path + "/exp/expCity/saveImage/",
        method: "post",
        dictDefaultMessage: "<i class='fa fa-file-image fa-2x' aria-hidden='true'></i><br>" + translateLabel('Drag_image_msg'),
        createImageThumbnails: true,
        maxFiles: 1,
        acceptedFiles: '.jpg, .jpeg, .png, .gif',
        parallelUploads: 1,
        autoProcessQueue: false,
        addRemoveLinks: true,
        init: function () {
            if($("#update-city-form").length > 0){
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: path + '/exp/expCity/loadImage',
                    data: {
                        cityID:  $('#cityID').val()
                    },
                    success: function(response){
                        var existingFileCount = 0;
                        
                        $.each(response, function(  key, value ) {
                            var fileList = {
                                idimage: value.idimage,
                                name: value.filename,
                                fname: value.fmtname,
                                size: value.size,
                                url: value.url
                            };
                            console.log(fileList.url+fileList.fname);
                            myDropzone.emit("addedfile", fileList);
                            myDropzone.files.push(fileList);
                            myDropzone.emit("thumbnail", fileList, fileList.url+fileList.fname);
                            myDropzone.emit("success", fileList);
                            myDropzone.emit("complete", fileList);
    
                            existingFileCount = existingFileCount + 1; // The number of files already uploaded
    
                        });
                        myDropzone.options.maxFiles = myDropzone.options.maxFiles - existingFileCount;
                        console.log('tem '+ myDropzone.getQueuedFiles().length + ' arquivos');
                    },
                    error: function (response) {
                        console.log("Erro no Dropzone!");
                    }
                });
            }
        }
    });

    myDropzone.on("maxfilesexceeded", function(file) {
        this.removeFile(file);
    });

    myDropzone.on("removedfile", function(file) {
        console.log(file);
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: path + '/exp/expCity/removeImage',
            data: {
                idimage:  file.idimage,
                filename: file.fname
            },
            success: function(response){
                var obj = jQuery.parseJSON(JSON.stringify(response));
                if(obj.success){
                    myDropzone.options.maxFiles = myDropzone.options.maxFiles + 1;
                }
            },
            error: function (response) {
                console.log("Erro no Dropzone!");
            }
        });
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
        var list,msg,typeMsg;

        if(errorname.length == 0 && (filesended == dropzonefiles)){
            if(btnClicked=="1"){
                saveData(upname,'add');
            }else if(btnClicked=="2"){
                saveData(upname,'upd')
            }                            
        }else{
            var totalAttach = dropzonefiles - filesended;
            list = '<h4>'+translateLabel('files_not_attach_list')+'</h4><br>';
            errorname.forEach(element => {
                list = list+element+'<br>';
            });
            list = list+'<br><strong>'+translateLabel('scm_attach_after')+'</strong>';
            typeMsg = 'warning';
            msg = translateLabel('save_anyway_question');
            showNextStep(list,msg,typeMsg,totalAttach);
        }        
        
        dropzonefiles = 0; 
        filesended = 0;
        flgerror = 0;
    });
    
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/exp/expCity/index');

    $("#btnCreateCity").click(function(){

        if (!$("#create-city-form").valid()) {
            return false ;
        }
        
        if(!$("#btnCreateCity").hasClass('disabled')){
            $("#btnCreateCity").html("<i class='fa fa-spinner fa-spin'></i> "+ translateLabel('Processing')).addClass('disabled');

            if (myDropzone.getQueuedFiles().length > 0) {
                btnClicked = "1";
                dropzonefiles = myDropzone.getQueuedFiles().length;
                myDropzone.processQueue();
            } else {
                saveData(upname,'add');
            }
        }
        
    });

    $("#btnUpdateCity").click(function(){

        if (!$("#update-city-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdateCity").hasClass('disabled')){
            $("#btnUpdateCity").html("<i class='fa fa-spinner fa-spin'></i> "+ translateLabel('Processing')).addClass('disabled');

            if (myDropzone.getQueuedFiles().length > 0) {
                btnClicked = "2";
                dropzonefiles = myDropzone.getQueuedFiles().length;
                    myDropzone.processQueue();
            } else {
                saveData(upname,'upd');
            }
        }

    });

    $("#btnNextYes").click(function(){        
        $('#modal-next-step').modal('hide');
        if(btnClicked=="1"){
            saveData(upname,'add');
        }else if(btnClicked=="2"){
            saveData(upname,'upd');
        }      
    });

    $("#btnNextNo").click(function(){
        if (!$("#btnNextNo").hasClass('disabled')) {
            $("#btnNextNo").removeClass('disabled');
            $('#modal-next-step').modal('hide');
            errorname = [];
            upname = [];

            location.href = path + '/exp/expCity/index'
        }
    });

    /*
     * Validate
     */
    $("#create-city-form").validate({
        ignore:[],
        rules: {
            cmbUF:    "required",
            cityName:{
                required:true,
                remote:{
                    url: path+'/exp/expCity/checkExist',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{  
                        _token:function(element){return $("#_token").val()},
                        uf:function(element){return $("#cmbUF").val()}
                    }
                }
            }
        },
        messages: {
            cmbUF:    translateLabel('Alert_field_required'),
            cityName:{required:translateLabel('Alert_field_required')}
        }
    });

    $("#update-city-form").validate({
        ignore:[],
        rules: {
            cmbUF:    "required",
            cityName:{
                required:true,
                remote:{
                    url: path+'/exp/expCity/checkExist',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{  
                        _token:function(element){return $("#_token").val()},
                        uf:function(element){return $("#cmbUF").val()},
                        cityID:function(element){return $("#cityID").val()}
                    }
                }
            }
        },
        messages: {
            cmbUF:    translateLabel('Alert_field_required'),
            cityName:{required:translateLabel('Alert_field_required')}
        }
    });

    /* when the modal is hidden */
    $('#modal-city-create').on('hidden.bs.modal', function() { 
        location.href = path + "/exp/expCity/index";        
    });

    if($("#update-city-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.href = path + "/exp/expCity/index" ;        
        });
    }

    $("#cityDefault").on('ifChecked ifUnchecked',function(e){
        if(e.type == 'ifChecked'){
            flgDefault = 1;
        }else{
            flgDefault = 0;
        }
    });
});

function saveData(aAttachs,op)
{
    var method = op == 'add' ? 'createCity' : 'updateCity', 
        alert = op == 'add' ? 'alert-create-city' : 'alert-update-city',
        btn = op == 'add' ? 'btnCreateCity' : 'btnUpdateCity',
        cityID = op == 'add' ? '' : $("#cityID").val();

    $.ajax({
        type: "POST",
        url: path + '/exp/expCity/'+ method,
        dataType: 'json',
        data: {
            _token: $("#_token").val(),
            cmbUF: $('#cmbUF').val(),
            cityName: $('#cityName').val(),
            foundationDate: $('#foundationDate').val(),
            cityDefault: flgDefault,
            attachments: aAttachs,
            cityID:cityID
        },
        error: function (ret) {
            modalAlertMultiple('danger',translateLabel('Alert_failure'),alert);
        },
        success: function(ret){
            //console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if(obj.success){
                if(op == 'add'){
                    $('#modal-uf').val($("#cmbUF option:selected" ).text());
                    $('#modal-idcity').val(obj.idcity);
                    $('#modal-city-name').val(obj.description);
                    $('#modal-dtfoundation').val(obj.dtfoundation);
    
                    $('#modal-city-create').modal('show');
                }else{
                    showAlert(translateLabel('Edit_sucess'),'success');
                }
                
            }else{
                modalAlertMultiple('warning',translateLabel('Alert_failure'),alert);
            }
        },
        beforeSend: function(){
            /*$("#btnCreateCity").html("<i class='fa fa-spinner fa-spin'></i> "+ translateLabel('Processing')).addClass('disabled');*/
        },
        complete: function(){
            $("#"+btn).html("<i class='fa fa-save'></i> "+ translateLabel('Save')).removeClass('disabled');
        }
    });

    return false ;

}
            