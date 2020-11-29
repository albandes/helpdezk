<!-- Print PHP SESSION -->
<div class="modal fade"  id="modal-session" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >


    <form class="form-sigin" id="alert_form" method="post">

        <div class="modal-dialog modal-lg"  role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Tckt_opened}
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-12">
                            <pre>
                                {$php_session}
                            </pre>

                        </div>
                    </div>


                </div>

                <div class="modal-footer">

                    <a href="" id="btnModalAlert" class="btn btn-success" role="button">

                        <span class="fa fa-check"></span>  &nbsp;
                        {$smarty.config.Ok_btn}
                    </a>


                </div>

            </div>

        </div>

    </form>
</div>

