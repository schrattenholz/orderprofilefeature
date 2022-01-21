
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

	<section class="container pt-5 pb-3 pb-lg-5" role="main" id="profile_orders">
	<% if $CurrentUser %>
		<div class="row">
			<div class="col-md-3 oderprofilefeature_profil_menu list-group">

				<% include ProfileNav %> 
				

			</div>
			<div class="col-md-9">
				<h2>Bestellvorlagen</h2>
				<div class="accordion" id="accordionExample">
				<% if $ClientOrders.Count>0 %>
					<% loop $ClientOrders.Filter('IsModel',1) %>
					<% if $ClientContainer.ClientID==$CurrentMember.ID %>
					
					  <div class="card productlist">
						<div class="card-header collapsed"  type="button" data-toggle="collapse" data-target="#orderlist_$ID" aria-expanded="false" aria-controls="orderlist_$ID">
							<h3 class="accordion-heading">
								<a href="#collapseThree" role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseThree">
								  <% if $Title %>$Title<% else %>Bestellnummer: $ID<% end_if %>
								  <span class="accordion-indicator">
									<i data-feather="chevron-up"></i>
								  </span>
								</a>
							</h3>
						</div>
						<div id="orderlist_$ID" class="collapse " aria-labelledby="order_$ID" data-parent="#accordionExample">
							<div class="card-body table-responsive">
								<table class="table table-striped">
									<thead>
										<tr>
										  <th>Produkt </th>
										  <th>Anzahl</th>
										   <th>Optionen</th>
										   <th></th>
										</tr>
									</thead>
									<tbody>
										<% loop $ProductContainers.Sort('ProductSort') %>
								<tr id="product_{$ID}" data-productid="$ProductID" data-quantity="$Quantity" data-priceblockelement="$PriceBlockElement.ID" data-vacuum="$Vacuum" >
									<td>
									<a href="$Product.Link">$Product.SummaryTitle</a>
									<% if $PriceBlockElement %>
									<div class="font-size-sm">
										<span class="text-muted mr-2">$PriceBlockElement.Title</span>
									</div>
									<% end_if %>
									</td>
									<td>
									<div class="quantity position-relative clearfix d-inline-block align-top">
									<input class="amount" type="number" min="0" max="100" step="1"value="$Quantity">
									</div>
									</td>
									<td data-value="$Vacuum">
									<% if $ProductOptions %>
										<dl class="row mb-0 font-size-xs">
										<% loop $ProductOptions %>
										<% loop $ProductContainers %>									
											<% if $ID==$Up.Up.ID && $ProductOptions_ProductContainer.Active  %>
												<dt class="col-sm-9 mb-0">$Up.Title</dt>
											<% end_if %>
										<% end_loop %>
										<% end_loop %>
										</dl>
									<% end_if %>
									</td>
									<td><a href="javascript:addProduct($ID);" title="Produkt in den Warenkorb legen"><i class="czi-basket"></i></a></td>
								</tr>
							  <% end_loop %>	 
								</table>
								<button type="button" data-toggle="modal" data-target="#orderModal" data-clientorderid="$ID" class="btn btn-primary btn-sm saveOrderAsModelButton" id="#generateNewOrder_$ID"><i class="czi-sign-in mr-2" ></i>Neue Bestellung generieren</button>
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
		</div>
	</div>
	<% end_if %>
</section>


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
function savePassword(id){
		jQuery.ajax({
			url: "{$Link}/SavePassword?passwords="+JSON.stringify(jQuery('#Form_OrderProfileFeature_Profile_ProfileData_PasswordForm').serializeObject())+"&email="+jQuery('#Form_OrderProfileFeature_Profile_ProfileData_Form_Email').val(),
			success: function(data) {
				showUserMessage(data);
			}
		});
	event.stopPropagation();
}

function saveProfileData(){
	jQuery.ajax({
		url: "{$Link}/SaveProfileData?profiledata="+encodeURIComponent(JSON.stringify(jQuery('#Form_OrderProfileFeature_Profile_ProfileData_Form').serializeObject())),
		success: function(data) {
		 showUserMessage(data);
		}
	});
}
function showUserMessage(data){
var response=JSON.parse(data);
					var status=response.Status;
					var message=response.Message;
					var value=response.Value;
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
					$('#messageBox .modal-content').find('div').each(function(){$(this).addClass('alert-danger')});
					$('#messageBox').modal('show');
					setTimeout(function(){
						$('#messageBox').modal("hide");
						$('#messageBox').on('hidden.bs.modal', function (e) {
						  $('#messageBox .modal-content').find('div').each(function(){$(this).removeClass('alert-danger')});
						})
					  }, 1500);
				}
}

</script>
