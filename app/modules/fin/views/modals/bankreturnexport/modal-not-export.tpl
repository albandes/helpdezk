<!-- Alert modal in user new ticket page -->
<div class="modal fade" data-backdrop="static" id="modal-not-export" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >


        <div class="modal-dialog modal-md"  role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"></h4>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div id="divReportTitle" class="col-sm-12 col-xs-12 bs-callout bs-callout-danger">
                            <div class="form-group text-center">
                                <h4 class="col-sm-12">{$smarty.config.Alert_not_export_list}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div id="divNotExport" class="col-sm-12 col-xs-12 text-center">
                            <table id="notExportTable" class="table table-hover table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th class="col-sm-4 text-center"><h3><strong>{$smarty.config.Grid_incharge}</strong></h3></th>
                                    <th class="col-sm-2 text-center"><h3><strong>{$smarty.config.FIN_portion}</strong></h3></th>
                                    <th class="col-sm-2 text-center"><h3><strong>{$smarty.config.ERP_Pay_Amount}</strong></h3></th>
                                    <th class="col-sm-2 text-center"><h3><strong>{$smarty.config.ERP_DueDate}</strong></h3></th>
                                    <th class="col-sm-2 text-center"><h3><strong>{$smarty.config.ERP_PayDate}</strong></h3></th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="btnCloseDelete" data-dismiss="modal">
                        <i class='fa fa-times'></i>
                        {$smarty.config.Close}
                    </button>
                    <button type="button" class="btn btn-primary" id="btnPrintList" data-loading-text="<i class='fa fa-spinner fa-spin'></i> {$smarty.config.Processing}">
                        <i class='fa fa-print'></i>
                        {$smarty.config.print_list}
                    </button>
                </div>



            </div>

        </div>

</div>

