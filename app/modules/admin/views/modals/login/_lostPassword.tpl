<!--<div id="modal-form-lost-password" class="modal fade" aria-hidden="true">-->
    <div class="modal fade"  id="modal-form-lost-password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>


                <div class="modal-body">

                    <div id="response"></div>

                    <div class="row">
                        <div class="col-sm-6 b-r"><h3 class="m-t-none m-b">Sign in</h3>

                            <p>Sign in today for more expirience.</p>

                            <form role="form">
                                <div class="form-group"><label>{$smarty.config.Login}</label> <input name="username" id="username" type="text" placeholder="{$smarty.config.User_login}" class="form-control"></div>

                                <div>
                                    <!--<button class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit"><strong>Log in</strong></button>-->
                                    <button class="btn btn-primary" id="btn">alert</button>
                                    <button class="btn btn-primary" id="btn-error">error</button>

                                </div>
                            </form>
                        </div>
                        <div class="col-sm-6">

                            <p class="text-center">
                                <a href=""><i class="fa fa-sign-in big-icon"></i></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
         </div>
    </div>
<!--</div>-->