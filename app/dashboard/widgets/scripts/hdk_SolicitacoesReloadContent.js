/**
 * @file
 *    Example implementation of a serverside executable javascript callback for the reloadContent event.
 *
 * Released under the GNU General Public License.  See LICENSE.txt.
 */

$(function() {

	//$(window.dashboardDemo.widgets['solicitacoes'].contentElement).append('<div id="dt_example"><div id="container"><div id="dynamic"><table cellpadding="0" cellspacing="0" border="0" class="display" id="example"><thead><tr><th width="5px"></th><th >Abertura</th><th width="240px">Assunto</th><th>link</th><th>vencimento</th></tr></thead><tbody></tbody><tfoot><tr><th ></th><th>Abertura</th><th>Assunto</th></tr></tfoot></table></div></div></div>');
		
		var oTable;
		
		oTable = $('#example').dataTable( {
			"oLanguage": { 
				"sProcessing":   "Processando...",
				"sLengthMenu":   "Mostrar _MENU_ registros",
				"sZeroRecords":  "Não foram encontrados resultados",
				"sInfo":         "Mostrando de _START_ a _END_ de _TOTAL_ registros",
				"sInfoEmpty":    "Mostrando de 0 a 0 de 0 registros",
				"sInfoFiltered": "(filtrado de _MAX_ registros no total)",
				"sInfoPostFix":  "",
				"sSearch":       "Buscar:",
				"sUrl":          "",
				"oPaginate": {
					"sFirst":    "Primeiro",
					"sPrevious": "Anterior",
					"sNext":     "Seguinte",
					"sLast":     "Último"
							}
			},
			"aoColumns":[
				{"sClass": "center", "bSortable": false },
				{"sClass": "abertura"},
				null,
				{"bVisible":    false },
				{"bVisible":    false }
			],
			"bProcessing": true,
			"sAjaxSource": 'widgets/ajax/hdk_solicitacoes.php'
		});	
			
			
		/* Formating function for row details */
		function fnFormatDetails ( nTr )
		{
			var aData = oTable.fnGetData( nTr );
			var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
			sOut += '<tr><td>Assunto:</td><td>'+aData[2]+'</td></tr>';
			sOut += '<tr><td>Link :</td><td>'+aData[3]+'</td></tr>';
			sOut += '<tr><td>Vencimento:</td><td>'+aData[4]+'</td></tr>';
			sOut += '</table>';
			
			return sOut;
		}				
			$('#example tbody td img').live('click', function() {
				var nTr = this.parentNode.parentNode;
		  		if ( this.src.match('details_close') )
				{
					/* This row is already open - close it */
					this.src = "widgets/classes/jquery.datatables/images/details_open.png";
					oTable.fnClose( nTr );
				}
				else
				{
					/* Open this row */
					this.src = "widgets/classes/jquery.datatables/images/details_close.png";
					oTable.fnOpen( nTr, fnFormatDetails(nTr), 'details' );
				}
			});

});
