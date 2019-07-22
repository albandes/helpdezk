

<div class="modal fade" data-backdrop="static" id="modal-form-topic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


        <input type="hidden" id="idperson" value="{$hidden_idperson}" />

        <div class="modal-dialog modal-lg"  role="document"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Warning_new_topic}</h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>



                    <form role="form" id="topic-form" name="topic-form" method="post" class="form-horizontal">

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>

                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.Title}:</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="modal_topic_title" name="modal_topic_title" class="form-control input-sm"  />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label text-right">{$smarty.config.Validity_Standard}:</label>
                                    <div class="col-sm-7">
                                        <div class="col-sm-12 radio i-checks"><label> <input type="radio" value="1" name="validity" id="validity_1"> <i></i>{$smarty.config.Until_closed}</label></div>
                                        <div class="col-sm-12 radio i-checks"><label> <input type="radio" value="2" name="validity" id="validity_2"> <i></i><input type="text" value="" id="hoursValidity" name="hoursValidity" size="3">&nbsp;{$smarty.config.Hours}</label></div>
                                        <div class="col-sm-12 radio i-checks"><label> <input type="radio" value="3" name="validity" id="validity_3"> <i></i><input type="text" value="" id="daysValidity" name="daysValidity" size="3">&nbsp;{$smarty.config.Days}</label></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>

                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-3 control-label text-right">{$smarty.config.Send_email}:</label>
                                    <div class="checkbox i-checks"><label> <input type="checkbox" id="send-email-topic" name="send-email-topic" value="S" > <i></i> &nbsp;{$smarty.config.Send_alerts_topic_email}</label></div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-2 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label text-right">{$smarty.config.Available_for}:</label>
                                    <div class="col-sm-7">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label text-left">{$smarty.config.type_user_operator}:</label> <span class="col-sm-3 form-control-static text-left">({$smarty.config.By_group})</span>
                                            <div class="col-sm-6">
                                                <label class="radio-inline i-checks"> <input type="radio" name="availableOperatorNew" id="availableOperator_1" value="1" checked>&nbsp;{$smarty.config.all}</label>
                                                <label class="radio-inline i-checks"> <input type="radio" name="availableOperatorNew" id="availableOperator_2" value="2">&nbsp;{$smarty.config.Select}</label>
                                            </div>
                                        </div>

                                        <div id="availableOpe_lineNew" class="form-group hide">
                                            <div id="availableOpe_listNew" class="col-sm-12"></div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label text-left">{$smarty.config.type_user_user}:</label> <span class="col-sm-3 form-control-static text-left">({$smarty.config.By_company})</span>
                                            <div class="col-sm-6">
                                                <label class="radio-inline i-checks"> <input type="radio" name="availableUserNew" id="availableUser_1" value="1" checked>&nbsp;{$smarty.config.all}</label>
                                                <label class="radio-inline i-checks"> <input type="radio" name="availableUserNew" id="availableUser_2" value="2">&nbsp;{$smarty.config.Select}</label>
                                            </div>
                                        </div>

                                        <div id="availableUser_lineNew" class="form-group hide">
                                            <div id="availableUser_listNew" class="col-sm-12"></div>
                                        </div>                                                                          
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row col-lg-12 "></div>

                    </form>


                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-12" style="padding-right: 5px;">
                            <div id="alert-topic"></div>
                        </div>
                    </div>

                    <div class="row">

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendTopic" data-loading-text="<i class='fa fa-circle-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Send}</button>
                </div>
            </div>
        </div>
    </form>
</div>
