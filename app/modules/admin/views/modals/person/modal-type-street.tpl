

<div class="modal fade"  id="modal-form-typestreet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


    <div class="modal-dialog modal-lg"  role="document"> {*style="width:1250px;"*}

        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Cadastro de logradouro</h4>
            </div>

            <div class="modal-body">
                {*
                 Need to change "div id" if have more than 1 modal in the page,
                 and use modalAlertMultiple instead modalAlert
                 *}
                <div id="alert-evaluate"></div>


                <form role="form" id="typestreet-form"  method="post" class="form-horizontal">
                    <!-- Hidden -->

                    <input type="hidden" name="_token" id= "_token" value="{$token}">

                    <div class="row col-lg-12 ">
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{$smarty.config.Type_adress}:</label>
                            <div class="col-lg-8">
                                <input type="text" id="modal-typestreet-name" name="modal-typestreet-name" class="form-control input-sm" />
                            </div>
                        </div>
                    </div>

                    <div class="row col-lg-12 ">
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{$smarty.config.Abbreviation}:</label>
                            <div class="col-lg-2">
                                <input type="text" id="modal-typestreet-abbr" name="modal-typestreet-abbr" class="form-control input-sm" />
                            </div>
                        </div>
                    </div>

                </form>


                <div class="row col-lg-12 ">
                    <div class="form-group col-lg-12" style="padding-right: 5px;">
                        <div id="alert-typestreet"></div>
                    </div>
                </div>

                <div class="row">

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                <button type="submit" class="btn btn-primary" id="btnSendTypeStreet" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Save}</button>
            </div>
        </div>
    </div>
    </form>
</div>
