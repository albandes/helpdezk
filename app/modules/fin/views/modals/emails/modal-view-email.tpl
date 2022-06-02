

<div class="modal fade" data-backdrop="static" id="modal-view-email" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->
    <div class="modal-dialog modal-lg"  role="document" > {*style="width:1250px;"*}

        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{$smarty.config.emq_view_message}</h4>
            </div>

            <div class="modal-body">
                {*
                 Need to change "div id" if have more than 1 modal in the page,
                 and use modalAlertMultiple instead modalAlert
                 *}
                <div id="alert-evaluate"></div>



                <form role="form" id="email-form"  method="post" class="form-horizontal">
                    <!-- Hidden -->
                    <input type="hidden" name="emailID" id= "emailID" value="">

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.status}:</label>
                                <div id="emailStatus" class="col-sm-4 form-control-static">

                                </div>
                                <label class="col-sm-2 control-label sentLine hide">{$smarty.config.emq_sent_email_date}:</label>
                                <div id="emailSent" class="col-sm-4 form-control-static sentLine hide">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.emq_to_name}:</label>
                                <div id="emailTo" class="col-sm-10 form-control-static">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="studentLine" class="row wrapper white-bg hide">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.itm_student}:</label>
                                <div id="studentData" class="col-sm-10 form-control-static">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Grid_subject}:</label>
                                <div id="emailSubject" class="col-sm-10 form-control-static">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.emq_message}:</label>
                                <div id="emailMessage" class="col-sm-10 form-control-static">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div id="attachLine" class="row wrapper white-bg hide">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Attachments}:</label>
                                <div id="emailAttachs" class="col-sm-10 form-control-static">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="emailLogLine" class="row wrapper white-bg">
                        <div id="srvSent" class="col-sm-12 b-l hide">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.emq_sent_lbl}:</label>
                                <div id="emailServer" class="col-sm-10 form-control-static">
                                </div>
                            </div>
                        </div>
                        <div id="mandrillLogLine" class="col-sm-12 b-l hide">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.ERP_Log}:</label>
                                <div class="col-sm-10">
                                    <table id="mandrillLog" class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th class="col-sm-1 text-center"><h4><strong>{$smarty.config.status}</strong></h4></th>
                                                <th class="col-sm-1 text-center"><h4><strong>{$smarty.config.emq_os_abbrev}</strong></h4></th>
                                                <th class="col-sm-1 text-center"><h4><strong>{$smarty.config.GES_DataHora}</strong></h4></th>
                                                <th class="col-sm-2 text-center"><h4><strong>{$smarty.config.itm_description}</strong></h4></th>
                                                <th class="col-sm-1 text-center"><h4><strong>Diag</strong></h4></th>
                                                <th class="col-sm-1 text-center"><h4><strong>Mobile</strong></h4></th>
                                                <th class="col-sm-1 text-center"><h4><strong>{$smarty.config.City}</strong></h4></th>
                                                <th class="col-sm-1 text-center"><h4><strong>{$smarty.config.Country}</strong></h4></th>
                                                <th class="col-sm-1 text-center"><h4><strong>UA</strong></h4></th>
                                                <th class="col-sm-1 text-center"><h4><strong>UA Family</strong></h4></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>


                <div class="row col-lg-12 ">
                    <div class="form-group col-lg-12" style="padding-right: 5px;">
                        <div id="alert-email"></div>
                    </div>
                </div>

                <div class="row">

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="btnCancel" data-dismiss="modal">{$smarty.config.Close}</button>
                <button type="button" class="btn btn-primary hide" id="btnReSend"><i class='fa fa-share-square'></i> {$smarty.config.resend}</button>
            </div>

        </div>
    </div>
</div>
