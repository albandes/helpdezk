<div class="modal fade"  id="modal-form-picture" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <!-- Hidden input to send value by POST  -->
    <div class="modal-dialog modal-lg"  role="document"> {*style="width:1250px;"*}
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>

            <div class="modal-body">
                <!--begin carousel-->
                <div id="myGallery" class="carousel slide" data-interval="false">
                    <div class="carousel-inner" id="gallery-inner">
                    </div>
                    <!--Begin Previous and Next buttons-->
                    <a class="left carousel-control" href="#myGallery" role="button" data-slide="prev"> <span class="glyphicon glyphicon-chevron-left"></span></a> <a class="right carousel-control" href="#myGallery" role="button" data-slide="next"> <span class="glyphicon glyphicon-chevron-right"></span></a>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">{$smarty.config.Close}</button>
            </div>
        </div>
    </div>
</div>
