
$(document).ready(function () {

    countdown.start(timesession);

    new gnMenu( document.getElementById( 'gn-menu' ) );


    // https://github.com/DataTables/Plugins/tree/master/i18n
    // https://datatables.net/plug-ins/filtering/type-based/accent-neutralise
    var table = $('#dash_table').DataTable( {
        responsive: true,
        "lengthMenu": [ 5, 10, 15, 50, 100 ],
        "language": {
            "url": path + "/includes/js/plugins/dataTables/i18n/Portuguese-Brasil.json"
        }
    });

    // Remove accented character from search input as well
    $('#myInput').keyup( function () {
        table
            .search(
              jQuery.fn.DataTable.ext.type.search.string( this.value )
            )
            .draw()
    });

    $('#dash_table tbody').on('click', 'tr', function () {
        var data = table.row( this ).data();
        var codeRequest = data[4]['@data-order'];
        console.log('Redirect to: ' + path + "/helpdezk/hdkTicket/viewrequest/" + codeRequest);
        document.location.href = path+"/helpdezk/hdkTicket/viewrequest/id/" + codeRequest;
    } );


    /*

        var table = $('#dash_table').DataTable({
            responsive: true,
            "dom": 'T<"clear">lfrtip',
            "language": {
                "url": path+"/includes/js/plugins/dataTables/i18n/Portuguese-Brasil.json"
            },
            "lengthMenu": [ 5, 10, 15, 50, 100 ]
            //"tableTools": {
                // "sSwfPath": "includes/js/plugins/dataTables/swf/copy_csv_xls_pdf.swf"
            //}
        });

    */

    /* Init DataTables */
    //var oTable = $('#editable').dataTable();

    // https://harvesthq.github.io/chosen/
    $("#person_country").chosen({ width: "95%", no_results_text: "Nothing found!"})
    $("#person_state").chosen({ width: "95%",   no_results_text: "Nothing found!"})
    $("#person_city").chosen({ width: "95%",    no_results_text: "Nothing found!"})
    $("#person_neighborhood").chosen({ width: "95%",    no_results_text: "Nothing found!"})
    $("#person_typestreet").chosen({ width: "95%", no_results_text: "Nothing found!"})

    // Mask
    $('#person_dtbirth').mask('00/00/0000');
    $('#person_number').mask('0000');
    $('#person_phone').mask(phone_mask);
    $('#person_cellphone').mask(cellphone_mask);
    $('#person_zipcode').mask(zip_mask);
    $('#person_ssn_cpf').mask(id_mask);

    // https://xdsoft.net/jqplugins/autocomplete/
    $("#person_address").autocomplete({
        source:[{
                    url: path+"/helpdezk/home/completeStreet/search/%QUERY%",
                    type: 'remote'
                }
                ],
        accents: true,
        replaceAccentsForRemote: false,
        minLength: 1
    });

    $("#btnUpdate").click(function(){
        $('#modal-form-persondata').modal('show');
        //countdown.start(timesession);
    });

    // Combos
    var formPersonData = $(document.getElementById("persondata_form"));
    var objPersonData = {
        changeState: function() {
            var countryId = $("#person_country").val();
            $.post(path+"/helpdezk/home/ajaxStates",{countryId: countryId},
                function(valor){
                    $("#person_state").html(valor);
                    /*
                     If you need to update the options in your select field and want Chosen to pick up the changes,
                     you'll need to trigger the "chosen:updated" event on the field. Chosen will re-build itself based on the updated content.
                     */
                    $("#person_state").trigger("chosen:updated");
                    return objPersonData.changeCity();
            })
        },
        changeCity: function() {
            var stateId = $("#person_state").val();
            $.post(path+"/helpdezk/home/ajaxCities",{stateId: stateId},
                function(valor) {
                    $("#person_city").html(valor);
                    $("#person_city").trigger("chosen:updated");
                    return objPersonData.changeNeighborhood();
            });
        },
        changeNeighborhood: function() {
            var stateId = $("#person_city").val();
            $.post(path+"/helpdezk/home/ajaxNeighborhood",{stateId: stateId},
                function(valor){
                    $("#person_neighborhood").html(valor);
                    /*
                     If you need to update the options in your select field and want Chosen to pick up the changes,
                     you'll need to trigger the "chosen:updated" event on the field. Chosen will re-build itself based on the updated content.
                     */
                    $("#person_neighborhood").trigger("chosen:updated");
                    return false;
                })
            return false ;
        }

    }

    $("#person_country").change(function(){
        objPersonData.changeState();
    });

    $("#person_state").change(function(){
        objPersonData.changeCity();
    });

    $("#person_city").change(function(){
        objPersonData.changeNeighborhood();
    });

    $("#persondata_form").validate({
        ignore:[],
        rules: {
            person_name: "required",
            person_email: "required",
            person_cellphone: "required"
        },
        messages: {
            person_name: makeSmartyLabel('Alert_field_required'),
            person_email: makeSmartyLabel('Alert_field_required'),
            person_cellphone: makeSmartyLabel('Alert_field_required')
        }
    });

    $("#btnSendUpdateUserData").click(function(){
        if ($("#persondata_form").valid()) {
            var $form = jQuery('#persondata_form'),
            formData = $form.serialize();
            console.log(formData);
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/home/updateUserData',
                dataType: 'text',
                data: {
                    idperson: $('#hidden-idperson').val(),
                    name: $('#person_name').val(),
                    ssn: $('#person_ssn_cpf').val().replace(/[^0-9]/gi, ''),
                    gender: $('#person_gender').val(),
                    dtbirth: $('#person_dtbirth').val(),
                    email: $('#person_email').val(),
                    phone: $('#person_phone').val().replace(/[^0-9]/gi, ''),
                    branch:$('#person_branch').val().replace(/[^0-9]/gi, ''),
                    cellphone: $('#person_cellphone').val().replace(/[^0-9]/gi, ''),
                    country: $('#person_country').val(),
                    state: $('#person_state').val(),
                    city: $('#person_city').val(),
                    zipcode: $('#person_zipcode').val().replace(/[^0-9]/gi, ''),
                    neighb: $('#person_neighborhood').val(),
                    typestreet: $('#person_typestreet').val(),
                    street: $('#person_address').val(),
                    number: $('#person_number').val().replace(/[^0-9]/gi, ''),
                    complement: $('#person_complement').val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-update');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(ret == 'OK') {
                        console.log('voltou e gravou');
                        modalAlertMultiple('success',makeSmartyLabel('Alert_success_update'),'alert-update');
                    } else if(ret == 'OK-without-address') {
                        modalAlertMultiple('success',makeSmartyLabel('Alert_success_withoutaddress'),'alert-update');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-update');
                    }
                }
            });
        } else {
            console.log('nao validou');
            return false;
        }

    });

    if(typeuser != '3'){
        $.post(path + "/helpdezk/home/checkapproval", {}, function(data) {
            if(data > 0){
                $('#tipo-alert-apvrequire').addClass('alert alert-info')
                $('#apvrequire-notification').html(makeSmartyLabel('Alert_approve'));
                $('#modal-approve-require').modal('show');
            }
        })

        $("#btnSendApvReqYes").click(function(){
            location.href = path + "/helpdezk/hdkTicket/index" ;
        });

        $("#btnNewTck").click(function(){
            $.post(path + "/helpdezk/home/checkapproval", {}, function(data) {
                if(data > 0){
                    $('#tipo-alert-apvrequire').addClass('alert alert-danger')
                    $('#apvrequire-notification').html(makeSmartyLabel('Request_approve'));
                    $('#modal-approve-require').modal('show');
                }else{
                    location.href = path + "/helpdezk/hdkTicket/newTicket" ;
                }
            })
        });

    }


});


