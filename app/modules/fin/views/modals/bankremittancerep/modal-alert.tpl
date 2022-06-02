
<div class="modal fade"  data-backdrop="static" id="modal-alert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <form class="form-sigin" id="alert_form" method="post">


        <div class="modal-dialog modal-md"  role="document" style="width:750px;">
            <div class="modal-content">

                <div  class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Vericação Arquivo Remessa
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class='col-sm-12 b-l bg-success'>
                            <div class='form-group'>
                                <label class='col-sm-4 control-label'>Arquivo:</label>
                                <div class='col-sm-2'>
                                    <span id='filename' class='form-control-static input-sm'></span>
                                </div>
                                <label class='col-sm-4 control-label'>Total Boletos:</label>
                                <div class='col-sm-2'>
                                    <span id='numrows' class='form-control-static input-sm'></span>
                                </div>
                            </div>
                            <div class='form-group'>
                                <label class='col-sm-4 control-label'>Sequencial:</label>
                                <div class='col-sm-2'>
                                    <span id='sequence' class='form-control-static input-sm'></span>
                                </div>
                                <label class='col-sm-4 control-label'>Boletos com Protesto:</label>
                                <div class='col-sm-2'>
                                    <span id='numprotest' class='form-control-static input-sm'></span>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='col-sm-4 control-label'>Valor Total:</label>
                                <div class='col-sm-2'>
                                    <span id='totalvalue' class='form-control-static input-sm'></span>
                                </div>
                                <label class='col-sm-4 control-label'>Boletos sem Protesto:</label>
                                <div class='col-sm-2'>
                                    <span id='numnoprotest' class='form-control-static input-sm'></span>
                                </div>
                            </div>

                            <div class="col-xs-12" style="height:20px;"></div>

                            <div class='form-group'>
                                <label class='col-sm-4 control-label'>Boletos com Envio pelo Banco:</label>
                                <div class='col-sm-2'>
                                    <span id='numsendbank' class='form-control-static input-sm'></span>
                                </div>
                                <label class='col-sm-4 control-label'>Boletos Impressos pelo Banco:</label>
                                <div class='col-sm-2'>
                                    <span id='numprintbank' class='form-control-static input-sm'></span>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='col-sm-4 control-label'>Boletos com Envio pela Escola:</label>
                                <div class='col-sm-2'>
                                    <span id='numnosendbank' class='form-control-static input-sm'></span>
                                </div>
                                <label class='col-sm-4 control-label'>Boletos Impressos pela Escola:</label>
                                <div class='col-sm-2'>
                                    <span id='numnoprintbank' class='form-control-static input-sm'></span>
                                </div>
                            </div>

                            <div class="col-xs-12" style="height:20px;"></div>

                            <div class='form-group'>
                                <label class='col-sm-4 control-label'>Boletos com Desconto:</label>
                                <div class='col-sm-2'>
                                    <span id='numdiscount' class='form-control-static input-sm'></span>
                                </div>
                                <label class='col-sm-4 control-label'>Tipo de Desconto:</label>
                                <div class='col-sm-2'>
                                    <span id='typediscount' class='form-control-static input-sm'></span>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='col-sm-4 control-label'>Boletos sem Desconto:</label>
                                <div class='col-sm-2'>
                                    <span id='numnodiscount' class='form-control-static input-sm'></span>
                                </div>
                                <label class='col-sm-4 control-label'>Desconto:</label>
                                <div class='col-sm-2'>
                                    <span id='valdiscount' class='form-control-static input-sm'></span>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

                <div class="modal-footer">

                    <a href="" id="btn-modal-ok" class="btn btn-white btn-md " role="button"><i class="fa fa-check" aria-hidden="true"></i> OK </a>
                    <a href="" id="btn-modal-print" class="btn btn-primary btn-md" role="button"><i class="fa fa-print" aria-hidden="true"></i> Imprimir Relatório </a>
                    <!--<button type="button" class="btn btn-primary btn-md " id="btn-modal-print" >
                        <span class="fa fa-check"></span>  &nbsp;Imprimir Relatório
                    </button>-->

                    <!--<a href="" id="btn-modal-ok" class="btn btn-success" role="button">
                        <span class="fa fa-check"></span>  &nbsp;OK
                    </a>-->


                </div>

            </div>

        </div>

    </form>
</div>

