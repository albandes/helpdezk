<?php
/**
 * Created by PhpStorm.
 * Date: 05/07/2019
 * Time: 10:25
 */

include ("../lang/". $_POST['i18n'] . ".php" ."");
session_start() ;
//echo '<pre';
//print_r($_SESSION);

?>
<div id=content class="col-md-12">

    <div class="ibox float-e-margins">
        <div class="ibox-title"> <h5><?php echo PROGRESS_STEP_7 ?></h5> </div>
        <div class="ibox-content">
            <div>
                <div class="feed-activity-list">
                    <div class="feed-element">
                        <div class="media-body ">
                            <form role="form" method="post" class="form-horizontal" name="form_admin" id="form_admin">
                                <div class="col-lg-12">
                                    <div id="install_status">
                                        <h5><i class="fa fa-spinner fa-spin"></i> <?php echo WAIT_INSTALL ?></h5>
                                    </div>
                                </div>

                                <div class="form-group">&nbsp;</div>


                        </div>



                        <!-- <div class="row">
                            <div class="col-lg-2"></div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btn-sm" onclick="step_5('<?php echo $_POST['i18n'] ?>')">
                                    <i class="fa fa-arrow-left"></i>&nbsp;<?php echo BACK ?></button>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary btn-sm" type="button" id="button_step_6" onclick="step_7('<?php echo $_POST['i18n'] ?>')">
                                    <i class="fa fa-arrow-right"></i>&nbsp;<?php echo NEXT ?></button>
                            </div>
                        </div>
                        -->

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
