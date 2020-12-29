

<div class="modal fade" data-backdrop="static" id="modal-change-user-password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


    <div class="modal-dialog modal-md"  role="document"> {*style="width:1250px;"*}

        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{$smarty.config.Change_password}</h4>
            </div>

            <div class="modal-body">
                {*
                 Need to change "div id" if have more than 1 modal in the page,
                 and use modalAlertMultiple instead modalAlert
                 *}
                <div id="alert-evaluate"></div>


                <form role="form" id="change_user_pwd_form"  method="post" class="form-horizontal">
                    <!-- Hidden -->
                    <input type="hidden" id="hidden-idperson" value="{$id_person}"/>
                    <input type="hidden" name="logindemo" id= "logindemo" value="{$hidden_login}"> <!-- Use in demo version -->

                    <div class="row col-lg-12 ">
                        <div class="row col-lg-12  b-l">
                            <div class="form-group col-lg-12">
                                <label class="col-md-5 control-label text-right">{$smarty.config.New_password}:</label>
                                <div class="col-md-7">
                                    <input type="password" id="userconf_password" name="userconf_password" class="form-control input-sm" value="" >
                                </div>
                            </div>
                        </div>
                        <div class="row col-lg-12 b-l">
                            <div class="form-group col-lg-12">
                                <label class="col-md-5 control-label text-right">{$smarty.config.Confirm_password}:</label>
                                <div class="col-md-7">
                                    <input type="password" id="userconf_cpassword" name="userconf_cpassword" class="form-control input-sm" value="" >
                                </div>
                            </div>
                        </div>
                    </div>

                </form>


                <div class="row col-lg-12 ">
                    <div class="form-group col-lg-12" style="padding-right: 5px;">
                        <div id="alert-change-user-pass"></div>
                    </div>
                </div>

                <div class="row">

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="btnCancelUserPass" data-dismiss="modal">{$smarty.config.Close}</button>
                <button type="submit" class="btn btn-primary" id="btnSaveChangeUserPass" ><i class="fa fa-save"></i>   {$smarty.config.Save}</button>
            </div>
        </div>
    </div>
    </form>
</div>

