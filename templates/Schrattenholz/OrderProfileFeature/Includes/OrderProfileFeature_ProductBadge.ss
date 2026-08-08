<%-- Verfuegbarkeits-Badge.

	 Der Zustand kommt aus Preis::AvailabilityInfo() (OrderSale_PreisExtension) --
	 dieselbe Quelle, die auch die strukturierten Daten und die App nutzen.
	 Hier steht nur noch die Darstellung: Farbe, Icon, Text.

	 $QuantityLeft aus loadSelectedParameters() wird weiterhin fuer die
	 Mengenanzeige gebraucht (inkl. Portionable/formattedWeight), der
	 Zustandsvergleich laeuft aber nicht mehr darueber. --%>
<% with $loadSelectedParameters() %>
	<% with $ProductDetails.AvailabilityInfo %>

	<% if $State == 'infinite' %>
		<div class="product-badge product-available mt-n3">
			<i class="czi-security-check"></i>
			<span id="Quantity" class="d-none d-sm-inline">Auf Lager</span>
		</div>

	<% else_if $State == 'soldout' %>
		<div class="product-badge product-not-available mt-n3">
			<i class="czi-close-circle"></i>
			<span id="Quantity" class="d-none d-sm-inline">Ausverkauft</span>
		</div>

	<% else_if $State == 'presale' || $State == 'presale-open' || $State == 'presale-done' %>
		<div class="product-badge <% if $State == 'presale-done' %>product-not-available<% else_if $Quantity <= 2 %>product-little-available<% else %>product-available<% end_if %> mt-n1">
			<span class="d-none d-sm-inline">
			<% if $State == 'presale' %>
				Im Vorverkauf bis $Up.ProductDetails.PreSaleEnd.Format("d MMM")<br>
				noch $Quantity übrig
			<% else_if $State == 'presale-open' %>
				Im Vorverkauf seit $Up.ProductDetails.PreSaleStart.Format("d MMM")<br>
				noch $Quantity vorhanden
			<% else %>
				Alles verteilt
			<% end_if %>
			</span>
		</div>

	<% else %>
		<div class="product-badge <% if $State == 'available' %>product-available<% else_if $State == 'low' %>product-little-available<% else %>product-not-available<% end_if %> mt-n3">
		<% if $IsOrderable %>
			<i class="czi-security-check"></i>
			<span class="d-none d-sm-inline">
			<% if $Up.ProductDetails.Portionable %>$Top.formattedWeight($Quantity)<% else %>$Quantity<% end_if %> auf Lager
			</span>
		<% else %>
			<i class="czi-close-circle"></i>
			<span class="d-none d-sm-inline">
			Nicht verfügbar
			</span>
		<% end_if %>
		</div>
	<% end_if %>

	<% end_with %>
<% end_with %>
