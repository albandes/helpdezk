<!-- Alert modal in user new ticket page -->
<div class="modal fade" data-backdrop="static" id="modal-dialog-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >


        <div class="modal-dialog modal-md"  role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"><span id="deleteTitle"></span></h4>
                </div>

                <div class="modal-body">
                    <form role="form" class="form-horizontal" id="delete_service_form" name="delete_service_form" method="post">
                        <input type="hidden" id="idtarget_modal" name="idtarget_modal" value="" />
                        <input type="hidden" id="type_delete" name="type_delete" value="" />
                        
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group alert-hdk alert-warning">
                                    <div class="col-sm-1 col-xs-12 text-center"><i class='fa fa-question-circle fa-2x'></i></div>
                                    <div class="col-sm-8 col-xs-12 text-center input-sm"><h4>{$smarty.config.Delete_record}</h4></div>
                                </div>
                            </div>
                        </div>

                    </form>

                    <div class="row">
                        <div class="col-sm-12">
                            <div id="alert-delete-service"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="btnDeleteNo">
                        <i class='fa fa-ban'></i>
                        {$smarty.config.No}
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnDeleteYes" data-loading-text="<i class='fa fa-spinner fa-spin'></i> {$smarty.config.Processing}">
                        <i class='fa fa-check-circle'></i>
                        {$smarty.config.Yes}
                    </button>
                </div>



            </div>

        </div>

</div>

