<!-- Reopen Print Request in user ticket page -->
<div class="modal fade"  id="modal-print" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    {*<form class="form-sigin" id="reopen_form" method="post">*}
        <!-- Hidden input to send value by POST  -->
        <input name="coderequest" type="hidden" id="coderequest" value="{$hidden_coderequest}" />

        <div class="modal-dialog modal-sm"  role="document" style="width:1250px;">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Tckt_Request}:&nbsp; {$request_code}</h4>
                </div>

                <div class="modal-body">
                    <!--
                         Need to change "div id" if have more than 1 modal in the page,
                         and use modalAlertMultiple instead modalAlert

                    -->
                    <div id="alert-reopen"></div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">


                                <button type="button" class="btn btn-default btn-lg" id="btnModalPrint">
                                    <span class="glyphicon glyphicon-print"></span>
                                </button>

                                <button type="button" class="btn btn-default btn-lg" id="btnModalPdf">
                                    <span class="fa fa-file-pdf-o"></span>
                                </button>


                                <button type="button" class="btn btn-default btn-lg" id="btnModalXls">
                                    <span class="fa fa-file-excel-o"></span>
                                </button>


                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">

                            teste: {$hidden_coderequest}

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <!-- <button type="submit" class="btn btn-primary" id="btnSendReopen" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Send}</button> -->
                </div>

            </div>

        </div>

    {*</form>*}
</div>