/**
 * When search a table with accented characters, it can be frustrating to have
 * an input such as _Zurich_ not match _Zürich_ in the table (`u !== ü`). This
 * type based search plug-in replaces the built-in string formatter in
 * DataTables with a function that will remove replace the accented characters
 * with their unaccented counterparts for fast and easy filtering.
 *
 * Note that with the accented characters being replaced, a search input using
 * accented characters will no longer match. The second example below shows
 * how the function can be used to remove accents from the search input as well,
 * to mitigate this problem.
 *
 *  @summary Replace accented characters with unaccented counterparts
 *  @name Accent neutralise
 *  @author Allan Jardine
 *
 *  @example
 *    $(document).ready(function() {
 *        $('#example').dataTable();
 *    } );
 *
 *  @example
 *    $(document).ready(function() {
 *        var table = $('#example').dataTable();
 *
 *        // Remove accented character from search input as well
 *        $('#myInput').keyup( function () {
 *          table
 *            .search(
 *              jQuery.fn.DataTable.ext.type.search.string( this.value )
 *            )
 *            .draw()
 *        } );
 *    } );
 */
(function(){

    function removeAccents ( data ) {
        return data
            .replace( /έ/g, 'ε' )
            .replace( /[ύϋΰ]/g, 'υ' )
            .replace( /ό/g, 'ο' )
            .replace( /ώ/g, 'ω' )
            .replace( /ά/g, 'α' )
            .replace( /[ίϊΐ]/g, 'ι' )
            .replace( /ή/g, 'η' )
            .replace( /\n/g, ' ' )
            .replace( /á/g, 'a' )
            .replace( /é/g, 'e' )
            .replace( /í/g, 'i' )
            .replace( /ó/g, 'o' )
            .replace( /ú/g, 'u' )
            .replace( /ê/g, 'e' )
            .replace( /î/g, 'i' )
            .replace( /ô/g, 'o' )
            .replace( /è/g, 'e' )
            .replace( /ï/g, 'i' )
            .replace( /ü/g, 'u' )
            .replace( /ã/g, 'a' )
            .replace( /õ/g, 'o' )
            .replace( /ç/g, 'c' )
            .replace( /ì/g, 'i' );
    }

    var searchType = jQuery.fn.DataTable.ext.type.search;

    searchType.string = function ( data ) {
        return ! data ?
            '' :
            typeof data === 'string' ?
                removeAccents( data ) :
                data;
    };

    searchType.html = function ( data ) {
        return ! data ?
            '' :
            typeof data === 'string' ?
                removeAccents( data.replace( /<.*?>/g, '' ) ) :
                data;
    };

}());