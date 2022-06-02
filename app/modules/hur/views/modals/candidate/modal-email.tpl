

<div class="modal fade"  id="modal-form-email" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


            <div class="modal-dialog modal-lg"  role="document" style="width:850px;"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Envio Email do Curr&iacute;culo # {$hidden_idcurriculum}</h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>



                    <form role="form" id="email-form"  method="post" class="form-horizontal">
                        <!-- Hidden -->

                        <input type="hidden" name="_token" id= "_token" value="{$token}">
                        <input type="hidden" name="idcurriculumemail" id= "idcurriculumemail" value="{$hidden_idcurriculum}">

                        <div class="row wrapper  white-bg ">
                            <input type="hidden" id="_totalto" value="1">
                            <div class="col-sm-11 b-l to" id="to">
                                <div class="form-group" id="to_1">
                                    <label class="col-sm-2 control-label">Para:</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="toAddress[]" id="toAddress_1" class="form-control input-sm" value=""  />
                                    </div>
                                    <div class="col-sm-1">
                                        <div class="btn-group">
                                            <button class="btn btn-success btn-sm" id="btnAddTo" type="button"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-11 b-l">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Assunto:</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="emailtitle" id="emailtitle" class="form-control input-sm" value=""  />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-11 b-l">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Anexar:</label>
                                    <div class="col-sm-8">
                                        <label class="checkbox-inline i-checks"> <input type="checkbox" name="curriculumItem[]" value="D">&nbsp;Dados Cadastrados</label>
                                        <label class="checkbox-inline i-checks"> <input type="checkbox" name="curriculumItem[]" value="F">&nbsp;Curr&iacute;culo Vitae</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper  white-bg ">
                            <div>
                                <div class="col-sm-2 b-l">
                                    <label class="col-sm-2 control-label">Mensagem:</label>
                                </div>
                                <div class="col-sm-9">
                                    <div id="emailMessage"></div>
                                </div>
                            </div>

                        </div>

                        <div class="row col-lg-12 ">

                        </div>
                    </form>


                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-12" style="padding-right: 5px;">
                            <div id="alert-email"></div>
                        </div>
                    </div>

                    <div class="row">

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendEmail" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Send}</button>
                </div>
            </div>
        </div>
    </form>
</div>
