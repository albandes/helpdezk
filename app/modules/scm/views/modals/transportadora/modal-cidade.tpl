

<div class="modal fade"  id="modal-form-cidade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


        <input type="hidden" id="idperson" value="{$hidden_idperson}" />

        <div class="modal-dialog modal-lg"  role="document"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Cadastro de cidade</h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>



                    <form role="form" id="cidade-form"  method="post">
                        <!-- Hidden -->
                        <input type="hidden" id="hidden-idestado" />
                        <input type="hidden" name="_token" id= "_token" value="{$token}">

                        <div class="row col-lg-12 ">
                            <div class="form-group col-lg-9" style="padding-right: 5px;">
                                    <label >Estado:</label>
                                    <div >
                                        <input type="text"  id="modal-estado-nome" class="form-control input-sm"  disabled/>
                                    </div>
                            </div>

                        </div>
                        <div class="form-group col-lg-10 ">
                            <label >Cidade:</label>
                            <div >
                                <input type="text"  id="modal_cidade_nome" name="modal_cidade_nome" class="form-control input-sm" />
                            </div>
                        </div>
                        <div class="row col-lg-12 ">

                        </div>



                    </form>


                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-12" style="padding-right: 5px;">
                            <div id="alert-cidade"></div>
                        </div>
                    </div>

                    <div class="row">

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendCidade" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Send}</button>
                </div>
            </div>
        </div>
    </form>
</div>
