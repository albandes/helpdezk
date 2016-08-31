$(document).ready(function(){


	var arraySearch = [
                        {
                            display: aLang['Date'].replace (/\"/g, ""),
                            name : 'date_request',
                            isdefault: true
                        },
                        {
				            display: 'N&deg;', 
				            name : 'code_request'

				        },
				
				        {
				            display: aLang['Subject'].replace (/\"/g, ""), 
				            name : 'subject'
				        },
				
				        {
				            display: aLang['Description'].replace (/\"/g, ""), 
				            name : 'description'
				        },
				        
				        {
				            display: aLang['User'].replace (/\"/g, ""), 
				            name : 'own.name'
				        },
				        
				        {
				            display: aLang['Company'].replace (/\"/g, ""), 
				            name : 'comp.name'
				        }];
				        
	if(document.getElementById('displayEquipment').value == 1){
		var searchEquipment = [{
									display: aLang['Tag'].replace (/\"/g, ""),
				            		name : 'a.label'
								},
								{
									display: aLang['Service_order_number'].replace (/\"/g, ""),
				            		name : 'a.os_number'
								},
								{
									display: aLang['Serial_number'].replace (/\"/g, ""),
				            		name : 'a.serial_number'
								}];
		
		arraySearch = arraySearch.concat(searchEquipment);
	}
		
	var SHColumn = JSON.parse(document.getElementById('grid_operator').value),
		WColumn = JSON.parse(document.getElementById('grid_operator_width').value),
		SortColumn = document.getElementById('sortname').value,
		SortColumnOrder = document.getElementById('sortorder').value;
	
    $("#flexigrid2").flexigrid({
        url: 'operator/json/',
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        dataType: 'json',
        preProcess: preProcessData,
        colModel : [
        {
            display: '&nbsp;', 
            name : 'des', 
            width : WColumn[0], 
            sortable : false, 
            align: 'center',
            hide: SHColumn[0]
        },					

        {
            display: '<img src="'+path+'/app/themes/'+theme+'/images/ico_anexos.gif" >', 
            name : 'totatt', 
            width : WColumn[1], 
            sortable : true, 
            align: 'center',
            hide: SHColumn[1]
        },

        {
            display: 'N&deg;', 
            name : 'a.code_request', 
            width : WColumn[2], 
            sortable : true, 
            align: 'left',
            hide: SHColumn[2]
        },

        {
            display: aLang['Opening_date'].replace (/\"/g, ""), 
            name : 'entry_date_order', 
            width : WColumn[3], 
            sortable : true, 
            align: 'center',
            hide: SHColumn[3]
        },

        {
            display: aLang['Company'].replace (/\"/g, ""), 
            name : 'company', 
            width : WColumn[4], 
            sortable : true, 
            align: 'left',
            hide: SHColumn[4]
        },

        {
            display: aLang['From'].replace (/\"/g, ""), 
            name : 'personname', 
            width : WColumn[5], 
            sortable : true, 
            align: 'left',
            hide: SHColumn[5]
        },

        {
            display: aLang['Type'].replace (/\"/g, ""), 
            name : 'type', 
            width : WColumn[6], 
            sortable : true, 
            align: 'left',
            hide: SHColumn[6]
        },

        {
            display: aLang['Item'].replace (/\"/g, ""), 
            name : 'item', 
            width : WColumn[7], 
            sortable : true, 
            align: 'left',
            hide: SHColumn[7]
        },

        {
            display: aLang['Service'].replace (/\"/g, ""), 
            name : 'service', 
            width : WColumn[8], 
            sortable : true, 
            align: 'left',
            hide: SHColumn[8]
        },

        {
            display: aLang['Subject'].replace (/\"/g, ""), 
            name : 'a.subject', 
            width : WColumn[9], 
            sortable : true, 
            align: 'left',
            hide: SHColumn[9]
        },

        {
            display: aLang['status'].replace (/\"/g, ""), 
            name : 'a.idstatus', 
            width : WColumn[10], 
            sortable : true, 
            align: 'left',
            hide: SHColumn[10]
        },

        {
            display: aLang['Var_incharge'].replace (/\"/g, ""), 
            name : 'in_charge', 
            width : WColumn[11], 
            sortable : true, 
            align: 'left',
            hide: SHColumn[11]
        },

        {
            display: aLang['Priority'].replace (/\"/g, ""), 
            name : 'priority', 
            width : WColumn[12], 
            sortable : true, 
            align: 'center',
            hide: SHColumn[12]
        },

        {
            display: aLang['Expire_date'].replace (/\"/g, ""), 
            name : 'entry_date_order', 
            width : WColumn[13], 
            sortable : true, 
            align: 'center',
            hide: SHColumn[13]
        }
        ],

        buttons : [
        {
            name: aLang['New'].replace (/\"/g, ""), 
            bclass: 'novas', 
            onpress : mostra
        },

        {
            name: aLang['Being_attended'].replace (/\"/g, ""), 
            bclass: 'atendimento', 
            onpress : mostra
        },

        {
            name: aLang['Waiting_for_approval'].replace(/\"/g, ""), 
            bclass: 'aguardando', 
            onpress : mostra
        },

        {
            name: aLang['Finished'].replace (/\"/g, ""), 
            bclass: 'encerradas', 
            onpress : mostra
        },

        {
            name: aLang['Rejected'].replace (/\"/g, ""), 
            bclass: 'aprovadas', 
            onpress : mostra
        },

        {
            name: aLang['all'].replace (/\"/g, ""), 
            bclass: 'todas', 
            onpress : mostra
        }
        ],

        searchitems : arraySearch,        
        sortname: SortColumn,
        sortorder: SortColumnOrder,
        usepager: true,
        title: aLang['Attendance'].replace (/\"/g, ""),
        useRp: true,
        rp: 20,
        showTableToggleBtn: false,
        width: 'auto',
        height: $(window).height()-268,
        useCOD_STATUS:true,
        COD_STATUS: 1,
        useTipconsulta: true,
        tipconsulta: 100,
        useDatvencimento: true,
        datvencimento: 0,
        tipconsultaOptions: [aLang['grd_show_all'].replace (/\"/g, ""),aLang['grd_show_only_mine'].replace (/\"/g, ""),aLang['grd_show_group'].replace (/\"/g, "")],
        datvencimentoOptions: ['-- '+aLang['Expire_date'].replace (/\"/g, "")+' -- ',aLang['grd_expiring'].replace (/\"/g, ""),aLang['grd_expiring_today'].replace (/\"/g, ""),aLang['grd_expired'].replace (/\"/g, ""),aLang['grd_expired_n_assumed'].replace (/\"/g, "")]

					
    }
    ); 
    function mostra(com){
        if (com==aLang['Being_attended'].replace (/\"/g, "")){
            trocaclasse(this);
            somapar('3');
        }
        if (com==aLang['New'].replace (/\"/g, "")){      
            trocaclasse(this);
            somapar('1');
        }
        if (com==aLang['Waiting_for_approval'].replace(/\"/g, "")){   
            trocaclasse(this);                         
            somapar('4');
        }
        if (com==aLang['Finished'].replace (/\"/g, "")){                             
            trocaclasse(this);
            somapar('5');
        }
        if (com==aLang['Rejected'].replace (/\"/g, "")){                   
            trocaclasse(this);
            somapar('6');
        }
        if (com==aLang['all'].replace (/\"/g, "")){                   
            trocaclasse(this);
            somapar('0');
        }				
    }
    
    $(document.getElementById('btn1')).addClass('ativo');
    
    
    $(".togCol").click(function(){    	
    	var arrCols = new Array();    	    	    	
    	$(".nDiv .togCol").each(function(key,value){
    		if($(this).is(":checked")) val = 0;
    		else val = 1;
    		arrCols[key] = val;  		
    	})    	
    	check = 0;
    	$.each( arrCols, function( i, val ) {
			if(val == 0) check = 1;
		});    	
    	if(check == 0) arrCols[0] = 0;    	
    	$.ajax({
			type: "POST",
			url: "config/setConfig",
			data: {value: JSON.stringify(arrCols), field: 'grid_operator'},
			error: function (ret) {
				objDefault.notification("error",aLang['Error'].replace (/\"/g, ""),"modalInfo");
				objModal.openModal("modalInfo");
			},
			success: function(ret) {
				if(!ret){
					objDefault.notification("info",aLang['Error'].replace (/\"/g, ""),"modalInfo");
	    			objModal.openModal("modalInfo");
				}					
			}
		});
    });
    
	
	$(".cDrag div").mouseup(function(){
		setTimeout(function(){
			var arrWidthCols = new Array();
			$(".hDivBox th div").each(function(key,value){
				arrWidthCols[key] = $(this).width();			
			})
			$.ajax({
				type: "POST",
				url: "config/setConfig",
				data: {value: JSON.stringify(arrWidthCols), field: 'grid_operator_width'},
				error: function (ret) {
					objDefault.notification("error",aLang['Error'].replace (/\"/g, ""),"modalInfo");
					objModal.openModal("modalInfo");
				},
				success: function(ret) {
					if(!ret){
						objDefault.notification("info",aLang['Error'].replace (/\"/g, ""),"modalInfo");
		    			objModal.openModal("modalInfo");
					}					
				}
			});
			
		},0);	 	
	});
    
    
});//init		   
		
function refresh(){
    $('#flexigrid2').flexReload();
}

var autoRefreshOpe = document.getElementById('autoRefreshGridOperator').value;

if(autoRefreshOpe > 0){
	setInterval(function(){
		$('#flexigrid2').flexReload();
	},autoRefreshOpe);	
}

function somapar(com2){
	var $tipCons = $("select[name=tipconsulta]"),
		$flexiGrid = $(document.getElementById("flexigrid2"));
	if(com2 == 3)	valTip = 101;	
	else			valTip = 100;
	$tipCons.find("option[value="+valTip+"]").attr("selected","selected");
    $flexiGrid.flexOptions({newp:1,tipconsulta: valTip, params:[{name:'COD_STATUS', value: com2}]}).flexReload();
}

function trocaclasse(btn){
    //remove a classe ativo 
    $('.tDiv2').find('.ativo').removeClass('ativo');
    //add a classe ativo ao btn clicado
    $(btn).addClass('ativo');
}


/**
 * Decodes string using MIME base64 algorithm
 * @see http://phpjs.org/functions/base64_decode
 */
function base64_decode( data ) {
    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0, ac = 0, dec = "", tmp_arr = [];

    if (!data) {
        return data;
    }

    data += '';

    do {  // unpack four hexets into three octets using index points in b64
        h1 = b64.indexOf(data.charAt(i++));
        h2 = b64.indexOf(data.charAt(i++));
        h3 = b64.indexOf(data.charAt(i++));
        h4 = b64.indexOf(data.charAt(i++));

        bits = h1<<18 | h2<<12 | h3<<6 | h4;

        o1 = bits>>16 & 0xff;
        o2 = bits>>8 & 0xff;
        o3 = bits & 0xff;

        if (h3 == 64) {
            tmp_arr[ac++] = String.fromCharCode(o1);
        } else if (h4 == 64) {
            tmp_arr[ac++] = String.fromCharCode(o1, o2);
        } else {
            tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
        }
    } while (i < data.length);

    dec = tmp_arr.join('');
    dec = this.utf8_decode(dec);
    return dec;
}

/**
 * Converts a UTF-8 encoded string to ISO-8859-1
 * @see http://phpjs.org/functions/utf8_decode
 */
function utf8_decode ( str_data ) {
    var tmp_arr = [], i = 0, ac = 0, c1 = 0, c2 = 0, c3 = 0;

    str_data += '';

    while ( i < str_data.length ) {
        c1 = str_data.charCodeAt(i);
        if (c1 < 128) {
            tmp_arr[ac++] = String.fromCharCode(c1);
            i++;
        } else if ((c1 > 191) && (c1 < 224)) {
            c2 = str_data.charCodeAt(i+1);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
            i += 2;
        } else {
            c2 = str_data.charCodeAt(i+1);
            c3 = str_data.charCodeAt(i+2);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
            i += 3;
        }
    }

    return tmp_arr.join('');
}


function preProcessData(data)
{
    /*
     * http://stackoverflow.com/questions/17075082/jquery-push-to-make-multidimensional-array
     */

    if(document.getElementById('license').value == 201001012) // IESA
    {

        var tempArray = [] ;
        tempArray.push({ rows: []}) ;

        var aArray = [];

        $.each(data.rows, function(key, value){
            aArray = [];
            aArray.push({ cell: []}) ;
            $.each(value.cell, function(key2, value2){
                if (key2 == 9 ){                             // column Subject
                    aArray[0].cell.push( '<a href=\'#/operator/viewrequest/id/'+value.id+'\' class=\'subject\' >'+base64_decode(value2)+'</a>');
                } else {
                    aArray[0].cell.push( value2);
                }
            });
            tempArray[0].rows.push({
                id: value.id,
                cell: aArray[0].cell
            } );
       });

        var returnArray = [];

        returnArray.push({
            page : data.page,
            total: data.total,
            //rows: data.rows,
            rows: tempArray[0].rows,
            params: data.params
        });

        return $.extend({}, returnArray[0]) ; // Convert array to object

    } else {

        return data ;

    }

}



