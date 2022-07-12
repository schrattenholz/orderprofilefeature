<span id="single_price" class="h3 font-weight-normal text-accent mr-1">
	<% if $GroupPreise %>
		<% if $GroupPreise.filter("ID",$Top.loadSelectedParameters(0).Variant01) %>
			<% loop $GroupPreise.filter("ID",$Top.loadSelectedParameters(0).Variant01) %>
				<% if $Top.loadSelectedParameters(0).ProductDetails.Portionable %>
				ab 
				<% end_if %>
				<% if $CaPrice %>ca. <% end_if %>
				$Top.formattedNumber($PriceObject.Price) &euro;
			<% end_loop %>
		<% else %>
			<% if $GroupPreise.First.CaPrice %>ca. <% end_if %>$Top.formattedNumber($GroupPreise.First.PriceObject.Price) &euro;
		<% end_if %>
		
	<% else %>
		<% if $CaPrice %>ca. <% end_if %>$formattedNumber($KiloPrice.Price) &euro;
	<% end_if %>
</span>
<% if not $ShowBasePrice %>**<% end_if %>
<% if $ShowBasePrice %>
	<span  class="font-weight-normal">($formattedNumber($KiloPrice.Price) &euro;/$Unit.Shortcode)</span>**
<% end_if %>
<span class="productbadge">
	$getProductBadge(0)
</span>


