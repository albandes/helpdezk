
// Global variable
var prefix;

$(document).ready(function(){

   addNew();

});


function addNew(){
    if (access[1]=='N'){
        objModal.openModal("modalPermission");
    } else{

        prefix = $('#loc').val();
        var modalInsert = $(document.getElementById("modal_LogosStart"));

        objDefault.maskLoaderShow();
        console.log('function addNew');
        modalInsert.load("logos/modalStart/loc/"+prefix, function(){
            objDefault.init();
            $('input[type=file]').on('change', prepareUpload);
            $('form').on('submit', uploadFiles);
            objModal.openModal("modal_LogosStart");
            objDefault.maskLoaderHide();
        })

    }
}


function prepareUpload(event)
{
    files = event.target.files;
}

function uploadFiles(event)
{
    event.stopPropagation(); // Stop stuff happening
    event.preventDefault();  // Totally stop stuff happening

    // START A LOADING SPINNER HERE

    // Create a formdata object and add the files
    var data = new FormData();

    $.each(files, function(key, value)
    {
        data.append(key, value);
    });

    //data.append( '0', files[0].name );

    data.append( 'prefix', prefix );

    console.log('upload: '+prefix);
    $.ajax({
        type: "POST",
        url: "logos/upload/?files",
        data: data,
        cache: false,
        dataType: 'json',
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
        success: function(data, textStatus, jqXHR)
        {
            if(typeof data.error === 'undefined')
            {
                // Success so call function to process the form
                console.log('Success, so call function to process the form');
                submitForm(event, data);
            }
            else
            {
                // Handle errors here
                console.log('ERRORS: ' + data.error);
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // Handle errors here
            console.log('ERRORS: ' + textStatus);
            // STOP LOADING SPINNER
        }
    });

}

function submitForm(event, data)
{
    var ele = $(document.getElementById('modal_LogosStart')).find('form');
    var ser = $(ele).serialize();

    // Create a jQuery object from the form and serialize the form data
    var formData = $(document.getElementById('modal_LogosStart')).find('form').serialize();
    //console.log(formData);

    // You should sterilise the file names
    $.each(data.files, function(key, value)
    {
        formData = formData + '&filenames[]=' + value;
    });

    $.ajax({
        url: 'logos/upload',
        type: 'POST',
        data: formData,
        cache: false,
        dataType: 'json',
        success: function(data, textStatus, jqXHR)
        {
            if(typeof data.error === 'undefined')
            {
                // Success so call function to process the form
                console.log('SUCCESS: ' + data.success);
                parseFile(data.formData);
                //objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modal_LogosStart");
            }
            else
            {
                // Handle errors here
                console.log('ERRORS: ' + data.error);
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // Handle errors here
            console.log('ERRORS: ' + textStatus);
        },
        complete: function()
        {
            // STOP LOADING SPINNER
        }
    });

}


function parseFile(formData){

    $.ajax({
        type: "POST",
        url: "logos/getImage/prefix/"+prefix,
        success: function(ret) {
            var obj = jQuery.parseJSON( ret );
            $('#myImage').attr('src',path+'/app/uploads/logos/'+formData['filenames']).attr('height',obj.height).attr('width',obj.width);
            //$('#myImage').attr('height',obj.height);
            //$('#myImage').attr('width',obj.width);

        }
    });


}




