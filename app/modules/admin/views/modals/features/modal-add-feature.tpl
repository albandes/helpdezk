

<div class="modal fade"  id="modal-add-feature" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


    <div class="modal-dialog modal-lg" role="document" style="width:800px;">

        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{$smarty.config.add_new_feature}</h4>
            </div>

            <div class="modal-body">
                {*
                    Need to change "div id" if have more than 1 modal in the page,
                    and use modalAlertMultiple instead modalAlert
                    *}
                
                <form role="form" class="form-horizontal" id="feature-add-form"  method="post">
                    <!-- Hidden -->

                    <input type="hidden" name="idmoduleAddFeat" id= "idmoduleAddFeat" value="">

                    <div class="row col-lg-12 ">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$smarty.config.Module}:</label>
                            <div class="col-sm-5 form-control-static">
                                <span id="moduleName"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$smarty.config.Category}:</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm"  id="cmbFeatureCat" name="cmbFeatureCat" >
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$smarty.config.Name}:</label>
                            <div class="col-sm-5">
                                <input type="text" id="txtNewFeature" name="txtNewFeature" class="form-control input-sm" required  value="" >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$smarty.config.Description}:</label>
                            <div class="col-sm-5">
                                <input type="text" id="newFeatureDesc" name="newFeatureDesc" class="form-control input-sm" value="" >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$smarty.config.lbl_session_name}:</label>
                            <div class="col-sm-5">
                                <input type="text" id="newFeatureSessionName" name="newFeatureSessionName" class="form-control input-sm" value="" >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$smarty.config.Smarty}:</label>
                            <div class="col-sm-5 tooltip-buttons"  data-toggle="tooltip" data-placement="right" title="{$smarty.config.Alert_add_feature_title}">
                                <input type="text" id="newFeatureSmartyVar" name="newFeatureSmartyVar" class="form-control input-sm" value="" >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$smarty.config.Type}:</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm"  id="cmbFieldTypeMod" name="cmbFieldTypeMod" >
                                    {html_options values=$fieldtypeids output=$fieldtypevals selected=$idfieldtype}
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$smarty.config.lbl_value}:</label>
                            <div id="inputVal" class="col-sm-5 hide">
                                <input type="text" id="valInputFeature" name="valInputFeature" class="form-control input-sm" value="" >
                            </div>
                            <div id="checkVal" class="checkbox i-checks hide"><label> <input type="checkbox" id="valCheckFeature" name="valCheckFeature" value="1" > {$smarty.config.Available}</label></div>
                        </div>

                        <div class="form-group">
                            <label  class="col-sm-3 control-label">{$smarty.config.Default}?</label>
                            <div class="checkbox i-checks"><label> <input type="checkbox" id="featureDefault" name="featureDefault" value="S" > </label></div>
                        </div>

                    </div>

                    <div class="row col-lg-12 ">

                    </div>
                </form>


                <div class="row col-lg-12 ">
                    <div class="form-group col-lg-12" style="padding-right: 5px;">
                        <div id="alert-add-feature"></div>
                    </div>
                </div>

                <div class="row">

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="btnCloseNewFeat" data-dismiss="modal">{$smarty.config.Close}</button>
                <button type="submit" class="btn btn-primary" id="btnSaveNewFeat"> <i class='fa fa-save'></i> {$smarty.config.Save}</button>
            </div>
        </div>
    </div>
</div>
