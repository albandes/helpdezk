

<div class="modal fade" data-backdrop="static" id="modal-config-external" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


    <div class="modal-dialog modal-md"  role="document"> {*style="width:1250px;"*}

        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{$smarty.config.trello}</h4>
            </div>

            <div class="modal-body">
                {*
                 Need to change "div id" if have more than 1 modal in the page,
                 and use modalAlertMultiple instead modalAlert
                 *}
                <div id="alert-evaluate"></div>


                <form role="form" id="modal-config-external-form"  method="post" class="form-horizontal">
                    <!-- Hidden -->
                    <input type="hidden" id="hidden-idperson" value="{$id_person}"/>

                    <div class="row col-lg-12 ">
                        <div class="row col-lg-12  b-l">
                            <div class="form-group col-lg-12">
                                <label class="col-md-4 control-label text-right"><i class="fab fa-trello"></i>&nbsp;&nbsp;{$smarty.config.trello_key}:</label>
                                <div class="col-md-5">
                                    <input type="text" id="trello_key" name="trello_key" class="form-control input-sm" value="{$trello_key}" >
                                </div>
                            </div>
                        </div>
                        <div class="row col-lg-12 b-l">
                            <div class="form-group col-lg-12">
                                <label class="col-md-4 control-label text-right"><i class="fab fa-trello"></i>&nbsp;&nbsp;{$smarty.config.trello_token}:</label>
                                <div class="col-md-8">
                                    <input type="text" id="trello_token" name="trello_token" class="form-control input-sm" value="{$trello_token}" >
                                </div>
                            </div>
                        </div>
                    </div>

                </form>


                <div class="row col-lg-12 ">
                    <div class="form-group col-lg-12" style="padding-right: 5px;">
                        <div id="alert-config-external"></div>
                    </div>
                </div>

                <div class="row">

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="btnCancelConfigExternal" data-dismiss="modal">{$smarty.config.Close}</button>
                <button type="submit" class="btn btn-primary" id="btnSaveConfigExternal" ><i class="fa fa-save"></i>   {$smarty.config.Save}</button>
            </div>
        </div>
    </div>
    </form>
</div>

