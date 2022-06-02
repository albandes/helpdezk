<!-- Alert modal in user new ticket page -->
<div class="modal fade" data-backdrop="static" id="modal-form-peoplegroup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <form class="form-horizontal" id="peoplegroup-form" method="post">
        
        <div class="modal-dialog modal-md"  role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Attendants_by_group}
                </div>

                <div class="modal-body">

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label  class="col-sm-5 control-label">{$smarty.config.Show_people_title}:</label>
                                <div class="col-sm-6">
                                    <select class="form-control  m-b" name="cmbGroups" id="cmbGroups" data-placeholder=" ">
                                        {html_options values=$groupsIds output=$groupsVals selected=$idgroup}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 white-bg" style="height:30px;"></div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12">
                            <div class="panel-group">
                                <div id="loaderPanel" class="text-center"></div>
                                <div id="panelPeople" class="panel panel-primary hide">
                                    <div id="tab-peoples" class="scrollable-panel"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12  b-l">
                            <div id="alert-modal-people-group"></div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <!--<button type="submit" class="btn btn-primary" id="btnSendAuxOpe" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Repass_btn}</button>-->

                </div>

            </div>

        </div>

    </form>
</div>

