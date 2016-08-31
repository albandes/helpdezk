$(document).ready(function(){	
	if(approving == 1) var status = "status/4", id_on = "btn3";
	else var status = "", id_on = "btn6";
	
	var SHColumnUser = JSON.parse(document.getElementById('grid_user').value),
		WColumnUser = JSON.parse(document.getElementById('grid_user_width').value);
    $("#flexigrid2").flexigrid({
        url: path+'/helpdezk/user/json/'+status,
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
            width : WColumnUser[0], 
            sortable : false, 
            align: 'left',
            hide: SHColumnUser[0]
        },
					

        {
            display: 'N&deg;', 
            name : 'code_request', 
            width : WColumnUser[1], 
            sortable : true, 
            align: 'left',
            hide: SHColumnUser[1]
        },

        {
            display: aLang['Opening_date'].replace (/\"/g, ""), 
            name : 'a.entry_date', 
            width : WColumnUser[2], 
            sortable : true, 
            align: 'left',
            hide: SHColumnUser[2]
        },

        {
            display:  aLang['From'].replace (/\"/g, ""), 
            name : 'owner', 
            width : WColumnUser[3], 
            sortable : true, 
            align: 'left',
            hide: SHColumnUser[3]
        },

        {
            display: aLang['Subject'].replace (/\"/g, ""), 
            name : 'subject', 
            width : WColumnUser[4], 
            sortable : true, 
            align: 'left',
            hide: SHColumnUser[4]
        },

        {
            display: aLang['Expire_date'].replace (/\"/g, ""), 
            name : 'a.expire_date', 
            width : WColumnUser[5], 
            sortable : true, 
            align: 'left',
            hide: SHColumnUser[5]
        },

        {
            display: aLang['Var_incharge'].replace (/\"/g, ""), 
            name : 'in_charge', 
            width : WColumnUser[6], 
            sortable : true, 
            align: 'left',
            hide: SHColumnUser[6]
        },

        {
            display: aLang['status'].replace (/\"/g, ""), 
            name : 'status', 
            width : WColumnUser[7], 
            sortable : true, 
            align: 'left',
            hide: SHColumnUser[7]
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
            name: aLang['Waiting_my_approval'].replace(/\"/g, ""), 
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

        searchitems : [
        {
            display: 'N&deg;', 
            name : 'code_request', 
            isdefault: true
        },

        {
            display: aLang['Subject'].replace (/\"/g, ""),
            name : 'subject'
        },

        {
            display: aLang['Description'].replace (/\"/g, ""),
            name : 'description'
        }
        ],

        sortname: "a.entry_date",
        sortorder: "asc",
        usepager: true,
        title: 'Minhas Solicita&ccedil;&otilde;es',
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: 'auto',
        height: $(window).height()-268,
        useFiltro: true,
        filtro: 0,        
        useTipconsulta: false,					
        tipconsulta: 0,				
        tipconsultaOptionsValues:['100,101'],
        tipconsultaOptions:['Minhas Solicitaca&ccedil;&otilde;es', 'Solicita&ccedil;&otilde;es globais']					
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
        if (com==aLang['Waiting_my_approval'].replace(/\"/g, "")){   
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
    
    $(document.getElementById(id_on)).addClass('ativo');
    
    
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
			data: {value: JSON.stringify(arrCols), field: 'grid_user'},
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
    })
    
    $(".cDrag div").mouseup(function(){
		setTimeout(function(){
			var arrWidthCols = new Array();
			$(".hDivBox th div").each(function(key,value){
				arrWidthCols[key] = $(this).width();			
			})
			$.ajax({
				type: "POST",
				url: "config/setConfig",
				data: {value: JSON.stringify(arrWidthCols), field: 'grid_user_width'},
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

function somapar(com2){
    $('#flexigrid2').flexOptions({
        newp:1, 
        params:[{
            name:'COD_STATUS', 
            value: com2
        }]
    }).flexReload();
}	
function trocaclasse(btn){

    //remove a classe ativo 
    $('.tDiv2').find('.ativo').removeClass('ativo');
    //add a classe ativo ao btn clicado
    $(btn).addClass('ativo');
}                           

approving = 0;

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
        console.log('entrou');
        var tempArray = [] ;
        tempArray.push({ rows: []}) ;

        var aArray = [];

        $.each(data.rows, function(key, value){
            aArray = [];
            aArray.push({ cell: []}) ;
            $.each(value.cell, function(key2, value2){
                if (key2 == 4 ){                             // column Subject
                    var chave = base64_decode(value2) ;
                    //  $ret = "<a href='javascript:;' class='linhas' onclick=\"$('#content2').load('user/viewrequest/id/" . $chave . "')\">" . $mostra . "</a>";
                    // <a href="javascript:;" class="linhas" onclick"$('#content2').load('user="" viewrequest="" id="" "+chave+"')="">201505001410</a>

                    aArray[0].cell.push( '<a href=\'javascript:;\' class=\'linhas\' onclick="$(\'#content2\').load(\'user/viewrequest/id/'+value.id+'\')">'+base64_decode(value2)+'</a>');
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
