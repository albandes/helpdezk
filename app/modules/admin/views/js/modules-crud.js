//Initial settings of Dropzone
Dropzone.autoDiscover = false;
var dropzonefiles = 0,filesended = 0, flgerror = 0, errorname=[], upname=[], flgDefault=0, btnClicked = 0;

$(document).ready(function () {
    countdown.start(timesession);
    
    /**
     * Select2
     */
    //$('#cmbMatType').select2({width:'100%',placeholder:vocab['Select'],allowClear:true});

    /**
     * Mask
     */
    $('#modulePrefix').mask('AAA');

    /**
     * iCheck - checkboxes/radios styling
     */
    $('#moduleDefault').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /**
     * Dropzone
     */
    var myDropzone = new Dropzone("#moduleLogo", {  url: path + "/admin/modules/saveLogo/",
        method: "post",
        dictDefaultMessage: "<i class='fa fa-file-image fa-2x' aria-hidden='true'></i><br>" + vocab['Drag_image_msg'],
        createImageThumbnails: true,
        maxFiles: 1,
        acceptedFiles: '.jpg, .jpeg, .png, .gif',
        parallelUploads: 1,
        autoProcessQueue: false,
        addRemoveLinks: true
    });

    myDropzone.on("maxfilesexceeded", function(file) {
        this.removeFile(file);
    });

    /*myDropzone.on("removedfile", function(file) {
        console.log(file);
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: path + '/lmm/lmmTitles/removeCover',
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
    });*/

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
            list = '<h4>'+vocab['files_not_attach_list']+'</h4><br>';
            errorname.forEach(element => {
                list = list+element+'<br>';
            });
            list = list+'<br><strong>'+vocab['scm_attach_after']+'</strong>';
            typeMsg = 'warning';
            msg = vocab['save_anyway_question'];
            showNextStep(list,msg,typeMsg,totalAttach);
        }        
        
        dropzonefiles = 0; 
        filesended = 0;
        flgerror = 0;
    });
    
    /**
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/admin/modules/index');

    $("#btnCreateModule").click(function(){

        if (!$("#create-module-form").valid()) {
            return false ;
        }

        var checkFields = validateFields();        
        if (!checkFields[0][0]) {
            modalAlertMultiple('danger',checkFields[1][0],'alert-create-title');
            return false ;
        }
        
        if(!$("#btnCreateTitle").hasClass('disabled')){
            $("#btnCreateTitle").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');

            if (myDropzone.getQueuedFiles().length > 0) {
                btnClicked = "1";
                dropzonefiles = myDropzone.getQueuedFiles().length;
                myDropzone.processQueue();
            } else {
                saveData(upname,'add');
            }
        }
        
    });

    $("#btnUpdateTitle").click(function(){

        if (!$("#update-title-form").valid()) {
            return false ;
        }

        var checkFields = validateFields();        
        if (!checkFields[0][0]) {
            modalAlertMultiple('danger',checkFields[1][0],'alert-update-title');
            return false ;
        }

        if(!$("#btnUpdateTitle").hasClass('disabled')){
            $("#btnUpdateTitle").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');

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

            location.href = path + '/lmm/lmmTitles/index'
        }
    });
    
    //show modal to add vocabulary key name
    $("#btnAddMaterialType").click(function(){
        $("#modal-add-material-type").modal('show');

        /**
         * iCheck - checkboxes/radios styling
         */
        $('#materialTypeDefault').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
        
        $("#material-type-form").validate({
            ignore:[],
            rules: {
                materialType: {
                    required:true,
                    minlength: 5,
                    remote:{
                        url: path+'/lmm/lmmMaterialType/checkExist',
                        type: 'post',
                        dataType:'json',
                        async: false,
                        data:{
                            _token:function(element){return $("#_token").val()}
                        }
                    }
                }
            },
            messages: {
                materialType: {required:vocab['Alert_field_required'], minlength:vocab['Alert_minimum_five_characters']}
            }
        }); 
    });

    $("#btnModMatTypeSave").click(function(){

        if (!$("#material-type-form").valid()) {
            return false ;
        }

        if(!$("#btnModMatTypeSave").hasClass('disabled')){  
            var data_save = $("#material-type-form").serialize();
            data_save = data_save + "&_token="+$("#_token").val();

            $.ajax({     
            type: "POST",
            url: path + '/lmm/lmmMaterialType/createMaterialType',
            dataType: 'json',
            data: data_save,
            error: function (ret) {
                modalAlertMultiple('danger',vocab['Alert_failure'],'alert-add-material-type-modal');
            },
            success: function(ret){    
                var obj = jQuery.parseJSON(JSON.stringify(ret));    
                if(obj.success) {
                    modalAlertMultiple('success',vocab['Alert_inserted'],'alert-add-material-type-modal');
                    objTitleData.loadMaterialType(obj.idmaterialtype);
                    setTimeout(function(){
                        $('#modal-add-material-type').modal('hide');
                    },2000);
                } else {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-add-material-type-modal');
                }
            },
            beforeSend: function(){
                $("#btnModMatTypeSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                $("#btnModMatTypeClose").addClass('disabled');
            },
            complete: function(){
                $("#btnModMatTypeSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                $("#btnModMatTypeClose").removeClass('disabled');
            }    
        });
    }

    });

    /**
     * Validate
     */
    $("#create-module-form").validate({
        ignore:[],
        rules: {
            moduleName:        {
                required: true,
                minlength: 3,
                remote: {
                    url: path + "/admin/modules/checkModule",
                    type: "post",
                    dataType:'json',
                    data:{
                        _token: function(element){return $('#_token').val();}
                    }
                }
            },
            modulePath:{
                required: true,
                minlength: 3,
                remote: {
                    url: path + "/admin/modules/checkModulePath",
                    type: "post",
                    dataType:'json',
                    data:{
                        _token: function(element){return $('#_token').val();}
                    }
                }
            },
            modulePrefix:      {
                required: true,
                minlength: 3
            },
            moduleKeyName:   {
                required: true,
                minlength: 3
            }
        },
        messages: {
            moduleName: {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']},
            modulePath: {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']},
            modulePrefix: {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']},
            moduleKeyName: {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']}
        }
    });

    $("#update-module-form").validate({
        ignore:[],
        rules: {
            cmbMatType:{
                required:true            
            },
            isbn:{
                required:true            
            },
            titleName: {
                required:true,
                remote:{
                    url: path+'/lmm/lmmTitles/checkExist',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{
                        _token:function(element){return $("#_token").val()},                    
                        isbn:function(element){return $("#isbn").val()},
                        titleID:function(element){return $("#titleID").val()}
                    }
                }
            },
            cmbCdd:{
                required:true            
            },
            cmbPublisher:{
                required:true            
            },
            cmbColor:{
                required:true            
            }
        },
        messages: {
            cmbMatType: {required:vocab['Alert_field_required']},
            isbn: {required:vocab['Alert_field_required']},
            titleName: {required:vocab['Alert_field_required']},
            cmbCdd: {required:vocab['Alert_field_required']},
            cmbPublisher: {required:vocab['Alert_field_required']},
            cmbColor: {required:vocab['Alert_field_required']}
        }
    });

    /* when the modal is hidden */
    $('#modal-title-create').on('hidden.bs.modal', function() { 
        location.href = path + "/lmm/lmmTitles/index";        
    });

    if($("#update-title-form").length > 0){
        $('#modal-title-update').on('hidden.bs.modal', function() { 
            location.href = path + "/lmm/lmmTitles/index" ;
        });
    }

    $("input[name='inCollection']").on('ifClicked',function(e){
        if($(this).val() == 'Y'){
            $("#inCollectionLine").removeClass('d-none');
            $('#cmbCollection').select2({with:'100%',placeholder:vocab['Select'],allowClear:true});
        }else{
            $("#inCollectionLine").addClass('d-none');
        }
    });

    $('.lbltooltip').tooltip();
});

function saveData(aAttachs,op)
{
    var method = op == 'add' ? 'createTitle' : 'updateTitle', 
        alert = op == 'add' ? 'alert-create-title' : 'alert-update-title',
        btn = op == 'add' ? 'btnCreateTitle' : 'btnUpdateTitle',
        formName = op == 'add' ? 'create-title-form' : 'update-title-form',
        data_save = $('#'+formName).serialize();
    
    // Add attachment's object to form serialized
    if(aAttachs.length > 0){
        data_save = data_save + "&attachments%5B%5D="+aAttachs;
    }

    $.ajax({
        type: "POST",
        url: path + '/lmm/lmmTitles/'+ method,
        dataType: 'json',
        data: data_save,
        error: function (ret) {
            modalAlertMultiple('danger',vocab['Alert_failure'],alert);
        },
        success: function(ret){
            //console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if(obj.success){
                if(op == 'add'){
                    $('#modal-idtitle').val(obj.idtitle);
                    $('#modal-title-name').val(obj.description);
                    $('#copyIDList tbody').html(obj.copiesList);
    
                    $('#modal-title-create').modal('show');
                }else{
                    var copiesHtml = '', aCopies;
                    $('#modal-idtitle').val(obj.idtitle);
                    $('#modal-title-name').val(obj.description);

                    if(obj.displayCopies) {
                        $.each(obj.copiesList, function(key,value) {
                            copiesHtml = copiesHtml + "<tr>"+
                                "<td class='text-center'>"+ value.registrationnumber +"</td>"+
                                "<td class='text-center'>"+ value.volume +"</td>"+
                                "<td class='text-center'>"+ value.edition +"</td>"+
                                "<td class='text-center'>"+ value.bookyear +"</td>"+
                              "</tr>";
                        });
                        
                        $('#copyIDList tbody').html(copiesHtml);
                        $('#copiesLine').removeClass('d-none');
                    }
    
                    $('#modal-title-update').modal('show');
                    //showAlert(vocab['Edit_sucess'],'success');
                }
                
            }else{
                modalAlertMultiple('danger',vocab['Alert_failure'],alert);
            }
        },
        beforeSend: function(){
            /*$("#btnCreateCity").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');*/
        },
        complete: function(){
            $("#"+btn).html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
        }
    });

    return false ;

}

function duplicateAuthorRow(){
    // First, lets create the new row using the last one as template...
    var clonedRow = $( "#authorList tr:last" ).clone();
    // Take the current identifier, some number in the first cell of the row
    intCurrentRowId = parseInt($( "#authorNumId:last",clonedRow ).val());
    // Set the new ID
    intNewRowId = intCurrentRowId + 1;
    // Change the current identifier of the row to the new one
    $( "#authorNumId:last", clonedRow ).val( intNewRowId );

    // Change the Id / Name or anything you want for the new attribs...
    //here is where you need to add a lot of stuff to change the id of your variables

    // The following code works without problems on Firefox or IE7
    $( "#cmbTabAuthor_"+ intCurrentRowId , clonedRow ).attr( { "id" :"cmbTabAuthor_" + intNewRowId, "accesskey" : intNewRowId } ).val("");
    $( "#tabCutter_"+ intCurrentRowId , clonedRow ).attr( { "id" :"tabCutter_" + intNewRowId, "accesskey" : intNewRowId } ).val('');

    // Add to the new row to the original table
    $( "#authorList" ).append( clonedRow );

    $('#cmbTabAuthor_' +intNewRowId).closest("td").find("#cmbTabAuthor_"+ intCurrentRowId+"-flexdatalist").remove();
    /**
     * Flexdatalist
     */
     $('#cmbTabAuthor_' +intNewRowId).flexdatalist({
        visibleProperties: ["name"],
        searchByWord: true,
        searchIn: 'name',
        minLength: 3,
        selectionRequired: true,
        valueProperty: 'id',
        url: path + '/lmm/lmmTitles/searchAuthor',
        noResultsText: vocab['no_result_found_for']+" {keyword}",
        requestType: 'post'
    });

    $('#cmbTabAuthor_' +intNewRowId).on('select:flexdatalist', function () {
        var element = $(this).attr('id'), aElement = element.split("_"), id = aElement[1];        
        objTitleData.loadCutter(id,$(this).val());
    });

    // And finally change the ID of the last row to something we can
    //delete later, not sure why can not be done before the append :S
    $( "#authorList tr:last" ).attr( "id", "authorItem_" + intNewRowId );

    $( "#cmbTabAuthor_"+ intNewRowId + "-flexdatalist" ).focus();
}

function removeAuthorRow(id,strTableName){
    var i = id.parentNode.parentNode.rowIndex;
    
    if($("#"+strTableName+" tbody tr").length == 1){
        modalAlertMultiple('info',vocab['Unable_to_Delete_Required_Items'],"alert-author-list");
    }else{
        document.getElementById(strTableName).deleteRow(i);
        reorderAuthorList();
    }
}

function duplicateCopyRow(){
    // First, lets create the new row using the last one as template...
    var clonedRow = $( "#copyList tr:last" ).clone();
    // Take the current identifier, some number in the first cell of the row
    intCurrentRowId = parseInt( $( "#copyNumId:last", clonedRow ).val() );    
    // Set the new ID
    intNewRowId = intCurrentRowId + 1;
    // Change the current identifier of the row to the new one
    $( "#copyNumId:last", clonedRow ).val( intNewRowId );

    // Change the Id / Name or anything you want for the new attribs...
    //here is where you need to add a lot of stuff to change the id of your variables

    // The following code works without problems on Firefox or IE7
    $( "#copyNum_"+ intCurrentRowId , clonedRow ).attr( { "id" :"copyNum_" + intNewRowId, "accesskey" : intNewRowId } ).val(intNewRowId);
    $( "#cmbLibrary_"+ intCurrentRowId , clonedRow ).attr( { "id" :"cmbLibrary_" + intNewRowId, "accesskey" : intNewRowId } ).removeAttr("data-select2-id","aria-hidden","tabindex").removeClass("select2-hidden-accessible");
    $( "#dtAcquisition_"+ intCurrentRowId , clonedRow ).attr( { "id" :"dtAcquisition_" + intNewRowId, "accesskey" : intNewRowId } ).val('');
    $( "#cmbOrigin_"+ intCurrentRowId , clonedRow ).attr( { "id" :"cmbOrigin_" + intNewRowId, "accesskey" : intNewRowId } ).removeAttr("data-select2-id","aria-hidden","tabindex").removeClass("select2-hidden-accessible");
    $( "#copyVol_"+ intCurrentRowId , clonedRow ).attr( { "id" :"copyVol_" + intNewRowId, "accesskey" : intNewRowId } ).val('');
    $( "#copyEdition_"+ intCurrentRowId , clonedRow ).attr( { "id" :"copyEdition_" + intNewRowId, "accesskey" : intNewRowId } ).val('');
    $( "#copyYear_"+ intCurrentRowId , clonedRow ).attr( { "id" :"copyYear_" + intNewRowId, "accesskey" : intNewRowId } ).val(''); 
    $( "#radioCell_"+ intCurrentRowId , clonedRow ).attr( { "id" :"radioCell_" + intNewRowId, "accesskey" : intNewRowId } );
       
    if($("#update-title-form").length > 0){
        $( "#idbookcopy_"+ intCurrentRowId , clonedRow ).attr( { "id" :"idbookcopy_" + intNewRowId, "accesskey" : intNewRowId } ).val('');
        $( ".register_"+ intCurrentRowId , clonedRow ).removeClass("register_"+ intCurrentRowId).addClass("register_"+ intNewRowId);
    }

    var checkedPrev = $("#copyList tbody tr input[type=radio]:checked");

    // Add to the new row to the original table
    $( "#copyList" ).append( clonedRow );
    
    $('#cmbLibrary_' +intNewRowId + ' + span').remove();
    $('#cmbLibrary_' +intNewRowId).select2({width:'100%',placeholder:vocab['Select'],allowClear:true});
    $('#cmbOrigin_' +intNewRowId + ' + span').remove();
    $('#cmbOrigin_' +intNewRowId).select2({width:'100%',placeholder:vocab['Select'],allowClear:true});
    $('#radioCell_' +intNewRowId).html("<input type='radio' name='hasCD["+intNewRowId+"]' id='hasCDYes_"+intNewRowId+"' value='Y' class='radio-inline copy-checks'> <label class='col-form-label'>"+vocab['Yes']+"</label>"+
    "&nbsp;&nbsp;"+
    "<input type='radio' name='hasCD["+intNewRowId+"]' id='hasCDNo_"+intNewRowId+"' value='N' class='radio-inline copy-checks' checked='checked'> <label class='col-form-label'>"+vocab['No']+"</label>");
    $('#copyYear_'+intNewRowId).mask('#0000');

    if($("#update-title-form").length > 0){
        $(".register_"+ intNewRowId).remove();
    }
    
    $('#hasCDYes_'+intNewRowId + ', #hasCDNo_'+intNewRowId).iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
    
    checkedPrev.each(function(){
        $(this).iCheck('check');
    });
    
    $('.input-group.date').datepicker(dpOptions);
    
    // And finally change the ID of the last row to something we can
    //delete later, not sure why can not be done before the append :S
    $( "#copyList tr:last" ).attr( "id", "copyItem_" + intNewRowId );
  
    $( "#cmbLibrary_"+ intNewRowId ).focus();
    
    $('.lbltooltip').tooltip({container:'body',trigger:'hover'});

} 

function removeCopyRow(id,strTableName){
    var i = id.parentNode.parentNode.rowIndex;
    $('.tooltip').hide();
    
    if($("#create-title-form").length>0){
        if($("#"+strTableName+" tbody tr").length == 1){
            modalAlertMultiple('warning',vocab['Unable_to_Delete_Required_Items'],"alert-copy-list");          
        }else{
            document.getElementById(strTableName).deleteRow(i);
            reorderCopyList();
        }   
    }else{        
        if($("#"+strTableName+" tbody tr").length == 1){
            modalAlertMultiple('warning',vocab['Unable_to_Delete_Required_Items'],"alert-copy-list");         
        }else if($("#idbookcopy_"+i).val()==""){
            $("#copyItem_"+i).remove();
            reorderCopyList();
        }else{           
            $("#modalCopyNum").val($("#idbookcopy_"+i).val());
            $("#lineToDel").val("copyItem_"+i);
            $("#modal-del-copy").modal('show');
        }       
    }
}

function validateFields(){
    var ret = [], tabautor=[], isAuthorEmpty=0, authorExists=0,
        isLibEmpty=0,isAdquisDtEmpty=0, isVolEmpty=0, isYearEmpty=0,msg;

    // check Authors items
    $("input[name='cmbTabAuthor[]']").each(function(index, element) {
        if($(this).val()==""){
            isAuthorEmpty=1;
        }else{   
            if(jQuery.inArray($(this).val(), tabautor) !== -1)
            {
                authorExists=1;                        
            }else{                
                tabautor.push($(this).val());
            } 
        }
        
    }); 
                
    if(isAuthorEmpty==1){
        ret.push([false],[vocab['The_author_field_is_empty']]);
        return ret;
    }

    if(authorExists==1){
        ret.push([false],[vocab['Duplicate_author']]);
        return ret;
    }

    // check adquisiton date items
    $("input[name='dtAcquisition[]']").each(function(index, element) {
        if($(this).val()==""){
            isAdquisDtEmpty=1;
        }           
    });

    if(isAdquisDtEmpty==1){
        msg = vocab['The_date_acquisition_field_is_empty'];
        ret.push([false],[msg]);
        return ret;
    }

    // check volume items
    $("input[name='copyVol[]']").each(function(index, element) {
        if($(this).val()==""){
            isVolEmpty=1;
        }           
    });

    if(isVolEmpty==1){  
        ret.push([false],[vocab['The_volume_field_is_empty']]);
        return ret;
    }

    // check copy year items
    $("input[name='copyYear[]']").each(function(index, element) {
        if($(this).val()==""){
            isYearEmpty=1;
        }            
    });

    if(isYearEmpty==1){ 
        ret.push([false],[vocab['The_year_field_is_empty']]);
        return ret;
    }

    ret.push([true],['']);
    return ret;
}

function reloadCmbTabAuthor(){
    $(".cmbTabAuthor").each(function(index, element) {
        var id = $(this).attr('id'), aID = id.split('_'), i = aID[1];
        objTitleData.loadAuthor(i);     
    });
}

function reorderAuthorList(){
    var i = 1;
    $("#authorList tbody tr").each(function(index, element) {
        var childs = $(this).children(), child, aChild;
        childs.each(function(index, element) {
            if( $(this).prop('tagName') == "INPUT"){    
                child = $(this).attr('id'); aChild = child.split('_');
                                            
                if(child == "authorNumId"){                                
                    $(this).val(i);                                 
                }
            }else{
                $(this).find('input,select').each(function(index, element) {
                    child = $(this).attr('id'); aChild = child.split('_');
                    var suffix = aChild[1].split('-');
                    console.log();
                    
                    if(suffix.length > 1){
                        $("#"+child).attr( { "id" : aChild[0]+"_"+i+"-"+suffix[1], "aria-owns" : aChild[0]+"_"+i+"-"+suffix[1]+"-results" });
                        $("#"+aChild[0]+"_"+i+"-"+suffix[1]).removeClass(child);
                        $("#"+aChild[0]+"_"+i+"-"+suffix[1]).addClass(aChild[0]+"_"+i+"-"+suffix[1]);
                    }else{
                        $("#"+child).attr( { "id" : aChild[0]+"_"+i, "accesskey" : i });
                    }
                });
            }                        
        });
        $(this).attr( { "id" : "authorItem_"+i, "accesskey" : i });
        i = i + 1;
    });
}

function reorderCopyList(){
    var i = 1;
    $("#copyList tbody tr").each(function(index, element) {
        var childs = $(this).children(), child, aChild;
        childs.each(function(index, element) {
            if($(this).prop('tagName') == "INPUT"){    
                child = $(this).attr('id'); aChild = child.split('_');
                                            
                if(child == "copyNumId"){                                
                    $(this).val(i);                                 
                }else if(aChild[0] == "idbookcopy"){ 
                    $("#"+child).attr( { "id" : "idbookcopy_"+i, "accesskey" : i });
                }
            }else{
                $(this).find('input,select,label,div').each(function(index, element) {
                    var itemParent = $(this).parent();
                    if($(this).prop('tagName') == "LABEL"){
                        child = $(this).attr('for'); 
                        if(typeof child !== 'undefined' && child !== false){
                            aChild = child.split('_');
                            $(this).attr( { "for" : aChild[0]+"_"+i, "accesskey" : i });
                            if(itemParent.hasClass("registerCell")){
                                itemParent.removeClass("register_"+aChild[1]).addClass("register_"+i)
                            }                                
                        }
                    }else if($(this).prop('tagName') == "DIV"){
                        child = $(this).attr('id'); 
                        if(typeof child !== 'undefined' && child !== false){
                            aChild = child.split('_');
                            $(this).attr( { "id" : aChild[0]+"_"+i, "accesskey" : i });
                        }
                    }else{
                        child = $(this).attr('id'); aChild = child.split('_');

                        $("#"+child).attr( { "id" : aChild[0]+"_"+i, "accesskey" : i });
                        if($(this).prop('tagName') == "SELECT"){
                            $("#"+child).removeAttr("data-select2-id","aria-hidden","tabindex").removeClass("select2-hidden-accessible");
                            $("#"+aChild[0]+"_"+i+" + span").remove();
                            $("#"+aChild[0]+"_"+i).select2({width:'100%',placeholder:vocab['Select'],allowClear:true});
                        }else if (aChild[0] == 'copyNum'){
                            $("#"+aChild[0]+"_"+i).val(i);
                        }else if (aChild[0] == 'hasCDYes' || aChild[0] == 'hasCDNo'){
                            $("#"+aChild[0]+"_"+i).attr({"name":"hasCD["+i+"]"});
                        }

                        if(itemParent.hasClass("registerCell")){
                            itemParent.removeClass("register_"+aChild[1]).addClass("register_"+i)
                            console.log("tem");
                        }
                    }                    
                });
            }                        
        });
        $(this).attr( { "id" : "copyItem_"+i, "accesskey" : i });
        i = i + 1;
    });
}
            