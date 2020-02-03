
<div class="modal fade"  data-backdrop="static" id="modal-info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <form class="form-msg" id="info_form" method="post">


        <div class="modal-dialog modal-md"  role="document">
            <div class="modal-content">

                <div  class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Notification}
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-12">
                            <div id="tipo-info" name="tipo-info" >
                                <spam id="info-notification"></spam>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">

                    <a href="" id="btn-modal-ok" class="btn btn-success" role="button" data-dismiss="modal">
                        <span class="fa fa-check"></span>  &nbsp;
                        OK

                    </a>


                </div>

            </div>

        </div>

    </form>
</div>

