<div class="d-flex justify-content-between">
	<div class="product-price">
		<span class="">
		<% if $ShowBasePrice %>
			$formattedNumber($KiloPrice.Price) &euro;/<% if $Unit.Type=="weight" %>$Unit.Shortcode<% else %>Stk<% end_if %>
		<% else_if $GroupPreise %>
			ab <% if $GroupPreise.Sort('Price','ASC').First.CaPrice %>ca. <% end_if %>$Top.formattedNumber($GroupPreise.Sort('Price','ASC').First.Price) &euro;
		<% else %>
		<% if $CaPrice %>ca. <% end_if %>$formattedNumber($KiloPrice.Price) &euro;
		<% end_if %>
		</span>
	</div>
	<div class="star-rating">
	<!--
	<i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star"></i>
	-->
	</div>
</div>