$(function(){
	$flexiGrid = $(document.getElementById("flexigrid2"));
	$("#flexigrid2").flexigrid({
        url: 'knowledgebase/json',  
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
            align: 'left',
            hide: true
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
        height: 400,     
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

	$("#klbCategories").treeview({
        url: "knowledgebase/getCategories"
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
	        modalInfoArticle.load("knowledgebase/getArticleInfoNote/id/"+$id, function(){
	        	objModal.openModal("modalInfoArticle");
	        	objDefault.maskLoaderHide();
	        })		
		},
		useSolution: function(){
			noteTxt = $(document.getElementById("solutionInfo")).html();
			CKEDITOR.instances['note'].setData(noteTxt);
			objModal.closeModal("modalInfoArticle");
		}
	}

	$("#content2")
		.off(".contentloaded")
		.on("click.contentloaded", ".loadArticles", objBase.changeCategory)
		.on("click.contentloaded", ".openArticle", objBase.openArticle)
		.on("click.contentloaded", "#btnUseSolution", objBase.useSolution);	

})