

<div class="modal fade"  id="modal-form-logradouro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

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



                <form role="form" id="logradouro-form"  method="post">
                    <!-- Hidden -->

                    <input type="hidden" name="_token" id= "_token" value="{$token}">

                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-9" style="padding-right: 5px;">
                            <label >Logradouro:</label>
                            <div >
                                <input type="text"  id="modal-logradouro-nome" class="form-control input-sm" />
                            </div>
                        </div>

                    </div>
                    <div class="row col-lg-12 ">

                    </div>



                </form>


                <div class="row col-lg-12 ">
                    <div class="form-group col-lg-12" style="padding-right: 5px;">
                        <div id="alert-logradouro"></div>
                    </div>
                </div>

                <div class="row">

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                <button type="submit" class="btn btn-primary" id="btnSendLogradouro" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Send}</button>
            </div>
        </div>
    </div>
    </form>
</div>
