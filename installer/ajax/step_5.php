<?php
/**
 * Date: 04/07/2019
 * Time: 11:18
 */

error_reporting(E_ERROR | E_PARSE);

include ("../lang/". $_POST['i18n'] . ".php" ."");

session_start() ;
$_SESSION['db_hostname'] = $_POST['db_hostname'];
$_SESSION['db_port']     = $_POST['db_port'];
$_SESSION['db_name']     = $_POST['db_name'];
$_SESSION['db_username'] = $_POST['db_username'];
$_SESSION['db_password'] = $_POST['db_password'];



?>
<script   type="text/javascript">
    $("#form_admin").validate({
        ignore:[],
        rules: {
            admin_username: {required:true},
            admin_password: {required:true},
            admin_email: {
                required:true,
                email: true
            }
        },
        messages: {
            admin_username:{required:'<?Php echo REQUIRED_FIELD ?>'},
            admin_password:{required:'<?Php echo REQUIRED_FIELD ?>'},
            admin_email:{required:'<?Php echo REQUIRED_FIELD ?>'}
        }
    });


    $("#button_step_6").click(function(){
        if (!$("#form_admin").valid()) {
            return false ;
        } else {
            step_6('<?php echo $_POST['i18n'] ?>');
        }
    });


</script>

<div id=content class="col-md-12">

    <div class="ibox float-e-margins">
        <div class="ibox-title"> <h5><?php echo PROGRESS_STEP_5 ?></h5> </div>
        <div class="ibox-content">
            <div>
                <div class="feed-activity-list">
                    <div class="feed-element">
                        <div class="media-body ">
                            <form role="form" method="post" class="form-horizontal" name="form_admin" id="form_admin">
                                <div class="col-lg-12">
                                    <h5><?php echo INFO_STEP_5 ?></h5>
                                </div>

                                <div class="form-group">&nbsp;</div>


                                <div class="col-lg-12">
                                    <?php echo ADMIN_CONFIGURE ?>
                                    <div class="hr-line-dashed"></div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo ADMIN_USERNAME ?></label>
                                    <div class="col-sm-9"><input type="text" id="admin_username" name="admin_username" class="form-control" value="admin" ></div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo ADMIN_PASSWORD ?></label>
                                    <div class="col-sm-9"><input type="text" id="admin_password" name="admin_password" class="form-control" value="1234" ></div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo ADMIN_EMAIL ?></label>
                                    <div class="col-sm-9"><input type="text" id="admin_email" name="admin_email" class="form-control" value="foo@domain.com"></div>
                                </div>

                                <div class="col-sm-6"></div>

                        </div>



                        <div class="row">
                            <div class="col-lg-2"></div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btn-sm" onclick="step_4('<?php echo $_POST['i18n'] ?>')">
                                    <i class="fa fa-arrow-left"></i>&nbsp;<?php echo BACK ?></button>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary btn-sm" type="button" id="button_step_6" onclick="">
                                    <i class="fa fa-arrow-right"></i>&nbsp;<?php echo NEXT ?></button>
                            </div>
                        </div>

                        </form>

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

