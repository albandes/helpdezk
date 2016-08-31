	
$(document).ready(function(){
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
            align: 'left'
        }                                        
        ],

        buttons : [
        {
            name: aLang['category_insert'].replace (/\"/g, ""),
            bclass: 'add',
            onpress: addCat
        },

        {
            separator:true
        },

        {
            name: aLang['Category_edit'].replace (/\"/g, ""),
            bclass: 'edit',
            onpress: editCat
        },
        
        {
            separator:true
        },

        {
            name: aLang['Article_insert'].replace (/\"/g, ""),
            bclass: 'add',
            onpress: addArt
        },

        {
            separator:true
        },

        {
            name: aLang['Article_remove'].replace (/\"/g, ""), 
            bclass: 'delete', 
            onpress: removeArticle
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
	        modalInfoArticle.load("knowledgebase/getArticleInfo/id/"+$id, function(){
	        	objModal.openModal("modalInfoArticle");
	        	objDefault.maskLoaderHide();
	        })		
		},
		removeAtt: function(){
			var $self = $(this),
				$id = $self.data("id");
	        $.ajax({
				type: "POST",
				url: "knowledgebase/removeAtt/id/"+$id,
				error: function (ret) {
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalInfo");
				},
				success: function(ret) {
					if(ret){
						//console.log($self.parent().empty());
						$self.parent().empty().append('<iframe src="knowledgebase/upload/id/null" name="ianexo" id="ianexo" height="22" frameborder="0" scrolling="no"></iframe>');
					}
					else
						objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalInfo");
				}
			});				
		}
	}

	$("#content2")
		.off(".contentloaded")
		.on("click.contentloaded", ".loadArticles", objBase.changeCategory)
		.on("click.contentloaded", ".openArticle", objBase.openArticle)
		.on("click.contentloaded", ".removeFile", objBase.removeAtt);
		

	$(document.getElementById('modalInfoArticle')).find('form').live("submit",function(){
		var $self = $(this),
			modalEditArticle = $(document.getElementById("modalEditArticle")),
			$idArt = $self.find("#idArticleEdit").val();
		modalEditArticle.load("knowledgebase/modalEditArticle", { id: $idArt } ,function(){
			objDefault.init();
			$("#formEditArticle").validate({
	    		ignore: "input:hidden:not(input:hidden.required)",
	    		wrapper: "li class='error'",            		
	    		errorPlacement: function(error, element) {
					error.appendTo(element.parent().parent());
				},
			  	rules: {
			  		cmbCategory: "required",
			  		txtTitle: "required",
			  		txtDescPro: {
			  			required: function(textarea){
							CKEDITOR.instances[textarea.id].updateElement(); // update textarea
							var editorcontent = textarea.value.replace(/<[^>]*>/gi, ''); // strip tags
							return editorcontent.length === 0;
			  			}
			  		},
			  		txtSolPro: {
			  			required: function(textarea){
							CKEDITOR.instances[textarea.id].updateElement(); // update textarea
							var editorcontent = textarea.value.replace(/<[^>]*>/gi, ''); // strip tags
							return editorcontent.length === 0;
			  			}
			  		} 
			 	}
			});
        	objModal.openModal("modalEditArticle");
        })
	});
    	
	$(document.getElementById('modalInsertCategory')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendCategory'));
		$.ajax({
			type: "POST",
			url: "knowledgebase/insertCategory",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalInsertCategory");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalInsertCategory");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalInsertCategory");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});
	
	$(document.getElementById("modalListEditCategory")).find(".loadCat").live("click",function(e){
		e.preventDefault();
		var modalEdit = $(document.getElementById("modalEditCategory")),
			id = $(this).attr('href');			
		objDefault.maskLoaderShow();
        modalEdit.load("knowledgebase/modalEditCategories/id/"+id, function(){
        	objDefault.init();        		        	
        	$("#formCategoryEdit").validate({
	    		wrapper: "li class='error'",            		
	    		errorPlacement: function(error, element) {
					error.appendTo(element.parent().parent());
				},
			  	rules: {
			  		txtName: {
			  			required: true
			  		}
			 	}
			});        	
        	objModal.openModal("modalEditCategory");
        	objDefault.maskLoaderHide();
        })	        
	})
	
	$(document.getElementById('modalEditCategory')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnEditCategory'));
		$.ajax({
			type: "POST",
			url: "knowledgebase/editCategory",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalEditCategory");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalEditCategory");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalEditCategory");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});
   
   	$(document.getElementById('modalInsertArticle')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnInsertArticle'));		
		$.ajax({
			type: "POST",
			url: "knowledgebase/insertArticle",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalInsertArticle");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalInsertArticle");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalInsertArticle");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});

	$(document.getElementById('modalEditArticle')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnEditArticle'));		
		$.ajax({
			type: "POST",
			url: "knowledgebase/editArticle",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalEditArticle");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_inserted'].replace (/\"/g, ""),"modalEditArticle");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_failure'].replace (/\"/g, ""),"modalEditArticle");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});

	$(document.getElementById('modalDeleteArticle')).find('form').live("submit",function(){
		var $self = $(this),
			$btn = $self.find(document.getElementById('btnSendArticleDelete'));		
		$.ajax({
			type: "POST",
			url: "knowledgebase/deleteArticle",
			data: $(this).serialize(),
			error: function (ret) {
				objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalDeleteArticle");
			},
			success: function(ret) {
				if(ret){
					objDefault.notification("success",aLang['Alert_deleted'].replace (/\"/g, ""),"modalDeleteArticle");
					$("#flexigrid2").flexReload();
				}
				else
					objDefault.notification("error",aLang['Alert_deleted_error'].replace (/\"/g, ""),"modalDeleteArticle");
			},
			beforeSend: function(){
				objDefault.buttonAction($btn,'disabled');
			},
			complete: function(){
				objDefault.buttonAction($btn,'enabled');
			}
		});		
	});

});//init		

function addCat(){
	var modalInsert = $(document.getElementById("modalInsertCategory"));
    objDefault.maskLoaderShow();
    modalInsert.load("knowledgebase/modalInsertCategories", function(){
    	objDefault.init();
    	$("#formCategoryInsert").validate({
    		wrapper: "li class='error'",            		
    		errorPlacement: function(error, element) {
				error.appendTo(element.parent().parent());
			},
		  	rules: {
		  		txtName: {
		  			required: true
		  		}
		 	}
		});    	
    	objModal.openModal("modalInsertCategory");
    	objDefault.maskLoaderHide();
    })
}

function editCat(){
	var modalInsert = $(document.getElementById("modalListEditCategory"));
    objDefault.maskLoaderShow();
    modalInsert.load("knowledgebase/modalListEditCategories", function(){
    	objModal.openModal("modalListEditCategory");
    	objDefault.maskLoaderHide();
    })
}

function addArt(){	
	var modalInsert = $(document.getElementById("modalInsertArticle"));
    objDefault.maskLoaderShow();
    modalInsert.load("knowledgebase/modalInsertArticle", function(){
    	objDefault.init();
    	$("#formInsertArticle").validate({
    		ignore: "input:hidden:not(input:hidden.required)",
    		wrapper: "li class='error'",            		
    		errorPlacement: function(error, element) {
				error.appendTo(element.parent().parent());
			},
		  	rules: {
		  		cmbCategory: "required",
		  		txtTitle: "required",
		  		txtDescPro: {
		  			required: function(textarea){
						CKEDITOR.instances[textarea.id].updateElement(); // update textarea
						var editorcontent = textarea.value.replace(/<[^>]*>/gi, ''); // strip tags
						return editorcontent.length === 0;
		  			}
		  		},
		  		txtSolPro: {
		  			required: function(textarea){
						CKEDITOR.instances[textarea.id].updateElement(); // update textarea
						var editorcontent = textarea.value.replace(/<[^>]*>/gi, ''); // strip tags
						return editorcontent.length === 0;
		  			}
		  		} 
		 	}
		});    	
    	objModal.openModal("modalInsertArticle");
    	objDefault.maskLoaderHide();
    })	
}

function removeArticle(com, grid){   
    if($('.trSelected',grid).length>0){
        
        var items = $('.trSelected');
        var id = items[0].id.substr(3);          
        var modalDelete = $(document.getElementById("modalDeleteArticle"));
        objDefault.maskLoaderShow();
        modalDelete.load("knowledgebase/modalDeleteArticle/id/"+id, function(){
        	objModal.openModal("modalDeleteArticle");
        	objDefault.maskLoaderHide();
        });
    }
    else{
        objDefault.notification("info",aLang['Alert_select_one'].replace (/\"/g, ""),"modalInfo");
    	objModal.openModal("modalInfo");
    }    
}