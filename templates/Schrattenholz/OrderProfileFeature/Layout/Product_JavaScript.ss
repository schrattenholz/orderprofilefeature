<script>
function getProductOptions(){
	var options=[]
	var c=0;
	if($('#variant01').length>0){
		var rootSelector="#product-options_"+jQuery("#variant01").val();
	}else{
		var rootSelector="#product-options";
	}
	//console.log("getProductOptions "+rootSelector);
	$(rootSelector).find("input").each(function(){
	//console.log("product-options "+$(this).attr("data-id"))
		var option=[];
		option["id"]=$(this).attr("data-id");
		option["value"]=getCheckbox($(this).attr("id"));
		options.push({
			"id":parseInt(option["id"]),
			"value":parseInt(option["value"])
		});
		c++;
	});
	return options;
}
function getOrderedProduct(){
			var quantity=jQuery("#amount").val().replace(",",".");
			var regex="/[a-z]|[A-Z]|\s/g";
			if(quantity.search("kg")>-1){
				multi=1000;
			}else{
				multi=1;
			}
			quantity = parseFloat(quantity.replace (regex,""))*multi;
	var orderedProductObj={
		id:'$ID',
		title:"$Title",
		productoptions:getProductOptions(),
		variant01:jQuery("#variant01").val(),
		vac:getVac(),
		quantity:quantity
	}
	return orderedProductObj;
}
function addToList(id,action){
	console.log("addToList");
	orderedProduct=getOrderedProduct()
	if(orderedProduct.quantity>0){
		$('#ProductInBasket').val(1);
		startUserActivityTimeout();
	}
		jQuery.ajax({
		url: "{$Link}/addToList?orderedProduct="+JSON.stringify(orderedProduct)+"&action="+action,
			success: function(data) {
				dataAr=data.split("|");
					var response=JSON.parse(data);
					var status=response.Status;
					var message=response.Message;
					var value=response.Value;
					/*
					JSON
						$returnValues->Status=false;
						$returnValues->Message="Das Passwort muss mindestens 8 Zeiechen haben!";
						$returnValues->Value='object';
					*/
				if(status=="good"){
					// Warenkorb leeren Dialog anzeigen wenn Produkte im Warenkorb sind
					if(value>0){
						var timeoutID = setTimeout(checkUserActivity,timeOutDelay);
					}
					$('#warenkorb_icon  .basket-count').html(value);
					$('#editFunction').css("display","flex");
					$('#addFunction').css("display","none");
					$('.messageBox .alert').html(message);
					$('.messageBox .alert').addClass('alert-success').css('display','block').fadeTo(100,1).delay(2000).fadeTo(100,0,function(){$(this).removeClass('alert-success');$(this).css('display','none');});
					
				}else if(status=="info"){
					$('.messageBox .alert').html(message);
					$('.messageBox .alert').addClass('warning-primary').css('display','block').fadeTo(100,1).delay(2000).fadeTo(100,0,function(){$(this).removeClass('alert-primary');$(this).css('display','none');});
				}else{
					$('.messageBox .alert').html(message);
					$('.messageBox .alert').addClass('alert-danger').css('display','block').fadeTo(100,1).delay(2000).fadeTo(100,0,function(){$(this).removeClass('alert-danger');$(this).css('display','none');});
				}
				refreshSelectedProduct('refreshSameProduct');
			}
		});
	}

    
