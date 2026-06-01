$(document).ready(function () {
    if($("#frm-login").length <= 0 && $("#lockscreen").length <= 0){
        sessionControl.init(timesession,inactivityLimit);
    }
});