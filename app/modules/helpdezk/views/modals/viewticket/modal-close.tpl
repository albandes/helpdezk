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
                        <div class="col-sm-12 b-l confirmBox alert-info">
                            <div class="form-group control-text">
                                <div class="col-lg-8 form-control-static">
                                    <strong>{$smarty.config.Confirm_close}</strong>
                                </div>
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

                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendCloseTicket" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Yes}</button>


                </div>

            </div>

        </div>

    </form>
</div>

