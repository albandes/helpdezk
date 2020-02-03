<!-- Alert modal in user new ticket page -->
<div class="modal fade" data-backdrop="static" id="modal-form-area" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >


        <div class="modal-dialog modal-lg"  role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"><span id="areaModalTitle"></span></h4>
                </div>

                <div class="modal-body">
                    <form role="form" class="form-horizontal" id="area_form" name="area_form" method="post">
                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-12 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-4 control-label">{$smarty.config.Area_name}:</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="modal_area_name" name="modal_area_name" class="form-control input-sm"  />
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 b-l">
                                <div class="form-group">
                                    <label  class="col-sm-4 control-label">{$smarty.config.Default}:</label>
                                    <div class="checkbox i-checks"><label> <input type="checkbox" id="checkDefaultArea" name="checkDefaultArea"> <i></i> &nbsp;{$smarty.config.Default_text}</label></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 white-bg" style="height:20px;"></div>

                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-12 b-l">
                                <table id='browser' class="table table-hover">
                                    <colgroup>
                                        <col width='90%'/>
                                        <col width='5%'/>
                                    </colgroup>
                                    <tbody id="areaslist">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-12 b-l">
                            <div id="alert-create-area"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        {$smarty.config.Close}
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSaveArea" data-loading-text="<i class='fa fa-spinner fa-spin'></i> {$smarty.config.Processing}">
                        <i class='fa fa-save'></i>
                        {$smarty.config.Save}
                    </button>
                </div>



            </div>

        </div>

</div>

