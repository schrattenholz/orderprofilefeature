<% loop $AllProductsOfCategory($CategoryID,$CurrentPageStart,$Top.CurrentOrderCustomerGroup.ID) %>
	<!-- Product-->
	<% if $Preise.Count>0 %>
		
		<% loop $Preise %>
		<div id="pbE_$ID" data-productid="$ProductID" data-variantid="$ID" data-title="$Up.MenuTitle.XML"  data-PreSaleCurrentInventory="$getPreSaleStatus.CurrentInventory" data-PreSaleStartInventory="$getPreSaleStatus.StartInventory" class="product col-12 col-sm-6 px-2 mb-4">
	  <div class="card product-card">
	  <% include Schrattenholz\OrderProfileFeature\Includes\ProductPreSaleList_Availability %>
						<!--
		<button class="btn-wishlist btn-sm" type="button" data-toggle="tooltip" data-placement="left" title="Add to wishlist">
			<i class="czi-heart"></i>
		</button>
		-->
		<% if $Up.DefaultImage %><a class="card-img-top d-block overflow-hidden" href="$Link"><img src="$Up.DefaultImage.Fill(518,484).URL" alt="Product"></a><% end_if %>
		<div class="card-body py-2">
		<a class="product-meta d-block font-size-xs pb-1" href="$Parent.Link">$Product.MenuTitle.XML</a>
		  <h3 class="product-title font-size-sm">
			<a href="$Link">$FullTitle.XML</a> 
		  </h3>
		  <% include Schrattenholz\OrderProfileFeature\Includes\ProductPreSaleList_Items_Price %>
<!-- Success progress bar -->
<div class="progress mb-3">
  <div class="progress-bar font-weight-medium bg-success" role="progressbar" style="width: $SoldPercentage%" aria-valuenow="$SoldPercentage" aria-valuemin="0" aria-valuemax="100">
  </div>
</div>
		</div>

		<% include Schrattenholz\OrderProfileFeature\Includes\ProductPreSaleList_Items_CardBody %>

	  </div>
	  <hr class="d-sm-none">
	</div>
		<% end_loop %>

	<% end_if %>
<% end_loop %>
	<input type="hidden" id="currentPage" name="currentPage" value="$AllProductsOfCategory($CategoryID,$CurrentPageStart,$Top.CurrentOrderCustomerGroup.ID).CurrentPage" readonly="readonly" />
	<input type="hidden" id="totalPages" name="totalPages" value="$AllProductsOfCategory($CategoryID,$CurrentPageStart,$Top.CurrentOrderCustomerGroup.ID).TotalPages" readonly="readonly" />