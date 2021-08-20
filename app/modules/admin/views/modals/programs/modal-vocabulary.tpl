

<div class="modal fade"  id="modal-form-vocabulary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


            <div class="modal-dialog modal-lg"  role="document"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times-circle"></i></button>
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Add} {$smarty.config.pgr_vocabulary}</h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>



                    <form role="form" class="form-horizontal" id="vocabulary-form"  method="post">
                        <!-- Hidden -->

                        <div class="row wrapper  white-bg ">

                            <div class="col-lg-1 b-l">
                            </div>

                            <div class="col-lg-10 b-l">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">{$smarty.config.Module}:</label>
                                    <div class="col-lg-4">
                                        <select class="form-control input-md" id="cmbModuleVocab" name="cmbModuleVocab" >
                                            <option value="">{$smarty.config.Select}</option>
                                            {html_options values=$moduleids output=$modulevals selected=$idmodule}
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-2 control-label">{$smarty.config.vocabulary_key_name}:</label>
                                    <div class="col-lg-7">
                                        <input type="text" id="keyName" name="keyName" class="form-control input-md" value="" >
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="col-lg-12 white-bg" style="height:30px;"></div>

                        <div class="row wrapper  white-bg textcenter">
                            <div class="col-lg-12 b-l">
                                <div class="col-lg-12 b-l">
                                    <table id="localeTab" class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th class="col-lg-3 text-center"><h4><strong>{$smarty.config.vocabulary_locale}</strong></h4></th>
                                            <th class="col-lg-8 text-center"><h4><strong>{$smarty.config.vocabulary_key_value}</strong></h4></th>
                                            <th class="col-lg-1 text-center"><h4><strong>{$smarty.config.TMS_Delete}</strong></h4></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <select class="form-control input-sm cmbLocale" name="localeID[]" id="localeID_1">
                                                    {html_options values=$localeids output=$localevals selected=$idlocale}
                                                </select>
                                                <input type="hidden" id="numId" value="1"/>
                                            </td>
                                            <td>
                                                <input type="text" name="keyValue[]" id="keyValue_1" class="form-control input-sm" />
                                            </td>
                                            <td class="text-center"><a href="javascript:;" onclick="removeRow(this,'localeTab','new')" class="btn btn-danger bt-xs"><i class="fa fa-times"></i></a></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-lg-12 b-l">
                                <div class="col-lg-12 b-l">
                                    <button type="button" class="btn btn-primary btn-md " id="btnAddKeyValue" >
                                        <span class="fa fa-plus"></span>  &nbsp;{$smarty.config.Add} {$smarty.config.pgr_vocabulary}
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>


                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-12" style="padding-right: 5px;">
                            <div id="alert-create-vocabulary"></div>
                        </div>
                    </div>

                    <div class="row">

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="btnCancelVocabulary" data-dismiss="modal"><i class='fa fa-times'></i> {$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveVocabulary"><i class='fa fa-save'></i> {$smarty.config.Save}</button>
                </div>
            </div>
        </div>
    </form>
</div>
