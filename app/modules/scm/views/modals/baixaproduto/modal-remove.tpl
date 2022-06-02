<div class="modal fade"  id="modal-form-status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->

            <div class="modal-dialog modal-md"  role="document"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Exclus&atilde;o da Baixa do Produto</h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>

                    <form role="form" id="remove-form" method="post" class="form-horizontal" >
                        <!-- Hidden -->

                        <input type="hidden" name="_token" id= "_token">
                        <input type="hidden" name="idbaixa" id= "idbaixa">

                        <div id="questionLine" class="row">
                            <div id="questionContent" class="col-sm-12 alert alert-warning">
                                Deseja realmente excluir esta Baixa de Produto?
                            </div>
                        </div>
                    </form>

                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-12" style="padding-right: 5px;">
                            <div id="alert-motivo"></div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="btnCloseRemove" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveRemove"><i class='fa fa-check'></i> Sim</button>
                </div>
            </div>
        </div>
    </form>
</div>
