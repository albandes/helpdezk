document.addEventListener("DOMContentLoaded", function(){
    /////// Prevent closing from click inside dropdown
    document.querySelectorAll('.dropdown-menu').forEach(function(element){
        element.addEventListener('click', function (e) {
          e.stopPropagation();
        });
    })

    // make it as accordion for smaller screens
    if (window.innerWidth < 992) {

        // close all inner dropdowns when parent is closed
        document.querySelectorAll('.navbar .dropdown').forEach(function(everydropdown){
            everydropdown.addEventListener('hidden.bs.dropdown', function () {
                // after dropdown is hidden, then find all submenus
                  this.querySelectorAll('.submenu').forEach(function(everysubmenu){
                      // hide every submenu as well
                      everysubmenu.style.display = 'none';
                  });
            })
        });
        
        document.querySelectorAll('.dropdown-menu a').forEach(function(element){
            element.addEventListener('click', function (e) {
    
                  let nextEl = this.nextElementSibling;
                  if(nextEl && nextEl.classList.contains('submenu')) {	
                      // prevent opening link if link needs to open dropdown
                      e.preventDefault();
                      console.log(nextEl);
                      if(nextEl.style.display == 'block'){
                          nextEl.style.display = 'none';
                      } else {
                          nextEl.style.display = 'block';
                      }

                  }
            });
        })
    }
    // end if innerWidth

}); 
// DOMContentLoaded  end


function showAlert(msg,typeAlert)
{
    $('#modal-notification').html(msg);
    $("#type-alert").attr('class', 'alert alert-'+typeAlert);
    $('#modal-alert').modal('show');

    return false;
}

function modalAlert(type,message)
{
    $("#response").animate({height: '+=72px'}, 300);

    $('<div class="alert alert-'+type+' hdk-alert">' +
        '<div class="row"><div class="col-md-2 close position-absolute end-0 text-center" data-bs-dismiss="alert">&times;</div><div class="col-md-10">'+message+'</div></div></div>')
        .hide().appendTo('#response').fadeIn(1000);

    $(".hdk-alert").delay(3500).fadeOut("normal", function(){ $(this).remove(); });

    $("#response").delay(4000).animate({ height: '-=72px' }, 300); 
}

function modalAlertMultiple(type,message,id)
{

    $("#"+id+"").animate({height: '+=72px'}, 300);

    $('<div class="alert alert-'+type+' hdk-alert">' +
    '<div class="row"><div class="col-md-2 close position-absolute end-0 text-center"><i class="close far fa-times-circle fa-2x" data-bs-dismiss="alert"></i></div><div class="col-md-10">'+message+'</div></div></div>')
        .hide().appendTo("#"+id+"").fadeIn(1000);

    $(".hdk-alert").delay(3500).fadeOut("normal", function(){ $(this).remove(); });

    $("#"+id+"").delay(4000).animate({ height: '-=72px' }, 300);

    return false;
}

function translateLabel(label){
    var lbl = $.ajax({
        type: "POST",
        url: path+"/main/home/translateLabel",
        data: {label:label},
        async: false,
        dataType: 'json'
    }).responseJSON;
    
    return lbl;

}

countdown = {
    start: function(seconds){
        if(typeof  this.time == "undefined"){
            this.time = seconds;
        }
        var tempo = seconds;

        if((tempo - 1) >= 0){
            var min = parseInt(tempo/60),
                hor = parseInt(min/60),
                min = min%60,
                seg = tempo%60;

            if(min < 10){
                min = "0"+min;
                min = min.substr(0, 2);
            }
            if(seg <=9){
                seg = "0"+seg;
            }
            if(hor <=9){
                hor = "0"+hor;
            }

            if(hor > 0)
                horaImprimivel = hor+'h ' + min + 'm ' + seg + 's';
            else if(min > 0)
                horaImprimivel = min + 'm ' + seg + 's';
            else
                horaImprimivel = seg + 's';
            $(document.getElementById("numberCountdown")).html(horaImprimivel);
            tempo--;
            timer = setTimeout(function(){
                countdown.start(tempo);
            },1000);
        } else {

            window.location = path + "/main/home/lockscreen";
        }
    },
    stop: function(){
        clearTimeout(timer);
    },
    refresh: function(){
        countdown.stop();
        countdown.start(this.time);
    }

}

function showNextStep(list,msg,typeAlert,totalAttach,modalSize="")
{
    $('#nexttotalattach').val(totalAttach);
    $('#next-step-list').html(list);
    $('#next-step-message').html(msg);
    $("#type-alert-files").attr('class', 'col-sm-12 col-xs-12 bs-callout-'+typeAlert);
    $(".modal-dialog").addClass(modalSize);
    $('#modal-next-step').modal('show');

    return false;
}

/**
 * Returns ID of the row selected of grig
 * 
 * @returns mixed
 */
 function getRowIndx(gridName) {
    var arr = $("#"+gridName).pqGrid("selection", { type: 'row', method: 'getSelection' });
    
    if (arr && arr.length > 0) {
        return arr[0].rowIndx;                                
    }
    else {
        return null;
    }
}

