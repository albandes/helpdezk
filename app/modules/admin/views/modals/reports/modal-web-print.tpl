
<div class="modal fade"  id="modal-web-print" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <form class="form-horizontal" id="form-web-print" method="post" role="form">


        <div class="modal-dialog modal-lg"  role="document">
            <div class="modal-content">

                <div  class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"><strong id="titlePrint"></strong></h4>
                </div>

                <div class="modal-body">

                    <div class="row wrapper">
                        <div id="divReportTitle" class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <img src="{$path}/app/uploads/logos/default/{$reportslogo}" height="{$reportsheight}px" width="{$reportswidth}px" id="logo" />
                                </div>
                                <div id="subHeader" class="col-sm-8">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 b-l hdk-table-wrapper-scroll-y hdk-custom-scrollbar">
                            <table id="returnTablePrint" class="table table-hover table-bordered table-striped">
                                <thead>
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

