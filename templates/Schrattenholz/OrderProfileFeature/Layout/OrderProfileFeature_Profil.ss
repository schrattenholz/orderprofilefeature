<style>
.orderlist_controls{
	/*position:absolute;*/
}
.orderlist_controls input {
	display: none;
	pointer-events: none;
	background-color: transparent;
	border: #e8c548  1px solid;
	text-indent: .2rem;
}
.orderlist_controls .title{
	pointer-events:none;
	background-color:transparent;
	float:left;
	margin:0;
}
.orderlist_controls .date{
	float: left;
	padding-right: 1rem;
}

</style>
	
	
	<script>
	var pageLink='$Link';
	</script>
<div class="modal fade" id="messageBox" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
    </div>
  </div>
</div>

<div class="container main" role="main">
<% if not $CurrentUser %>
	<% if $Content %>
		<div class="row  pb-4">
			<article class="col-12">
			<% if $URLSegment=="home" || $URLSegment=="" %>
			<% if $Headline %><h1>$Headline</h1><% else %><h1>$MenuTitle.XML</h1><% end_if %>
			<% else %>
			<% if $Headline %><h2>$Headline</h2><% end_if %>
			<% end_if %>
				$Content
				$Form
			</article>
			<% if $SidebarObjects %>
			<aside class="col-12 col-md-6 pr-lg-0 pt-3 ratio_right">
				<% loop SidebarObjects.Sort('SortID') %>
				$renderIT
				
				<% end_loop %>
			</aside>
			<% end_if %>
		</div>
	<% end_if %>
	<% else %>
	
	<% end_if %>
	<div class="row   pb-4">
	<% if not $CurrentUser %>
		<div class="col-12">
			<div id="useraccounttab">
				<ul>
					<li><a href="#logintab">Einloggen</a></li>
					<li><a href="#registertab">Registrieren</a></li>
				</ul>
				<div id="logintab">
					$OrderProfileFeature_ProfilLoginForm
				</div>
				 <div id="registertab">
					$OrderProfileFeature_ProfilRegistrationForm
				</div>
			</div>
		</div>
	<% else %>
	
		<div class="col-12">
		<p>Sie sind eingeloggt als <% if $CurrentMember.Company %>$CurrentMember.Company <% else %> $CurrentMember.FirstName $CurrentMember.Surname<% end_if %>.</p>
		</div>
		<div class="col-12">
			<h2>Ihre Bestellungen</h2>
			<div class="accordion" id="accordionExample">
			<% if $ClientOrders.Count>0 %>
			<% loop $ClientOrders %>
			<% if $ClientContainer.ClientID==$CurrentMember.ID %>
			
			  <div class="card productlist">
				 
				<div class="card-header collapsed"  type="button" data-toggle="collapse" data-target="#orderlist_$ID" aria-expanded="false" aria-controls="orderlist_$ID">
				<div class="orderlist_controls">
				<span class="date">$Created.Format('dd.MM.y')</span>
					<h3 id="title_$ID" class="title"><% if $Title %>$Title<% else %>Bestellnummer: $ID<% end_if %></h3>
					<input id="titlefield_$ID" value="<% if $Title %>$Title<% else %>Bestellnummer: $ID<% end_if %>" disabled="disabled"  />
					&nbsp;
					<a onclick="javascript:saveTitle('$ID')" id="save_$ID" style="display:none;">
						<i class="fas fa-save" ></i>
					</a>
					<a onclick="javascript:editTitel('$ID')" id="edit_$ID">
						<i class="fas fa-edit" ></i>
					</a>
				</div>	  
					<!--<a href="javascript:copyBasket($ID);" title="Neue Bestellung generieren"><i class="fas fa-shopping-basket" id='title_$ID'></i></i></a>
-->
				</div>
				<div id="orderlist_$ID" class="collapse" aria-labelledby="order_$ID" data-parent="#accordionExample">
				  <div class="card-body">
					<table class="table table-striped">
							  <thead>
								<tr>
								  <th>Produkt </th>
								  <th>Anzahl</th>
								  <th>Menge</th>
								   <th>Vakuumieren</th>
								   <th></th>
								</tr>
							  </thead>
							   <tbody>
							 <% loop $ProductContainers.Sort('ProductID') %>
								<tr id="product_{$ID}" data-productid="$ProductID" data-quantity="$Quantity" data-priceblockelement="$PriceBlockElement.ID" data-vacuum="$Vacuum" >
									<td><a href="$Product.Link">$Product.SummaryTitle</a></td>
									<td>
									<div class="quantity position-relative clearfix d-inline-block align-top">
									<input class="amount" type="number" min="0" max="100" step="1"value="$Quantity">
									</div>
									</td>
									<td>$PriceBlockElement.Amount</td>
									<td data-value="$Vacuum">$Top.VacReadable($Vacuum)</td>
									<td><a href="javascript:addProduct($ID);" title="Produkt in den Warenkorb legen"><i class="fas fa-shopping-basket"></i></a></td>
								</tr>
							  <% end_loop %>
							 </tbody>	 
						</table>
				  </div>
				</div>
			  </div>
			  <% end_if %>
			<% end_loop %>
			<% else %>
			Es liegen noch keine Bestellungen vor.
			<% end_if %>
			</div>
		</div>
	<% end_if %>
	</div>
