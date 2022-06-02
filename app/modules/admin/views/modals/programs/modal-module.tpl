

<div class="modal fade"  id="modal-form-module" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


            <div class="modal-dialog modal-lg"  role="document"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times-circle"></i></button>
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Module_insert}</h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>

                    <form role="form" class="form-horizontal" id="module-form"  method="post">
                        <!-- Hidden -->

                        <input type="hidden" name="_token" id= "_token" value="{$token}">

                        <div class="row col-lg-12 ">
                            <div class="col-lg-3 b-l"> 
                                <div class="col-lg-12 b-l text-center">
                                    <div id="myDropzone" class="dropzone dz-default dz-message" ></div>
                                </div>                   
                            </div>

                            <div class="col-lg-9 b-l">
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">{$smarty.config.Module_name}:</label>
                                    <div class="col-lg-6">
                                        <input type="text" id="txtName" name="txtName" class="form-control input-sm" required placeholder="{$smarty.config.plh_module_description}" value="" >
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">{$smarty.config.Module_path}:</label>
                                    <div class="col-lg-6">
                                        <input type="text" id="txtPath" name="txtPath" maxlength="3" class="form-control input-sm" required placeholder="{$smarty.config.plh_module_path}" value="" >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-4 control-label">{$smarty.config.Smarty}:</label>
                                    <div class="col-lg-6">
                                        <input type="text" id="txtSmartyVar" name="txtSmartyVar" class="form-control input-sm lbltooltip" data-toggle="tooltip" data-placement="bottom" title="{$smarty.config.tt_lbl_add_vocabulary_module}" required placeholder="{$plh_module_smartyvar}" value="" >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-4 control-label">{$smarty.config.Table_prefix}:</label>
                                    <div class="col-lg-6">
                                        <input type="text" id="txtPrefix" name="txtPrefix" maxlength="3" class="form-control input-sm" required placeholder="{$smarty.config.plh_module_prefix}" value="" >
                                    </div>
                                </div>

                                <div class="form-group">
                                    &nbsp;
                                        <!-- <label  class="col-sm-2 control-label" style="text-align: right;padding-right: 5px;">{$smarty.config.Module_default}?</label>
                                    <div class="checkbox i-checks"><label> <input type="checkbox" id="module-default" name="module-default" value="S" > <i></i> &nbsp;{$smarty.config.Yes}</label></div>-->
                                </div>
                            </div>
                        </div>
                        <div class="row col-lg-12 ">

                        </div>
                    </form>


                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-12" style="padding-right: 5px;">
                            <div id="alert-module"></div>
                        </div>
                    </div>

                    <div class="row">

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="btnCancelModule" data-dismiss="modal"><i class='fa fa-times'></i> {$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendModule"><i class='fa fa-save'></i> {$smarty.config.Save}</button>
                </div>
            </div>
        </div>
    </form>
</div>
