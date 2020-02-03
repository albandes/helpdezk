

aLang = new Array();

$(document).ready(function() {
    //alert('flex_lang: '+path);
    var arq_lang = path+"/app/lang/"+default_lang+".txt";

    GetArqLang(arq_lang)

});

function GetArqLang(arq_lang)
{
    $.ajax({
        type: "GET",
        url: arq_lang,
        async: false,
        // Precisa setar como sincrona, pois por padrão, o método jQuery.Ajax vem com o parâmetro async setado como true
        dataType: "text",
        success: function(data) {processData(data);}
    });
}

function processData(allText) {
    var allTextLines = allText.split(/\r\n|\n/);
    var myarray = new Array();

    for (var i=0; i<allTextLines.length; i++)
    {
        if (allTextLines[i].indexOf("#") >= 0)
        {
            //alert('achei na lnha:' + (i+1));
            continue;
        }
        var data = allTextLines[i].split('=');
        myarray[$.trim(data[0])] = $.trim(data[1]);
    }

    aLang = myarray;

}