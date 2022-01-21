	<% loop $loadSelectedParameters() %>
		<% if not $ProductDetails.InfiniteInventory %>
		<div class="product-badge <% if $QuantityLeft>=2 %>product-available<% else_if $QuantityLeft>0 && $QuantityLeft<=2 %>product-little-available<% else %>product-not-available<% end_if %> <% if not $ProductDetails.InPreSale %>mt-n3 <% else %>mt-n1 <% end_if %>">
		<% if not $ProductDetails.InPreSale %>
			<% if $QuantityLeft>0 %>
				<i class="czi-security-check"></i>
				<span wfd-id="215" class="d-none d-sm-inline">
				<% if $ProductDetails.Portionable %>$Top.formattedWeight($QuantityLeft)<% else %>$QuantityLeft<% end_if %> auf Lager
				</span>
			<% else %>
				<i class="czi-close-circle"></i>
				<span wfd-id="215" class="d-none d-sm-inline">
				Nicht verfügbar
				</span>
			<% end_if %>
		<% else %>
		<span wfd-id="215" class="d-none d-sm-inline">
			<% if $QuantityLeft>0 %>
				Vorverkauf<br/>noch $QuantityLeft übrig
			<% else %>
				Alles verteilt
			<% end_if %>
		</span>
		<% end_if %>
		</div>
		<% else %>
			<div class="product-badge product-available mt-n3"><i class="czi-security-check"></i>
				<span id="Quantity" class="d-none d-sm-inline">Auf Lager</span>
			</div>
		<% end_if %>
	<% end_loop %>

