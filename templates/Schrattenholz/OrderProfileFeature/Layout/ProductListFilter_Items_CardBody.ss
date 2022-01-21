                <div class="card-body card-body-hidden">
                  <div class="text-center pb-2">
                    <!-- Produktvarianten Staffelpreise -->
					<% if $GroupPreise %>
					<div class="form-group product-variants">
						<div class="selectric-wrapper">
							<select class="custom-select variants" style="width:100%;" onchange="refreshProductPrice()">
							<% loop $GroupPreise.Sort('Price','ASC') %>
								<option value="$ID" data-caprice="$CaPrice" data-productid="$Up.Up.ID" data-price="$Top.formattedNumber($PriceObject.Price)" data-quantity="$FreeQuantity" >
								<% if $Content %>$Content<% if $Attributes || $DisplayAmount %>,<%end_if %> <% end_if %>
								<% if $Attributes %>
									<% loop $Attributes %>
										<% if $Shortcode %>$Shortcode<% else %>$Title<%end_if %>
										<% if not $Last %>, <% end_if %>
									<% end_loop %>
									<% if $ShowAmount && $DisplayAmount %>, <% end_if %>
								<% end_if %>
								<% if $ShowAmount && $DisplayAmount %>
									<% if $CaPrice %>ca. <% end_if %>
									$DisplayAmount
								<% end_if %>
							<% end_loop %>
							</select>
						</div>
					</div>
					<% end_if %>
					<!-- Produktoptionen -->
				<% if $ProductOptions && not $GroupPreise %>
				<div id="product-options" class="form-group product-options ">
					<% loop $ProductOptions %>
					<% if $ProductOptions_Product.Active %>
						<div class="custom-control custom-checkbox">
						  <input type="checkbox" class="custom-control-input product-option" id="productoption_$ID" data-id="$ID" onchange="refreshSelectedProduct('attributeChange')" /> 
						  <label class="custom-control-label" for="productoption_$ID">$Title (zzgl. $Top.formattedNumber($ProductOptions_Product.PriceObject.Price) &euro;/Stk.)</label>
						</div>
						<% end_if %>
					<% end_loop %>
				</div>
				<% else_if $ProductOptions && $GroupPreise %>
					<% loop $GroupPreise.Sort('Price','ASC') %>
					<div id="product-options_$ID" data-id="$ID" class="form-group product-options <% if not $First %> d-none<% end_if %>">
						<% loop $ProductOptions %>
						<% if $ProductOptions_Preis.Active %>
							<div class="custom-control custom-checkbox">
							  <input type="checkbox" class="custom-control-input product-option" id="productoption_{$Up.ID}_{$ID}" data-id="$ID" onchange="refreshSelectedProduct('attributeChange')" 
							  
							  /> 
							  <label class="custom-control-label" for="productoption_{$Up.ID}_{$ID}">$Title (zzgl. $Top.formattedNumber($ProductOptions_Preis.PriceObject.Price) &euro;/Stk.)</label>
							</div>
							<% end_if %>
						<% end_loop %>
					</div>
					<% end_loop %>
				<% end_if %>
                  </div>
                  <a class="btn btn-primary btn-sm btn-block mb-2 toaster" type="button" data-toggle="toastOFF" data-target="#cart-toast" href="javascript:addToList('$ID','new');"><i class="czi-cart font-size-sm mr-1"></i>In den Warenkorb</a>
                  <div class="text-center"><a class="nav-link-style font-size-ms" href="#quick-view" data-toggle="modal"><i class="czi-eye align-middle mr-1"></i>Quick view</a></div>
                </div>