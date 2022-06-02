

<div class="modal fade" data-backdrop="static" id="modal-form-schedule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


    <div class="modal-dialog modal-lg"  role="document" style="width:600px;" > {*style="width:1250px;"*}

        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close closeMsg" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{$smarty.config.schedule_email_sending}</h4>
            </div>

            <div class="modal-body">
                {*
                 Need to change "div id" if have more than 1 modal in the page,
                 and use modalAlertMultiple instead modalAlert
                 *}
                <div id="alert-evaluate"></div>



                <form role="form" id="schedule-form"  method="post" class="form-horizontal">
                    <!-- Hidden -->

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{$smarty.config.Company}:</label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm" id="cmbCompany" name="cmbCompany" >
                                        <option value="">{$smarty.config.Select_company}</option>
                                        {html_options values=$companyids output=$companyvals selected=$idcompany}
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{$smarty.config.fin_competence}:</label>
                                <div class="col-sm-8">
                                    <input type="text" name="competence" id="competence" class="form-control input-sm" value="{$competence}" readonly  />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>


                <div class="row col-sm-12 ">
                    <div class="form-group col-sm-12">
                        <div id="alert-schedule"></div>
                    </div>
                </div>

                <div class="row"></div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="btnCancelSchedule" data-dismiss="modal"><i class='fa fa-times' aria-hidden="true"></i> {$smarty.config.Close}</button>
                <button type="submit" class="btn btn-primary" id="btnSaveSchedule"><i class='fa fa-save' aria-hidden="true"></i> {$smarty.config.Save}</button>
            </div>

        </div>
    </div>
</div>
