$(document).ready(function () {

    countdown.start(timesession);

    /*
     * Demo version
     */
    if (demoVersion == 1){
        if ($('#logindemo').val() == 'user' || $('#logindemo').val() == 'operator' ) {
            $('#btnUpdateTitles').prop('disabled', true);
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

   $('.ExemplaryYear').mask('0000');

    /*     
     *  Chosen
     */
   $("#edit").chosen({ width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
   $("#Color").chosen({ width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
   $("#classif").chosen({ width: "100%", no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
   $("#materialtype").chosen({ width: "100%", no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
   $("#Collection").chosen({ width: "100%", no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
   $("#CDD").chosen({ width: "100%", no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
   $("#Capacity").chosen({ width: "100%", no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
   $("#origin").chosen({ width: "100%", no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
   $("#tabAuthor").chosen({ width: "100%", no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
   $("#CDD").chosen({ width: "100%", no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
   $("#Library").chosen({ width: "100%", no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});



    var objProgramData = {
        changeAuthor: function() {
            $.post(path+"/lmm/lmmTitles/ajaxAtualAuthor",
                function(valor) {
                    $("#tabAuthor").html(valor);
                    $("#tabAuthor").trigger("chosen:updated");
                    return false;
                });
        },
        changeMaterialtype: function() {
            $.post(path+"/lmm/lmmTitles/ajaxAtualMaterialtype",
                function(valor) {
                    $("#Materialtype").html(valor);
                    $("#Materialtype").trigger("chosen:updated");
                    return false;
                });
        },
        changeCollection: function() {
            $.post(path+"/lmm/lmmTitles/ajaxAtualCollection",
                function(valor) {
                    $("#Collection").html(valor);
                    $("#Collection").trigger("chosen:updated");
                    return false;
                });
        },
        changePublishing: function() {
            $.post(path+"/lmm/lmmTitles/ajaxAtualPublishing",
                function(valor) {
                    $("#edit").html(valor);
                    $("#edit").trigger("chosen:updated");
                    return false;
                });
        },
        changeColor: function() {
            $.post(path+"/lmm/lmmTitles/ajaxAtualColor",
                function(valor) {
                    $("#Color").html(valor);
                    $("#Color").trigger("chosen:updated");
                    return false;
                });
        },

        changeClassification: function() {
            $.post(path+"/lmm/lmmTitles/ajaxAtualClassification",
                function(valor) {
                    $("#classif").html(valor);
                    $("#classif").trigger("chosen:updated");
                    return false;
                });
        },
        changeCDD: function() {
            $.post(path+"/lmm/lmmTitles/ajaxAtualCDD",
                function(valor) {
                    $("#CDD").html(valor);
                    $("#CDD").trigger("chosen:updated");
                    return false;
                });
        }

    }

    
    
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/lmm/lmmTitles/index');

    $("#btnAddPublishing_company").click(function(){
        $("#modal-form-publishing").modal('show');  
    });

    $("#btnAddColor").click(function(){
        $("#modal-form-color").modal('show');
    });

    $("#btnAddClassification").click(function(){
        $("#modal-form-classification").modal('show');
    });

    $("#btnAddMaterialtype").click(function(){
        $("#modal-form-materialtype").modal('show');
    });

    $("#btnAddCollection").click(function(){
        $("#modal-form-collection").modal('show');
    });

    $("#btnAddAuthor").click(function(){
        $("#modal-form-author").modal('show');
    });

    $("#btnAddCDD").click(function(){
        $("#modal-form-cdd").modal('show');
    });

    $("#btnSendPublishing").click(function(){
        console.log('clicou salvar');
        if (!$("#publishing-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/lmm/lmmPublishing/createPublishing',
            dataType: 'json',
            data:{_token:$('#_token').val(), 
            publishing:$('#publishing').val(),},
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-module');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if(obj.success) {
                    modalAlertMultiple('success',aLang['Alert_inserted'].replace (/\"/g, ""),'alert-module');
                    objProgramData.changePublishing();
                    setTimeout(function(){
                        $('#modal-form-publishing').modal('hide');
                    },2000);
                }else{
                    modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-module');
                }
            }
        });

    });

    $("#btnSendColor").click(function(){
        console.log('clicou salvar');
        if (!$("#color-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/lmm/lmmColor/createColor',
            dataType: 'json',
            data:{_token:$('#_token').val(), 
            color:$('#color').val(),
            cityDefault: $("input[name=cityDefault]:checked").length > 0 ? "Y" : "N" },
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-color');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if(obj.success) {
                    modalAlertMultiple('success',aLang['Alert_inserted'].replace (/\"/g, ""),'alert-color');
                    objProgramData.changeColor();
                    setTimeout(function(){
                        $('#modal-form-color').modal('hide');
                    },2000);
                }else{
                    modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-color');
                }
            }
        });
        
    });

    $("#btnSendClassification").click(function(){
        console.log('clicou salvar');
        if (!$("#classification-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/lmm/lmmClassification/createClassification',
            dataType: 'json',
            data:{_token:$('#_token').val(), 
            classification:$('#classification').val(), 
            cityDefault: $("input[name=cityDefault]:checked").length > 0 ? "Y" : "N" },
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-classification');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if(obj.success) {
                    modalAlertMultiple('success',aLang['Alert_inserted'].replace (/\"/g, ""),'alert-classification');
                    objProgramData.changeClassification();
                    setTimeout(function(){
                        $('#modal-form-classification').modal('hide');
                    },2000);
                }else{
                    modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-classification');
                }
            }
        });
        
    });

    $("#btnSendMaterialtype").click(function(){
        console.log('clicou salvar');
        if (!$("#materialtype-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/lmm/lmmMaterialtype/createMaterialtype',
            dataType: 'json',
            data:{_token:$('#_token').val(), 
            nome:$('#nome').val(),},    
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-materialtype');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if(obj.success) {
                    modalAlertMultiple('success',aLang['Alert_inserted'].replace (/\"/g, ""),'alert-materialtype');
                    objProgramData.changeMaterialtype();
                    setTimeout(function(){
                        $('#modal-form-materialtype').modal('hide');
                    },2000);
                }else{
                    modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-materialtype');
                }
            }
        });
        
    });

    $("#btnSendCollection").click(function(){
        console.log('clicou salvar');
        if (!$("#collection-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/lmm/lmmCollection/createCollection',
            dataType: 'json',
            data:{_token:$('#_token').val(), 
            collection:$('#collection').val(),},        
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-collection');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if(obj.success) {
                    modalAlertMultiple('success',aLang['Alert_inserted'].replace (/\"/g, ""),'alert-collection');
                    objProgramData.changeCollection();
                    setTimeout(function(){
                        $('#modal-form-collection').modal('hide');
                    },2000);
                }else{
                    modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-collection');
                }
            }
        });
        
    });

    $("#btnSendCDD").click(function(){
        console.log('clicou salvar');
        if (!$("#cdd-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/lmm/lmmCDD/createCDD',
            dataType: 'json',
            data:{_token:$('#_token').val(), 
            cdd:$('#cdd').val(),
            descr:$('#descr').val(),},        
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-cdd');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if(obj.success) {
                    modalAlertMultiple('success',aLang['Alert_inserted'].replace (/\"/g, ""),'alert-cdd');
                    objProgramData.changeCollection();
                    setTimeout(function(){
                        $('#modal-form-cdd').modal('hide');
                    },2000);
                }else{
                    modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-collection');
                }
            }
        });
        
    });


    $("#btnAdd_exemplary").click(function(){
        duplicateRowExemplares("tab-1")
    });

    $("#btnAdd_author").click(function(){
        duplicateRowAutores("tab-2")
    });


    $("#btnSendAuthor").click(function(){
        console.log('clicou salvar');
        if (!$("#author-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/lmm/lmmAuthor/createAuthor',
            dataType: 'json',
            data:{_token:$('#_token').val(), 
            author:$('#author').val(),
            cutter:$('#cutter').val(),},
            //cityDefault: $("input[name=cityDefault]:checked").length > 0 ? "Y" : "N" },
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-author');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if(obj.success) {
                    modalAlertMultiple('success',aLang['Alert_inserted'].replace (/\"/g, ""),'alert-author');
                    objProgramData.changeAuthor();
                    setTimeout(function(){
                        $('#modal-form-author').modal('hide');
                    },2000);
                }else{
                    modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-author');
                }
            }
        });
        
    });   



    $("#tabAuthor1").change(function(){
        $.post(path+"/lmm/lmmTitles/ajaxcutter",{idauthor:$("#tabAuthor1").val()},
                function(valor) {
                    $("#tabcutter1").val(valor);                                 
                    return false;
                });
    });
         

    $("#tabAuthor1").change();


    $("#btnSave").click(function(){
        if (!$("#create-titles-form").valid()) {            
            modalAlertMultiple('danger',makeSmartyLabel('Fill_required_field'),'alert-titles-create');
           // showAlert(makeSmartyLabel('Fill_required_field'),'warning','');
            return false ;
        }
       var tabautor=[],flag=0, flag1=0;
        $("select[name='tabAuthor[]']").each(function(index, element) {
            if($(this).val()==""){
                flag1=1;
            }else{   
                if(jQuery.inArray($(this).val(), tabautor) !== -1)
                    {
                        flag=1;                        
                    }else{                
                    tabautor.push($(this).val());
                } 
            }
            
        }); 
                 
       if(flag==1){           
            modalAlertMultiple('danger',makeSmartyLabel('Duplicate_author'),'alert-titles-create');
            return false;
       }

       if(flag1==1){          
            modalAlertMultiple('danger',makeSmartyLabel('The_author_field_is_empty'),'alert-titles-create');
            return false;
        }

        var dataaquis=[], flag=0;
        $("input[name='aquis[]']").each(function(index, element) {
            if($(this).val()==""){
                flag=1;
            }else{                
                dataaquis.push($(this).val());                  
            }            
        });

        if(flag==1){           
            modalAlertMultiple('danger',makeSmartyLabel('The_date_acquisition_field_is_empty'),'alert-titles-create');
            return false;
        }

        var vol=[], flag=0;
        $("input[name='Volume[]']").each(function(index, element) {
            if($(this).val()==""){
                flag=1;
            }else{                
                vol.push($(this).val());                  
            }            
        });

        if(flag==1){           
            modalAlertMultiple('danger',makeSmartyLabel('The_volume_field_is_empty'),'alert-titles-create');
            return false;
        }

        var tabyear=[], flag=0;
        $("input[name='Year[]']").each(function(index, element) {
            if($(this).val()==""){
                flag=1;
            }else{                
                tabyear.push($(this).val());                  
            }            
        });

        if(flag==1){           
            modalAlertMultiple('danger',makeSmartyLabel('The_year_field_is_empty'),'alert-titles-create');
            return false;
        }
               
        if(!$("#btnSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lmm/lmmTitles/createTitles',
                dataType: 'json',
                data: $('#create-titles-form').serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-titles-create');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/lmm/lmmTitles/index');                        
                    }else{
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-titles-create');
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
        if (!$("#update-titles-form").valid()) {        
            modalAlertMultiple('danger',makeSmartyLabel('Fill_required_field'),'alert-titles-update');
            // showAlert(makeSmartyLabel('Fill_required_field'),'warning','');
             return false ;
        }
        var tabautor=[],flag=0, flag1=0;
        $("select[name='tabAuthor[]']").each(function(index, element) {
            if($(this).val()==""){
                flag1=1;
            }else{   
                if(jQuery.inArray($(this).val(), tabautor) !== -1)
                    {
                        flag=1;                        
                    }else{                
                    tabautor.push($(this).val());
                } 
            }
            
        }); 
                 
        if(flag==1){           
            modalAlertMultiple('danger',makeSmartyLabel('Duplicate_author'),'alert-titles-update');
            return false;
        }

        if(flag1==1){           
        modalAlertMultiple('danger',makeSmartyLabel('The_author_field_is_empty'),'alert-titles-update');
        return false;
        }

        var dataaquis=[], flag=0;
        $("input[name='aquis[]']").each(function(index, element) {
            if($(this).val()==""){
                flag=1;
            }else{                
                dataaquis.push($(this).val());                  
            }            
        });

        if(flag==1){           
            modalAlertMultiple('danger',makeSmartyLabel('The_date_acquisition_field_is_empty'),'alert-titles-update');
            return false;
        }

        var vol=[], flag=0;
        $("input[name='Volume[]']").each(function(index, element) {
            if($(this).val()==""){
                flag=1;
            }else{                
                vol.push($(this).val());                  
            }            
        });

        if(flag==1){           
            modalAlertMultiple('danger',makeSmartyLabel('The_volume_field_is_empty'),'alert-titles-update');
            return false;
        }

        var tabyear=[], flag=0;
        $("input[name='Year[]']").each(function(index, element) {
            if($(this).val()==""){
                flag=1;
            }else{                
                tabyear.push($(this).val());                  
            }            
        });

        if(flag==1){           
            modalAlertMultiple('danger',makeSmartyLabel('The_year_field_is_empty'),'alert-titles-update');
            return false;
        }


        if(!$("#btnSaveUpdate").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lmm/lmmTitles/updateTitles',
                dataType: 'json',
                data: $("#update-titles-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-titles-update');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/lmm/lmmTitles/index');
                    }else{
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-titles-update');
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
    * Validate  Create Titles
    */
    $("#create-titles-form").validate({
        ignore:[],
        rules: {
            titles: {
                required:true,                    
    
            },
                
            ISBN:{
                required:true,
            
            }

        },
        messages: {
            titles: {required:makeSmartyLabel('Alert_field_required')},
            ISBN: {required:makeSmartyLabel('Alert_field_required')},        
              
        }
    });

      /*
    * Validate Update Titles
    */
    $("#update-titles-form").validate({
        ignore:[],
        rules: {
            titles: {
                required:true,                

            },
            
            ISBN:{
                required:true,
        
            }
        },
        messages: {
            titles: {required:makeSmartyLabel('Alert_field_required')},
            ISBN: {required:makeSmartyLabel('Alert_field_required')},
          
        }
    });



    /*
    * Validate Publishing Company
    */
    $("#publishing-form").validate({
        ignore:[],
        rules: {
            publishing: {
                required:true,
                minlength: 5,
                remote:{
                    url: path+'/lmm/lmmPublishing/existPublishing',
                    type: 'post',
                    dataType:'json',
                    async: false
                }
            }
    
            
        },
        messages: {
            publishing: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_five_characters')},
            
        
        }
    });

    $("input[name='col']").click(function(){ 
       // console.log("teste");
        if($(this).val()=='Y'){
            $("#Collectionline").removeClass("hide");
        }else{
            $("#Collectionline").addClass("hide");
        }
    
    });

    /*
    * Validate Color
    */
    $("#color-form").validate({
        ignore:[],
        rules: {
            color: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+'/lmm/lmmColor/existColor',
                    type: 'post',
                    dataType:'json',
                    async: false
                }
            }    
            
        },
        messages: {
            color: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_three_characters')},
            
        
        }
    });


    /*
     * Validate Classification
     */
    $("#classification-form").validate({
        ignore:[],
        rules: {
            classification: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+'/lmm/lmmClassification/existClassification',
                    type: 'post',
                    dataType:'json',
                    async: false
                }
            }

    
            
        },
        messages: {
            classification: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_three_characters')},
            
        
        }
    
    });

    /*
     * Validate Material type
     */
    $("#materialtype-form").validate({
        ignore:[],
        rules: {
            nome: {
                required:true,
                minlength: 5,
                remote:{
                    url: path+'/lmm/lmmMaterialType/existMaterialtype',
                    type: 'post',
                    dataType:'json',
                    async: false
                }

            }
            
        },
        messages: {
            nome: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_five_characters')},
        
        }
    });

     /*
     * Validate Collection
     */
     $("#collection-form").validate({
        ignore:[],
        rules: {
            collection: {
                required:true,
                minlength: 5,
                remote:{
                    url: path+'/lmm/lmmCollection/existCollection',
                    type: 'post',
                    dataType:'json',
                    async: false
                }
            }
    
            
        },
        messages: {
            collection: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_five_characters')},
            
        
        }
    });

     /*
     * Validate Author
     */
     $("#author-form").validate({
        ignore:[],
        rules: {
            author: {
                required:true,
                minlength: 5,
                remote:{
                    url: path+'/lmm/lmmAuthor/existAuthor',
                    type: 'post',
                    dataType:'json',
                    async: false
                }

            },
            cutter: {
                required:true,
                minlength:3,
            }
            
        },
        messages: {
            author: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_five_characters')},
            cutter: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_three_characters')},
        
        }
    });

    /*
    * Validate CDD
    */
    $("#cdd-form").validate({
        ignore:[],
        rules: {
            cdd: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+'/lmm/lmmCDD/existCDD',
                    type: 'post',
                    dataType:'json',
                    async: false
                }
            }    
            
        },
        messages: {
            cdd: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_three_characters')},
            
        
        }
    });
    $('.lbltooltip').tooltip();


});


function duplicateRowExemplares( strTableName ){
    // First, lets create the new row using the last one as template...
    var clonedRow = $( "#" + strTableName + " .exemplaryform:last" ).clone();
    // Take the current identifier, some number in the first cell of the row
    intCurrentRowId = parseInt( $( "#numId:last", clonedRow ).val() );
    // Set the new ID
    intNewRowId = intCurrentRowId + 1;
    // Change the current identifier of the row to the new one
    $( "#numId:last", clonedRow ).val( intNewRowId );

    // Change the Id / Name or anything you want for the new attribs...
    //here is where you need to add a lot of stuff to change the id of your variables

    // The following code works without problems on Firefox or IE7
    $( "#Exemplary"+ intCurrentRowId , clonedRow ).attr( { "id" :"Exemplary" + intNewRowId, "accesskey" : intNewRowId } ).val(intNewRowId);
    $( "#Library"+ intCurrentRowId , clonedRow ).attr( { "id" :"Library" + intNewRowId, "accesskey" : intNewRowId } );
    $( "#aquis"+ intCurrentRowId , clonedRow ).attr( { "id" :"aquis" + intNewRowId, "accesskey" : intNewRowId } );
    $( "#origin"+ intCurrentRowId , clonedRow ).attr( { "id" :"origin" + intNewRowId, "accesskey" : intNewRowId } );
    $( "#Volume"+ intCurrentRowId , clonedRow ).attr( { "id" :"Volume" + intNewRowId, "accesskey" : intNewRowId } );
    $( "#Edition"+ intCurrentRowId , clonedRow ).attr( { "id" :"Edition" + intNewRowId, "accesskey" : intNewRowId } );
    $( "#Year"+ intCurrentRowId , clonedRow ).attr( { "id" :"Year" + intNewRowId, "accesskey" : intNewRowId } ); 
    $( "#sim"+ intCurrentRowId , clonedRow ).attr( { "id" :"sim" + intNewRowId,"name":"hascd["+intNewRowId+"]","checked" :"", "accesskey" : intNewRowId } );
    $( "#nao"+ intCurrentRowId , clonedRow ).attr( { "id" :"nao" + intNewRowId,"name":"hascd["+intNewRowId+"]","checked" :"checked", "accesskey" : intNewRowId } );
    $( "#btndelbook"+ intCurrentRowId , clonedRow ).attr( { "id" :"btndelbook" + intNewRowId, "accesskey" : intNewRowId } );
    
       

    // Add to the new row to the original table
    $( "#" + strTableName +" .exemplaryform:last" ).after( clonedRow );
    

    // And finally change the ID of the last row to something we can
    //delete later, not sure why can not be done before the append :S
    $( "#" + strTableName + " .exemplaryform:last" ).attr( "id", "detailsTr" + intNewRowId );

  
    $( "#Library"+ intNewRowId ).focus();
    $('.lbltooltip').tooltip();

} 


function removeRow(id,strTableName,ope='add'){
    var i = $(id).attr('id'),nj=i.slice(-1), msgDiv;    
    
    if(ope == 'upd')
        msgDiv = 'alert-titles-update';
    else
        msgDiv = 'alert-titles-create';       

    if($("#"+strTableName+ " .exemplaryform").length == 1){
        modalAlertMultiple('info', makeSmartyLabel('Unable_to_delete'),msgDiv);
    }else{
        $("#detailsTr"+nj).remove();
        reordenarex();     
       
    }
}


function reordenarex(){
    var i=1;
    $(".exemplaryform").each(function(index, element) {               
        var child = $(this).children();        
        
        child.each(function(idx,e){
            if($(this).attr('id') == "numId"){
                $(this).val(i);
                
            }else{
                var totalelem = $(this).find('input,select').length;
                if(totalelem > 1){
                    $(this).find('input,select').each(function(index, element) {
                    var elem = $(this).attr('id'); 
                    if(elem != undefined){
                        var nj=elem.slice(0,-1);
                        if(nj == "Exemplary")
                            ("#"+elem).val(i);        
                            $("#"+elem).attr( { "id" : nj+i, "accesskey" : i });              
                        }
                    });
                }else{
                    var elem = $(this).find('input,select').attr('id');
                    if(elem != undefined){
                        var nj=elem.slice(0,-1);
                        if(nj == "Exemplary")
                            $("#"+elem).val(i);
    
                        $("#"+elem).attr( { "id" : nj+i, "accesskey" : i });
                                  
                    }
                }                
            }
        });   

        $(this).attr( { "id" : "detailsTr"+i, "accesskey" : i });
        i = i + 1;   
        
    });
        
}




function duplicateRowAutores( strTableName ){
    // First, lets create the new row using the last one as template...
    var clonedRow = $( "#" + strTableName + " .authorform:last" ).clone();
    // Take the current identifier, some number in the first cell of the row
    intCurrentRowId = parseInt($( "#nummId:last",clonedRow ).val());
    // Set the new ID
    intNewRowId = intCurrentRowId + 1;
    // Change the current identifier of the row to the new one
    $( "#nummId:last", clonedRow ).val( intNewRowId );

    // Change the Id / Name or anything you want for the new attribs...
    //here is where you need to add a lot of stuff to change the id of your variables

    // The following code works without problems on Firefox or IE7
    if($("#update-baixaproduto-form").length > 0){
        $( "#iditembaixas_"+ intCurrentRowId , clonedRow ).attr( { "id" :"iditembaixas_" + intNewRowId, "accesskey" : intNewRowId } );
    }
    $( "#Author"+ intCurrentRowId , clonedRow ).attr( { "id" :"Author" + intNewRowId, "accesskey" : intNewRowId } ).val(intNewRowId);
    $( "#tabAuthor"+ intCurrentRowId , clonedRow ).attr( { "id" :"tabAuthor" + intNewRowId, "accesskey" : intNewRowId } );
    $( "#tabcutter"+ intCurrentRowId , clonedRow ).attr( { "id" :"tabcutter" + intNewRowId, "accesskey" : intNewRowId } );
    $( "#btndelauthor"+ intCurrentRowId , clonedRow ).attr( { "id" :"btndelauthor" + intNewRowId, "accesskey" : intNewRowId } );
    
      

    // Add to the new row to the original table
    $( "#" + strTableName +" .authorform:last" ).after( clonedRow );  


   $.post(path+"/lmm/lmmTitles/ajaxcutter",{idauthor:$("#tabAuthor"+ intNewRowId).val()},
   function(valor) {
       $("#tabcutter"+ intNewRowId).val(valor);                                 
       return false;
   });

     

    // And finally change the ID of the last row to something we can
    //delete later, not sure why can not be done before the append :S
    $( "#" + strTableName + " .authorform:last" ).attr( "id", "detailsAuthor" + intNewRowId );
    $("#detailsTr" + intNewRowId +" #quantidades_"+intCurrentRowId+"-error").remove();


    $( "#tabAuthor"+ intNewRowId ).focus();
    $('.lbltooltip').tooltip();
}

function delRow(id,strTableName,ope='add'){
    var i = $(id).attr('id'),nj=i.slice(-1), msgDiv; 

    
    if(ope == 'upd')
        msgDiv = 'alert-titles-update';
       
    else
        msgDiv = 'alert-titles-create';

    if($("#"+strTableName+ " .authorform").length == 1){
        modalAlertMultiple('info', makeSmartyLabel('Unable_to_delete'),msgDiv);
    }else{
        $("#detailsAuthor"+nj).remove(); 
        reordenarauthor();     
    }
    
      
}


function reordenarauthor(){
    var i=1;
    $(".authorform").each(function(index, element) {                
        var child = $(this).children();        
        
        child.each(function(idx,e){
            if($(this).attr('id') == "nummId"){
                $(this).val(i);
                
            }else{
                var totalelem = $(this).find('input,select').length;
                if(totalelem > 1){
                    $(this).find('input,select').each(function(index, element) {
                        var elem = $(this).attr('id');
                        if(elem != undefined){
                            var nj=elem.slice(0,-1);
                            if(nj == "Author")
                                $("#"+elem).val(i);
        
                            $("#"+elem).attr( { "id" : nj+i, "accesskey" : i });              
                        }
                    });
                }else{
                    var elem = $(this).find('input,select').attr('id');
                    if(elem != undefined){
                        var nj=elem.slice(0,-1);
                        if(nj == "Author")
                            $("#"+elem).val(i);
    
                        $("#"+elem).attr( { "id" : nj+i, "accesskey" : i });              
                    }
                }                
            }
        });   

        $(this).attr( { "id" : "detailsAuthor"+i, "accesskey" : i });
        i = i + 1;   
        
    });
        
}


function loadcutter(i){
    var n = $(i).attr('id'),ni=n.slice(-1);
    $.post(path+"/lmm/lmmTitles/ajaxcutter",{idauthor:$("#tabAuthor"+ ni).val()},
                function(valor) {
                    $("#tabcutter"+ ni).val(valor);                                 
                    return false;
                });    
    
    console.log(ni);
}

function addauthor(j){
     var n = $(j).attr('id'),nj=n.slice(-1);
     $("#modal-form-author").modal('show');    
   
    console.log(nj);
}




