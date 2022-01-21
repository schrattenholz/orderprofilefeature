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

