
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <!-- Released under the GNU General Public License.  See LICENSE.txt. -->
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>jQuery.dashboard() plugin</title>

    <!-- Include jQuery.dashboard() and dependencies -->
    <script type="text/javascript" src="classes/jquery/jquery-1.4.4.js"></script>
    <script type="text/javascript" src="jquery-ui-personalized-1.6rc6.min.js"></script>
	
	<?php $tema = "mq"; ?>
	<script type="text/javascript" >
		var tema = "<?=$tema?>"
		var temaimg = "temas/"+tema+"/images/" ;
		$(document).ready(function() {
			//alert('tema: '+tema);
		});
	  
	</script>
	
	
    <script type="text/javascript" src="jquery.dashboard.js"></script>
	
	 

	<!-- Adsense e Analytics -->	
	
	<link rel="stylesheet" type="text/css" href="widgets/css/analytics.css" />
	
	<!-- Plot -->
	<!--[if IE]><script language="javascript" type="text/javascript" src="widgets/classes/excanvas/excanvas.js"></script><![endif]-->
	<script language="javascript" type="text/javascript" src="widgets/classes/query.flot/jquery.flot.js"></script>
	<script language="javascript" type="text/javascript" src="widgets/classes/query.flot/jquery.flot.pie.js"></script>
	
	<!-- Jquery Datatables -->
	<script language="javascript" type="text/javascript" src="widgets/classes/jquery.datatables/jquery.dataTables.js"></script>	
	<style type="text/css" title="currentStyle">
		@import "widgets/css/demo_page.css";
		@import "widgets/css/demo_table.css";
	</style>

	
    <!-- Include the demo implementation's files -->
    <script type="text/javascript" src="demo.js"></script>
    <link rel="stylesheet" type="text/css" href="demo.css" />
	
  </head>
  <body>
  
    <div id="dashboard-demo">
      <!-- You can put anything you like here.  jQuery.dashboard() will remove it. -->
      You need javascript to use the dashboard.
    </div>
  </body>
</html>
