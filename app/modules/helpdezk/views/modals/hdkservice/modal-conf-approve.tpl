

<div class="modal fade" data-backdrop="static" id="modal-form-conf-apprv" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


        <div class="modal-dialog modal-lg"  role="document"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.conf_approvals}</h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>


                    <form role="form" id="conf_apprv_form" name="conf_apprv_form" method="post" class="form-horizontal">

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.type}:</label>
                                    <div class="col-sm-5">
                                        <select class="form-control form-control-sm" name="confCmbType" id="confCmbType">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.Item}:</label>
                                    <div class="col-sm-5">
                                        <select data-placeholder="{$smarty.config.Alert_choose_type}" class="form-control form-control-sm" name="confCmbItem" id="confCmbItem">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.Service}:</label>
                                    <div class="col-sm-5">
                                        <select data-placeholder="{$smarty.config.Alert_choose_item}" class="form-control form-control-sm" name="confCmbService" id="confCmbService">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.User}:</label>
                                    <div class="col-sm-5">
                                        <select data-placeholder="&nbsp;" class="form-control form-control-sm" name="confCmbUser" id="confCmbUser">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row col-lg-12 ">

                        </div>

                        <div class="row wrapper ">
                            <div class="col-sm-12 b-l">
                                <table id='browser' class="table table-hover">
                                    <colgroup>
                                        <col width='85%'/>
                                        <col width='5%'/>
                                        <col width='5%'/>
                                        <col width='5%'/>
                                    </colgroup>
                                    <tbody id="userslist">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.Recalculate}:</label>
                                    <div class="checkbox i-checks"><label> <input type="checkbox" id="checkRecalculate" name="checkRecalculate"> <i></i> &nbsp;{$smarty.config.Recalculate_msg_chk}</label></div>
                                </div>
                            </div>
                        </div>

                    </form>

                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-12" style="padding-right: 5px;">
                            <div id="alert-modal-confApprv"></div>
                        </div>
                    </div>

                    <div class="row">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        {$smarty.config.Close}
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSaveConfApprv" data-loading-text="<i class='fa fa-spinner fa-spin'></i> {$smarty.config.Processing}">
                        <i class='fa fa-save'></i>
                        {$smarty.config.Save}
                    </button>
                </div>
            </div>
        </div>
</div>
