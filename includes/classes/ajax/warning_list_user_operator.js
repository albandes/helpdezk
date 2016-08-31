$(document).ready(function(){

    $("#flexigrid2").flexigrid({
        url: 'warnings/json',  
        dataType: 'json',
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        colModel : [
        {
            display: aLang['Topic'].replace (/\"/g, ""), 
            name : 'title_topic', 
            width : 350, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['Title'].replace (/\"/g, ""), 
            name : 'title_warning', 
            width : 350, 
            sortable : true, 
            align: 'left'
        }                                        
        ],

        buttons : [
        {
            name: aLang['New'].replace (/\"/g, ""),
            bclass: 'ativo',
            onpress: news
        },

        {
            separator:true
        },

        {
            name: aLang['Read'].replace (/\"/g, ""),
            onpress: read
        },
        
        {
            separator:true
        },

        {
            name: aLang['Closed'].replace (/\"/g, ""),
            onpress: closed
        }
        ],

        sortname: "title_topic",
        sortorder: "ASC",
        usepager: true,
        title:  ":: "+aLang['pgr_warnings'].replace (/\"/g, ""),
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: 'auto',
        COD_STATUS: 1,
        height: $(window).height()-262,     
        resizable: false,
        minimizado: false,
        singleSelect: true
    }); 	    
   
	
	$(".openWarning ").live("click",function(){
		var id = $(this).attr("rel"),
			modalWarning= $(document.getElementById("modalWarning"));
			objDefault.maskLoaderShow();
			modalWarning.load("warnings/getWarningInfo/id/"+id, function(){
        	objModal.openModal("modalWarning");
        	$("#flexigrid2").flexReload();		            	
        	objDefault.maskLoaderHide();
        	
        	$.ajax({
				url: "user/numNewWarningsAjax",				
				success: function(ret) {
					$numW = $(document.getElementById("nav")).find(".numWarning");
					if(ret > 0)
						$numW.text(ret).show();
					else
						$numW.hide();
				}
			});	
        })
		
	})
	
   
});//init		 
total_warnings = $(document.getElementById("total_warnings")).val();
if(total_warnings > 0){
	$(document.getElementById("nav")).find(".numWarning").text(total_warnings).show();	 
}

$flexiGrid = $(document.getElementById("flexigrid2"));

function news(){
	$('.tDiv2').find('.ativo').removeClass('ativo');
	$(this).addClass('ativo');
	$flexiGrid.flexOptions({params:[{name:'COD_STATUS', value: 1}]}).flexReload();
}

function read(){
	$('.tDiv2').find('.ativo').removeClass('ativo');
	$(this).addClass('ativo');
	$flexiGrid.flexOptions({params:[{name:'COD_STATUS', value: 2}]}).flexReload();
}

function closed(){
	$('.tDiv2').find('.ativo').removeClass('ativo');
	$(this).addClass('ativo');
	$flexiGrid.flexOptions({params:[{name:'COD_STATUS', value: 3}]}).flexReload();
}



