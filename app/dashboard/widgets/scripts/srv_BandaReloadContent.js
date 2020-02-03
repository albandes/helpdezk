
$(function() {
 
  	
	window.dashboardDemo.widgets['srv-banda'].contentElement.empty().append('<img src="images/throbber.gif" />');
	banda();


});


	function banda()
	{
		
		var t = setTimeout("banda()",180000); // Loop

		$.ajax({
			url: "widgets/ajax/srv_banda.php",
			cache: false,
			dataType: "json",
			success: function(retorno) {
				window.dashboardDemo.widgets['srv-banda'].contentElement.empty().append(retorno.texto);				
			}
		});	
		

	}		
