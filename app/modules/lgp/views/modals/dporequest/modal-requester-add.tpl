<div class="modal fade" data-backdrop="static" id="modal-form-requester" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <div class="modal-dialog modal-lg"  role="document"> {*style="width:1250px;"*}

        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="fa fa-times-circle"></span></button>
                <h4 class="modal-title" id="myModalLabel"> {$smarty.config.Record_btn} {$smarty.config.available_note_holder}</h4>
            </div>

            <div class="modal-body">
                {*
                    Need to change "div id" if have more than 1 modal in the page,
                    and use modalAlertMultiple instead modalAlert
                    *}
                <div id="alert-evaluate"></div>

                <form role="form" id="requester-form" name="requester-form" method="post" class="form-horizontal">

                    <div class="row wrapper">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label  class="col-sm-2 control-label text-right">{$smarty.config.Name}:</label>
                                <div class="col-sm-10">
                                    <input type="text" id="requesterName" name="requesterName" class="form-control input-sm"  />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label  class="col-sm-2 control-label text-right">{$smarty.config.cpf}:</label>
                                <div class="col-sm-10">
                                    <input type="text" id="requesterCPF" name="requesterCPF" class="form-control input-sm"  />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label  class="col-sm-2 control-label text-right">{$smarty.config.email}:</label>
                                <div class="col-sm-10">
                                    <input type="text" id="requesterEmail" name="requesterEmail" class="form-control input-sm"  />
                                </div>
                            </div>
                        </div>
                    </div>

                </form>


                <div class="row col-lg-12 ">
                    <div class="form-group col-lg-12" style="padding-right: 5px;">
                        <div id="alert-requester"></div>
                    </div>
                </div>

                <div class="col-xs-12 white-bg" style="height:10px;"></div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="btnCloseRequester"data-dismiss="modal"><i class='fa fa-times'></i> {$smarty.config.Close}</button>
                <button type="submit" class="btn btn-primary" id="btnSaveRequester"><i class='fa fa-save'></i> {$smarty.config.Save}</button>
            </div>
        </div>
    </div>

</div>