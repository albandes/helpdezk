<!-- Modal used for integration with Trell -->
<div class="modal fade" data-backdrop="static" id="modal-form-trello" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <form class="form-horizontal" id="trello-form" method="post">

        <!-- Hidden input to send value by POST  -->
        <input type="hidden" name="idperson" id="idperson" value="{$hidden_idperson}" />

        <div class="modal-dialog modal-sm"  role="document" style="width:850px">

            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.trello_integration}
                </div>

                <div class="modal-body">


                    <!-- -->
                    <div class="row wrapper  white-bg ">

                            <div class="form-group">
                                <label  class="col-sm-3 control-label">{$smarty.config.trello_boards}:</label>
                                <div class="col-sm-9">
                                    <select class="form-control  form-control-sm" id="cmbBoard" data-placeholder=" ">
                                    </select>
                                </div>

                            </div>

                    </div>

                    <div class="row wrapper  white-bg ">

                            <div class="form-group">
                                <label  class="col-sm-3 control-label">{$smarty.config.trello_lists}:</label>
                                <div class="col-sm-9">
                                    <select class="form-control  form-control-sm" id="cmbList" data-placeholder=" ">
                                    </select>
                                </div>
                            </div>

                    </div>




                    <div  id="add-card">

                        <div class="row wrapper  white-bg ">


                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{$smarty.config.trello_title}:</label>
                                    <div class="col-sm-9 b-l">
                                        <input type="text" id="titlecard" class="form-control input-sm" required placeholder="{$plh_holiday_description}" value="" >
                                    </div>
                                </div>

                        </div>

                        <div class="row wrapper  white-bg ">

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{$smarty.config.trello_description}:</label>
                                    <div class="col-sm-9 b-l">
                                        <div id="desc-card" class="summernote" ></div>
                                    </div>
                                </div>

                        </div>


                    </div>

                    <div id="list-card" class="row wrapper  white-bg ">

                            <div class="form-group">
                                <label  class="col-sm-3 control-label">{$smarty.config.trello_cards}:</label>
                                <!-- -->
                                <div class="col-sm-8" id="tableCard"></div>
                                <!-- -->

                                <div class="col-sm-1 ">
                                    <button class="btn btn-default tooltip-buttons" id="btnAddCard" type="button" data-toggle="tooltip" data-placement="top" title="{$smarty.config.trello_tooltip_card}" tabindex="-1"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>
                    </div>



                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendTrello" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.btn_save_changes}</button>


                </div>

            </div>

        </div>

    </form>

    <style>
        .scrollable-panel{
            max-height: 150px;
            overflow: auto;
        }
    </style>
</div>

