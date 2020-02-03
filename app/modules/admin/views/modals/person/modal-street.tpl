

<div class="modal fade"  id="modal-form-street" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


    <div class="modal-dialog modal-lg"  role="document"> {*style="width:1250px;"*}

        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Cadastro de {$smarty.config.Adress}</h4>
            </div>

            <div class="modal-body">
                {*
                 Need to change "div id" if have more than 1 modal in the page,
                 and use modalAlertMultiple instead modalAlert
                 *}
                <div id="alert-evaluate"></div>


                <form role="form" id="street-form"  method="post" class="form-horizontal">
                    <!-- Hidden -->

                    <input type="hidden" name="_token" id= "_token" value="{$token}">

                    <div class="row col-lg-12 ">
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{$smarty.config.Type_adress}:</label>
                            <div class="col-lg-5">
                                <select class="form-control input-sm" name="modalTypeStreet" id="modalTypeStreet" {$person_typestreet_disabled}>
                                    {html_options values=$typestreetids output=$typestreetvals selected=$idtypestreet}
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">{$smarty.config.Adress}:</label>
                            <div class="col-lg-8">
                                <input type="text" id="modalStreetName" name="modalStreetName" class="form-control input-sm" />
                            </div>
                        </div>
                    </div>

                </form>


                <div class="row col-lg-12 ">
                    <div class="form-group col-lg-12" style="padding-right: 5px;">
                        <div id="alert-street"></div>
                    </div>
                </div>

                <div class="row">

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                <button type="submit" class="btn btn-primary" id="btnSendStreet" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Save}</button>
            </div>
        </div>
    </div>
    </form>
</div>