function setActionsBtn(aPermissions)
{
    if($('#btnCreate').length > 0 && aPermissions[1] == 'N'){// new 
        $('#btnCreate').removeClass('active').addClass('disabled').attr('disabled','disabled');
    }else{
        $('#btnCreate').removeClass('disabled').addClass('active').removeAttr('disabled');
    }

    if($('#btnUpdate').length > 0 && aPermissions[2] == 'N'){// edit
        $('#btnUpdate').removeClass('active').addClass('disabled').attr('disabled','disabled');
    }else{
        $('#btnUpdate').removeClass('disabled').addClass('active').removeAttr('disabled');
    }

    if($('#btnEnable').length > 0 && aPermissions[2] == 'N'){// activate
        $('#btnEnable').removeClass('active').addClass('disabled').attr('disabled','disabled');
    }else{
        $('#btnEnable').removeAttr('disabled');
    }

    if($('#btnDisable').length > 0 && aPermissions[2] == 'N'){// deactivate
        $('#btnDisable').removeClass('active').addClass('disabled').attr('disabled','disabled');
    }else{
        $('#btnDisable').removeAttr('disabled');
    }

    if($('#btnDefault').length > 0 && aPermissions[2] == 'N'){// make default
        $('#btnDefault').removeClass('active').addClass('disabled').attr('disabled','disabled');
    }else{
        $('#btnDefault').removeAttr('disabled');
    }

    if($('#btnDelete').length > 0 && aPermissions[3] == 'N'){// delete
        $('#btnDelete').removeClass('active').addClass('disabled').attr('disabled','disabled');
    }else{
        $('#btnDelete').removeClass('disabled').addClass('active').removeAttr('disabled');
    }

    return false;
}

function makeFilterValueField(fieldType)
{
    if($("#filter-value").length > 0){
        $("#filter-value").maskMoney('destroy');
        $("#filter-value").unmask();
    }

    switch(fieldType){
        case 'date':
            if($("#action-list").val() == 'rg'){
                $("#filter-value-field").html("<div class='col-sm-5'>"
                                        +"<div class='input-group date'>"
                                            +"<input type='text' id='filter-date-start' name='filter-date-start' class='form-control input-sm' value='' readonly />"
                                            +"<span class='input-group-addon'><i class='fa fa-calendar-alt'></i></span>"
                                        +"</div>"
                                        +"<div id='filter-date-start_validate_error' class='row'></div>"
                                    +"</div>"
                                    +"<div class='col-sm-2 text-center'>"
                                        +"<label for='filter-date-end' class='hdk-label col-form-label text-end'>"+vocab['until']+"</label>"
                                    +"</div>"
                                    +"<div class='col-sm-5'>"
                                        +"<div class='input-group date'>"
                                            +"<input type='text' id='filter-date-end' name='filter-date-end' class='form-control input-sm' value='' readonly />"
                                            +"<span class='input-group-addon'><i class='fa fa-calendar-alt'></i></span>"
                                        +"</div>"
                                        +"<div id='filter-date-end_validate_error' class='row'></div>"
                                    +"</div>");
            }else{
                $("#filter-value-field").html("<div class='col-sm-8'>"
                                        +"<div class='input-group date'>"
                                            +"<input type='text' id='filter-date-start' name='filter-date-start' class='form-control input-sm' value='' readonly />"
                                            +"<span class='input-group-addon'><i class='fa fa-calendar-alt'></i></span>"
                                        +"</div>"
                                        +"<div id='filter-date-start_validate_error' class='row'></div>"
                                    +"</div>");
            }

            if (dtpLanguage == '' || dtpLanguage === 'undefined' || !dtpLanguage) {
                // Default language en (English)
                var dpOptions = {
                    format: dtpFormat,
                    autoclose: dtpAutoclose,
                    orientation: dtpOrientation
                };
            } else {
                var dpOptions = {
                    format: dtpFormat,
                    language: dtpLanguage,
                    autoclose: dtpAutoclose,
                    orientation: dtpOrientation
                };
            }
        
            $('.input-group.date').datepicker(dpOptions);

            break;
        case 'text':
            if($('#filter-value').length <= 0)
                $("#filter-value-field").html("<div class='col-sm-12'><input type='text' class='form-control' id='filter-value' name='filter-value'><div id='filter-value_validate_error' class='row'></div></div>");
            break;
        case 'money':
            if($('#filter-value').length <= 0)
                $("#filter-value-field").html("<div class='col-sm-12'><input type='text' class='form-control' id='filter-value' name='filter-value'><div id='filter-value_validate_error' class='row'></div></div>");
            
            $('#filter-value').maskMoney({thousands:'.', decimal:',', allowZero:false, prefix: moneyPrefix+' '});
            break;
    }
}

$(document).ready(function () {
    // -- Date validation methods --
    $.validator.addMethod('checkStartDate', function(startDate, element, params) {
        var paramsTmp = $(params).val();
        if(paramsTmp && paramsTmp.trim() !== ""){
            var parts = startDate.split('/') , endDate = $(params).val(), partsFinish = endDate.split('/');

            startDate = new Date(parts[2], parts[1] - 1, parts[0]);
            endDate = new Date(partsFinish[2], partsFinish[1] - 1, partsFinish[0]);

            return startDate <= endDate;
        }

        return true;

    }, vocab['Alert_start_date_error']);

    $.validator.addMethod('checkEndDate', function(endDate, element, params) {
        var paramsTmp = $(params).val();
        if(paramsTmp && paramsTmp.trim() !== ""){
            var parts = endDate.split('/') , startDate = $(params).val(), partsStart = startDate.split('/');
    
            endDate = new Date(parts[2], parts[1] - 1, parts[0]);
            startDate = new Date(partsStart[2], partsStart[1] - 1, partsStart[0]);
    
            return endDate >= startDate;
        }

        return true;

    }, vocab['Alert_finish_date_error']);
});