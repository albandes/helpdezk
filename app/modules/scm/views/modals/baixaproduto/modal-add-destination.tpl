

<div class="modal fade" data-backdrop="static" id="modal-add-destination" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


            <div class="modal-dialog modal-lg"  role="document"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Add} {$smarty.config.destination}</h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>



                    <form role="form" class="form-horizontal" id="add-destination-form"  method="post">
                        <!-- Hidden -->

                        <div class="row col-lg-12 ">
                            <div class="form-group">
                                <label class="col-lg-2 control-label">{$smarty.config.Name}:</label>
                                <div class="col-lg-5">
                                    <input type="text" id="destName" name="destName" class="form-control input-sm" value="" >
                                </div>
                            </div>
                        </div>
                    </form>


                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-12" style="padding-right: 5px;">
                            <div id="alert-add-destination"></div>
                        </div>
                    </div>

                    <div class="row">

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="btnModalClose" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveDest"><span class="fa fa-save"></span>  {$smarty.config.Save}</button>
                </div>
            </div>
        </div>
    </form>
</div>
