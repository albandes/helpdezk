<?php
/**
 * Date: 01/07/2019
 * Time: 15:51
 */

session_start();
session_destroy();

if ( !isset($_POST['i18n']) ){
    $lang = "en_US";
} else {
    $lang = $_POST['i18n'];
}

include ("../lang/". $lang . ".php" );


?>

<div id=content class="col-md-8">

    <div class="ibox float-e-margins">

        <div class="ibox-title">
            <h5><?php echo PROGRESS_STEP_1 ?></h5>

        </div>


        <div class="ibox-content">

            <div>
                <div class="feed-activity-list">

                    <div class="feed-element">

                        <div class="media-body ">
                            <form method="get" class="form-horizontal">
                                <div class="form-group">
                                    <div class="col-lg-12">
                                        <h5><?php echo INFO_STEP_1 ?></h5>
                                        <div class="hr-line-dashed"></div>
                                    </div>

                                    <label class="col-sm-2 control-label">Language</label>

                                    <div class="col-sm-6">
                                        <select id="field_language" class="form-control m-b" name="field_language">
                                            <option value="en_US">English - U.S.A.</option>
                                            <option value="pt_BR">Portuguese - Brazil </option>
                                            <!--
                                            <option value="es_ES">Spanish - Spain </option>
                                            <option value="es_PY">Spanish - Paraguay </option>
                                            -->
                                        </select>
                                    </div>

                                    <div class="col-sm-6"></div>

                                </div>

                            </form>

                            <div class="row">
                                <div class="col-lg-2"></div>

                                <div class="col-md-3">
                                    <button class="btn btn-primary btn-sm" type="button" id="button_step_1" onclick="step_2(document.getElementById('field_language').value)">
                                    <i class="fa fa-arrow-right"></i>&nbsp;<?php echo NEXT ?></button>
                                </div>
                            </div>


                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

</div>

