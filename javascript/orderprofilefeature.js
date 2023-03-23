/* 
vendor/schrattenholz/orderprofilefeature/javascript/orderprofilefeature.js
*/
function loadDiscountScale(priceBlockElementID,productID){
	jQuery.ajax({
		url: pageLink+"getDiscountScale?priceBlockElementID="+priceBlockElementID+"&productID="+productID,
		success: function(data) {
		if(data=='nodiscount'){
			$('#discountScaleCard').addClass("d-none");
		}else{
			$('#discountScaleCard').removeClass("d-none");
		}
		/*
		JSON
			$returnValues->Status=false;
			$returnValues->Message="Das Passwort muss mindestens 8 Zeiechen haben!";
			$returnValues->Value='object';
		*/
					$('#discountScale').html(data);

		}
	});
}

function loadFilteredProductList(type,id,close){
		$(window).unbind('scroll',shopPageListener);
		$('#categoryID').val(id);
		$('#currentPage').val(0);
		loadShopPage("new");
		if(close=="1"){
			 $("#shop-sidebar").removeClass("show");
			 $("body").removeClass("offcanvas-open");
		}
		window.location.href=pageLink+"#top";
}
	function loadShopPage(type){
		console.log("loadShopPage="+type);
		jQuery.ajax({

			url: pageLink+"/loadShopPage?page="+($('#currentPage').val()*9)+"&categoryID="+$('#categoryID').val(),
			success: function(data) {
			
			/*
			JSON
				$returnValues->Status=false;
				$returnValues->Message="Das Passwort muss mindestens 8 Zeiechen haben!";
				$returnValues->Value='object';
			*/
					$('#currentPage').remove();
					$('#totalPages').remove();
					if(type=="add"){
						$('.product-list-items').append(data);
					}else if(type=="new"){
						$('.product-list-items').html(data);
					}
					console.log("currentPage="+$('#currentPage').val()+"totalPages="+$('#totalPages').val());
					if(parseInt($('#currentPage').val())<parseInt($('#totalPages').val())){
						console.log("call startShopPageListener");
						startShopPageListener();
					}else{
						$('#pageLoadIcon').addClass('d-none');
					}
			}
		});
	}
	function startShopPageListener(){
		$('#pageLoadIcon').removeClass('d-none').addClass('d-inlineblock');
		$(window).unbind('scroll').bind('scroll',shopPageListener);
	}
	var shopPageListener= function ShopPageListener(){
		var footer = document.getElementById("footer");
			if ($(window).scrollTop() >= footer.offsetTop - $(window).height()){
				console.log("laden "+(footer.offsetTop - $(window).height()) +"--neue inhalte laden-- scrollTop="+$(window).scrollTop());
				loadShopPage("add");
				$(window).unbind('scroll',shopPageListener);
			}
	}
