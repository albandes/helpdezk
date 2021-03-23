<!-- Alert modal in user new ticket page -->
{literal}
    <style>
        .bs-callout-danger {
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ce4844;
            border-left-width: 5px;
            border-radius: 3px;
        }

        .bs-callout-danger h4,.bs-callout-danger p {
            color: #ce4844;
        }

        .bs-callout-warning {
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #f8ac59;
            border-left-width: 5px;
            border-radius: 3px;
        }

        .bs-callout-warning h4,.bs-callout-warning p {
            color: #f8ac59;
        }

    </style>
{/literal}
<div class="modal fade" data-backdrop="static" id="modal-next-step" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >


        <div class="modal-dialog modal-md"  role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Notification}</h4>
                </div>

                <div class="modal-body">
                    <form role="form" class="form-horizontal" id="next_step_form" name="next_step_form" method="post">
                        <!--<input type="hidden" id="nextcoderequest" name="nextcoderequest" value="" />
                        <input type="hidden" id="nextnoteid" name="nextnoteid" value="" />-->
                        <input type="hidden" id="nexttotalattach" name="nexttotalattach" value="" />

                        <div class="row">
                            <div id="type-alert">
                                <div class="form-group">
                                    <div class="col-sm-12 col-xs-12 text-center"><p id="next-step-list"></p></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group alert-hdk alert-warning">
                                    <div class="col-sm-2 col-xs-12 text-center"><i class='fa fa-question-circle fa-2x'></i></div>
                                    <div id="next-step-message" class="col-sm-10 col-xs-12 text-center input-sm"></div>
                                </div>
                            </div>
                        </div>

                    </form>

                    <div class="row">
                        <div class="col-sm-12">
                            <div id="alert-next-step"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="btnNextNo">
                        <i class='fa fa-ban'></i>
                        {$smarty.config.No}
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnNextYes" data-loading-text="<i class='fa fa-spinner fa-spin'></i> {$smarty.config.Processing}">
                        <i class='fa fa-check-circle'></i>
                        {$smarty.config.Yes}
                    </button>
                </div>



            </div>

        </div>

</div>

