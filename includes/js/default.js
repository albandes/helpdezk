$(function() {			

	$(document).ajaxStart(function() {
		if(!document.getElementById("form_login")){
			$.ajax({
				url: path+"/admin/index/valida",
				success: function(data){
					if(data) window.location = path+"/admin/login";
					else countdown.refresh();
				}
			})
		}
	});

	objValidate = {
		validate: function(){
			$.ajax({
				url: path+"/admin/index/valida",
				success: function(data){
					if(data) window.location = path+"/admin/login";
					else countdown.refresh();
				}
			})	
		}
	}

	/* jQuery Validator additional methods */	
	if(jQuery.validator){
	
		jQuery.validator.addMethod("hdTime", function(value, element) {  
			return this.optional(element) || /^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/i.test(value);  
		});
	
		jQuery.validator.addMethod("hdDate", function(value, element) {
			var data   = value,
				barra1 = data.substr(2,1),
				barra2 = data.substr(5,1),
				ano    = data.substr(6,4);
			if(default_lang == "pt_BR"){
				if(value.length!=10) return false;
				var dia = data.substr(0,2),
					mes = data.substr(3,2);
			}else{
				var mes = data.substr(0,2),
					dia = data.substr(3,2);
			}
			if(data.length!=10||barra1!="/"||barra2!="/"||isNaN(dia)||isNaN(mes)||isNaN(ano)||dia>31||mes>12)return false;
				if((mes==4||mes==6||mes==9||mes==11) && dia==31)return false;
				if(mes==2  &&  (dia>29||(dia==29 && ano%4!=0)))return false;
				if(ano < 1900)return false;
				return true;
		});
		
		jQuery.validator.addMethod("notEqualTo", function(value, element, param) {
	          return this.optional(element) || value != $(param).val();
	    });
	    
	    jQuery.validator.addMethod("haveOption", function(value, element) {
	          if($(element).find("option").length > 0) return true;
	          else return false;
	    });
	}
	/* // jQuery Validator additional methods */
	
	/* START MODAL */
	$("body").delegate(".openModal", "click", function(e) {
		$('#mask, .window').hide();
		e.preventDefault();		
		
		var $self = $(this),
			$href = $($self.attr('href')),
			$win = $(window),
			winH = $win.height(),
			winW = $win.width(),
			scroll = $win.scrollTop();
		
		if($self.hasClass('print')){
			var $rel = $(document.getElementById($self.attr('rel'))),
				$modalContent = $href.find('.modalPrintContent');
				
				$modalContent.empty();
				$modalContent.append($rel.html());
				
				
				$href.find('.btnPrintModal').css('left',$href.width()+30);
				
				/* TIME OF REPORT */
				var currentTime = new Date(),
					month = currentTime.getMonth() + 1,
					day = currentTime.getDate(),
					year = currentTime.getFullYear(),
					hours = currentTime.getHours(),
					minutes = currentTime.getMinutes();
					
				if (minutes < 10) minutes = "0" + minutes;
				
				if(default_lang == "pt_BR"){
					date = day + "/" + month + "/" + year;
					hour = hours+"h "+	minutes+"min";			
				}
				else if(default_lang == "en_US"){
					if(hours > 11) pmam = "PM";
					else pmam = "AM";
					date = month + "/" + day + "/" + year;
					hour = hours + ":" + minutes + " " + pmam;
				}
				$href.find(".date").text(date +" - "+ hour);
				/* //TIME OF REPORT */		
		}
		
		var maskHeight = $(document).height();
		var maskWidth = $(window).width();

		$(document.getElementById('mask')).css({'width':maskWidth,'height':maskHeight});	
		$(document.getElementById('mask')).fadeTo("fast",0.5);	
		


		var top = winH/2-$href.height()/2 + scroll - 21;
		if(top < 0) top = 30;
		
		$href.css('top',  top);
		$href.css('left', winW/2-$href.width()/2);
		$href.fadeIn(500); 
		return true;
	});

	$('#mask, .closeModal').live("click",function (e) {
		e.preventDefault();
		if($('.window:visible').hasClass("noHideMask")){
			return false;
		}
		if($('body').hasClass("noHideMask") || $('body').hasClass("loading")){
			return false;
		}
		objModal.closeModal();	
	});
	
	$('.closeModalEver').live("click",function (e) {
		e.preventDefault();
		objModal.closeModal();	
	});

	$('body').keyup(function(e) {
		if($('.window:visible').hasClass("noHideMask")){
			return false;
		}
		if($('body').hasClass("noHideMask") || $('body').hasClass("loading")){
			return false;
		}
		if(e.which===27){ objModal.closeModal();} // Fechar ao apertar ESC
	});
	
	objModal = {
		closeModal: function(){
			$('#mask, .window').fadeOut("fast");
		},
		openModal: function(href,hidereturn){
			$('#mask, .window').hide();
			
			var $href = $(document.getElementById(href)),
				$win = $(window),
				winH = $win.height(),
				winW = $win.width(),
				scroll = $win.scrollTop ();
			
			$(document.getElementById('activeModal')).val(href);
			var maskHeight = $(document).height();
			var maskWidth = $(window).width();

			$(document.getElementById('mask')).css({'width':maskWidth,'height':maskHeight});
			$(document.getElementById('mask')).fadeTo("fast",0.5);	
	
			var top = winH/2-$href.height()/2 + scroll - 21;
			if(top < 0) top = 30;
			
			$href.css('top',  top);
			$href.css('left', winW/2-$href.width()/2);
			
			if(hidereturn) speed = 250;
			else speed = 500;
			
			$href.fadeIn(speed,function(){				
				if(hidereturn)
					objModal.closeModal();
			}); 
			return true;
		},
		refreshPosition: function(modal){
			var $win = $(window),
				winH = $win.height(),
				$modal = $(document.getElementById(modal)),
				scroll = $win.scrollTop(),
				top = winH/2-$modal.height()/2 + scroll - 21,
				maskHeight = $(document).height(),
				maskHeight2 = $(window).height(),
				maskWidth = $(window).width();
			if(top < 0) top = 30;	
			$modal.animate({top: top},600);	
			
			
			$(document.getElementById('mask')).css({'width':maskWidth,'height':maskHeight});
			/*
			if($modal.height() > maskHeight2)
				$(document.getElementById('mask')).css({'width':maskWidth,'height':maskHeight});
			else
				$(document.getElementById('mask')).css({'width':maskWidth,'height':maskHeight2});
			*/
		},
		getActive: function(){
			return $(document.getElementById('activeModal')).val();
		}
	}
	
	/* END MODAL */
	
	if(default_lang == 'pt_BR'){
        var format = '%d/%m/%Y';
    }
    else{
        var format = '%m/%d/%Y';
    }
	
	objDefault = {
		init: function(){
			for(var i in CKEDITOR.instances) {
				CKEDITOR.instances[i].destroy(true); 
			}
			objDefault.calendar();
			objDefault.mask();	
			objDefault.ckeditor();
		},
		zebrar: function(selector){
			$(selector).find('tbody tr').removeClass("odd");
			$(selector).find('tbody tr:visible').each(function(i){
		    	if(i%2) $(this).addClass("odd");
		    });
		},
		calendar: function(){
			$('body').find('.calendar').each(function(){
				$self = $(this);
				
				if($self.data('prev')) idprev = $self.data('prev');
				else idprev = $self.prev().attr('id');
				
				Calendar.setup({
	                inputField : idprev,
	                trigger : $self.attr('id'),
	                onSelect : function() {
	                    this.hide()
	                },
	                showTime : 12,
	                align : "B3/2",
	                dateFormat : format,
	                showTime : false,
	                fdow : 0,
	                bottomBar : false
	            });
				
			});
		},
		mask: function(){
			$('body').find('.mask').each(function(){
				$self = $(this);
				$self.mask($self.data('format'));
			});
		},
		buttonAction: function(self, action){
			var $body = $('body');
			
			if(action == "disabled"){
				$('body').addClass('loading');
				self.attr('disabled','disabled');
			}
			else if(action == "enabled"){
				$('body').removeClass('loading');
				self.removeAttr('disabled');
			}
			
		},
		ckeditor: function(){
			$('body').find('.ckeditor').each(function(){
				var $self = $(this),
					$id = $self.attr('id'),
					$height = $self.data('height'),
					$width = $self.data('width');
				
				if (CKEDITOR.instances[$id]) {
					return false;
				}				
				
				if($self.data('readonly')) readonly = true;
				else readonly = false;
				
				if($self.data('toolbar')) toolbar = $self.data('toolbar');
				else toolbar = 'SOLICITA';
				
                var editor = CKEDITOR.replace($id,
                {
                    toolbar : toolbar,
                    skin: 'kama',
                    width : $width,
                    height : $height,
                    toolbarCanCollapse : false,
                    resize_enabled : false,
    				readOnly: readonly
                });	
				
			});
		},
		notification: function(type, message, modal){
			var $modal = $(document.getElementById(modal)),
				$modalContent = $modal.find(".modalContent"),
				$modalFooter = $modal.find(".modalFooter");
			$modalFooter.slideUp(function(){
				$modalFooter.remove();	
			});
			$modalContent.slideUp(function(){
				$modalContent.empty();
				notification = "<div class='notification "+type+"'>";
				notification += "<p>"+message+"</p>";
				notification += "</div>";
				$modalContent.append(notification);
				$modalContent.slideDown();
				objModal.refreshPosition(modal);
			});
		},
		notificationNoRemove: function(type, message, modal){
			var $modal = $(document.getElementById(modal)),
				$modalContent = $modal.find(".modalContent"),
				$modalFooter = $modal.find(".modalFooter");

			$modalContent.slideUp(function(){
				$modalContent.empty();
				notification = "<div class='notification "+type+"'>";
				notification += "<p>"+message+"</p>";
				notification += "</div>";
				$modalContent.append(notification);
				$modalContent.slideDown();
				objModal.refreshPosition(modal);
			});
		},
		notificationReload: function(type, message, modal){
			var $modal = $(document.getElementById(modal)),
				$modalContent = $modal.find(".modalContent"),
				$modalHeader= $modal.find(".modalHeader"),
				$modalFooter = $modal.find(".modalFooter").find(".lst-btn");
				$modal.addClass("noHideMask");
				$modalHeader.find(".closeModal").remove();
				$modalFooter.empty();
				$modalFooter.append('<li class="last"><a href="operator" class="btnOrange tp1">Ok</a></li>');
				
			$modalContent.slideUp(function(){
				$modalContent.empty();
				notification = "<div class='notification "+type+"'>";
				notification += "<p>"+message+"</p>";
				notification += "</div>";
				$modalContent.append(notification);
				$modalContent.slideDown();
				objModal.refreshPosition(modal);
			});
		},
        notificationRedirect: function(type, message, modal,url){
            var $modal = $(document.getElementById(modal)),
                $modalContent = $modal.find(".modalContent"),
                $modalHeader= $modal.find(".modalHeader"),
                $modalFooter = $modal.find(".modalFooter").find(".lst-btn");
            $modal.addClass("noHideMask");
            $modalHeader.find(".closeModal").remove();
            $modalFooter.empty();
            $modalFooter.append('<li class="last"><a href="'+url+'" class="btnOrange tp1">Ok</a></li>');

            $modalContent.slideUp(function(){
                $modalContent.empty();
                notification = "<div class='notification "+type+"'>";
                notification += "<p>"+message+"</p>";
                notification += "</div>";
                $modalContent.append(notification);
                $modalContent.slideDown();
                objModal.refreshPosition(modal);
            });
        },
		maskLoaderShow: function(){
			var $mask = $(document.getElementById("mask")),
				$loader = $mask.find(".maskLoader"),
				$win = $(window),
				winH = $win.height(),
				winW = $win.width(),
				scroll = $win.scrollTop();
			$('body').addClass("noHideMask");
			var top = winH/2 - 21;
			if(top < 0) top = 30;
			
			var maskHeight = $(document).height();
			var maskWidth = $(window).width();
			$mask.css({'width':maskWidth,'height':maskHeight});
			
			$loader.show();
			$mask.fadeTo("fast",0.5);
			$loader.css('top',  top);
			$loader.css('left', winW/2-$mask.width()/2);
		},
		maskLoaderHide: function(){
			var $mask = $(document.getElementById("mask")),
				$loader = $mask.find(".maskLoader");
			$('body').removeClass("noHideMask");
			$loader.hide();
		},
		maskLoaderHideAll: function(){
			var $mask = $(document.getElementById("mask")),
				$loader = $mask.find(".maskLoader");
			$('body').removeClass("noHideMask");
			$mask.fadeOut("fast");
			$loader.hide();
		},
		loaderMenu: function(id, num){
			var $content = $(document.getElementById(id)),
				$height = $(window).height()-num+"px";
			$content.empty();			
			$content.append("<div class='loader'></div>");			
			$content.find(".loader").css("height",$height);
		}
	}
	
	
	$("body").delegate(".backModal", "click", function(e) {
		objModal.openModal(objModal.getActive());
	})
	
	var $content = $(document.getElementById('content'));
	objFont = {
		font: function(){
			return parseInt($(document.getElementById('boxImpressao')).find("table").css("font-size"));
		},
		less: function(btn){
			var min = 10,
				fSize = objFont.font(),
				$boxImpressao = $(document.getElementById('boxImpressao')).find("table");				
			if(fSize > min){
				fFim = fSize-2;
				$boxImpressao.css("font-size",fSize-2 + "px");
				if(fFim == min){
					$(btn).css("opacity","0.5");
					$content.find(".btn-font-more").css("opacity","1");
				}
				else{
					$(btn).css("opacity","1");
					$content.find(".btn-font-more").css("opacity","1");
					console.log($content.find(".btn-font-more"));
				}
			}
			 
		},
		more: function(btn){
			var max = 14,
				fSize = objFont.font(),
				$boxImpressao = $(document.getElementById('boxImpressao')).find("table");				
			if(fSize < max){
				fFim = fSize+2;
				$boxImpressao.css("font-size",fFim + "px");
				if(fFim == max){
					$(btn).css("opacity","0.5");
					$content.find(".btn-font-less").css("opacity","1");
				}
				else{
					$(btn).css("opacity","1");
					$content.find(".btn-font-less").css("opacity","1");
				}
			}	
		}
	}
	
	$content.find(".btn-font-less").live("click",function(){
		objFont.less(this);
	});
	$content.find(".btn-font-more").live("click",function(){
		objFont.more(this);
	})
	
	
	countdown = {
		start: function(seconds){
			if(typeof  this.time == "undefined"){
				this.time = seconds;
			}
			var tempo = seconds;
			
			if((tempo - 1) >= 0){
				var min = parseInt(tempo/60),
					hor = parseInt(min/60),
					min = min%60,
					seg = tempo%60;
		
				if(min < 10){
					min = "0"+min;
					min = min.substr(0, 2);
				}
				if(seg <=9){
					seg = "0"+seg;
				}
				if(hor <=9){
				hor = "0"+hor;
				}
		
				if(hor > 0)
					horaImprimivel = hor+'h ' + min + 'm ' + seg + 's';
				else if(min > 0)
					horaImprimivel = min + 'm ' + seg + 's';
				else
					horaImprimivel = seg + 's';
				$(document.getElementById("numberCountdown")).html(horaImprimivel);
				tempo--;
				timer = setTimeout(function(){
							countdown.start(tempo);
						},1000);
			} else {
				//window.open('../controllers/logout.php', '_self');
				 window.location = path+"/admin/login";
			}
		},
		stop: function(){
			clearTimeout(timer);
		},
		refresh: function(){
			countdown.stop();
			countdown.start(this.time);
		}
		
	}
	
});