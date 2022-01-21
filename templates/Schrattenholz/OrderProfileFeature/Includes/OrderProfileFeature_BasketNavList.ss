<a class="navbar-tool-icon-box bg-secondary dropdown-toggle" href="$OrderConfig.Basket.Link" id="warenkorb_icon">
	<span class="navbar-tool-label basket-count">$ListCount</span>
	<i class="navbar-tool-icon czi-cart"></i>
</a>
<a class="navbar-tool-text <% if not $Basket.ProductContainers %> d-none <% end_if %>" href="$OrderConfig.Basket.Link" style="min-width: 90px;">
	<small>Warenkorb</small>
	 <% if $Basket.ProductContainers %>
		<% if $Basket.TotalPrice.CaPrice %>ca. <% end_if %>
		$Top.formattedNumber($Basket.TotalPrice.Price) &euro;
	<% end_if %>
</a>
                <!-- Cart dropdown-->
                <div class="dropdown-menu dropdown-menu-right" style="width: 20rem;">
            
                   <div class="widget widget-cart px-3 pt-2 pb-3">
				<div style="max-height: 15rem;" data-simplebar data-simplebar-auto-hide="false">
				<% if $Basket.ProductContainers %>
					<% loop $Basket.ProductContainers.Sort('ProductSort') %>
					<div class="widget-cart-item pb-2 border-bottom">
						<a class="close text-danger" type="button" aria-label="Remove" href="javascript:removeProductFromBasketByID('$ID');" title="Produkt aus dem Warenkorb entfernen"><span aria-hidden="true">&times;</span></a>
						<div class="media align-items-center"><a class="d-block mr-2" href="$Product.Link?id=$ID&v=$PriceBlockElement.ID&vac=$Vacuum"><img width="64" src="$Product.ProductImages.First.Fill(200,200).URL" alt="Product"/></a>
						<div class="media-body">
						<a href="$Product.Link?id=$ID&v=$PriceBlockElement.ID">
							<h6 class="widget-product-title  <% if $PriceBlockElementID %>mb-n1"><% end_if %>
							$Product.SummaryTitle</h6>
							<% if $PriceBlockElementID %>
							<p class="text-muted font-size-ms  mb-1">$PriceBlockElement.FullTitle(0)</p>
							<% end_if %></a>
							<div class="widget-product-meta"><span class="text-muted">
							<% if $PriceBlockElement.Portionable %>$Top.formattedWeight($Quantity):
							<% else %>{$Quantity}x =<% end_if %>&nbsp;</span><span class="text-accent mr-2"><% if $CompletePrice.CaPrice %>ca. <% end_if %>$Top.formattedNumber($CompletePrice.Price) &euro;</span></div>
						</div>
						</div>
					</div>
					<% end_loop %>

				<% else %>
						<li class="text-center entry">Es befindet sich kein Produkt im Warenkorb</li>
				<% end_if %>
				</div>
				<% if $Basket.ProductContainers %>
				<div class="d-flex flex-wrap justify-content-between align-items-center py-3">
					<div class="font-size-sm mr-2 py-2"><span class="text-muted">Gesamt:</span><span class="text-accent font-size-base ml-1"><% if $Basket.TotalPrice.CaPrice %>ca. <% end_if %>$Top.formattedNumber($Basket.TotalPrice.Price) &euro;</span></div><a class="btn btn-outline-primary btn-sm" href="$OrderConfig.Basket.Link">Zum Warenkorb<i class="czi-arrow-right ml-1 mr-n1"></i></a>
				</div><a class="btn btn-primary btn-sm btn-block" href="$LinkCheckoutAddress"><i class="czi-card mr-2 font-size-base align-middle"></i>Zur Kasse</a>
				<% end_if %>
			</div>
                 
             </div>



