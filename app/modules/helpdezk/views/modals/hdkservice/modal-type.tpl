

<div class="modal fade" data-backdrop="static" id="modal-form-type" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


        <div class="modal-dialog modal-lg"  role="document"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span id="typeModalTitle"></span></h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>


                    <form role="form" id="type_form" name="type_form" method="post" class="form-horizontal">
						<input type="hidden" id="idtype_modal" name="idtype_modal" value="" />
                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>

                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.Type_name}:</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="modal_type_name" name="modal_type_name" class="form-control input-sm"  />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.Area}:</label>
                                    <div class="col-sm-5">
                                        <select class="form-control form-control-sm" name="modal_cmbArea" id="modal_cmbArea">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.Available}:</label>
                                    <div class="checkbox i-checks"><label> <input type="checkbox" id="checkAvailable" name="checkAvailable"> <i></i> &nbsp;{$smarty.config.Available_text}</label></div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.Default}:</label>
                                    <div class="checkbox i-checks"><label> <input type="checkbox" id="checkDefault" name="checkDefault"> <i></i> &nbsp;{$smarty.config.Default_text}</label></div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.Classification}:</label>
                                    <div class="checkbox i-checks"><label> <input type="checkbox" id="checkClassification" name="checkClassification"> <i></i> &nbsp;{$smarty.config.Classification_text}</label></div>
                                </div>
                            </div>
                        </div>


                        <div class="row col-lg-12 ">

                        </div>



                    </form>


                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-12" style="padding-right: 5px;">
                            <div id="alert-modal-type"></div>
                        </div>
                    </div>

                    <div class="row">

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        {$smarty.config.Close}
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSaveType" data-loading-text="<i class='fa fa-spinner fa-spin'></i> {$smarty.config.Processing}">
                        <i class='fa fa-save'></i>
                        {$smarty.config.Save}
                    </button>
                </div>
            </div>
        </div>
</div>
