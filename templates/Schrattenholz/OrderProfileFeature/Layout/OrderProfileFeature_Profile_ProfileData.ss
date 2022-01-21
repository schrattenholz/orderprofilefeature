
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
		<div class="col-md-3 oderprofilefeature_profil_menu">
		
			<% include ProfileNav %> 
			
		</div>
		<div class="col-md-9">
			<div class="col-12">
				$OrderProfileFeature_Profile_ProfileData_Form
			</div>
			<div class="col-12">
				$OrderProfileFeature_Profile_ProfileData_PasswordForm
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
			var response=JSON.parse(data);
			var message=Object.keys(response);
			var hideDelay=2500;
			console.log(response[message[0]]['message']);

				showUserMessage(data,hideDelay);
			}
		});
	event.stopPropagation();
}

function saveProfileData(){
	jQuery.ajax({
		url: "{$Link}/SaveProfileData?profiledata="+encodeURIComponent(JSON.stringify(jQuery('#Form_OrderProfileFeature_Profile_ProfileData_Form').serializeObject())),
		success: function(data)
		{
			var response=JSON.parse(data);
			var status=response.Status;
			var message=response.Message;
			var value=response.Value;
			var hideDelay=1500;
			
			showUserMessage(data,hideDelay);
			$('#welcome').html("Sie sind eingeloggt als "+value+".");
		}
	});
}
function showUserMessage(data,hideDelay){
	var response=JSON.parse(data);
	var status=response.Status;
	var message=response.Message;
	var value=response.Value;
	if(status=="good"){
		//$('#warenkorb_icon').html(value+" <i class='fas fa-shopping-bag' aria-hidden='true'></i>");
		//console.log("id="+id+" wurde dem Warenkorb hinzugefügt");
		$('#messageBox .modal-content .modal-title').html(message);
		$('#messageBox .modal-content').find('div').each(function(){$(this).addClass('alert-success');});
		$('#messageBox').modal('show');
		setTimeout(function(){
			$('#messageBox').modal("hide");
			$('#messageBox').on('hidden.bs.modal', function (e) {
			  $('#messageBox .modal-content').find('div').each(function(){$(this).removeClass('alert-success');});
			})
		  }, hideDelay);
		
	}else if(status=="info"){
		$('#messageBox .modal-content .modal-title').html(message);
		$('#messageBox .modal-content').find('div').each(function(){$(this).addClass('warning-primary');});
		$('#messageBox').modal('show');
		setTimeout(function(){
			$('#messageBox').modal("hide");
			$('#messageBox').on('hidden.bs.modal', function (e) {
			  $('#messageBox .modal-content').find('div').each(function(){$(this).removeClass('warning-primary');});
			})
		  
		}, hideDelay);
	}else{
		$('#messageBox .modal-content .modal-title').html(message);
		$('#messageBox .modal-content').find('div').each(function(){$(this).addClass('alert-danger')});
		$('#messageBox').modal('show');
		setTimeout(function(){
			$('#messageBox').modal("hide");
			$('#messageBox').on('hidden.bs.modal', function (e) {
			  $('#messageBox .modal-content').find('div').each(function(){$(this).removeClass('alert-danger')});
			})
		  }, hideDelay);
	}
}

</script>
