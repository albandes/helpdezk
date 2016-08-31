<?php

?>

        <script type="text/javascript" src="/".path."/includes/classes/jquery/jquery-1.7.1.min.js"></script>
<script src="/".path."/includes/classes/jquery.countdown.package-1.5.11/jquery.countdown.js"></script>
<link rel="stylesheet" type="text/css" href="/".path."/includes/classes/jscalendar1.9jquery.countdown.package-1.5.11/jquery.countdown.css" /> 
<script src="/".path."/includes/classes/jquery.countdown.package-1.5.11/jquery.countdown-pt-BR.js"></script>
<style type="text/css">
    #defaultCountdown { width: 240px; height: 45px; }
</style>
<script>
var longWayOff = new Date();
longWayOff.setDate(longWayOff.getDate() + 500);
var liftoffTime = new Date();
liftoffTime.setDate(liftoffTime.getDate() + 5);
var startYear = new Date();
startYear = new Date(startYear.getFullYear(), 1 - 1, 1, 0, 0, 0);
var shortly = null;

 $('#defaultCountdown').countdown({since: startYear, compact: true, 
    format: 'HMS', description: ''});
	</script>
	
	
	<div id="defaultCountdown">
		
	</div>
