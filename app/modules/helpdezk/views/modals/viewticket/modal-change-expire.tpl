<!-- Alert modal in user new ticket page -->
<div class="modal fade" data-backdrop="static" id="modal-form-changeexpire" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <form class="form-horizontal" id="changeexpire-form" method="post">
        <!-- Hidden input to send value by POST  -->
        <input name="coderequest" type="hidden" id="coderequest" value="{$hidden_coderequest}" />

        <div class="modal-dialog modal-sm"  role="document" style="width:850px">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Change_date}
                </div>

                <div class="modal-body">

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label  class="col-sm-2 control-label">{$smarty.config.Current_date}:</label>
                                <div class="col-sm-4 form-control-static">
                                    <p id="lblExpDate">{$mod_expire_date}</p>
                                </div>
                                <label  class="col-sm-2 control-label">{$smarty.config.Current_time}:</label>
                                <div class="col-sm-4 form-control-static">
                                    <p id="lblExpHour">{$mod_expire_hour}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label  class="col-sm-2 control-label">{$smarty.config.New_date}:</label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                        <input type="text" id="dateChangeExpire" name="dateChangeExpire" class="form-control input-sm" value="{$now}" readonly />
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                                <label  class="col-sm-2 control-label">{$smarty.config.New_time}:</label>
                                <div class="col-sm-4">
                                    <div class="input-group clockpicker">
                                        <input type="text" id="requesttime" name="requesttime" class="form-control" value="{$timedefault}" readonly>
                                        <span class="input-group-addon">
                                        <span class="fa fa-clock-o"></span>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label  class="col-sm-2 control-label">{$smarty.config.Reason}:</label>
                                <div class="col-sm-10 form-control-static">
                                    <div id="reasonchangeexpire"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-lg-12 ">
                            <div class="form-group">
                                <div id="alert-changeexpire-form"></div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendChangeExpireDate" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Save}</button>


                </div>

            </div>

        </div>

    </form>
</div>

