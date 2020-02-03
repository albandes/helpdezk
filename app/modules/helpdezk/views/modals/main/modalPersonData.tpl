<!-- Person Data modal in main page -->

<div class="modal fade"  id="modal-form-persondata" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


        <input type="hidden" id="idperson" value="{$hidden_idperson}" />

        <div style="width:800px;" class="modal-dialog modal-md"  role="document"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{$smarty.config.Login}:&nbsp; {$login}</h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>



                    <form role="form" id="persondata_form" name="persondata_form" method="post">
                        <!-- Hidden -->
                        <input type="hidden"  id="hidden-idperson"      value="{$id_person}"/>

                        <div class="row col-lg-12 ">
                            <div class="form-group col-sm-4 col-lg-5" style="padding-right: 5px;">
                                &nbsp;
                            </div>
                            <div class="form-group" style="padding-right: 5px;">
                                <div class="col-sm-4  col-lg-3 text-center">
                                    <div id="personPhoto">
                                        <img  alt="image" class="img-circle m-t-xs img-responsive" src="{$person_photo_nav}">
                                    </div>
                                    <div class="m-t-xs font-bold">{$person_department}</div>
                                </div>
                            </div>
                            <div class="form-group col-sm-4  col-lg-4" style="padding-right: 5px;">
                                &nbsp;
                            </div>
                        </div>

                        <div class="row col-lg-12 ">
                            <div class="form-group col-lg-9" style="padding-right: 5px;">
                                    <label title="Choose a disease of interest.">{$smarty.config.Name}</label>
                                    <div >
                                        <input type="text" placeholder="{$placeholder_name}" id="person_name" name="person_name" class="form-control input-sm" value="{$person_name}" required {$person_name_disabled}/>
                                    </div>
                            </div>

                            <div class="form-group col-lg-3 ">
                                <label >{$smarty.config.cpf}</label>
                                <div >
                                    <input type="text"  id="person_ssn_cpf" class="form-control input-sm" placeholder="{$placeholder_ssn_cpf}" value="{$ssn_cpf}" {$ssn_cpf_disabled}/>
                                </div>
                            </div>
                        </div>

                        <div class="row col-lg-12 ">

                            <div class="form-group col-lg-3" style="padding-right: 5px;">
                                <label >{$smarty.config.Gender}</label>

                                <select class="form-control input-sm" id="person_gender" {$person_gender_disabled} >
                                    {html_options values=$genderids output=$gendervals selected=$idgender}
                                </select>
                            </div>

                            <!-- Birth date -->
                            <div class="form-group col-xs-3"  >
                                <label >{$smarty.config.Birth_date}</label>
                                <input type="text" id="person_dtbirth" class="form-control input-sm" placeholder="{$placeholder_dtbirth}" value="{$person_dtbirth}" {$person_dtbirth_disabled}/>
                            </div>

                            <!--- Email --->
                            <div class="form-group col-xs-6"  >
                                <label >{$smarty.config.email}</label>
                                <input type="text" id="person_email" name="person_email" class="form-control input-sm" required placeholder="{$placeholder_email}" value="{$person_email}"  {$person_email_disabled}/>
                            </div>

                        </div>

                        <!-- Phone Number -->
                        <div class="row col-lg-12 ">
                            <div class="form-group col-lg-5" style="padding-right: 5px;">
                                <label >{$smarty.config.Phone}</label>
                                <div >
                                    <input type="text" id="person_phone" class="form-control input-sm" data-mask="(99) 999999999" placeholder="{$placeholder_phone}" value="{$person_phone}" {$person_phone_disabled}/>
                                </div>
                            </div>

                            <div class="form-group col-lg-2 ">
                                <label >{$smarty.config.Branch}</label>
                                <div >
                                    <input type="text" id="person_branch" class="form-control input-sm" data-mask="9999"  value="{$person_branch}" {$person_branch_disabled}/>
                                </div>
                            </div>

                            <!-- Cell phone -->
                            <div class="form-group col-lg-5 ">
                                <label >{$smarty.config.Mobile_phone}</label>
                                <div >
                                    <input type="text" id="person_cellphone" name="person_cellphone" class="form-control input-sm" value="{$person_cellphone}" placeholder="{$placeholder_cellphone}" {$person_cellphone_disabled} required/>
                                </div>
                            </div>
                        </div>

                        <div class="row col-lg-12 ">

                            <!-- Country -->
                            <div class="form-group col-lg-3" style="padding-right: 5px;">

                                <label >{$smarty.config.Country}</label>
                                <select class="form-control input-sm" id="person_country" {$person_country_disabled}>
                                    {html_options values=$countryids output=$countryvals selected=$idcountry}
                                </select>
                            </div>

                            <!-- State -->
                            <div class="form-group col-lg-3" style="padding-right: 5px;">
                                <label >{$smarty.config.State}</label>
                                <select class="form-control input-sm" id="person_state" {$person_state_disabled}>
                                    {html_options values=$stateids output=$statevals selected=$idstate}
                                </select>
                            </div>

                            <!-- city -->
                            <div class="form-group col-lg-6" style="padding-right: 5px;">
                                <label >{$smarty.config.City}</label>
                                <select class="form-control input-sm" id="person_city" {$person_city_disabled}>
                                    {html_options values=$cityids output=$cityvals selected=$idcity}
                                </select>
                            </div>
                            {*
                            <div class="form-group col-lg-6" style="padding-right: 5px;">
                                <label >{$smarty.config.City}</label>
                                <input type="text" id="person_city" name="person_city" class="form-control input-sm" placeholder="{$placeholder_city}" value="{$person_city}" {$person_city_disabled}/>
                            </div>
                            *}
                        </div>

                        <div class="row col-lg-12 ">
                            <!-- Zip Code -->
                            <div class="form-group col-lg-3" style="padding-right: 5px;">
                                <label >{$smarty.config.Zipcode}</label>
                                <input type="text" id="person_zipcode" class="form-control input-sm" data-mask="99999-999"  placeholder="{$placeholder_zipcode}" value="{$person_zipcode}" {$person_zipcode_disabled}/>
                            </div>

                            <!-- Neighborhood -->
                            <div class="form-group col-lg-5" style="padding-right: 5px;">
                                <label >{$smarty.config.Neighborhood}</label>
                                <select class="form-control input-sm" id="person_neighborhood" {$person_neighborhood_disabled}>
                                    {html_options values=$neighborhoodids output=$neighborhoodvals selected=$idneighborhood}
                                </select>
                            </div>
                            <div class="form-group col-lg-4" style="padding-right: 5px;">
                                <label >{$smarty.config.Type_adress}</label>
                                <select class="form-control input-sm" id="person_typestreet" {$person_typestreet_disabled}>
                                    {html_options values=$typestreetids output=$typestreetvals selected=$idtypestreet}
                                </select>
                            </div>

                        </div>

                        <div class="row col-lg-12 ">
                            <div class="form-group col-lg-7" style="padding-right: 5px;">
                                <label >{$smarty.config.Adress}</label>
                                <div >
                                    <input type="text" id="person_address" class="form-control input-sm" placeholder="{$placeholder_address}" value="{$person_address}" {$person_address_disabled}/>
                                </div>
                            </div>

                            <!-- number -->
                            <div class="form-group col-lg-2 " style="padding-right: 5px;">
                                <label >{$smarty.config.Number}</label>
                                <div >
                                    <input type="text" id="person_number" name="person_number" class="form-control input-sm" placeholder="{$placeholder_number}" value="{$person_number}" {$person_number_disabled}/>
                                </div>
                            </div>

                            <div class="form-group col-lg-3 " style="padding-right: 5px;">
                                <label >{$smarty.config.Complement}</label>
                                <div >
                                    <input type="text" id="person_complement" class="form-control input-sm"  value="{$person_complement}" {$person_complement_disabled}/>
                                </div>
                            </div>

                        </div>

                        <div class="row col-lg-12 ">
                            <div class="form-group col-lg-12" style="padding-right: 5px;">
                                <label>{$smarty.config.Lbl_photo}</label>
                                <div>
                                    <div class="text-center">
                                        {*<img alt="image" class="m-t-xs img-thumbnail" src="{$person_photo}">*}
                                        <div id="userPhotoDropzone" class="dropzone dz-default dz-message" ></div>
                                    </div>
                                </div>
                            </div>
                        </div>



                    </form>




                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-12" style="padding-right: 5px;">
                            <div id="alert-update"></div>
                        </div>
                    </div>

                    <div class="row">

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendUpdateUserData" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Send}</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!--</div>-->