<!-- Alert modal in user new ticket page -->
<div class="modal fade" data-backdrop="static" id="modal-form-grp-repass" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <form class="form-horizontal" id="grp-repass-form" method="post">
        
        <div class="modal-dialog modal-md"  role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Set_repass_groups}
                </div>

                <div class="modal-body">

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label  class="col-sm-3 control-label">{$smarty.config.Group}:</label>
                                <div class="col-sm-9">
                                    <select class="form-control  m-b" name="cmbGroupsRepass" id="cmbGroupsRepass" data-placeholder=" ">
                                        {html_options values=$grpsrepassids output=$grpsrepassvals selected=$idgrpsrepass}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 white-bg" style="height:30px;"></div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12">
                            <div class="panel-group">
                                <div id="loaderGrpsRepass" class="text-center"></div>
                                <div id="panelGrpsRepass" class="panel panel-primary hide">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">{$smarty.config.List_comp_groups}</h4>
                                    </div>
                                    <div id="tab-groups-repass" class="scrollable-panel"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12  b-l">
                            <div id="alert-modal-groups-repass"></div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        {$smarty.config.Close}
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSaveSetGrpRepass" data-loading-text="<i class='fa fa-spinner fa-spin'></i> {$smarty.config.Processing}">
                        <i class='fa fa-save'></i>
                        {$smarty.config.Save}
                    </button>
                </div>

            </div>

        </div>

    </form>
</div>

