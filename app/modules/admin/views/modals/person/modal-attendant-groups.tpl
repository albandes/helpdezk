<!-- Alert modal in user new ticket page -->
<div class="modal fade" data-backdrop="static" id="modal-form-attendantgrps" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <form class="form-horizontal" id="attendantgrps-form" method="post">
        <!-- Hidden input to send value by POST  -->
        <input name="idattendant" type="hidden" id="idattendant" value="" />

        <div class="modal-dialog modal-md"  role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.View_groups}</h4>
                </div>

                <div class="modal-body">

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label  class="col-sm-4 control-label">{$smarty.config.Groups}:</label>
                                <div class="col-sm-8">
                                    <select class="form-control  m-b" name="cmbGroups" id="cmbGroups" data-placeholder=" ">
                                        <option value="0">{$smarty.config.Select} </option>
                                        {html_options values=$auxopeids output=$auxopevals selected=$idauxope}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <table id='browser' class="table table-hover">
                                <colgroup>
                                    <col width='90%'/>
                                    <col width='5%'/>
                                </colgroup>
                                <tbody id="tablelist">                                
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <!--<button type="submit" class="btn btn-primary" id="btnSendAuxOpe" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Repass_btn}</button>-->

                </div>

            </div>

        </div>

    </form>
</div>