var pageLink="$Link";
	jQuery( document ).ready(function() {
		
		if($('.masonry').length>0){
			$('.masonry').masonry({
			  itemSelector: '.masonry-item', // use a separate class for itemSelector, other than .col-
			  columnWidth: '.masonry-sizer',
			  percentPosition: true
			});
		}
		startUserActivityTimeout();
	if($('.product-list-filter').length>0){
		startShopPageListener();
	}
	if($('#OrderProfileFeature_RegistrationForm_useraccounttab_UserAccount').length>0){
		
		var useraccount=$('#OrderProfileFeature_RegistrationForm_useraccounttab_UserAccount');
		useraccount.on("change",function(){
			if(getCheckbox($(this).attr('id'))==1){
				$(this).attr('aria-required','true');
				$(this).attr('required','required');
				$('#OrderProfileFeature_RegistrationForm_useraccounttab_CustomerGroup_Holder').find('input').each(function(){
					$(this).attr('required','required');
				});
			}else{
				$(this).removeAttr('aria-required');
				$(this).removeAttr('required','required');
				$('#OrderProfileFeature_RegistrationForm_useraccounttab_CustomerGroup_Holder').find('input').each(function(){
					$(this).removeAttr('required');
				});
			}
		});
		if(getCheckbox(useraccount.attr('id'))==1){
			useraccount.attr('aria-required','true');
			useraccount.attr('required','required');
			$('#OrderProfileFeature_RegistrationForm_useraccounttab_CustomerGroup_Holder').find('input').each(function(){
				$(this).attr('required','required');
			});
		}else{
			useraccount.removeAttr('aria-required');
			useraccount.removeAttr('required','required');
			$('#OrderProfileFeature_RegistrationForm_useraccounttab_CustomerGroup_Holder').find('input').each(function(){
				$(this).removeAttr('required');
			});
		}
	}
	if($('.product-list-filter').length>0){
		$('.product-list-filter').find('.ajaxLink').each(function(){
			$(this).on('click',function(e){
				e.preventDefault();
				loadFilteredProductList($(this).attr('data-type'),$(this).attr('data-id'),$(this).attr('data-closemenu'));
			});
		});
	}
	if(jQuery('#search-field').length>0){
		jQuery('#search-field').on('focusout',function(){
			jQuery('#search-field-box .dropdown-menu').removeClass('show');
			if(jQuery(this).val()==""){
				$('#search-field-list li').remove();
			}
		});
		jQuery('#search-field').on('focusin',function(){
			if(	jQuery('#search-field-list li').length>0){
				jQuery('#search-field-box .dropdown-menu').addClass('show');
			}
		});
		$( "#search-field" ).autocomplete({
		  source: function( request, response ) {
			$.ajax( {
			  url: "$Link/searchProducts",
			  dataType: "json",
			  data: {
				searchTerm: request.term
			  },
			  success: function(responseData) {
					$('#search-field-list li').remove();
					response(responseData);
					
					
					
					if(responseData.length>0){
						jQuery('#search-field-box .dropdown-menu').addClass('show');
					}else{
						jQuery('#search-field-box .dropdown-menu').removeClass('show');
						$('#search-field-list li').remove();
					}
			  }
			} );
		  },
		  minLength: 2,
		  select: function( event, ui ) {
			log( "Selected: " + ui.item.ID + " aka " + ui.item.Title );
		  }
		}).data('ui-autocomplete')._renderItem = function(ul, item) {

			
			
			
			
			
			var listItem = $('<li></li>')
			.data('ui-autocomplete-item', item);
			$('#search-field-list').removeAttr('style');
			return $('<li data-value="'+item.ID +'">')
			.append()
			
			
			
			
			.append('<div class="media align-items-center"><a class="d-block mx-2" href="'+item.Link+'"><img src="'+item.CoverImage+'" alt="Product" width="64"></a><div class="media-body"><h6 class="widget-product-title"><a href="'+item.Link+'">' + item.Title +'</a></h6></div></div></div>')
			.appendTo($('#search-field-list'));
			
			return listItem.appendTo($('#search-field-list'));
		};
	}
	jQuery('#profile_orders').find('.saveOrderAsModelButton').each(function(){
		jQuery(this).on('click',function(){
			$('#ClientOrderID').val($(this).attr('data-clientorderid'));
			$('#OrderName').val("Vorlage "+$(this).attr('data-clientorderid'));
			
			$('#orderModal').modal("toggle");
		});
	});

	jQuery('#orderModal').find('.saveModelBtn').each(function(){
		jQuery(this).on('click',function(){
			var orderModel={
				id:$('#orderModal #ClientOrderID').val(),
				title:$('#orderModal #OrderName').val()
			}
			jQuery.ajax({
				url: "$Link/saveOrderModel?orderModel="+JSON.stringify(orderModel),
				success: function(data) {
						var response=JSON.parse(data);
						var status=response.Status;
						var message=response.Message;
						var value=response.Value;
					/*
						dataAr[0] = 0 -> error
						dataAr[0] = 1 -> ok
						dataAr[1] = error-code/product-number
						dataAr[2]=new productsquantitiy
					 */
					window.loction
				}
			});
		});
	});
});
var timeOutDelay=600000;
function startUserActivityTimeout(){
	
	setTimeout(function(){
		console.log("timeout checkUserActivity $TotalProducts");
		if ($('#ProductInBasket').val()>0){
			var timeoutID = setTimeout(checkUserActivity,timeOutDelay);
		}
	},
	1000);
}
function removeProductFromBasketByID(id){
		jQuery.ajax({
			url: "$Link/removeProductFromBasketByID?id="+id,
			success: function(data) {
					var response=JSON.parse(data);
					var status=response.Status;
					var message=response.Message;
					var value=response.Value;
				/*
					dataAr[0] = 0 -> error
					dataAr[0] = 1 -> ok
					dataAr[1] = error-code/product-number
					dataAr[2]=new productsquantitiy
				 */
				if(parseInt(value)>0){
					$('#warenkorb_icon .basket-count').html(value);
				}else{
				
					$('#warenkorb_icon .basket-count').html(0);
				}
				loadBasketNavList();
				$('#tr_product_'+id).remove();
			}
		});	
	}
