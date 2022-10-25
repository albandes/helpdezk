$(document).ready(function () {
    if(typeUser == 3){
        var flgApvRequire = 0;
    }else{
        if(flgOperator == 0){
            var flgApvRequire = $.ajax({type: "POST",url: path+"/helpdezk/hdkTicket/checkApproval",async: false}).responseText;
        }
    }

    $("#btnNewTck").click(function(e){
        if(typeUser == 3){
            location.href = path + "/helpdezk/hdkTicket/newTicket" ;
        }else{
            if(flgOperator == 1){
                location.href = path + "/helpdezk/hdkTicket/newTicket" ;
            }else{
                if(flgApvRequire > 0){
                    $("#approvalRequireText h4").html(vocab['Request_approve']);
                    $('#modal-approval-require').modal('show');
                }else{
                    location.href = path + "/helpdezk/hdkTicket/newTicket" ;
                }
            }
        }
        return false;
    });

    $("#btnApprovalRequireYes").click(function(){
        location.href = path + "/helpdezk/hdkTicket/index" ;
    });
});