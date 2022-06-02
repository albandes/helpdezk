function showAlert(msg,typeAlert,btnOk)
{
    $('#modal-notification').html(msg);
    $("#btn-modal-ok").attr("href", btnOk);
    $("#tipo-alert").attr('class', 'alert alert-'+typeAlert);
    $('#modal-alert').modal('show');

    return false;
}

function modalAlert(type,message)
{

    $("#response").animate({height: '+=72px'}, 300);

    $('<div class="alert alert-'+type+'">' +
        '<button type="button" class="close" data-dismiss="alert">&times;</button>'+message+'</div>')
        .hide().appendTo('#response').fadeIn(1000);

    $(".alert").delay(3500).fadeOut("normal", function(){ $(this).remove(); });

    $("#response").delay(4000).animate({ height: '-=72px' }, 300);
}

function modalAlertMultiple(type,message,id)
{

    $("#"+id+"").animate({height: '+=72px'}, 300);

    $('<div class="alert alert-'+type+'">' +
    '<button type="button" class="close" data-dismiss="alert">&times;</button>'+message+'</div>')
        .hide().appendTo("#"+id+"").fadeIn(1000);

    $(".alert").delay(3500).fadeOut("normal", function(){ $(this).remove(); });

    $("#"+id+"").delay(4000).animate({ height: '-=72px' }, 300);

    return false;
}

function makeSmartyLabel(label){
    if(!aLang[label]) { // quick and dirty will be true for '', null, undefined, 0, NaN and false.
        console.log('It is missing in the language file: '+label);
        return ' ... ';
    } else {
        return aLang[label].replace (/\"/g, "");
    }

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