</div>


<script>
function editTitel(id){
	jQuery('#title_'+id).css('display','none');
	jQuery('#titlefield_'+id).css('display','inline');
	jQuery('#titlefield_'+id).removeAttr('disabled');
	jQuery('#titlefield_'+id).select();

	jQuery('#edit_'+id).css('display','none');
	jQuery('#save_'+id).css('display','inline');
	event.stopPropagation();
}
function saveTitle(id){

		jQuery.ajax({
		url: "{$Link}/saveClientOrderTitle?title="+jQuery('#titlefield_'+id).val()+"&id="+id,
			success: function(data) {
					dataAr=data.split("|");
					var response=JSON.parse(data);
					var status=response.Status;
					var message=response.Message;
					var value=response.Value;
					
				if(status=="good"){
					jQuery('#title_'+value).html(jQuery('#titlefield_'+value).val());
					jQuery('#title_'+value).css('display','block');
					jQuery('#titlefield_'+value).css('display','none');
					jQuery('#titlefield_'+value).attr('disabled','disabled');
					jQuery('#save_'+value).css('display','none');
					jQuery('#edit_'+value).css('display','inline');
					alert(message);
				}else{
					jQuery('#title_'+value).css('display','block');
					jQuery('#titlefield_'+value).css('display','none');
					jQuery('#titlefield_'+value).attr('disabled','disabled');
					jQuery('#save_'+value).css('display','none');
					jQuery('#edit_'+value).css('display','inline');
					alert(message);
				}
			
			}
		});
	event.stopPropagation();
}
function addProduct(id){
	var orderedProductObj={
		id:jQuery('#product_'+id).attr('data-productid'),
		variant01:jQuery('#product_'+id).attr('data-priceblockelement'),
		vac:jQuery('#product_'+id).attr('data-vacuum'),
		quantity:jQuery('#product_'+id+' .amount').val()
	}
		jQuery.ajax({
		url: "{$Link}/addProductFromOrderList?orderedProduct="+JSON.stringify(orderedProductObj),
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
					$('#warenkorb_icon').html(value+" <i class='fas fa-shopping-bag' aria-hidden='true'></i>");
					//console.log("id="+id+" wurde dem Warenkorb hinzugefügt");
					$('#editFunction').css("display","block");
					$('#addFunction').css("display","none");
					$('#messageBox .modal-content .modal-title').html(message);
					$('#messageBox .modal-content').find('div').each(function(){$(this).addClass('alert-success');});
					$('#messageBox').modal('show');
					setTimeout(function(){
						$('#messageBox').modal("hide");
						$('#messageBox').on('hidden.bs.modal', function (e) {
						  $('#messageBox .modal-content').find('div').each(function(){$(this).removeClass('alert-success');});
						})
					  }, 1500);
					
				}else if(status=="info"){
					$('#messageBox .modal-content .modal-title').html(message);
					$('#messageBox .modal-content').find('div').each(function(){$(this).addClass('warning-primary');});
					$('#messageBox').modal('show');
					setTimeout(function(){
						$('#messageBox').modal("hide");
						$('#messageBox').on('hidden.bs.modal', function (e) {
						  $('#messageBox .modal-content').find('div').each(function(){$(this).removeClass('warning-primary');});
						})
					  
					}, 1500);
				}else{
				$('#messageBox .modal-content .modal-title').html(message);
					$('#messageBox .modal-content').find('div').each(function(){$(this).addClass('alert-danger');});
					$('#messageBox').modal('show');
					setTimeout(function(){
						$('#messageBox').modal("hide");
						$('#messageBox').on('hidden.bs.modal', function (e) {
						  $('#messageBox .modal-content').find('div').each(function(){$(this).removeClass('alert-danger');});
						})
					  }, 1500);
				}
				//refreshSelectedProduct('refreshSameProduct');
			}
		});
	
}
function checkoutAddress(){
	var nextLink='$LinkCheckoutSummary';
	var pageLink='$Link';
	
	jQuery.ajax({
	
		url: pageLink+"/setCheckoutAddress?person="+encodeURIComponent(JSON.stringify(jQuery('#Form_OrderProfileFeature_ProfilRegistrationForm').serializeObject())),
		success: function(data) {
		var response=JSON.parse(data);
		var status=response.Status;
		var message=response.Message;
		var object=response.Value;
		/*
		JSON
			$returnValues->Status=error/info/warning/good;
			$returnValues->Message="Das Passwort muss mindestens 8 Zeiechen haben!";
			$returnValues->Value='object';
		*/
		 
		console.log("checkoutAddress="+data);
			if(status=='error'){
				alert(message);
			}else{
				if(status=='info' || status=='warning'){
					alert(message);
				}
				window.location=self.location;
			}
		}
	});
}
</script>
