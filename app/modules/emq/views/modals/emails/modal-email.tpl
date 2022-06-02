

<div class="modal fade"  id="modal-form-email" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


            <div class="modal-dialog modal-lg"  role="document" style="width:850px;"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close closeMsg" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.emq_compose_message}</h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>



                    <form role="form" id="email-form"  method="post" class="form-horizontal">
                        <!-- Hidden -->

                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-11 b-l">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">{$smarty.config.Title}:</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="emailtitle" id="emailtitle" class="form-control input-sm" value=""  />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-11 b-l">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">{$smarty.config.emq_message}:</label>
                                    <div class="col-sm-10">
                                        <div id="emailMessage"></div>
                                    </div>
                                </div>                                
                            </div>

                        </div>

                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-11 b-l">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">{$smarty.config.Attachments}:</label>
                                    <div class="col-sm-10 text-center">
                                        <!-- This is the dropzone element -->
                                        <div id="myDropzone" class="dropzone dz-default dz-message">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>


                    <div class="row col-sm-12 ">
                        <div class="form-group col-sm-12">
                            <div id="alert-email"></div>
                        </div>
                    </div>

                    <div class="row"></div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="btnCancelMsg" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveMsg"><i class='fa fa-paper-plane' aria-hidden="true"></i> {$smarty.config.Send}</button>
                </div>

            </div>
        </div>
    </form>
</div>
