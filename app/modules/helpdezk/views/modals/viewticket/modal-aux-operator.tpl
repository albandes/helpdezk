<!-- Alert modal in user new ticket page -->
<div class="modal fade" data-backdrop="static" id="modal-form-auxoperator" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <form class="form-horizontal" id="auxoperator-form" method="post">
        <!-- Hidden input to send value by POST  -->
        <input name="coderequest" type="hidden" id="coderequest" value="{$hidden_coderequest}" />

        <div class="modal-dialog modal-sm"  role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.auxiliary_operator_include}
                </div>

                <div class="modal-body">

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label  class="col-sm-4 control-label">{$smarty.config.Operator}:</label>
                                <div class="col-sm-8">
                                    <select class="form-control  m-b" name="cmbAuxOpe" id="cmbAuxOpe" data-placeholder=" ">
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

