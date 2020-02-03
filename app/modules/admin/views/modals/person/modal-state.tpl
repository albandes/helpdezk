

<div class="modal fade" data-backdrop="static" id="modal-form-state" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


        <div class="modal-dialog modal-lg"  role="document"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Cadastro de estado</h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>

                    <form role="form" id="state-form"  method="post" class="form-horizontal">

                        <div class="row col-lg-12 ">
                            <div class="form-group">
                                <label class="col-lg-2 control-label">{$smarty.config.Country}:</label>
                                <div class="col-lg-8 form-control-static">
                                    <span id="txtCountry"></span>
                                    <input type="hidden" id="modalIdCountry" name="modalIdCountry" class="form-control input-sm" value=""/>
                                </div>
                            </div>
                        </div>

                        <div class="row col-lg-12 ">
                            <div class="form-group">
                                <label class="col-lg-2 control-label">{$smarty.config.State}:</label>
                                <div class="col-lg-8">
                                    <input type="text" id="modalStateName" name="modalStateName" class="form-control input-sm" />
                                </div>
                            </div>
                        </div>
                        
                        <div class="row col-lg-12 ">
                            <div class="form-group">
                                <label class="col-lg-2 control-label">{$smarty.config.Abbreviation}:</label>
                                <div class="col-lg-2">
                                    <input type="text" id="modalStateAbbr" name="modalStateAbbr" class="form-control input-sm" />
                                </div>
                            </div>
                        </div>

                    </form>

                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-12" style="padding-right: 5px;">
                            <div id="alert-state"></div>
                        </div>
                    </div>

                    <div class="row">

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendState" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Save}</button>
                </div>
            </div>
        </div>
    </form>
</div>
