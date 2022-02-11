 <style>
.refresh{
	float: left;
	text-align: center;
	display: block;
	margin: 0;
	padding: 10px;
	font-size: 14px;
	line-height: 1.2857142857;
	/* width: 100px; */
	height: 48px;
	color: #b4bf5e;
}
 </style>
 
 <% include PageTitleOverlap %>
  <div class="container pb-5 mb-2 mb-md-4">
      <div class="row">
        <!-- List of items-->
        <section class="col-lg-8">
          <div class="d-flex justify-content-between align-items-center pt-3 pb-2 pb-sm-5 mt-1">
            <h2 class="h6 text-light mb-0">Deine Produkte</h2><a class="btn btn-outline-primary btn-sm pl-2" href="$OrderConfig.ProductRoot.Link"><i class="czi-arrow-left mr-2"></i>Weiter einkaufen</a>
          </div>
		  <% if $Basket.ProductContainers %>
		   <% loop $Basket.ProductContainers.Sort('ProductSort') %>
	 
          <!-- Item-->
          <div id="pc_$ID" data-productID="$ProductID" data-title="$Product.Title" data-variant01="$PriceBlockElementID" class="row d-sm-flex justify-content-between align-items-center my-4 pb-3 border-bottom">
            <div class="col-8 media media-ie-fix d-block d-sm-flex align-items-center text-center text-sm-left w-100"><a class="d-inline-block mx-auto mr-sm-4" href="$Product.Link?id=$ID&v=$PriceBlockElement.ID&vac=$Vacuum" style="width: 10rem;"><img src="$Product.CoverImage.Fill(200,200).URL" alt="Zum Produkt wechseln"></a>
              <div class="media-body pt-2">
                <h3 class="product-title font-size-base mb-2"><a href="$Product.Link?id=$ID&v=$PriceBlockElement.ID&vac=$Vacuum">$Product.SummaryTitle</a></h3>
                <% if $PriceBlockElement %>
					<div class="font-size-sm">
					<span class="text-muted mr-2"><% loop $PriceBlockElement %>$FullTitle<% end_loop %></span>
					</div>
					
				<% end_if %>
				<% if $ProductOptions.ProductOptions_ProductContainer.Filter('Active',1).Count>0 %>
					<dl class="row mb-0 font-size-xs">
					<% loop $ProductOptions %>
						<% if  $ProductOptions_ProductContainer.Active  %>
						<dt class="col-sm-9 mb-0">$Title</dt>
							  <dd class="col-sm-3 mb-0 font-size-xs"> $Top.formattedNumber($ProductOptions_ProductContainer.Price) &euro;</dd>
						<% end_if %>
					<% end_loop %>
					</dl>
				<% end_if %>
                <div class="font-size-lg text-accent pt-2 productTotalPrice">	<% if $CompletePrice.CaPrice %>ca. <% end_if %>$Top.formattedNumber($CompletePrice.Price) &euro;</div>
              </div>
            </div>
            <div class="col-2 pt-2 pt-sm-0 mx-auto mx-sm-0 text-center text-sm-left" style="max-width: 9rem;">
              <div class="form-group mb-0">
                 <label class="font-weight-medium" for="quantity1"><% if $PriceBlockElement.Portionable %>Menge in Gramm<% else %>Anzahl in Stück<% end_if %></label>
						<div class="quantity position-relative clearfix d-inline-block align-top">
						
							<input id="pc_{$ID}_amount"
							class="amount" 
							readonly="readonly"
							type="text" 
							data-pcID="$ID"
							data-originalamount="<% if $Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).Quantity>0 %><% if $Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).ProductDetails.Portionable %>$Top.formattedWeight($Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).Quantity)<% else %>$Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).Quantity<% end_if %><% else %><% if $Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).ProductDetails.Portionable %>{$Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).ProductDetails.PortionMin}g<% else %>0<% end_if %><% end_if %>"
							data-portionable="$Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).ProductDetails.Portionable"
							min="<% if $Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).ProductDetails.Portionable %>$Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).ProductDetails.PortionMin<% else %>0<% end_if %>" 
							max="<% if  $Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).ProductDetails.Portionable %> $Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).ProductDetails.PortionMax<% else %><% if  $Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).ProductDetails.InfiniteInventory %>99999999<% else %> $Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).ClientsQuantityMax<% end_if %><% end_if %>" 
							step="<% if $Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).ProductDetails.Portionable %>$Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).ProductDetails.Portion<% else %>1<% end_if %>"
							onchange="calculatePrice(this.value)" 
							value="<% if $Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).Quantity>0 %><% if $Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).ProductDetails.Portionable %>$Top.formattedWeight($Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).Quantity)<% else %>$Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).Quantity<% end_if %><% else %><% if $Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).ProductDetails.Portionable %>{$Top.loadSelectedParametersFromTemplate($ProductID,$PriceBlockElementID).ProductDetails.PortionMin}g<% else %>0<% end_if %><% end_if %>">
							<script>
							jQuery("#pc_{$ID}_amount").on("change",function(){
							
								var refreshBtn=jQuery("#pc_"+jQuery(this).attr("data-pcID")+" .refresh");
								if(jQuery(this).val()!=jQuery(this).attr("data-originalamount")){
									refreshBtn.removeClass("invisible").addClass("visible");
								}else{
									refreshBtn.removeClass("visible").addClass("invisible");
								}
							});
							</script>
						</div>
					</div>

            </div>
			 <div class="col-8 pt-2 pt-sm-0  mx-auto mx-sm-0 text-center text-sm-left" style="max-width: 9rem;">
              <div class="form-group mb-0">
			  <label class="font-weight-medium" for="">&nbsp;</label>
			<a class="btn btn-link px-0 text-danger refresh mt-4 invisible" href="javascript:addToList('$ID');" title="Produkt aktualisieren"><i class="h2 text-body czi-loading"></i></a>
			<div class="spinner-border text-primary d-none" role="status">
			  <span class="sr-only">Loading...</span>
			</div>
          </div> </div> </div>
           <% end_loop %>
		   <% else %>
		   <h2>Leere im Warenkorb</h2>
		   <p>
  Es befinden sich keine Produkte in deinem Warenkorb.
