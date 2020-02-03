
<div class="modal fade"  data-backdrop="static" id="modal-approve-require" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <form class="form-msg" id="apvrequire_form" method="post">


        <div class="modal-dialog modal-md"  role="document">
            <div class="modal-content">

                <div  class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Notification}</h4>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-12">
                            <div id="tipo-alert-apvrequire" name="tipo-alert-apvrequire" >
                                <spam id="apvrequire-notification"></spam>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-primary" id="btnSendApvReqYes" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Yes}</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.No}</button>

                </div>

            </div>

        </div>

    </form>
</div>

