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

var timer = null;
var inactivityTimer = null;

countdown = {

    originalTime: 0,
    currentTime: 0,
    started: false,

    start: function(seconds){
        this.currentTime = seconds;
        this.started = true;

        var tempo = seconds;

        // Opens the modal only once
        if(typeof Swal !== 'undefined' && !Swal.isVisible()){
            Swal.fire({
                title: vocab['inactive_session'],
                html: `
                    <p>${vocab['session_locked_in']}</p>

                    <h2 id="swalCountdown" style="color:#3085d6;">
                        00:00
                    </h2>
                `,
                icon: 'warning',
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: vocab['continue_session'],
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if(result.isConfirmed){
                    sessionControl.reset();
                }
            });
        }

        if((tempo - 1) >= 0){
            var min = parseInt(tempo / 60);
            var hor = parseInt(min / 60);

            min = min % 60;

            var seg = tempo % 60;

            if(min < 10){
                min = "0" + min;
            }

            if(seg < 10){
                seg = "0" + seg;
            }

            if(hor < 10){
                hor = "0" + hor;
            }

            var horaImprimivel = '';

            if(parseInt(hor) > 0){
                horaImprimivel = hor + 'h ' + min + 'm ' + seg + 's';
            }else if(parseInt(min) > 0){
                horaImprimivel = min + 'm ' + seg + 's';
            }else{
                horaImprimivel = seg + 's';
            }

            $("#numberCountdown").html(horaImprimivel);
            $("#swalCountdown").html(horaImprimivel);

            // Changes the color when 1 minute remains
            if(tempo <= 60){
                $("#swalCountdown").css("color", "#dc3545");

            }else{
                $("#swalCountdown").css("color", "#3085d6");
            }

            tempo--;

            timer = setTimeout(function(){
                countdown.start(tempo);

            }, 1000);

        }else{
            if(typeof Swal !== 'undefined'){
                Swal.close();
            }

            window.location = path + "/main/home/lockscreen";
        }
    },

    stop: function(){
        clearTimeout(timer);

        this.started = false;

        $("#numberCountdown").html('');

        if(typeof Swal !== 'undefined'){
            Swal.close();
        }
    }
};


/**
 * Session Inactivity Control
 */
var sessionControl = {
    eventsBinded: false,

    init: function(seconds, inactivityLimit = 300){
        countdown.originalTime = seconds;

        this.inactivityLimit = inactivityLimit;

        if(!this.eventsBinded){
            this.bindEvents();

            this.eventsBinded = true;
        }

        this.reset();
    },

    reset: function(){
        clearTimeout(inactivityTimer);

        if(countdown.started){
            countdown.stop();
        }

        inactivityTimer = setTimeout(function(){
            // Always restarts from the original value
            countdown.start(countdown.originalTime);

        }, sessionControl.inactivityLimit * 1000);
    },

    bindEvents: function(){
        $(document).on(
            'mousemove keydown click scroll touchstart',
            function(){

                sessionControl.reset();
            }
        );

        $(document).ajaxComplete(function(){
            sessionControl.reset();
        });

        document.addEventListener("visibilitychange", function(){
            if(!document.hidden){

                sessionControl.reset();
            }
        });
    }
};

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

/**
 * enableTableNavigation(className)
 * 
 * en_us Allows arrow-key navigation between inputs inside the same HTML table.
 *       It scans each table that contains inputs with the provided class and builds
 *       a logical grid numbering only the rows that actually contain inputs.
 *       Works with single-column tables and with rows that have different numbers of inputs.
 *       Call it after the table is in the DOM (re-call if you add/remove rows dynamically).
 * 
 * pt_br Permite navegação com as setas do teclado entre inputs dentro da mesma tabela HTML.
 *       A função varre cada tabela que contenha inputs com a classe fornecida e cria
 *       uma grade lógica numerando apenas as linhas que possuem inputs.
 *       Funciona para tabelas com uma única coluna e para linhas com quantidade variável de inputs.
 *       Chame a função após a tabela existir no DOM (re-chame se inserir/remover linhas dinamicamente).
 */
