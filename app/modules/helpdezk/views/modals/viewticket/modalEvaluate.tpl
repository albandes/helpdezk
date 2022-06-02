<!-- Evaluate modal in user ticket page -->

<div class="modal fade"  id="modal-form-evaluate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <!-- Hidden input to send value by POST  -->
    <form class="form-sigin" id="evaluate_form" method="post">
        <input name="coderequest" type="hidden" id="coderequest" value="{$hidden_coderequest}" />
        <div class="modal-dialog modal-lg" style="width:1250px;" role="document">

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
                    <div id="alert-evaluate"></div>

                    <div class="row">
                        <div class="form-group">
                                <div class="col-sm-12">
                                    <label class="col-sm-12 control-label">{$smarty.config.Approve_text}</label>
                                    <div class="col-sm-12">
                                        <div class="radio i-checks"><label> <input type="radio"  name="approve" id="approve" value="A"  required> <i></i>&nbsp;&nbsp;{$smarty.config.Approve_yes}</label></div>
                                        <div class="radio i-checks"><label> <input type="radio"  name="approve" id="approve" value="N"                   required> <i></i>&nbsp;&nbsp;{$smarty.config.Approve_no} </label></div>
                                        <div class="radio i-checks"><label> <input type="radio"  name="approve" id="approve" value="O"                   required> <i></i>&nbsp;&nbsp;{$smarty.config.Approve_obs}</label></div>
                                    </div>
                                </div>
                        </div>
                    </div>

                    <div class="row" id="aprove-obs" style="display:none;">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="col-sm-8">
                                    <label class="col-sm-12 control-label">{$smarty.config.Observation}</label>
                                    <div class="col-sm-12">
                                        <textarea class="form-control" rows="5" id="observation" required></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="questions" style="display:none;" > <!-- style="display:none;" -->
                        <div class="form-group">

                                <div class="col-sm-12">
                                    {$questions}
                                </div>

                        </div>
                    </div>


                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendEvaluate" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Send}</button>



                </div>
            </div>
        </div>
    </form>
</div>

<!--</div>-->