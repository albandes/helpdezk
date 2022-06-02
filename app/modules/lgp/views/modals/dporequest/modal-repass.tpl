<!-- Alert modal in user new ticket page -->
<div class="modal fade" data-backdrop="static" id="modal-form-repass" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <form class="form-horizontal" id="repass-form" method="post">
        <!-- Hidden input to send value by POST  -->
        <input name="coderequest" type="hidden" id="coderequest" value="{$hidden_coderequest}" />

        <div class="modal-dialog modal-sm"  role="document" style="width:850px">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Repass_btn} {$smarty.config.Request}
                </div>

                <div class="modal-body">

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-10 b-l">
                            <div class="form-group">
                                <label  class="col-sm-4 control-label">{$smarty.config.Repass_request_to}:</label>
                                <div class="col-sm-5">
                                    <label class="radio-inline i-checks"> <input type="radio" name="typerep" id="cmbGroup" value="group" checked>&nbsp;{$smarty.config.Group}</label>
                                    <label class="radio-inline i-checks"> <input type="radio" name="typerep" id="cmbOperator" value="operator">&nbsp;{$smarty.config.Operator}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-10 b-l">
                            <div class="form-group">
                                <label  class="col-sm-4 control-label">&nbsp;</label>
                                <div class="col-sm-7">
                                    <select class="form-control  form-control-sm" name="replist" id="replist" data-placeholder=" ">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-10 b-l">
                            <div class="form-group">
                                {if $displayViewGroup == 1}
                                    <div class="col-sm-9"><label class="radio i-checks"> <input type="radio" name="repoptns" id="repoptnsG" value="G" {$checkedassume}>&nbsp;{$smarty.config.Group_still_viewing}</label></div>
                                    {if $typeincharge == "P"}
                                    <div id="OpeGroupsList" class="col-sm-12 {if !$checkedassume} hide {/if}">
                                        <label class="col-sm-2 control-label">{$smarty.config.Group}:</label>
                                        <div class="col-sm-5">
                                            <select class="form-control  m-b" name="cmbOpeGroups" id="cmbOpeGroups">
                                                {html_options values=$grpids output=$grpvals selected=$idgrp}
                                            </select>
                                        </div>
                                    </div>
                                    {/if}
                                {/if}
                                <div class="col-sm-9"><label class="radio i-checks"> <input type="radio" name="repoptns" id="repoptnsP" value="P">&nbsp;{$smarty.config.Still_viewing}</label></div>
                                <div class="col-sm-9"><label class="radio i-checks"> <input type="radio" name="repoptns" id="repoptnsN" value="N" {if !$checkedassume} checked="checked" {/if}>&nbsp;{$smarty.config.Stop_viewing}</label></div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-lg-12 ">
                            <div class="form-group">
                                <div id="alert-repass-form"></div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="btnCancelRepassTicket" data-dismiss="modal"><i class="fa fa-times"></i>  {$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveRepassTicket"><i class="fa fa-share"></i>  {$smarty.config.Repass_btn}</button>
                </div>

            </div>

        </div>

    </form>

    <style>
        .scrollable-panel{
            max-height: 150px;
            overflow: auto;
        }
    </style>
</div>

