
<div class="modal fade"  id="modal-web-print" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <form class="form-horizontal" id="form-web-print" method="post" role="form">


        <div class="modal-dialog modal-lg"  role="document">
            <div class="modal-content">

                <div  class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"><strong>{$smarty.config.pgr_enrollment_report}</strong></h4>
                </div>

                <div class="modal-body">

                    <div class="row wrapper">
                        <div id="divReportTitle" class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <img src="{$reportslogo}" height="{$reportsheight}px" width="{$reportswidth}px" id="logo" />
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <div class="col-sm-12"><label class="control-label">{$smarty.config.pgr_enrollment_report}</label></div>
                                        <div class="col-sm-12"><label id="lblPeriod" class="control-label"></label></div>
                                        <div class="col-sm-12"><label id="lblStatus" class="control-label"></label></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 b-l hdk-table-wrapper-scroll-y hdk-custom-scrollbar">
                            <table id="returnTablePrint" class="table table-hover table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th class="col-sm-2 text-center"><h3><strong>{$smarty.config.TMS_Matricula}</strong></h3></th>
                                    <th class="col-sm-4 text-center"><h3><strong>{$smarty.config.TMS_Aluno}</strong></h3></th>
                                    <th class="col-sm-2 text-center"><h3><strong>{$smarty.config.tms_turma}</strong></h3></th>
                                    <th class="col-sm-2 text-center"><h3><strong>{$smarty.config.Grid_status}</strong></h3></th>
                                    <th class="col-sm-2 text-center"><h3><strong>{$smarty.config.Date}</strong></h3></th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-sm-12 white-bg" style="height:20px;"></div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="btnCancelPrint" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnPrintModal"><i class='fa fa-print' aria-hidden="true"></i> {$smarty.config.Print}</button>
                </div>

            </div>

        </div>

    </form>
</div>

