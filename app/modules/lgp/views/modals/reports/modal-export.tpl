
<div class="modal fade"  id="modal-export" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <form class="form-horizontal" id="form-export-select" method="post" role="form">


        <div class="modal-dialog modal-md"  role="document">
            <div class="modal-content">

                <div  class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"><strong id="titleExport"></strong></h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="col-sm-12text-left"><strong>{$smarty.config.Choose_format}</strong></label>
                            <div class="col-sm-12 white-bg" style="height:15px;"></div>
                            <div class="col-sm-12 text-center">
                                <!--<label class="radio-inline i-checks">
                                    <input type="radio" name="filetype" id="pdf" value="PDF" checked>&nbsp;&nbsp;
                                    <i class="fa fa-file-pdf fa-5x icoToolTip" data-toggle="tooltip" data-placement="bottom" title="{$smarty.config.File_PDF}"></i>
                                </label>
                                <label class="radio-inline i-checks">
                                    <input type="radio" name="filetype" id="xls" value="XLS">&nbsp;&nbsp;
                                    <i class="fa fa-file-excel fa-5x icoToolTip" data-toggle="tooltip" data-placement="bottom" title="{$smarty.config.File_XLS}"></i>
                                </label>-->
                                <label class="radio-inline i-checks">
                                    <input type="radio" name="filetype" id="csv" value="CSV">&nbsp;&nbsp;
                                    <i class="fa fa-file-csv fa-5x icoToolTip" data-toggle="tooltip" data-placement="bottom" title="{$smarty.config.File_CSV}"></i>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 white-bg" style="height:20px;"></div>

                    <div id="csvDelimiter" class="row wrapper hide">
                        <div class="col-sm-12 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{$smarty.config.Delimiter}:</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control input-sm " id="txtDelimiter" name="txtDelimiter" value=","/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 white-bg" style="height:20px;"></div>

                    <div class="row wrapper white-bg">
                        <div class="col-sm-12 b-l">
                            <div id="alert-modal-export"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="btnCancelExp" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnExport"><i class='fa fa-file-export' aria-hidden="true"></i> {$smarty.config.Export}</button>
                </div>

            </div>

        </div>

    </form>
</div>

