var step = 1;

$(document).ready(function ()
{

    step_1();
})

function doIt(language)
{
        alert('<?php echo ENABLE ?>');

}

function step_1(lang){

    if(lang === undefined) {
        var lang = 'en_US';
    }
    $.ajax(
        {
            type: "POST",
            url:  "ajax/step_1.php",
            data: 	{ i18n:lang },
            success:
                function(ret)
                {
                    //$('#mostra').empty().append(t);
                    $('#content').html(ret);
                },
            error:
                function()
                {
                    $('#content').html("An error occurred during processing");
                },
            complete:
                function()
                {

                    $("#field_language").val(lang).change();
                    $('#etapa1').removeClass("fa fa-check-square").addClass("fa fa-square");
                }
        });

}


function step_2(lang){

    $.ajax(
        {
            type: "POST",
            url: "ajax/step_2.php",
            data: 	{i18n:lang},
            beforeSend:
                function() {
                    $('#etapa2').removeClass("fa fa-check-square").addClass("fa fa-square");
            },
            success:
                function(ret)
                {
                    $('#content').html(ret);
                    $('#etapa1').removeClass("fa fa-square").addClass("fa fa-check-square");
                    //$('#etapa2').removeClass("next").addClass("current");
                    console.log('depois do ajax: troquei as classes');
                },
            error:
                function()
                {
                    alert("An error occurred during processing");
                }

        });
}

function step_3(lang){
    $.ajax(
        {
            type: "POST",
            url:  "ajax/step_3.php",
            data: 	{ i18n:lang },
            beforeSend:
                function() {
                    $('#etapa3').removeClass("fa fa-check-square").addClass("fa fa-square");
                },
            success:
                function(ret)
                {
                    $('#content').html(ret);
                },
            error:
                function()
                {
                    $('#content').html("An error occurred during processing");
                },
            complete:
                function()
                {
                    $('#etapa2').removeClass("fa fa-square").addClass("fa fa-check-square");
                }
        });

}

function step_4(lang)
{
    $.ajax(
        {
            type: "POST",
            url:  "ajax/step_4.php",
            data:
                {
                i18n:lang,
                site_url:$("#site_url").val(),
                lang_default:$("#language_default").val(),
                timezone_default:$("#timezone_default").val(),
                theme_default:$("#theme_default").val()
                },
            beforeSend:
                function() {
                    $('#etapa4').removeClass("fa fa-check-square").addClass("fa fa-square");
                },

            success:
                function(ret)
                {
                    $('#content').html(ret);
                },
            error:
                function()
                {
                    $('#content').html("An error occurred during processing");
                },
            complete:
                function()
                {
                    $('#etapa3').removeClass("fa fa-square").addClass("fa fa-check-square");
                }
        });

}

function step_5(lang)
{
    $.ajax(
        {
            type: "POST",
            url:  "ajax/step_5.php",
            data:
                {
                i18n:lang,
                db_hostname:$("#db_hostname").val(),
                db_port:$("#db_port").val() ,
                db_name:$("#db_name").val() ,
                db_username:$("#db_username").val(),
                db_password:$("#db_password").val()
                },
            beforeSend:
                function() {
                    $('#etapa5').removeClass("fa fa-check-square").addClass("fa fa-square");
                },

            success:
                function(ret)
                {
                    $('#content').html(ret);
                },
            error:
                function()
                {
                    $('#content').html("An error occurred during processing");
                },
            complete:
                function()
                {
                    $('#etapa4').removeClass("fa fa-square").addClass("fa fa-check-square");
                }
        });

}

function step_6(lang)
{
    $.ajax(
        {
            type: "POST",
            url: "ajax/step_6.php",
            data:
                {
                i18n:lang,
                admin_username:$("#admin_username").val(),
                admin_password:$("#admin_password").val() ,
                admin_email:$("#admin_email").val()
                },
            beforeSend:
                function() {
                    $('#etapa6').removeClass("fa fa-check-square").addClass("fa fa-square");
                },

            success:
                function(ret)
                {
                    $('#content').html(ret);
                },
            error:
                function()
                {
                    $('#content').html("An error occurred during processing");
                },
            complete:
                function()
                {
                    $('#etapa5').removeClass("fa fa-square").addClass("fa fa-check-square");
                }
        });
}


function step_7(lang){


    $.ajax(
        {
            type: "POST",
            url: "ajax/step_7.php",
            data: 	{
                i18n:lang
            },
            beforeSend:
                function() {
                    $('#etapa7').removeClass("fa fa-check-square").addClass("fa fa-square");
                },

            success:
                function(ret)
                {
                    $('#content').html(ret);
                },
            error:
                function()
                {
                    alert("An error occured during processing");
                },
            complete:
                function()
                {
                    $('#etapa6').removeClass("fa fa-square").addClass("fa fa-check-square");
                }


        });

    $.ajax(
        {
            type: "POST",
            url: "ajax/step_7_proced.php",
            data: 	{
                i18n:lang
            },
            success:
                function(ret)
                {
                    $('#install_status').html(ret);
                },
            error:
                function()
                {
                    alert("An error occured during processing");
                },
            complete:
                function()
                {
                    $('#etapa7').removeClass("fa fa-square").addClass("fa fa-check-square");
                }


        });

}
