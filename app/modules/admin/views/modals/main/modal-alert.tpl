
<div class="modal fade"  data-backdrop="static" id="modal-alert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <form class="form-msg" id="alert_form" method="post">


        <div class="modal-dialog modal-md"  role="document">
            <div class="modal-content">

                <div  class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Notification}
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-12">
                            <div id="tipo-alert" name="tipo-alert" >
                                <spam id="modal-notification"></spam>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">

                    <a href="" id="btn-modal-ok" class="btn btn-success" role="button">
                        <span class="fa fa-check"></span>  &nbsp;
                        OK

                    </a>


                </div>

            </div>

        </div>

    </form>
</div>

