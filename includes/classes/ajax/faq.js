	
$(document).ready(function(){
	$flexiGrid = $(document.getElementById("flexigrid2"));
	
    $("#flexigrid2").flexigrid({
        url: 'faq/json',  
        dataType: 'json',
        pagestat: aLang['showing'].replace (/\"/g, "")+' {from} '+ aLang['to'].replace (/\"/g, "")+' {to} '+aLang['of'].replace (/\"/g, "")+' {total} '+ aLang['Items'].replace (/\"/g, ""),
        pagetext: aLang['Page'].replace (/\"/g, ""),
        outof: aLang['of'].replace (/\"/g, ""),
        findtext: aLang['Search'].replace (/\"/g, ""),
        procmsg: aLang['Loading'].replace (/\"/g, ""),
        nomsg: aLang['Empty'].replace (/\"/g, ""),
        colModel : [
        {
            display: aLang['Category'].replace (/\"/g, ""), 
            name : 'b.name', 
            width : 100, 
            sortable : true, 
            align: 'left'
        },
        {
            display: aLang['Title'].replace (/\"/g, ""), 
            name : 'a.name', 
            width : 500, 
            sortable : true, 
            align: 'left'
        },

        {
            display: aLang['Author'].replace (/\"/g, ""), 
            name : 'c.name', 
            width : 250, 
            sortable : true, 
            align: 'left'
        }                                        
        ],       

        sortname: "a.name",
        sortorder: "ASC",
        searchitems : [					
				        {
				            display: aLang['Title'].replace (/\"/g, ""), 
				            name : 'a.name'
				        },
				        {
				            display: aLang['Description'].replace (/\"/g, ""), 
				            name : 'a.problem'
				        },{
				            display: aLang['Solution'].replace (/\"/g, ""), 
				            name : 'a.solution'
				        }
				      ],
        usepager: true,
        useRp: true,
        rp: 15,
        showTableToggleBtn: false,
        width: '100%',
        COD_STATUS: 1,
        height: $(window).height()-262,     
        resizable: false,
        minimizado: false,
        singleSelect: true,
        params:[
    			{
    				name:'ID_CATEGORY', 
    				value: 'all'
    			}
    		]
    }); 	    
   

	$("#faqCategories").treeview({
        url: "faq/getCategories"
    });

	var objBase = {		
		changeCategory: function(){
			var $self = $(this),
				$id = $self.data("id");
				$flexiGrid.flexOptions({params:[{name:'ID_CATEGORY', value: $id}]}).flexReload();
		},
		openArticle: function(){
			var $self = $(this),
				$id = $self.data("id")
				modalInfoArticle= $(document.getElementById("modalInfoArticle"));
	        objDefault.maskLoaderShow();
	        modalInfoArticle.load("faq/getArticleInfo/id/"+$id, function(){
	        	objModal.openModal("modalInfoArticle");
	        	objDefault.maskLoaderHide();
	        })		
		}
	}

	$("#content2")
		.off(".contentloaded")
		.on("click.contentloaded", ".loadArticles", objBase.changeCategory)
		.on("click.contentloaded", ".openArticle", objBase.openArticle);
		

	
    	


});//init		