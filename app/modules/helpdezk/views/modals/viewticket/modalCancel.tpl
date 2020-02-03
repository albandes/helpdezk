<!-- Cancel modal in user ticket page -->
<div class="modal fade"  id="modal-form-cancel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <form class="form-sigin" id="cancel_form" method="post">
        <!-- Hidden input to send value by POST  -->
        <input name="coderequest" type="hidden" id="coderequest" value="{$hidden_coderequest}" />

        <div class="modal-dialog modal-sm"  role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Tckt_Request}:&nbsp; {$request_code}</h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-cancel"></div>

                    <div class="row">
                        <div class="col-sm-12">

                                {$smarty.config.Tckt_cancel_request}

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendCancel" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Send}</button>
                </div>

            </div>

        </div>

    </form>
</div>

<!--</div>-->