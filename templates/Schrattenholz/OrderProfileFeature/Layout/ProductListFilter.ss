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
	
<div class="container pb-5 mb-2 mb-md-4 product-list-filter">
      <div class="row">
	$Aside
	<!-- Content  -->
        <section class="col-lg-8  ">
          <!-- Toolbar -->
          <div class="d-flex justify-content-center justify-content-sm-between align-items-center pt-2 pb-4 pb-sm-5">
           <!--
		   <div class="d-flex flex-wrap">
              <div class="form-inline flex-nowrap mr-3 mr-sm-4 pb-3">
                <label class="text-light opacity-75 text-nowrap mr-2 d-none d-sm-block" for="sorting">Sort by:</label>
                <select class="form-control custom-select" id="sorting">
                  <option>Popularity</option>
                  <option>Low - Hight Price</option>
                  <option>High - Low Price</option>
                  <option>Average Rating</option>
                  <option>A - Z Order</option>
                  <option>Z - A Order</option>
                </select><span class="font-size-sm text-light opacity-75 text-nowrap ml-2 d-none d-md-block">of 287 products</span>
              </div>
            </div>
			-->
		  </div>
		 <!-- -->
		<!-- Products grid-->
		<div class="row mx-n2 product-list-items" >
			<% include Schrattenholz\OrderProfileFeature\Includes\ProductListFilter_ProductList %>
		</div>
		<div class="row">
			<div class="col d-flex justify-content-center">
				<input type="hidden" id="categoryID" name="categoryID" value="$ID" readonly="readonly" />
				<button id="pageLoadIcon" class="btn btn-primary mr-3 mb-3" type="button"><span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Lade weitere Produkte</button>
			</div>
		</div>
		<!--
		  <hr class="my-3">
           Pagination
          <nav class="d-flex justify-content-between pt-2" aria-label="Page navigation">
            <ul class="pagination">
              <li class="page-item"><a class="page-link" href="#"><i class="czi-arrow-left mr-2"></i>Prev</a></li>
            </ul>
            <ul class="pagination">
              <li class="page-item d-sm-none"><span class="page-link page-link-static">1 / 5</span></li>
              <li class="page-item active d-none d-sm-block" aria-current="page"><span class="page-link">1<span class="sr-only">(current)</span></span></li>
              <li class="page-item d-none d-sm-block"><a class="page-link" href="#">2</a></li>
              <li class="page-item d-none d-sm-block"><a class="page-link" href="#">3</a></li>
              <li class="page-item d-none d-sm-block"><a class="page-link" href="#">4</a></li>
              <li class="page-item d-none d-sm-block"><a class="page-link" href="#">5</a></li>
            </ul>
            <ul class="pagination">
              <li class="page-item"><a class="page-link" href="#" aria-label="Next">Next<i class="czi-arrow-right ml-2"></i></a></li>
            </ul>
          </nav>
		  -->
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
	
	console.log("getProductOptions "+rootSelector);
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
function getOrderedProduct(id){

	var orderedProductObj={
		id:id,
		title:$("#p"+id).attr('data-title'),
		productoptions:getProductOptions(id),
		variant01:jQuery("#p"+id+" .variants").val(),
		quantity:1
	}
	return orderedProductObj;
}
	function addToList(id,action){
	console.log("addToList");

		jQuery.ajax({
		url: "{$Link}/addToList?orderedProduct="+JSON.stringify(getOrderedProduct(id))+"&action=add",
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
				$('#cart-toast .toast-body').html(message);
				
			}
		});
	}
	</script>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
