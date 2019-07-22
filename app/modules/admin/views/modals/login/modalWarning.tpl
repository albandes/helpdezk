
<!-- Warning modal in login page -->
<div class="bs-example">
    <div id="myModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><b><spam id="title_topic"></b></h4>
                </div>


                <div class="modal-body">
                   <b><p  id="title_warning"></b>

                    <p id="description" ></p>
                    <p class="text-success"><small><spam id="valid_msg"></spam></small></p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <!--<button type="button" class="btn btn-primary">Save changes</button>-->
                </div>
            </div>
        </div>
    </div>
</div>
