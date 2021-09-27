

<div class="modal fade"  id="modal-it-card-info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


            <div class="modal-dialog modal-lg"  role="document"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times-circle"></i></button>
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Additional_information}</h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>



                    <form role="form" class="form-horizontal" id="card-info-form"  method="post">
                        <!-- Hidden -->

                        <div class="row wrapper white-bg ">

                            <div class="col-lg-1 b-l">
                            </div>

                            <div class="col-lg-10 b-l">
                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-3 col-md-3 control-label">{$smarty.config.task}:</label>
                                    <div id="taskLine" class="col-lg-7 form-control-static">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-3 col-md-3 control-label">{$smarty.config.activities}:</label>
                                    <div id="activityLine" class="col-sm-7 col-lg-7 col-md-7">
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="col-lg-12 white-bg" style="height:30px;"></div>

                    </form>


                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-12" style="padding-right: 5px;">
                            <div id="alert-itcard-info"></div>
                        </div>
                    </div>

                    <div class="row">

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="btnCloseInfo" data-dismiss="modal"><i class='fa fa-times'></i> {$smarty.config.Close}</button>
                </div>
            </div>
        </div>
    </form>
</div>