function enableTableNavigation(className) {
  // Remove previous key handler for this class to avoid duplicate handlers when re-initializing
  $(document).off('keydown', '.' + className);

  // Build logical grid: for each table that contains inputs with the class
  $("." + className).closest("table").each(function() {
    var $table = $(this);
    var logicalRow = 0;

    // Iterate rows and only assign row numbers to rows that have at least one input of the class
    $table.find("tr").each(function() {
      var $row = $(this);
      var $rowInputs = $row.find("." + className);

      if ($rowInputs.length === 0) return; // skip rows without matching inputs

      // remove any old attributes and assign new data attributes
      $rowInputs.removeAttr("data-tbl-row data-tbl-col");
      $rowInputs.each(function(colIdx) {
        $(this).attr("data-tbl-row", logicalRow).attr("data-tbl-col", colIdx);
      });

      logicalRow++;
    });
  });

  // Helper: get input in the same table at logical (row, col).
  // If exact column doesn't exist in that row, clamp to the nearest available (last).
  function getInputInTable($table, row, col) {
    var selector = "." + className + '[data-tbl-row="' + row + '"]';
    var $rowInputs = $table.find(selector);

    if ($rowInputs.length === 0) return $(); // empty jQuery set

    if (col >= 0 && col < $rowInputs.length) return $rowInputs.eq(col);
    return $rowInputs.eq(Math.max(0, Math.min(col, $rowInputs.length - 1)));
  }

  // Bind keydown handler (delegated) to handle current and future inputs (attributes should be re-assigned if new rows are added)
  $(document).on("keydown", "." + className, function(e) {
    // Read logical coordinates from data attrs
    var $current = $(this);
    var row = parseInt($current.attr("data-tbl-row"), 10);
    var col = parseInt($current.attr("data-tbl-col"), 10);
    var $table = $current.closest("table");

    // If attributes missing, do nothing (safe guard)
    if (Number.isNaN(row) || Number.isNaN(col)) return;

    var $target;
    switch (e.which) {
      case 37: // left
        $target = getInputInTable($table, row, col - 1);
        if ($target.length) { $target.focus(); e.preventDefault(); }
        break;

      case 39: // right
        $target = getInputInTable($table, row, col + 1);
        if ($target.length) { $target.focus(); e.preventDefault(); }
        break;

      case 38: // up
        $target = getInputInTable($table, row - 1, col);
        if ($target.length) { $target.focus(); e.preventDefault(); }
        break;

      case 40: // down
        $target = getInputInTable($table, row + 1, col);
        if ($target.length) { $target.focus(); e.preventDefault(); }
        break;
    }
  });
}


$(document).ready(function () {
    if($("#frm-login").length <= 0 && $("#lockscreen").length <= 0){
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

        // -- Time validation methods --
        $.validator.addMethod('checkStartTime', function(startTime, element, params) {
            var paramsTmp = $(params).val();
            if(paramsTmp && paramsTmp.trim() !== ""){
                var parts = startTime.split(':') , endTime = $(params).val(), partsFinish = endTime.split(':');

                var startMinutes = parseInt(parts[0]) * 60 + parseInt(parts[1]);
                var endMinutes = parseInt(partsFinish[0]) * 60 + parseInt(partsFinish[1]);

                return startMinutes <= endMinutes;
            }

            return true;

        }, vocab['Alert_start_time_error']);

        $.validator.addMethod('checkEndTime', function(endTime, element, params) {
            var paramsTmp = $(params).val();
            if(paramsTmp && paramsTmp.trim() !== ""){
                var parts = endTime.split(':') , startTime = $(params).val(), partsStart = startTime.split(':');
        
                var endMinutes = parseInt(parts[0]) * 60 + parseInt(parts[1]);
                var startMinutes = parseInt(partsStart[0]) * 60 + parseInt(partsStart[1]);
        
                return endMinutes >= startMinutes;
            }

            return true;

        }, vocab['Alert_finish_time_error']);
    }

    /* let isNavigatingInternally = false;

    // Detect any changes in the URL (includes clicks on links and buttons)
    $(document).on('click', function(e) {
        // Use event delegation to handle all clicks and detect internal links and buttons
        const target = $(e.target).closest('a, button');
        if (target.length > 0) {
            const href = target.attr('href') || target.data('href');
            if (href && href.startsWith(window.location.origin)) {
                isNavigatingInternally = true;
            }
        }
    });

    // Detect changes in history (e.g. back/forward buttons)
    $(window).on('popstate', function(e) {
        isNavigatingInternally = true;
    });

    // Before downloading the page
    $(window).on('beforeunload', function(e) {
        if (!isNavigatingInternally) {
            // Send a request using sendBeacon
            navigator.sendBeacon(path+"/main/home/closeBrowser");
        } else {
            // Reset the flag after a short delay for the next page load
            setTimeout(function() {
                isNavigatingInternally = false;
            }, 0);
        }
    });

    // Ensure the flag is reset when a new page is loaded
    $(window).on('load', function() {
        isNavigatingInternally = false;
    }); */
});

function showToast(message, type = 'success')
{
    const toastEl = document.getElementById('app-toast');
    const toastMsg = document.getElementById('toast-message');

    toastMsg.textContent = message;

    // Remove cores anteriores
    toastEl.classList.remove(
        'bg-success',
        'bg-danger',
        'bg-warning',
        'bg-info'
    );

    switch(type) {
        case 'error':
            toastEl.classList.add('bg-danger');
            break;

        case 'warning':
            toastEl.classList.add('bg-warning');
            toastEl.classList.remove('text-white');
            toastEl.classList.add('text-dark');
            break;

        default:
            toastEl.classList.add('bg-success');
            toastEl.classList.remove('text-dark');
            toastEl.classList.add('text-white');
    }

    const toast = bootstrap.Toast.getOrCreateInstance(toastEl, {
        delay: 4000
    });

    toast.show();
}