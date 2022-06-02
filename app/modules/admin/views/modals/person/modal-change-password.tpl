

<div class="modal fade"  id="modal-form-changepassword" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


    <div class="modal-dialog modal-lg"  role="document"> {*style="width:1250px;"*}

        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Cadastro de logradouro</h4>
            </div>

            <div class="modal-body">
                {*
                 Need to change "div id" if have more than 1 modal in the page,
                 and use modalAlertMultiple instead modalAlert
                 *}
                <div id="alert-evaluate"></div>


                <form role="form" id="changepassword-form"  method="post" class="form-horizontal">
                    <!-- Hidden -->

                    <div class="row col-lg-12 ">
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{$smarty.config.New_password}:</label>
                            <div class="col-lg-4">
                                <input type="password" id="modal_password" name="modal_password" class="form-control input-sm" aplaceholder="{$plh_program_description}" value="" >
                            </div>
                            <label class="col-lg-2 control-label">{$smarty.config.Confirm_password}:</label>
                            <div class="col-lg-4">
                                <input type="password" id="modal_cpassword" name="modal_cpassword" class="form-control input-sm" placeholder="{$plh_program_description}" value="" >
                            </div>
                        </div>
                    </div>

                    <div class="row col-lg-12 ">
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{$smarty.config.Change_password}:</label>
                        <div class="checkbox i-checks"><label> <input type="checkbox" name="modal-changePass" id="modal-changePass" value="1"> <i></i> &nbsp;{$smarty.config.Change_password_required}</label></div>
                        </div>
                    </div>

                </form>


                <div class="row col-lg-12 ">
                    <div class="form-group col-lg-12" style="padding-right: 5px;">
                        <div id="alert-modal-changepass"></div>
                    </div>
                </div>

                <div class="row">

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                <button type="submit" class="btn btn-primary" id="btnSendChangePassword" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Save}</button>
            </div>
        </div>
    </div>
    </form>
</div>
