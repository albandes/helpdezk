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
                                <div class="col-sm-4">
                                    <button type="button" id="btnAbilities" class="btn btn-default">{$smarty.config.Abilities}</button>
                                    <button type="button" id="btnGroups" class="btn btn-white off">{$smarty.config.Groups}</button>
                                </div>
                                <div class="col-sm-7">
                                    <div class="panel-group">
                                        <div id="panelSearch" class="panel panel-primary">
                                            <div class="panel-heading">
                                                <h4 class="panel-title" id="titleAbiGrp"></h4>
                                            </div>
                                            <div id="tabAbiGrp" class="scrollable-panel"></div>
                                        </div>
                                    </div>
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
                                <div class="col-sm-9"><label class="radio i-checks"> <input type="radio" name="repoptns" id="repoptnsN" value="N">&nbsp;{$smarty.config.Stop_viewing}</label></div>
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

                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendRepassTicket" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Repass_btn}</button>


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

