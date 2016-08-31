<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<title>HELPDEZK Setup Wizard</title>
<link type="text/css" rel="stylesheet" href="css/style.css" />
<script src="js/jquery-1.7.1.min.js"  type="text/javascript"></script>

<script   type="text/javascript">
var step = 1;

$(document).ready(function ()
{
	step_1();
})

function step_1(){
	
	$.ajax( 
    { 
        type: "POST", 
        url:  "ajax/step_1.php", 
        success: 
            function(ret) 
            { 
                //$('#mostra').empty().append(t); 
				$('#content').html(ret);
            }, 
        error: 
            function() 
            { 
                $('#content').html("An error occured during processing"); 
            } 
			
    });
		
}

function step_2(lang){
	  
	$.ajax( 
    { 
        type: "POST", 
        url: "ajax/step_2.php", 
		data: 	{
				i18n:lang
				},	
        success: 
            function(ret) 
            {
				$('#content').html(ret);
				$('#etapa1').removeClass("current").addClass("completed");
				$('#etapa2').removeClass("next").addClass("current");
            }, 
        error: 
            function() 
            { 
                alert("An error occured during processing"); 
            } 
			
    });	
}

function step_3(lang){
	
	$.ajax( 
    { 
        type: "POST", 
        url: "ajax/step_3.php", 
		data: 	{
				i18n:lang
				},	
        success: 
            function(ret) 
            {
				$('#content').html(ret);
				$('#etapa2').removeClass("current").addClass("completed");
				$('#etapa3').removeClass("next").addClass("current");
            }, 
        error: 
            function() 
            { 
                alert("An error occured during processing"); 
            } 
			
    });	
}

function step_4(lang){
	
	$.ajax( 
    { 
        type: "POST", 
        url: "ajax/step_4.php", 
		data: 	{
				i18n:lang,
				site_url:$("#field_site_url").val(),
				lang_default:$("#field_language_default").val(),
				theme_default:$("#field_theme_default").val()
				},	
        success: 
            function(ret) 
            {
				$('#content').html(ret);
				$('#etapa3').removeClass("current").addClass("completed");
				$('#etapa4').removeClass("next").addClass("current");	
            }, 
        error: 
            function() 
            { 
                alert("An error occured during processing"); 
            } 
			
    });	
}

function step_5(lang){
	//alert ($("#field_db_hostname").val());
	$.ajax( 
    { 
        type: "POST", 
        url: "ajax/step_5.php", 
		data: 	{
				i18n:lang,
				db_hostname:$("#field_db_hostname").val(),
				db_port:$("#field_db_port").val() ,
				db_name:$("#field_db_name").val() ,
				db_username:$("#field_db_username").val(),
				db_password:$("#field_db_password").val()
				},	
        success: 
            function(ret) 
            {
				$('#content').html(ret);
				$('#etapa4').removeClass("current").addClass("completed");
				$('#etapa5').removeClass("next").addClass("current");
            }, 
        error: 
            function() 
            { 
                alert("An error occured during processing"); 
            } 
			
    });	
}

function step_6(lang){
	$.ajax( 
    { 
        type: "POST", 
        url: "ajax/step_6.php", 
		data: 	{
				i18n:lang,
				admin_username:$("#field_admin_username").val(),
				admin_password:$("#field_admin_password").val() ,
				admin_email:$("#field_admin_email").val() 
				},	
        success: 
            function(ret) 
            {
				$('#content').html(ret);
				$('#etapa5').removeClass("current").addClass("completed");
				$('#etapa6').removeClass("next").addClass("current");
            }, 
        error: 
            function() 
            { 
                alert("An error occured during processing"); 
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
        success: 
            function(ret) 
            {
				$('#content').html(ret);
				$('#etapa6').removeClass("current").addClass("completed");
				$('#etapa7').removeClass("next").addClass("current");

            }, 
        error: 
            function() 
            { 
                alert("An error occured during processing"); 
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
				//$('#etapa6').removeClass("current").addClass("completed");
				$('#etapa7').removeClass("current").addClass("completed");
            }, 
        error: 
            function() 
            { 
                alert("An error occured during processing"); 
            } 
			
    });		
}

</script>


</head>
<body>

<div id="container">
	<div id="page" style='overflow:auto;'>
		<div id="header">
			<h1>HELPDEZK Setup Wizard </h1>
			<p>This wizard will guide you through the installation process</p>
		</div>
		<div id="sidebar">
			<div class="progress">
				<ul>
					<li id="etapa1" class="current">
						Select your language						
					</li>
					<li id="etapa2" class="next">
						Server requirements						
					</li>
					<li id="etapa3" class="next">
						Website URL						
					</li>
					<li id="etapa4" class="next">
						Database settings						
					</li>
					<li id="etapa5" class="next">
						Administrator account						
					</li>
					<li id="etapa6" class="next">
						Ready to install					
					</li>
					<li id="etapa7" class="next">
						Installing						
					</li>
				</ul>
			</div>
		</div>
		
		<div id="content">
		
		</div>
	</div>	
</div>
	<div id="footer"><a href="http://www.helpdezk.org" target="_blank">Helpdezk </a> &copy; 2012</div>
</body>
</html>