function checkUserActivity(){
	console.log("checkUserActivity");
	jQuery.ajax({
		url: "$Link/getListCount",
		success: function(data) {
		if(parseInt(data)>0){
				//startActivitiyModal
				var message="Du bist seit 10 Minuten inaktiv. Um Deine Bestellung fortzusetzen, klicke bitte auf \"Weiter bestellen\". Anderenfalls wird Deine Warenkorb geleert.";
				var title="Einkauf fortsetzen?";
				$('#dialogBox .modal-title').html(title);
				$('#dialogBox .modal-body').html(message);
				countdown(1);
				
				$('#dialogBox').modal()
				$('#clearBasket').removeClass("d-none").on('click',function(){
					clearBasket();
				});
				$('#keepBasket').removeClass("d-none").on('click',function(){
					keepBasket();
				});
				
				//$('#dialogBox .alert').addClass('alert-danger').css('display','block').fadeTo(100,1);//.delay(2000).fadeTo(100,0,function(){$(this).removeClass('alert-danger');$(this).css('display','none');});
			}

		}
	});
}
function loadBasketNavList(){
	jQuery.ajax({
		url: "$Link/getBasketNavList",
		success: function(data) {
			$('.basket-nav-list').html(data);
		}
	});
	jQuery.ajax({
		url: "$Link/getHandheldToolbar",
		success: function(data) {
			$('body').remove('.cz-handheld-toolbar');
			$('body').append(data);
		}
	});
	
}
function loadProductBadge(id){
	jQuery.post({
		url: "$Link/getProductBadge?id=$ID&v="+jQuery(id+" .variant01").val()+"&vac="+getVac(),
		success: function(data) {
			$('.productbadge').html(data);
		}
	});
}
function clearBasket(){
	jQuery.ajax({
		url: "$Link/deleteInactiveBasket",
		success: function(data) {
			var message="Ihr Warenkorb wurde zurückgesetzt und die Produkte für andere Kunden wieder frei zu gegeben, weil Sie 10 Minuten inaktiv waren und wir davon ausgehen, dass Sie Ihren Einkauf nicht mehr abschliessen möchten.";
			var title="Warenkorb wurde geleert";
			$('#clearBasket').addClass("d-none").attr("disabled","disabled");
			$('#keepBasket').addClass("d-none").attr("disabled","disabled");
			$('#dialogBox .modal-title').html(title);
			$('#dialogBox .modal-body').html(message);
			$('#ProductInBasket').val(0);
			loadBasketNavList();
			//window.location.reload(false);
		}
	});
}
function keepBasket(){
	jQuery.ajax({
		url: "$Link/keepBasket",
		success: function(data) {
			window.location.reload(false);
		}
	});
}

function countdown(minutes) {
    var seconds = 60;
    var mins = minutes
    function tick() {
        //This script expects an element with an ID = "counter". You can change that to what ever you want.
        var counter = document.getElementById("clearBasket");
        var current_minutes = mins-1
        seconds--;
        counter.innerHTML = "Warenkorb zurücksetzten (" + current_minutes.toString() + ":" + (seconds < 10 ? "0" : "") + String(seconds)+")";
        if( seconds > 0 ) {
            setTimeout(tick, 1000);
        } else {
            if(mins > 1){
                countdown(mins-1);
            }else{
				clearBasket();
			}
        }
    }
    tick();
}
function getCheckbox(id){
	if($('#'+id).prop('checked')) {
		return 1;
	}  else {
		return 0;
	}
}
function getReadableCheckbox(id){
	if($('#'+id).length>0){
		if($('#'+id).prop('checked')) {
			return "on";
		}  else {
			return "off";
		}
	}else{
		return "notinuse";
	}
}
//You can use this script with a call to onclick, onblur or any other attribute you would like to use.
