

<div class="modal fade" data-backdrop="static" id="modal-form-service" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


        <div class="modal-dialog modal-lg"  role="document"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span id="serviceModalTitle"></span></h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>


                    <form role="form" id="service_form" name="service_form" method="post" class="form-horizontal">
                        <input type="hidden" id="iditem_service" name="iditem_service" value="" />
						<input type="hidden" id="idservice_modal" name="idservice_modal" value="" />
                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>

                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.Service_name}:</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="modal_service_name" name="modal_service_name" class="form-control input-sm"  />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.Group}:</label>
                                    <div class="col-sm-5">
                                        <select class="form-control form-control-sm" name="modal_cmbGroup" id="modal_cmbGroup">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.Priority}:</label>
                                    <div class="col-sm-5">
                                        <select class="form-control form-control-sm" name="modal_cmbPriority" id="modal_cmbPriority">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <div class="form-inline">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.Attendance_time}:</label>
                                    <div class="col-sm-4">

                                    </div>
                                    <div class="col-sm-9 form-inline">
                                        <input class="form-control input-sm" type="text" value="0" id="limit_days" name="limit_days" size="3">
                                        &nbsp;{$smarty.config.Days} &nbsp;{$smarty.config.and} &nbsp;
                                        <input class="form-control input-sm" type="text" value="0" id="limit_time" name="limit_time" size="3"> &nbsp;
                                        <label class="radio-inline i-checks"> <input type="radio" name="time" id="hours" value="H" checked>&nbsp;&nbsp;{$smarty.config.Hours}</label>
                                        <label class="radio-inline i-checks"> <input type="radio" name="time" id="minutes" value="M">&nbsp;&nbsp;{$smarty.config.Minutes}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.Available}:</label>
                                    <div class="checkbox i-checks"><label> <input type="checkbox" id="checkServAvailable" name="checkServAvailable"> <i></i> &nbsp;{$smarty.config.Available_text}</label></div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.Default}:</label>
                                    <div class="checkbox i-checks"><label> <input type="checkbox" id="checkServDefault" name="checkServDefault"> <i></i> &nbsp;{$smarty.config.Default_text}</label></div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.Classification}:</label>
                                    <div class="checkbox i-checks"><label> <input type="checkbox" id="checkServClassification" name="checkServClassification"> <i></i> &nbsp;{$smarty.config.Classification_text}</label></div>
                                </div>
                            </div>
                        </div>


                        <div class="row col-lg-12 ">

                        </div>



                    </form>


                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-12" style="padding-right: 5px;">
                            <div id="alert-modal-service"></div>
                        </div>
                    </div>

                    <div class="row">

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        {$smarty.config.Close}
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSaveService" data-loading-text="<i class='fa fa-spinner fa-spin'></i> {$smarty.config.Processing}">
                        <i class='fa fa-save'></i>
                        {$smarty.config.Save}
                    </button>
                </div>
            </div>
        </div>
</div>
