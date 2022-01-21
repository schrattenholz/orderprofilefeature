<% loop $AllProductsOfCategory($CategoryID,$CurrentPageStart) %>
	<!-- Product-->
	<div id="p$ID" data-productid="$ID" data-title="$MenuTitle.XML"  class="col-md-4 col-sm-6 px-2 mb-4">
	  <div class="card product-card">
	  <% include Schrattenholz\OrderProfileFeature\Includes\Availability %>
						<!--
		<button class="btn-wishlist btn-sm" type="button" data-toggle="tooltip" data-placement="left" title="Add to wishlist">
			<i class="czi-heart"></i>
		</button>
		-->
		<a class="card-img-top d-block overflow-hidden" href="$Link"><img src="$CoverImage.Fill(518,484).URL" alt="Product"></a>
		<div class="card-body py-2">
		<a class="product-meta d-block font-size-xs pb-1" href="$Parent.Link">$Parent.MenuTitle.XML</a>
		  <h3 class="product-title font-size-sm">
			<a href="$Link">$MenuTitle.XML</a>
		  </h3>
		  <% include Schrattenholz\OrderProfileFeature\Layout\ProductListFilter_Items_Price %>

		</div>

		<% include Schrattenholz\OrderProfileFeature\Layout\ProductListFilter_Items_CardBody_Dummy %>

	  </div>
	  <hr class="d-sm-none">
	</div>
<% end_loop %>
	<input type="hidden" id="currentPage" name="currentPage" value="$AllProductsOfCategory($CategoryID,$CurrentPageStart).CurrentPage" readonly="readonly" />
	<input type="hidden" id="totalPages" name="totalPages" value="$AllProductsOfCategory($CategoryID,$CurrentPageStart).TotalPages" readonly="readonly" />