function refreshSelectedProduct(action){
		if($('#variant01').length>0){
		var selectedVariant=jQuery('#variant01 option[value=' + jQuery('#variant01').val() + ']');
			if(selectedVariant.attr('data-caprice')=="1"){
				var price=	"ca. "+selectedVariant.attr('data-price')+" &euro;"
			}else{
				var price=selectedVariant.attr('data-price')+" &euro;"
			}
			jQuery('#single_price').html(price);
		}else{
		
		}
		if($('#vac').prop('checked')) {
			var vac="1";
		}  else {
			var vac="0";
		}
		if(action=="variantChange"){
					jQuery('#amount').attr('data-portionable',selectedVariant.attr('data-portionable'));

					//jQuery('#amount').val(getAmountWithUnit(0));
					jQuery('.product-options').each(function(){
					console.log("vC tk pC="+jQuery('#variant01').val());
						$(this).removeClass('d-block').addClass('d-none');
						if("product-options_"+jQuery('#variant01').val()==$(this).attr('id')){
							$(this).removeClass('d-none').addClass('d-block');
						}
					});
					jQuery('.product-option').each(function(){
						$(this).prop( "checked", false );
					});
				}
		
		loadBasketNavList();
		jQuery.ajax({
			url:"{$Link}/FreeQuantityAjax?orderedProduct="+JSON.stringify(getOrderedProduct()),
			success: function(data){
			data=JSON.parse(data);
				/*
					data.ClientsPossibleQuantity = amount
					data.QuantityLeft
					data.ClientsQuantity
					data.ClientsQuantityVac
					data.ClientQuantities
				*/
				//alert("action="+action)
				console.log("data="+JSON.stringify(data.ClientQuantities));
				var selectedVariant=jQuery('#variant01 option[value=' + jQuery('#variant01').val() + ']');
				var quantities=checkProductOptions(data);
				var quantity=quantities.ClientsQuantity;
				var possibleQuantity=data.QuantityLeft+quantity;

				console.log("ClientsQuantity ="+quantity+" action="+action+" --------");
				if(quantity>0){
				

				}else if(action=="attributeChange"){
				
				<% if not $InfiniteInventory %>
					if(getAmountWithoutUnit()>possibleQuantity){
						//console.log("anzahl soll uebernommen werden");
						jQuery('#amount').val(getAmountWithUnit(possibleQuantity));
					}
				<% else %>


				<% end_if %>
					
				}else if(action=="variantChange"){
				console.log("variantChange portionable");
					
				}
				jQuery('#amount').val(getAmountWithUnit(quantity));
					if(data.ProductDetails['Portionable']==1){
					
						if(data.ProductDetails['PortionMax']>0){
							jQuery('#amount').attr('max',data.ProductDetails['PortionMax']);
						}else{
							jQuery('#amount').attr('max',possibleQuantity);
						}
						jQuery('#amount').attr('min',data.ProductDetails['PortionMin']);
						jQuery('#amount').attr('step',data.ProductDetails['Portion']);
					}else{
						jQuery('#amount').attr('max',possibleQuantity);
						jQuery('#amount').attr('min',0);
						jQuery('#amount').attr('step',1);
					}
				if(quantity>0){
				jQuery('#currentlyInBasket').removeClass('d-none').addClass('d-block');
				}else{
				jQuery('#currentlyInBasket').addClass('d-none').removeClass('d-block');
				}
				jQuery('#Quantity').html(data.QuantityLeft);
				
				calculatePrice(getAmountWithoutUnit());
				showHideEditFunction(quantity);
				refreshProductBadge();
				loadShippingOptions(jQuery('#variant01').val());
			}
		});
}
function getAmountWithUnit(amount){
	if(jQuery('#amount').attr('data-portionable')=="1"){
		if(amount>=1000){
			amount=(amount/1000).toFixed(2)+"kg";
		}else{
			amount=amount+"g";
		}
		return amount;
	}else{
		return amount;
	}
}
function getAmountWithoutUnit(){
	if(jQuery('#amount').attr('data-portionable')=="1"){
		var quantity=jQuery("#amount").val().replace(",",".");
		var regex="/[a-z]|[A-Z]|\s/g";
		if(quantity.search("kg")>-1){
			multi=1000;
		}else{
			multi=1;
		}
		return quantity = parseFloat(quantity.replace (regex,''))*multi;
	}else{
		return jQuery("#amount").val();
	}
	 
}
function checkProductOptions(data){

	var productOptions=getProductOptions();
	var pOString="";
	console.log("pos"+productOptions.length);
	for (var i = 0; i < data.ClientQuantities.length; i++) {
		var hit=true;
		for(var j=0;j<data.ClientQuantities[i]["ProductOptions"].length;j++){
			
			for (var k=0;k<productOptions.length;k++){
				if(data.ClientQuantities[i]["ProductOptions"][j]["ID"]==productOptions[k]["id"]){
					if(data.ClientQuantities[i]["ProductOptions"][j]["Active"]!=productOptions[k]["value"]){
						hit=false;
						console.log(data.ClientQuantities[i]["ProductContainerID"]+" falsch="+data.ClientQuantities[i]["ProductOptions"][j]["Active"]+"="+productOptions[k]["value"]);
					}
				}
			}
		}
		if (hit){
			console.log("produkt gefunden"+data.ClientQuantities[i]["ProductContainerID"]+" anz="+data.ClientQuantities[i]["Quantity"]);
			return {"ClientsQuantity":data.ClientQuantities[i]["Quantity"]};
			break;
		}
			
		
	}
	return {"ClientsQuantity":0};
}
function refreshProductBadge(){
	if($('.productbadge').length>0){
		loadProductBadge();
	}
}

function showHideEditFunction(quantity){
	console.log("showHideEditFunction "+quantity);
	if(quantity>0){
		$('#editFunction').css('display','flex');
		$('#addFunction').css('display','none');
	}else{
		$('#editFunction').css('display','none');
		$('#addFunction').css('display','flex');
	}
}
function calculatePrice(quantity){
	if(jQuery('#variant01').length>0){
		var price=jQuery('#variant01  :selected').attr('data-price')*quantity;
		if(getVac()=="on"){
			console.log("vac"+(parseInt(quantity)*parseFloat(jQuery('#vac').attr('data-price'))));
			price=price+(parseInt(quantity)*parseFloat(jQuery('#vac').attr('data-price')));
		}
	}else{
		var price=jQuery('#variant01  :selected').attr('data-price')*quantity;
	}
	console.log("price="+price);
	if(parseInt(quantity)>0){
		jQuery('#price').html('ca. '+price.toFixed(2).replace(".",",")+' &euro;*');
	}else{
		jQuery('#price').html('&nbsp;');
	}
}


function getVac(){
	if($('#vac').length>0){
		if($('#vac').prop('checked')) {
			return "on";
		}  else {
			return "off";
		}
	}else{
		return "notinuse";
	}
}
function getVariant01(){
	if($('#variant01').length>0){
		return jQuery("#variant01").val();
	}else{
		return "notinuse";
	}
}

	function loadPage(){
	
	window.location.href=jQuery("#products").val();
}
	function removeProductFromBasket(id){
		jQuery('#amount').val(0);
		addToList(id,"remove");
	}
	function getListCount(){
		jQuery.ajax({
			url: "{$Link}/getListCount",
			success: function(data) {
			if(parseInt(data)>0){
				$('#warenkorb_icon .basket-count').html(data);
				}
			}
		});
	}
	function resetBasket(){
		jQuery.ajax({
			url: "{$Link}/ClearBasket",
			success: function(data) {
				$('#warenkorb_icon .basket-count').html('0');
			}
		});
	}

</script>