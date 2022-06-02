<!--<div id="modal-form-lost-password" class="modal fade" aria-hidden="true">-->
    <div class="modal fade"  id="modal-form-lost-password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Lost_password}</h4>
                </div>

                <div class="modal-body">
                    <div id="response"></div>

                    <div class="row">
                        <div class="col-sm-6 b-r">

                            {*<h3 class="m-t-none m-b">Sign in</h3>*}




                                <div class="form-group">
                                    <label>{$smarty.config.Login}</label>
                                    <input name="username" id="username" type="text" placeholder="{$smarty.config.User_login}" class="form-control">
                                </div>



                        </div>
                        <div class="col-sm-6">

                            <p class="text-center">
                                <a href="#" id="urlLostPasssword"><i class="fa fa-sign-in big-icon"></i></a>
                            </p>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.btn_cancel}</button>
                    {*<button type="button" class="btn btn-primary" id="btnSend" >{$smarty.config.Send}</button>*}


                    <button type="button" class="btn btn-primary" id="btnSend" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.btn_submit}</button>



                </div>
            </div>
        </div>
    </div>
<!--</div>-->