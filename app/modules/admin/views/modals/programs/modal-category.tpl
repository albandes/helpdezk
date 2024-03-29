

<div class="modal fade"  id="modal-form-category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


            <div class="modal-dialog modal-lg"  role="document"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times-circle"></i></button>
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Category_insert}</h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>



                    <form role="form" class="form-horizontal" id="category-form"  method="post">
                        <!-- Hidden -->

                        <input type="hidden" name="_token" id= "_token" value="{$token}">

                        <div class="row col-lg-12 ">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Module}:</label>
                                <div class="col-sm-5">
                                    <select class="form-control input-sm"  id="cmbModuleMod" name="cmbModuleMod" data-placeholder="{$smarty.config.Select_module}" >
                                        {html_options values=$moduleids output=$modulevals selected=$idmodule}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-2 control-label">{$smarty.config.New_category}:</label>
                                <div class="col-lg-5">
                                    <input type="text" id="txtNewCategory" name="txtNewCategory" class="form-control input-sm" required placeholder="{$smarty.config.plh_category_description}" value="" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-2 control-label">{$smarty.config.Smarty}:</label>
                                <div class="col-lg-5">
                                    <input type="text" id="txtCatSmartyVar" name="txtCatSmartyVar" class="form-control input-sm lbltooltip" data-toggle="tooltip" data-placement="bottom" title="{$smarty.config.tt_lbl_add_vocabulary_category}" required placeholder="{$plh_module_smartyvar}" value="" >
                                </div>
                            </div>

                        </div>

                        <div class="row col-lg-12 ">

                        </div>
                    </form>


                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-12" style="padding-right: 5px;">
                            <div id="alert-category"></div>
                        </div>
                    </div>

                    <div class="row">

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="btnCancelCategory" data-dismiss="modal"><i class='fa fa-times'></i> {$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendCategory"><i class='fa fa-save'></i> {$smarty.config.Save}</button>
                </div>
            </div>
        </div>
    </form>
</div>
