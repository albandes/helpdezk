<!-- Alert modal in user new ticket page -->
<div class="modal fade" data-backdrop="static" id="modal-form-reject" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <form class="form-horizontal" id="reject-form" method="post">
        <!-- Hidden input to send value by POST  -->
        <input name="coderequest" type="hidden" id="coderequest" value="{$hidden_coderequest}" />

        <div class="modal-dialog modal-sm"  role="document" >
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Reason}
                </div>

                <div class="modal-body">

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label  class="col-sm-2 control-label">{$smarty.config.Reason}:</label>
                                <div class="col-sm-10 form-control-static">
                                    <div id="reasonreject"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-lg-12 ">
                            <div class="form-group">
                                <div id="alert-reject-form"></div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendRejectTicket" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Reject_btn}</button>


                </div>

            </div>

        </div>

    </form>
</div>

