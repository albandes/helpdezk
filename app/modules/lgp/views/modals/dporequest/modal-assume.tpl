<!-- Alert modal in user new ticket page -->
<div class="modal fade" data-backdrop="static" id="modal-form-assume" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <form class="form-horizontal" id="assume-form" method="post">

        <div class="modal-dialog modal-sm"  role="document" >
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Assume_request}
                </div>

                <div class="modal-body">

                    <div class="row wrapper white-bg groupAssumeLine">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label  class="col-sm-10 control-label">{$smarty.config.Group_still_viewing}:</label>
                                <div class="col-sm-2">
                                    <label class="checkbox-inline i-checks"> <input type="checkbox" name="grpkeep" id="grpkeep" value="S" {$checkedassume}></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper white-bg groupAssumeLine">
                        {if $typeincharge == "P"}
                        <div id="assumeGroupsList" class="col-sm-10 b-l {if !$checkedassume} hide {/if}">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Group}:</label>
                                <div class="col-sm-8">
                                    <select class="form-control  m-b" name="cmbAssumeGroups" id="cmbAssumeGroups">
                                        {html_options values=$grpids output=$grpvals selected=$idgrp}
                                    </select>
                                </div>
                            </div>
                        </div>
                        {/if}
                    </div>

                    <div class="row dpoAssumeLine">
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group alert-hdk alert-warning">
                                <div class="col-sm-2 col-xs-12 text-center"><i class='fa fa-info-circle fa-2x'></i></div>
                                <div id="assume-message" class="col-sm-10 col-xs-12 text-left input-md">{$smarty.config.info_assume}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-lg-12 ">
                            <div class="form-group">
                                <div id="alert-assume-form"></div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="btnCancelAssumeTicket" data-dismiss="modal"><i class="fa fa-times-circle"></i>  {$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveAssumeTicket"><i class="fa fa-check-square"></i>  {$smarty.config.btn_assume}</button>
                </div>

            </div>

        </div>

    </form>
</div>

