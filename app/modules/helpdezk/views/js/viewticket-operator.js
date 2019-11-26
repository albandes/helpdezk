var idRequest = 0;
$(document).ready(function () {

    countdown.start(timesession);

    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('[data-toggle="tooltip"]').tooltip();

    /*
     *  Chosen
     */
    $("#area").chosen({ width: "100%",      no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#cmbType").chosen({ width: "100%",    no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#priority").chosen({ width: "100%",    no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#item").chosen({ width: "100%",    no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#way").chosen({ width: "100%", no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#service").chosen({ width: "100%", no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#reason").chosen({ width: "100%", no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#typehour").chosen({ width: "100%", no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#typenote").chosen({ width: "100%", no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#cmbAuxOpe").chosen({ width: "100%", no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#replist").chosen({ width: "100%", no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#cmbOpeGroups").chosen({ width: "100%", no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#cmbAssumeGroups").chosen({ width: "100%", no_results_text: "Nada encontrado!", disable_search_threshold: 10});

    var objViewTicket = {
        changeArea: function() {
            var areaId = $("#area").val();
            $.post(path+"/helpdezk/hdkTicket/ajaxTypes",{areaId: areaId},
                function(valor){
                    $('#cmbType').removeAttr('disabled');
                    $("#cmbType").html(valor);
                    $("#cmbType").trigger("chosen:updated");
                    return objViewTicket.changeItem();
                })
        },
        changeItem: function(){
            var typeId = $("#cmbType").val();
            $.post(path+"/helpdezk/hdkTicket/ajaxItens",{typeId: typeId},
                function(valor){
                    $('#item').removeAttr('disabled');
                    $("#item").html(valor);
                    $("#item").trigger("chosen:updated");
                    return objViewTicket.changeService();
                })
        },
        changeService: function(){
            var itemId = $("#item").val();
            console.log(itemId);
            $.post(path+"/helpdezk/hdkTicket/ajaxServices",{itemId: itemId},
                function(valor){
                    $('#service').removeAttr('disabled');
                    $("#service").html(valor);
                    $("#service").trigger("chosen:updated");
                    return objViewTicket.changeReason();
                })
        },
        changeReason: function(){
            var serviceId = $("#service").val();
            console.log(serviceId);
            $.post(path+"/helpdezk/hdkTicket/ajaxReasons",{serviceId: serviceId},
                function(valor){
                    $('#reason').removeAttr('disabled');
                    $("#reason").html(valor);
                    $("#reason").trigger("chosen:updated");
                })
        },
        insertAuxOpe: function(){
            var auxopeId = $("#cmbAuxOpe").val(), code_request = $('#coderequest').val();

            $.post(path+"/helpdezk/hdkTicket/insertAuxOperator",{code_request: code_request,auxopeid: auxopeId},
                function(valor){
                    var obj = jQuery.parseJSON(JSON.stringify(valor));
                    $('#cmbAuxOpe').html(obj.cmblist);
                    $("#cmbAuxOpe").trigger("chosen:updated");

                    if(obj.tablelist){
                        $("#tablelist").html(obj.tablelist);
                        if($("#auxopelist").hasClass("hide")){
                            $("#auxopelist").removeClass("hide");
                        }
                        $("#auxopediv").html(obj.auxopelist);
                    }else{
                        if(!$("#auxopelist").hasClass("hide")){
                            $("#auxopelist").addClass("hide");
                        }
                    }
                },'json');
        },
        loadRepassList: function(){
            var repassType = $("input[name='typerep']:checked").val();

            $.post(path+"/helpdezk/hdkTicket/ajaxRepassList",{typerep: repassType},
                function(valor){
                    $("#replist").html(valor);
                    $("#replist").trigger("chosen:updated");
                    if(!$("#btnAbilities").hasClass("off")){
                        objViewTicket.getAbilities();
                    }
                    else if(!$("#btnGroups").hasClass("off")){
                        objViewTicket.getGroups();
                    }
                });
        },
        getAbilities: function(){
            var valType = $("input[name='typerep']:checked").val(),
                rep = $("#replist").val(),
                $tabAbiGrp = $("#tabAbiGrp"),
                titleAbiGrp =$("#titleAbiGrp");
            $tabAbiGrp.html('');

            $.post(path+"/helpdezk/hdkTicket/ajaxAbilitiesList", {type: valType, rep: rep}, function(data){
                $tabAbiGrp.html('');
                titleAbiGrp.html(aLang['Related_abilities'].replace (/\"/g, ""));

                if(data){
                    $tabAbiGrp.html(data);
                }else{
                    $tabAbiGrp.html('<div class="panel-body">'+aLang['No_abilities'].replace (/\"/g, "")+'</div>');
                }
            });
        },
        getGroups: function(){
            var valType = $("input[name=typerep]:checked").val(),
                rep = $("#replist").val(),
                $tabAbiGrp = $("#tabAbiGrp"),
                titleAbiGrp =$("#titleAbiGrp");
            $tabAbiGrp.html('');

            $.post(path+"/helpdezk/hdkTicket/ajaxgroupsList", {type: valType, rep: rep}, function(data){
                $tabAbiGrp.html('');
                if(valType == "operator")
                    titleAbiGrp.html(aLang['Operator_groups'].replace (/\"/g, ""));
                else
                    titleAbiGrp.html(aLang['Group_operators'].replace (/\"/g, ""));

                if(data){
                    $tabAbiGrp.html(data);
                }else{
                    $tabAbiGrp.html('<div class="panel-body">'+aLang['No_data'].replace (/\"/g, "")+'</div>');
                }
            });
        },
        reloadAttWay: function(){
            $.post(path+"/helpdezk/hdkTicket/ajaxAttWay",
                function(valor){
                    $("#way").html(valor);
                    $("#way").trigger("chosen:updated");
                })
        },
    }

    $("#area").change(function(){
        objViewTicket.changeArea();
    });

    $("#cmbType").change(function(){
        objViewTicket.changeItem();
    });

    $("#item").change(function(){
        objViewTicket.changeService();
    });

    $("#cmbAuxOpe").change(function(){
        objViewTicket.insertAuxOpe();
    });

    // https://stackoverflow.com/questions/31519812/what-about-dropzone-js-within-an-existing-form-submitted-by-ajax
    $('#btnSendNote').click(function(e) {

        e.preventDefault();

        if(!$("#btnSendNote").hasClass('disabled')){
            if (emptynote == 0 && $('#requestnote').summernote('isEmpty')) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_empty_note'),'alert-noteadd');
                return false;
            }

            if (obtime == 1 && ( $("#totalminutes").val() == 0 || $("#totalminutes").val() == '')) {
                modalAlertMultiple('danger',makeSmartyLabel('Obrigatory_time'),'alert-noteadd');
                return false;
            }

            if ($("#callback").is(":checked")) var callback = '1';
            else var callback = '0';

            $.ajax({
                type: "POST",
                url: path + "/helpdezk/hdkTicket/saveNote",
                data: {
                    noteContent: $('#requestnote').summernote('code'),
                    code_request: $('#coderequest').val(),
                    totalminutes: $("#totalminutes").val(),
                    starthour: $("#started").val(),
                    finishour: $("#finished").val(),
                    execDate: $("#execdate").val(),
                    typehour: $("#typehour").val(),
                    typeNote: $("#typenote").val(),
                    flagNote: 3

                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
                },
                success: function(ret) {

                    console.log('ajax saveNote, return: ' + ret);

                    if($.isNumeric(ret)) {
                        idNote = ret ;
                        console.log('idnote: ' + idNote);
                        if (myDropzone.getQueuedFiles().length > 0) {
                            console.log('tem '+ myDropzone.getQueuedFiles().length + ' arquivos');
                            myDropzone.options.params = {idNote: idNote };
                            myDropzone.processQueue();
                        } else {
                            console.log('No files, no dropzone processing');
                            //sendNotification('addnote',$('#coderequest').val(),false);
                            console.log('Sent email, without attachments');
                            // clear summernote
                            $('#requestnote').summernote('code','');
                            showNotes( $('#coderequest').val());
                            modalAlertMultiple('info',makeSmartyLabel('Alert_note_sucess'),'alert-noteadd');
                        }
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
                    }
                },
                beforeSend: function(){
                    $("#btnSendNote").html("<i class='fa fa-spinner fa-spin' aria-hidden='true'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                },
                complete: function(){
                    $("#btnSendNote").html("<i class='fa fa-paper-plane' aria-hidden='true'></i> "+ makeSmartyLabel('Send')).removeClass('disabled');
                }
            });
        }

        return false;  // <- cancel event

    });

    if($('#idstatus').val() == 3  && $('#myDropzone').length > 0 ) {
        Dropzone.autoDiscover = false;
        var myDropzone = new Dropzone("#myDropzone", {
            url: path + "/helpdezk/hdkTicket/saveNoteAttach/",
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
            dictRemoveFile: "<span class='text-danger'>" + makeSmartyLabel('hdk_remove_file') + "</span>"
        });


        myDropzone.on("maxfilesexceeded", function(file) {
            this.removeFile(file);
        });

        myDropzone.on("queuecomplete", function (file) {        // https://stackoverflow.com/questions/18765183/how-do-i-refresh-the-page-after-dropzone-js-upload
            console.log('Completed the dropzone queue');
            //sendNotification('addnote',$('#coderequest').val(),true);
            console.log('Sent email, with attachments');
            // clear summernote and dropzone
            $('#requestnote').summernote('code','');
            myDropzone.removeAllFiles(true);
            showNotes( $('#coderequest').val());
            modalAlertMultiple('info',makeSmartyLabel('Alert_note_sucess'),'alert-noteadd');
        });


    }

    $('#button-reload').click(function() {
        location.reload();
    });

    $('#summernote').summernote(
        {
            toolbar:[
                ["view",[]],
                ["help",[]]
            ],
            disableDragAndDrop: true,
            minHeight: null,
            maxHeight: 250,
            height: 250,
            focus: false
        }
    );

    $('#requestnote').summernote(
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
            width: 750,       // set editor width
            focus: false,     // set focus to editable area after initializing summernote
            placeholder:  makeSmartyLabel('Editor_Placeholder_insert')

        }
    );

    $('#summernote').next().find(".note-editable").attr("contenteditable", false);

    $('#reasonchangeexpire').summernote(
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
            focus: false,     // set focus to editable area after initializing summernote
            placeholder:  makeSmartyLabel('Editor_Placeholder_reason')

        }
    );

    $('#reasonreject').summernote(
        {
            toolbar:[
                ["font",["bold","italic","underline","clear"]],
                ["insert",["link"]]
            ],
            disableDragAndDrop: true,
            minHeight: null,  // set minimum height of editor
            maxHeight: 250,   // set maximum height of editor
            height: 250,      // set editor height
            focus: false,     // set focus to editable area after initializing summernote
            placeholder:  makeSmartyLabel('Editor_Placeholder_reason')

        }
    );

    $("#btnAddWay").click(function(){
        $('#modal-form-attway').modal('show');
    });

    $('#btnSendAttWay').click(function() {
        var txtattway = $('#attWay').val();
        
        if (!$("#attway-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkTicket/saveNewAttWay',
            dataType: 'json',
            data: {txtattway: txtattway},
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-attway-form');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status === "OK") {
                    modalAlertMultiple('success',makeSmartyLabel('Alert_inserted'),'alert-attway-form');
                    objViewTicket.reloadAttWay();
                    setTimeout(function(){
                        $('#modal-form-attway').modal('hide');
                        $('#attway-form').trigger('reset');
                    },2000);
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-attway-form');
                }

            },
            beforeSend: function(){
                $("#btnSendAttWay").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSendAttWay").removeAttr('disabled');
            }

        });
        return false;  // <- cancel event
    });

    $("#btnChangeExpireDate").click(function(){
        $('#modal-form-changeexpire').modal('show');
    });

    $("#btnSendChangeExpireDate").click(function(){
        var code_request = $("#coderequest").val(),
            dateChangeExpire = $("#dateChangeExpire").val(),
            timeChangeExpire = $("#requesttime").val(),
            reason = $('#reasonchangeexpire').summernote('code');

        if ($('#reasonchangeexpire').summernote('isEmpty')) {
            modalAlertMultiple('danger',makeSmartyLabel('Alert_empty_reason'),'alert-changeexpire-form');
            return false;
        }

        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkTicket/changeExpireDate',
            dataType: 'json',
            data: {
                code_request: code_request,
                dateChangeExpire: dateChangeExpire,
                timeChangeExpire: timeChangeExpire,
                reason: reason
            },
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-changeexpire-form');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status === "OK") {
                    modalAlertMultiple('success',makeSmartyLabel('Expire_date_sucess'),'alert-changeexpire-form');
                    setTimeout(function(){
                        $('#txtExpireDate').html(obj.newdate);
                        $('#lblExpDate').html(obj.newmod_date);
                        $('#lblExpHour').html(obj.newmod_hour);
                        $('#modal-form-changeexpire').modal('hide');
                        $('#changeexpire-form').trigger('reset');
                        $('#reasonchangeexpire').summernote('reset');

                    },2000);
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-changeexpire-form');
                }

            },
            beforeSend: function(){
                $("#btnSendChangeExpireDate").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSendChangeExpireDate").removeAttr('disabled');
            }

        });

    });

    $('#btnSaveChanges').click(function() {
        var code_request = $('#coderequest').val();
        if (!$("#editticket-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkTicket/saveChangesTicket",
            data: $('#editticket-form').serialize(),
            error: function (ret) {
                $('#modal-notification').html(makeSmartyLabel('Alert_failure'));
                $("#btn-modal-ok").attr("href", path+"/helpdezk/hdkTicket/viewrequest/id/"+code_request);
                $("#tipo-alert").setAttribute('class', 'alert alert-danger');
                $('#modal-alert').modal('show');
                //modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
            },
            success: function(ret) {
                if(ret){
                    if(ret=='OK') {
                        $('#modal-notification').html(makeSmartyLabel('Save_changes_sucess'));
                        $("#btn-modal-ok").attr("href", path+"/helpdezk/hdkTicket/viewrequest/id/"+code_request);
                        $("#tipo-alert").attr('class', 'alert alert-success');
                        $('#modal-alert').modal('show');
                    } else {
                        $('#modal-notification').html(makeSmartyLabel('Alert_failure'));
                        $("#btn-modal-ok").attr("href", path+"/helpdezk/hdkTicket/viewrequest/id/"+code_request);
                        $("#tipo-alert").attr('class', 'alert alert-danger');
                        $('#modal-alert').modal('show');
                        //modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
                    }
                }
                else {
                    $('#modal-notification').html(makeSmartyLabel('Alert_failure'));
                    $("#btn-modal-ok").attr("href", path+"/helpdezk/hdkTicket/viewrequest/id/"+code_request);
                    $("#tipo-alert").attr('class', 'alert alert-danger');
                    $('#modal-alert').modal('show');
                    //modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
                }
            },
            beforeSend: function(){
                $("button").attr('disabled','disabled');
            },
            complete: function(){
                $("button").removeAttr('disabled')
            }
        });
        return false;  // <- cancel event
    });

    $("#btnAssume").click(function(){
        $('#modal-form-assume').modal('show');
    });

    $("#btnSendAssumeTicket").click(function(){
        var code_request = $("#coderequest").val(),
            incharge = $("#incharge").val(),
            typeincharge = $("#typeincharge").val(),
            groupAssume = $("#cmbAssumeGroups").val();

        if ($("#grpkeep").is(":checked")) var grpkeep = '1';
        else var grpkeep = '0';

        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkTicket/assumeTicket',
            dataType: 'json',
            data: {
                code_request: code_request,
                grpview: grpkeep,
                typeincharge: typeincharge,
                incharge: incharge,
                groupAssume: groupAssume
            },
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-assume-form');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status === "OK") {
                    modalAlertMultiple('success',makeSmartyLabel('Assumed_successfully'),'alert-assume-form');
                    setTimeout(function(){
                        $('#modal-form-assume').modal('hide');
                        location.reload();
                    },2000);
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-assume-form');
                }

            },
            beforeSend: function(){
                $("#btnSendAssumeTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSendAssumeTicket").html(makeSmartyLabel('btn_assume'));
            }

        });

    });

    $("#btnOpAux").click(function(){
        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkTicket/modalAuxOperator",
            dataType: 'json',
            data: {
                code_request: $('#coderequest').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj) {
                    $('#cmbAuxOpe').html(obj.cmblist);
                    $("#cmbAuxOpe").trigger("chosen:updated");

                    if(obj.tablelist){
                        $("#tablelist").html(obj.tablelist);
                    }
                    
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
                }
            }
        });
        $('#modal-form-auxoperator').modal('show');
    });

    $("#btnRepass").click(function(){
        $('#modal-form-repass').modal('show');
        objViewTicket.loadRepassList();
    });

    $("#btnSendRepassTicket").click(function(){
        var type = $('input:radio[name=typerep]:checked').val(),
            idgrouptrack = 0,
            view = $('input:radio[name=repoptns]:checked').val(),
            new_rep = $("#replist").val(),
            code_request = $("#coderequest").val(),
            incharge = $("#incharge").val(),
            typeincharge = $("#typeincharge").val();

        if(typeof(view) =="undefined"){
            modalAlertMultiple('danger',makeSmartyLabel('Alert_follow_repass'),'alert-repass-form');
            return false;
        }

        if (typeincharge == "P"){
            if(view == "G"){
                idgrouptrack = $("#cmbOpeGroups").val();
            }
        }

        if($("#repass-form").valid()){

            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkTicket/repassTicket',
                dataType: 'json',
                data: {
                    type: type,
                    repassto: new_rep,
                    code_request: code_request,
                    view: view,
                    idgrouptrack: idgrouptrack,
                    incharge: incharge
                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-repass-form');
                },
                success: function(ret){

                    var obj = jQuery.parseJSON(JSON.stringify(ret));

                    if(obj.status === "OK") {
                        modalAlertMultiple('success',makeSmartyLabel('Alert_sucess_repass'),'alert-repass-form');
                        setTimeout(function(){
                            $('#modal-form-repass').modal('hide');
                            location.href = path+"/helpdezk/hdkTicket/index";
                        },2000);
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-repass-form');
                    }

                },
                beforeSend: function(){
                    $("#btnSendRepassTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).attr('disabled','disabled');
                },
                complete: function(){
                    $("#btnSendRepassTicket").html(makeSmartyLabel('Repass_btn'));
                }

            });
        } else {
            return false;
        }

    });

    $("#btnReject").click(function(){
        $('#modal-form-reject').modal('show');
    });

    $("#btnSendRejectTicket").click(function(){
        var code_request = $("#coderequest").val(),
            incharge = $("#incharge").val(),
            typeincharge = $("#typeincharge").val(),
            rejectReason = $('#reasonreject').summernote('code');

        if ($('#reasonreject').summernote('isEmpty')) {
            modalAlertMultiple('danger',makeSmartyLabel('Alert_empty_reason'),'alert-reject-form');
            return false;
        }

        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkTicket/rejectTicket',
            dataType: 'json',
            data: {
                code_request: code_request,
                typeincharge: typeincharge,
                incharge: incharge,
                rejectreason: rejectReason
            },
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reject-form');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status === "OK") {
                    modalAlertMultiple('success',makeSmartyLabel('Reject_sucess'),'alert-reject-form');
                    setTimeout(function(){
                        $('#modal-form-reject').modal('hide');
                        location.href = path+"/helpdezk/hdkTicket/index";
                    },2000);
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reject-form');
                }

            },
            beforeSend: function(){
                $("#btnSendRejectTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSendRejectTicket").html(makeSmartyLabel('Reject_btn'));
            }

        });
        return false;
    });

    $("#btnClose").click(function(){
        $('#modal-form-close').modal('show');
    });

    $("#btnSendCloseTicket").click(function(){
        var code_request = $("#coderequest").val();

        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkTicket/finishTicket',
            dataType: 'json',
            data: {code_request: code_request},
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-close-form');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status === "OK") {
                    modalAlertMultiple('success',makeSmartyLabel('Alert_close_request'),'alert-close-form');
                    setTimeout(function(){
                        $('#modal-form-close').modal('hide');
                        location.href = path+"/helpdezk/hdkTicket/index";
                    },2000);
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-close-form');
                }

            },
            beforeSend: function(){
                $("#btnSendCloseTicket").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSendCloseTicket").html(makeSmartyLabel('Yes'));
            }

        });
        return false;
    });

    $("#btnCancel").click(function(){
        $('#modal-form-cancel').modal('show');
    });

    $("#btnEvaluate").click(function(){
        $('#modal-form-evaluate').modal('show');
    });

    $("#btnReopen").click(function(){
        console.log('reopen click');
        $('#modal-form-reopen').modal('show');
    });

    $("#btnPrint").click(function(){
        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkTicket/makeReport",
            data: { code_request : $('#coderequest').val()},
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
            },
            success: function(fileName) {
                console.log(fileName);
                if(fileName){
                    /*
                     * I had to make changes to open the file in a new window
                     * because I could not use the jquery.download with the .pdf extension
                     */
                    if (fileName.indexOf(".pdf") >= 0) {
                        window.open(fileName, '_blank');
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


    // http://icheck.fronteed.com
    // https://stackoverflow.com/questions/20736315/icheck-check-if-checkbox-is-checked
    $('input[name="approve').on('ifChecked', function(event){
        var value = $(this).val() ;
        if(value == "A"){
            $('#questions').show();
            $('#aprove-obs').hide();
            $('#observation').prop('required',false);
        }else if(value == "N"){
            $('#aprove-obs').show();
            $('#questions').hide();
            $('input[name^="question-"]').prop('required',false);
        }else if(value == "O"){
            $('#aprove-obs').show();
            $('#questions').show();
            $('input[name^="question-"]').prop('required',false);
        }
        console.log('value: '+$(this).val()+' coderequest: ' + $('#coderequest').val());
    });

    $('#cancel_form').submit(function() {
        var dataForm = $('#cancel_form').serialize();
        console.log('Inside');
        if(!$("#btnSendCancel").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + "/helpdezk/hdkTicket/cancelTicket",
                data: dataForm,
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-cancel');
                },
                success: function(ret) {
                    if(ret){
                        if(ret=='OK') {
                            modalAlertMultiple('info',makeSmartyLabel('Alert_Cancel_sucess'),'alert-cancel');
                            setTimeout(function(){
                                $('#modal-form-cancel').modal('hide');
                                location.reload();
                            },2000);
                        } else {
                            modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-cancel');
                        }
                    }
                    else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-cancel');
                    }
                },
                beforeSend: function(){
                    $("#btnSendCancel").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                },
                complete: function(){
                    $("#btnSendCancel").html(makeSmartyLabel('Send'));
                }
            });
        }
        return false;  // <- cancel event
    });

    $('#evaluate_form').submit(function() {

        // jquery lives out textarea in serialize, so I add extra data
        var data = $('#evaluate_form').serialize() + "&observation=" + $("#observation").val();

        $("input[name='radioName']:checked").val()

        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkTicket/evaluateTicket",
            data: data,
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-evaluate');
            },
            success: function(ret) {
                if(ret){
                    if(ret=='OK') {
                        modalAlertMultiple('info',makeSmartyLabel('Tckt_evaluated_success'),'alert-evaluate');
                        $('#btnSendEvaluate').hide();
                        setTimeout(function(){
                            $('#modal-form-evaluate').modal('hide');
                            location.href = path + "/helpdezk/hdkTicket/viewrequest/id/"+$("#coderequest").val() ;
                        },3000);
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-evaluate');
                    }
                }
                else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-evaluate');
                }
            },
            beforeSend: function(){
                $("#btnSendEvaluate").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSendEvaluate").html(makeSmartyLabel('Send'));
            }
        });
        return false;  // <- cancel event
    });

    $('#reopen_form').submit(function() {
        var dataForm = $('#reopen_form').serialize();
        console.log('Inside');
        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkTicket/reopenTicket",
            data: dataForm,
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
            },
            success: function(ret) {
                if(ret){
                    if(ret=='OK') {
                        modalAlertMultiple('info',makeSmartyLabel('Alert_reopen_sucess'),'alert-reopen');
                        $('#btnSendReopen').hide();
                        setTimeout(function(){
                            $('#modal-form-reopen').modal('hide');
                            location.reload();
                        },3000);
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
                    }
                }
                else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
                }
            },
            beforeSend: function(){
                $("#btnSendReopen").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSendReopen").html(makeSmartyLabel('Send'));
            }
        });
        return false;  // <- cancel event
    });

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green'
    });

    /*
     * Mask
     */
    $("#started").mask('00:00:00');
    $("#finished").mask('00:00:00');

    /*
     * Datepicker
     */
    $('.input-group.date').datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR",
        autoclose: true
    });

    /*
     * Clockpicker
     */
    $('.clockpicker').clockpicker({
        autoclose: true
    });


    $("#btnTimer").click(function(){
        if($(this).hasClass('active')){
            $(this).removeClass('active');
            $('#btnTimer i').removeClass('fa-spin');
            clearInterval(clock);
        }
        else{
            $(this).addClass('active');
            $('#btnTimer i').addClass('fa-spin');
            clock = setInterval(function() {
                var currentTime = new Date();
                var currentHours = currentTime.getHours();
                var currentMinutes = currentTime.getMinutes();
                var currentSeconds = currentTime.getSeconds();
                currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
                currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;
                var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds;

                $('#finished').val(currentTimeString)
                calctotal();
            }, 1000);
        }
    });

    $("#finished").change(function(){
        calctotal();
    });

    function calctotal(){
        var start = $("#started").val(),
            finish = $("#finished").val();
        $('#totalminutes').val(calcmin(start,finish));
    }

    function calcmin(start,finish) {
        if(start == '' || finish ==''){
            return;
        }

        split = new Array();

        split[2] = start.split(":");
        split[1] = finish.split(":");

        hour1 = split[1][0];
        minute1 = split[1][1];
        second1 = split[1][2];

        hour2 = split[2][0];
        minute2 = split[2][1];
        second2 = split[2][2];

        total_minutes = (((hour1 * 60) - (hour2 * 60)) + (minute1 - minute2) + ((second1 / 60) - (second2 / 60)));
        return total_minutes.toFixed(2);
    }

    /**
     ** .validate() is what initializes the Validation plugin on your form.
     ** .valid() returns true or false depending on if your form is presently valid.
     **/
    $("#editticket-form").validate({
        ignore:[],
        rules: {
            area: "required",  // simple rule, converted to {required:true}
            cmbType: "required",
            item: "required",
            service: "required",
            priority: "required"
        },
        messages: {
            area: makeSmartyLabel('Alert_choose_area'),
            cmbType: makeSmartyLabel('Alert_choose_type'),
            item: makeSmartyLabel('Alert_choose_item'),
            service: makeSmartyLabel('Alert_choose_service')
        }

    });

    $("#attway-form").validate({
        ignore:[],
        rules: {
            attWay: "required"
        },
        messages: {
            attWay: makeSmartyLabel('Alert_field_required')
        }

    });

    $("input[name='typerep']").on('ifChecked', function() { // bind a function to the change event
        objViewTicket.loadRepassList();
    });

    $("#replist").change(function(){
        if(!$("#btnAbilities").hasClass("off")){
            objViewTicket.getAbilities();
        }
        else if(!$("#btnGroups").hasClass("off")){
            objViewTicket.getGroups();
        }
    });

    //btnAbilities btnGroups
    $("#btnAbilities").click(function(){
        var rep = $("#replist").val(),
            $tabAbiGrp = $("#tabAbiGrp");

        if($(this).hasClass("off")){
            $tabAbiGrp.html('');
            $(this).removeClass("btn-white off").addClass("btn-default");
            $("#btnGroups").removeClass("btn-default").addClass("btn-white off");
            if(rep){
                objViewTicket.getAbilities();
            }else{
                $tabAbiGrp.append('<div class="panel-body">'+aLang['Select_group_operator'].replace (/\"/g, "")+'</div>');
            }
        }

    });

    $("#btnGroups").click(function(){
        var rep = $("#replist").val(),
            $tabAbiGrp = $("#tabAbiGrp");

        if($(this).hasClass("off")){
            $tabAbiGrp.html('');
            $(this).removeClass("btn-white off").addClass("btn-default");
            $("#btnAbilities").removeClass("btn-default").addClass("btn-white off");
            if(rep){
                objViewTicket.getGroups();
            }else{
                $tabAbiGrp.append('<div class="panel-body">'+aLang['Select_group_operator'].replace (/\"/g, "")+'</div>');
            }
        }
    });

    $("#grpkeep").on('ifChecked', function() { // bind a function to the change event
        $("#assumeGroupsList").removeClass('hide');
    }).on('ifUnchecked', function() { // bind a function to the change event
        $("#assumeGroupsList").addClass('hide');
    });

    $("input[name='repoptns']").on('ifClicked', function() { // bind a function to the change event
        if($(this).val() == 'G'){
            $("#OpeGroupsList").removeClass('hide');
        }else{
            $("#OpeGroupsList").addClass('hide');
        }
    });

    $("#repass-form").validate({
        ignore:[],
        rules: {
            typerep: "required",  // simple rule, converted to {required:true}
            replist: "required"
        },
        messages: {
            typerep: makeSmartyLabel('Alert_field_required'),
            replist: makeSmartyLabel('Alert_field_required')
        }

    });

    $("#btnSendApvReqYes").click(function(){
        location.href = path + "/helpdezk/hdkTicket/index" ;
    });

    $("#btnNewTck").click(function(){
        location.href = path + "/helpdezk/hdkTicket/newTicket" ;
    });

});

function deleteNote(idnote)
{

    // Create Instances
    var dialogInstanceAlertOK = new BootstrapDialog({
        title: makeSmartyLabel('Note') + ': ' + idnote,
        message: makeSmartyLabel('Alert_deleted_note'),
        type: BootstrapDialog.TYPE_SUCCESS,
        buttons:    [{
                        label: makeSmartyLabel('Close'),
                        action: function(dialogItself){
                            dialogItself.close();
                            //location.reload();
                            showNotes( $('#coderequest').val());
                        }
                     }]

    });

    var dialogInstanceAlertERROR = new BootstrapDialog({
        title: makeSmartyLabel('Note') + ': ' + idnote,
        message: makeSmartyLabel('Tckt_del_note_failure'),
        type: BootstrapDialog.TYPE_WARNING,
        buttons:    [{
            label: makeSmartyLabel('Close'),
            action: function(dialogItself){
                dialogItself.close();
            }
        }]

    });

    BootstrapDialog.show({
        message: makeSmartyLabel('Tckt_delete_note'),
        type: BootstrapDialog.TYPE_DANGER,
        title: makeSmartyLabel('Note') + ': ' + idnote,
        buttons:    [{
                        label: makeSmartyLabel('Close'),
                        action: function(dialogItself){
                            dialogItself.close();
                        }
                    },
                    {
                        label: makeSmartyLabel('Send'),
                        // no title as it is optional
                        cssClass: 'btn-primary',
                        action: function(dialogItself){
                            $.post( path + '/helpdezk/hdkTicket/deleteNote', {
                                idnote : idnote
                            }, function(ret) {
                                if(ret){
                                    console.log('r: '+ret);
                                    if(ret=='OK') {
                                        dialogItself.close();
                                        dialogInstanceAlertOK.open();
                                    } else {
                                        dialogItself.close();
                                        dialogInstanceAlertERROR.open();
                                    }
                                } else {
                                    dialogItself.close();
                                    dialogInstanceAlertERROR.open();
                                }
                            });
                        }
                    }]
    });

    return false;  // <- cancel event
}

function download(idFile, typeAttach)
{
    var urlDownload = path+'/helpdezk/hdkTicket/downloadFile/id/'+idFile+'/type/'+typeAttach+'/';
    $(location).attr('href',urlDownload);
}

function sendNotification(transaction,codeRequest,hasAttachments)
{
    /*
     *
     * This was necessary because in some cases we have to send e-mail with the newly added attachments.
     * We only have access to these attachments after the execution of the dropzone.
     *
     */
    $.ajax({
        url : path + '/helpdezk/hdkTicket/sendNotification',
        type : 'POST',
        data : {
            transaction: transaction,
            code_request: $('#coderequest').val(),
            has_attachment: hasAttachments
        },
        success : function(data) {

        },
        error : function(request,error)
        {

        }
    });

    return false ;

}

function showNotes(codeRequest)
{
    $.ajax({
        url : path + '/helpdezk/hdkTicket/ajaxNotes',
        type : 'POST',
        data : {
            code_request: codeRequest
        },
        success : function(data) {
            $('#ticket_notes').html(data);
        },
        error : function(request,error)
        {

        }
    });

    return false ;
}

function removeAuxOpe(handler){
    var tr = $(handler).closest('tr'), auxopeId = $(handler).closest('tr').find('.hdkAuxOpe').val(),
        code_request = $('#coderequest').val();

    $.post(path+"/helpdezk/hdkTicket/deleteAuxOperator",{code_request: code_request,auxopeid: auxopeId},
        function(valor){
            var obj = jQuery.parseJSON(JSON.stringify(valor));
            $('#cmbAuxOpe').html(obj.cmblist);
            $("#cmbAuxOpe").trigger("chosen:updated");

            if(obj.tablelist){
                $("#tablelist").html(obj.tablelist);
                if($("#auxopelist").hasClass("hide")){
                    $("#auxopelist").removeClass("hide");
                }
                $("#auxopediv").html(obj.auxopelist);
            }else{
                $("#tablelist").empty();
                if(!$("#auxopelist").hasClass("hide")){
                    $("#auxopelist").addClass("hide");
                }
            }
        },'json')
}