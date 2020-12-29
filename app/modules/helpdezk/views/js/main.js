
$(document).ready(function () {

    countdown.start(timesession);

    new gnMenu( document.getElementById( 'gn-menu' ) );




    /*
     * Determines the dataTables plugin file, according to the language
     */
    switch (default_lang) {
        case 'en_US':
            var langFile='English';
            break;
        case 'pt_BR':
            var langFile='Portuguese-Brasil.json';
            break;
        default:
            var langFile='English';
    }

    // https://github.com/DataTables/Plugins/tree/master/i18n
    // https://datatables.net/plug-ins/filtering/type-based/accent-neutralise
    var table = $('#dash_table').DataTable( {
        responsive: true,
        "lengthMenu": [ 5, 10, 15, 50, 100 ],
        "language": {
            "url": path + "/includes/js/plugins/dataTables/i18n/"+langFile
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