<!-- Alert modal in user new ticket page -->
<div class="modal fade" data-backdrop="static" id="modal-dialog-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >


        <div class="modal-dialog modal-md"  role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"></h4>
                </div>

                <div class="modal-body">
                    <form role="form" class="form-horizontal" id="delete_department_form" name="delete_department_form" method="post">
                        <input type="hidden" id="iddepartment_modal" name="iddepartment_modal" value="" />
                        <input type="hidden" id="has_person" name="has_person" value="" />

                        <div id="depHasPersonLine" class="row hide">
                            <div class="col-sm-12 col-xs-12 bs-callout bs-callout-danger">
                                <div class="form-group text-center">
                                    <h4 class="col-sm-12">{$smarty.config.Alert_department_person}</h4>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{$smarty.config.Company}:</label>
                                    <div class="col-sm-5 form-control-static">
                                        <span id="companyName"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{$smarty.config.Department}:</label>
                                    <div class="col-sm-5">
                                        <select class="form-control input-sm"  id="cmbDepartment" name="cmbDepartment" data-placeholder="{$plh_category_select}" >
                                            <option value="">{$smarty.config.Select}</option>
                                            {html_options values=$departmentids output=$departmentvals selected=$iddepartment}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 white-bg" style="height:10px;"></div>

                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group alert-hdk alert-warning">
                                    <div class="col-sm-1 col-xs-12 text-center"><i class='fa fa-question-circle fa-2x'></i></div>
                                    <div class="col-sm-8 col-xs-12 text-center input-sm"><h4>{$smarty.config.Delete_record}</h4></div>
                                </div>
                            </div>
                        </div>

                    </form>

                    <div class="row">
                        <div class="col-sm-12">
                            <div id="alert-delete-department"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="btnCloseDelete" data-dismiss="modal">
                        <i class='fa fa-ban'></i>
                        {$smarty.config.No}
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSaveDelete" data-loading-text="<i class='fa fa-spinner fa-spin'></i> {$smarty.config.Processing}">
                        <i class='fa fa-check-circle'></i>
                        {$smarty.config.Yes}
                    </button>
                </div>



            </div>

        </div>

</div>

