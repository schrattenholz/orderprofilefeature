	<!-- Page Title (Shop)-->
    <div class="page-title-overlap bg-dark pt-4">
      <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <% include BreadCrumbs Design="-light"%>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 text-light mb-0">$MenuTitle.XML</h1>
        </div>
      </div>
    </div>
	
<div class="container pb-5 mb-2 mb-md-4 ">
      <div class="row">
	$Aside
	<!-- Content  -->
        <section class="col-lg-6  ">
          <!-- Toolbar -->
          <div class="d-flex justify-content-center justify-content-sm-between align-items-center pt-2 pb-4 pb-sm-5">
           
		  </div>
		 <!-- -->
		<!-- Products grid-->
		<div class="row mx-n2 product-list-items" >
			<% include Schrattenholz\OrderProfileFeature\Includes\ProductPreSaleList_ProductList %>
		</div>
	
        </section>
      </div>
    </div>
	<div aria-live="polite" aria-atomic="true" class="d-flex justify-content-center align-items-center">
    <!-- Toast: Added to Cart-->
    <div class="toast-container toast-bottom-center">
      <div class="toast mb-3" id="cart-toast" data-delay="5000" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white"><i class="czi-check-circle mr-2"></i>
          <h6 class="font-size-sm text-white mb-0 mr-auto">Added to cart!</h6>
          <button class="close text-white ml-2 mb-1" type="button" data-dismiss="toast" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="toast-body">This item has been added to your cart.</div>
      </div>
    </div>
	</div>
	
	
	
	
	

	
	
	
	<script>
	 setInterval( refreshStock, 5000);
	jQuery( document ).ready(function() {
		jQuery('#percentageSlider').css('height',calculatePrecentage()+'%');
		
		
	});
	function refreshStock(){
	//console.log("refres");
			jQuery('.product').each(function(){
			jQuery.ajax({
			url: "{$Link}/FreeQuantityAjax?orderedProduct="+JSON.stringify(getOrderedProduct(jQuery(this).attr("data-productID"),jQuery(this).attr("data-variantID"))),
				success: function(data) {
					dataAr=data.split("|");
					var response=JSON.parse(data);
					var status=response.Status;
					var message=response.Message;
					var quantityLeft=response.QuantityLeft;
					var productID=response.ProductDetails.ProductID;
					var variantID=response.ProductDetails.ID;
					
					$('#pbE_'+variantID).attr('data-presalecurrentinventory',quantityLeft);
					var startInventory=$('#pbE_'+variantID).attr('data-presalestartinventory');
					$('#pbE_'+variantID+' .progress-bar').css('width',100-(quantityLeft/startInventory*100)+'%');
					$('#pbE_'+variantID+' .progress-bar').attr("aria-valuenow",100-(quantityLeft/startInventory*100));
					jQuery('#percentageSlider').css('height',calculatePrecentage()+'%');
					
					/*
					JSON
						$returnValues->Status=false;
						$returnValues->Message="Das Passwort muss mindestens 8 Zeichen haben!";
						$returnValues->Value='object';
					*/	
					
				}
			});
		});	
	}
	function calculatePrecentage(){
		var pVs=0
		var count=0;
		jQuery('.card .progress-bar').each(function(){
		
			count++;
			pVs=pVs+parseInt(jQuery(this).attr("aria-valuenow"));
			//console.log(pVs+"   "+jQuery(this).attr("aria-valuenow"))
		});	
		var pV=pVs/count;
		
		return pV;
	}
		function refreshProductPrice(){
		var ref=jQuery('.variants option[value=' + jQuery('.variants').val() + ']');
		var price=ref.attr('data-price');
		var productID=ref.attr('data-productid');
		var caPrice=ref.attr('data-caprice');
		var str='ab ';
			if(caPrice=="1"){
				str=str+'ca. ';
			}
		str=str+price+' €';
		$('#p'+productID).find('.product-price').html('<span class="text-accent">'+str+'</span>');
	}
	function getProductOptions(id){
	var options=[]
	var c=0;
	if($('#p'+id+" .variants").length>0){
		var rootSelector="#product-options_"+$('#p'+id+" .variants").val();
	}else{
		var rootSelector=".product-options";
	}
	
	//console.log("getProductOptions "+rootSelector);
	$("#p"+id+' '+rootSelector).find("input").each(function(){
	console.log("product-options "+$(this).attr("data-id"))
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
function getOrderedProduct(id,variantID){

	var orderedProductObj={
		id:id,
		title:$("#pbE_"+variantID).attr('data-title'),
		productoptions:getProductOptions(id),
		variant01:variantID,
		quantity:1
	}
	return orderedProductObj;
}
	function addToList(productID,variantID,action){
	console.log("addToList");

		jQuery.ajax({
		url: "{$Link}/addToList?orderedProduct="+JSON.stringify(getOrderedProduct(productID,variantID))+"&action=list",
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
					
					$('#cart-toast').toast('show')
				if(status=="good"){
					loadBasketNavList();
					// Warenkorb leeren Dialog anzeigen wenn Produkte im Warenkorb sind
					if(value>0){
						var timeoutID = setTimeout(checkUserActivity,timeOutDelay);
					}
					$('#warenkorb_icon  .basket-count').html(value);
					//console.log("id="+id+" wurde dem Warenkorb hinzugefügt");
					//$('#editFunction').css("display","flex");
					//$('#addFunction').css("display","none");
					
					$('.messageBox .alert').html(message);
					$('#cart-toast .toast-header h6').html("Alles klar");
					$('#cart-toast .toast-header').removeClass().addClass('bg-success toast-header text-white');

				}else if(status=="info"){
					$('.messageBox .alert').html(message);
					$('.messageBox .alert').addClass('warning-primary').css('display','block').fadeTo(100,1).delay(2000).fadeTo(100,0,function(){$(this).removeClass('alert-primary');$(this).css('display','none');});
					$('#cart-toast .toast-header h6').html("Achtung");
					$('#cart-toast .toast-header').removeClass().addClass('bg-warning toast-header text-white');
				}else{
					$('.messageBox .alert').html(message);
					$('.messageBox .alert').addClass('alert-danger').css('display','block').fadeTo(100,1).delay(2000).fadeTo(100,0,function(){$(this).removeClass('alert-danger');$(this).css('display','none');});
					$('#cart-toast .toast-header h6').html("Fehler");
					$('#cart-toast .toast-header').removeClass().addClass('bg-danger toast-header text-white');
					
					var status="alert-danger";
				}
				refreshStock();
				$('#cart-toast .toast-body').html(message);
				
			}
		});
	}
	</script>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
