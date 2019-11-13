

<div class="modal fade"  id="modal-add-category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


    <div class="modal-dialog modal-lg" role="document" style="width:800px;">

        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{$smarty.config.Add_category}</h4>
            </div>

            <div class="modal-body">
                {*
                    Need to change "div id" if have more than 1 modal in the page,
                    and use modalAlertMultiple instead modalAlert
                    *}
                
                <form role="form" class="form-horizontal" id="categ-feat-add-form"  method="post">
                    <!-- Hidden -->

                    <input type="hidden" name="idmoduleAddCateg" id= "idmoduleAddCateg" value="">

                    <div class="row col-lg-12 ">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$smarty.config.Module}:</label>
                            <div class="col-sm-5 form-control-static">
                                <span id="moduleNameCateg"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$smarty.config.Name}:</label>
                            <div class="col-sm-5">
                                <input type="text" id="txtNewCategory" name="txtNewCategory" class="form-control input-sm" value="" >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">{$smarty.config.Smarty}:</label>
                            <div class="col-sm-5 tooltip-buttons"  data-toggle="tooltip" data-placement="right" title="{$smarty.config.Alert_add_config_categ_title}">
                                <input type="text" id="newCategorySmartyVar" name="newCategorySmartyVar" class="form-control input-sm" value="" >
                            </div>
                        </div>

                        <!--<div class="form-group">
                            <label  class="col-sm-3 control-label">{$smarty.config.Default}?</label>
                            <div class="checkbox i-checks"><label> <input type="checkbox" id="categorySetup" name="categorySetup" value="Y" > </label></div>
                        </div>-->

                    </div>

                    <div class="row col-lg-12 ">

                    </div>
                </form>


                <div class="row col-lg-12 ">
                    <div class="form-group col-lg-12" style="padding-right: 5px;">
                        <div id="alert-add-category"></div>
                    </div>
                </div>

                <div class="row">

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="btnCloseNewCateg" data-dismiss="modal">{$smarty.config.Close}</button>
                <button type="submit" class="btn btn-primary" id="btnSaveNewCateg"> <i class='fa fa-save'></i> {$smarty.config.Save}</button>
            </div>
        </div>
    </div>
</div>
