<!-- Alert modal in user new ticket page -->
<div class="modal fade" data-backdrop="static" id="modal-form-close" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <form class="form-horizontal" id="close-form" method="post">
        <!-- Hidden input to send value by POST  -->
        <input name="coderequest" type="hidden" id="coderequest" value="{$hidden_coderequest}" />

        <div class="modal-dialog modal-sm"  role="document" >
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Tckt_finish_request}</h4>
                </div>

                <div class="modal-body">

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group alert-hdk alert-info">
                                <div class="col-sm-2 col-xs-12 text-center"><i class='fa fa-question-circle fa-2x'></i></div>
                                <div id="close-message" class="col-sm-10 col-xs-12 text-left input-md"><strong>{$smarty.config.Confirm_close}</strong></div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-lg-12 ">
                            <div class="form-group">
                                <div id="alert-close-form"></div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" d="btnCancelCloseTicket" data-dismiss="modal"><i class='fa fa-times-circle'></i>  {$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveCloseTicket"><i class='fa fa-check'></i>  {$smarty.config.Yes}</button>
                </div>

            </div>

        </div>

    </form>
</div>