</p><!-- Success button -->
<a href="$OrderConfig.ProductRoot.Link" class="btn btn-success">Zur Produktübersicht</a>

          <% end_if %>
		  

                 
        </section>
        <!-- Sidebar-->
        <aside class="col-lg-4 pt-4 pt-lg-0">
		<% if $Basket.ProductContainers %>
          <div class="cz-sidebar-static rounded-lg box-shadow-lg ml-lg-auto">
            <div class="text-center mb-4 pb-3 border-bottom">
              <h2 class="h6 mb-3 pb-1">Gesamt</h2>
              <h3 class="font-weight-normal basketTotalPrice"><% if $Basket.TotalPrice.CaPrice %>ca. <% end_if %>$Top.formattedNumber($Basket.TotalPrice.Price) &euro;</h3>
			  <% if $Top.CurrentOrderCustomerGroup.VatExluded %>
			  zzgl. 
			  <% else %>
			  inkl. 
			  <% end_if %>
			  MwSt.({$Top.CurrentOrderCustomerGroup.Vat}%):</span>
			  <span class="text-right basketTotalVat">
				<% if $Basket.TotalPrice.CaPrice %>ca. <% end_if %>$Top.formattedNumber($Basket.TotalPrice.Vat) &euro;
			  </span>
            </div>
			<a class="btn btn-primary btn-shadow btn-block mt-4" href="$LinkCheckoutAddress"><i class="czi-card font-size-lg mr-2"></i>Zur Kasse</a>
          </div>
		  <% end_if %>
        </aside>
      </div>
    </div>    

  
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
function getOrderedProduct(pcID){

			var p=jQuery("#pc_"+pcID);
			var quantity=jQuery("#pc_"+pcID+ " .amount").val().replace(",",".");
			var productID=p.attr("data-productID");
			var regex="/[a-z]|[A-Z]|\s/g";
			if(quantity.search("kg")>-1){
				multi=1000;
			}else{
				multi=1;
			}
			quantity = parseFloat(quantity.replace (regex,""))*multi;
	var orderedProductObj={
		id:productID,
		title:p.attr("data-title"),
		productoptions:getProductOptions(),
		variant01:p.attr("data-variant01"),
		vac:"",
		quantity:quantity
	}
	return orderedProductObj;
}
function addToList(pcID,action){
	console.log("addToList");
	jQuery("#pc_"+pcID+" .refresh").removeClass("visible").addClass("invisible").addClass("d-none");
	jQuery("#pc_"+pcID+" .spinner-border").removeClass("d-none").addClass("d-block");
		jQuery.ajax({
		url: "{$Link}/addToList?orderedProduct="+JSON.stringify(getOrderedProduct(pcID))+"&action="+action,
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
					jQuery('.basketTotalPrice').html(value.TotalPrice);
					jQuery('.basketTotalVat').html(value.TotalVat);
					jQuery('#pc_'+pcID+' .productTotalPrice').html(value.ProductPrice);
					jQuery("#pc_"+pcID+" .spinner-border").removeClass("d-block").addClass("d-none");
					jQuery("#pc_"+pcID+" .refresh").removeClass("d-none");
					var amount=jQuery("#pc_"+pcID+" .amount");
					amount.attr('data-originalamount',amount.val());
					// Warenkorb leeren Dialog anzeigen wenn Produkte im Warenkorb sind
					if(value>0){
						var timeoutID = setTimeout(checkUserActivity,timeOutDelay);
					}
					loadBasketNavList();
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
				//refreshSelectedProduct('refreshSameProduct');
			}
		});
	}

function removeProductFromBasket(productContainerID){
	alert("removeProductFromBasket");
		jQuery.ajax({
			url: "{$Link}/removeProductFromBasket?id="+productContainerID,
			success: function(data) {
				if(parseInt(data)>0){
					$('#warenkorb_icon').html(data+' <i class="fas fa-shopping-bag">');
				}else{
					$('#warenkorb_icon').html('0 <i class="fas fa-shopping-bag">');
				}
				$('#'+anchor).remove();
			}
		});	
	}
	function getListCount(){
		jQuery.ajax({
			url: "{$Link}/getListCount",
			success: function(data) {
			if(parseInt(data)>0){
				$('#warenkorb_icon').html(data+'<span>Warenkorb</span>');
				}
			}
		});
	}
	
  </script>