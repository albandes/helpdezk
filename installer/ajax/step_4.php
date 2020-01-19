<?php
/**
 * Date: 04/07/2019
 * Time: 10:46
 */
error_reporting(E_ERROR | E_PARSE);

include ("../lang/". $_POST['i18n'] . ".php" ."");

session_start() ;
$_SESSION['site_url'] 		= $_POST['site_url'];
$_SESSION['lang_default'] 	= $_POST['lang_default'];
$_SESSION['theme_default'] 	= $_POST['theme_default'];
$_SESSION['timezone_default'] 	= $_POST['timezone_default'];


?>


<div id=content class="col-md-12">

    <div class="ibox float-e-margins">
        <div class="ibox-title"> <h5><?php echo PROGRESS_STEP_4 ?></h5> </div>
        <div class="ibox-content">
            <div>
                <div class="feed-activity-list">
                    <div class="feed-element">
                        <div class="media-body ">
                            <form method="get" class="form-horizontal">
                                <div class="col-lg-12">
                                    <h5><?php echo INFO_STEP_4 ?></h5>
                                </div>

                                <div class="form-group">&nbsp;</div>


                                <div class="col-lg-12">
                                    <?php echo DB_CONFIGURE ?>
                                    <div class="hr-line-dashed"></div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo DB_HOSTNAME ?></label>
                                    <div class="col-sm-9"><input type="text" id="db_hostname" name="db_hostname" class="form-control" value="127.0.0.1" ></div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo DB_PORT ?></label>
                                    <div class="col-sm-9"><input type="text" id="db_port" name="db_port" class="form-control" value="3306" ></div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo DB_NAME ?></label>
                                    <div class="col-sm-9"><input type="text" id="db_name" name="db_name" class="form-control"  value="helpdezk" ></div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo DB_USERNAME ?></label>
                                    <div class="col-sm-9"><input type="text" id="db_username" name="db_username" class="form-control" value="root" ></div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo DB_PASSWORD ?></label>
                                    <div class="col-sm-9"><input type="text" id="db_password" name="db_password" class="form-control" value="" ></div>
                                </div>

                                <div class="col-sm-6"></div>

                        </div>

                        </form>

                        <div class="row">
                            <div class="col-lg-2"></div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btn-sm" onclick="step_3('<?php echo $_POST['i18n'] ?>')">
                                    <i class="fa fa-arrow-left"></i>&nbsp;<?php echo BACK ?></button>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary btn-sm" type="button" id="button_step_5" onclick="step_5('<?php echo $_POST['i18n'] ?>')">
                                    <i class="fa fa-arrow-right"></i>&nbsp;<?php echo NEXT ?></button>
                            </div>
                        </div>


                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

