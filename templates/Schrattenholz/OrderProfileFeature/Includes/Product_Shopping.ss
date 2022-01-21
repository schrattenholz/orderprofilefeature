				<div class="mb-grid-gutter">
<!-- Produktauswahl  -->
				<% if $Parent.ClassName="Schrattenholz\Order\CollectedProduct" %>
					<div id="collected-products" class="form-group collected-products">
						<h5>{$Parent.MenuTitle.XML}-Produkte</h5>
						<div class="selectric-wrapper">
							<select id="products"   class="custom-select" style="width:100%;" onchange="loadPage()">
							<% loop $Parent.Children %>
								<option value="$Link" <% if $Top.ID==$ID %> selected="selected"<% else_if $First %> selected="selected"<% end_if %>>$MenuTitle.XML</option>
							<% end_loop %>
							</select>
						</div>
					</div>
					<% end_if %>
				<!-- Produktvarianten/Staffelpreise -->
				<% if $GroupPreise %>
					<div id="product-variants" class="form-group product-variants" >
						<h5>Varianten</h5>
						
						<div class="selectric-wrapper">
							<select id="variant01"   class="custom-select" style="width:100%;" onchange="refreshSelectedProduct('variantChange')" <% if $GroupPreise.Count<=1 %>disabled="disabled"<% end_if %>>
							<% loop $GroupPreise.Sort('SortID','ASC') %>
								<option 
									value="$ID" 
									data-portionable="$Portionable"
									<% if $Portionable %>
									data-portion="$Portion"
									data-portionMin="$PortionMin"
									data-portionMax="$PortionMax"
									<% end_if %>
									data-caprice="$CaPrice" 
									data-price="$Top.formattedNumber($PriceObject.Price)" 
									data-quantity="$FreeQuantity" <% if $Top.loadSelectedParameters(0).Variant01==$ID %> 
									selected="selected"<% else_if $First %> selected="selected"<% end_if %>>
								
								$FullTitle(0)
								</option>
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
						  <input type="checkbox" class="custom-control-input product-option" id="productoption_$ID" data-id="$ID" onchange="refreshSelectedProduct('attributeChange')" 
						  <% if $Top.loadSelectedParameters(0).Variant01==$ID %> selected="selected"<% end_if %>
						  /> 
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
							  <% if $Top.loadSelectedParameters(0).Variant01==$ID %> selected="selected"<% end_if %>
							  /> 
							  <label class="custom-control-label" for="productoption_{$Up.ID}_{$ID}">$Title (zzgl. $Top.formattedNumber($ProductOptions_Preis.PriceObject.Price) &euro;/Stk.)</label>
							</div>
							<% end_if %>
						<% end_loop %>
					</div>
					<% end_loop %>
				<% end_if %>

				<!-- Bestellmenge -->
				<div class="row">
					<div class="col-3 form-group d-flex align-items-center">
						<div class="quantity position-relative clearfix d-inline-block align-top">
						
							<input 
							oninput="this.value=onlyNumber(this.value);"
							id="amount" 
							type="text" 
							data-portionable="$loadSelectedParameters(0).ProductDetails.Portionable"
							min="<% if $loadSelectedParameters(0).ProductDetails.Portionable %>$loadSelectedParameters(0).ProductDetails.PortionMin<% else %>0<% end_if %>" 
							max="<% if $loadSelectedParameters(0).ProductDetails.Portionable %>$loadSelectedParameters(0).ProductDetails.PortionMax<% else %><% if $loadSelectedParameters(0).ProductDetails.InfiniteInventory %>99999999<% else %>$loadSelectedParameters(0).ClientsQuantityMax<% end_if %><% end_if %>" 
							step="<% if $loadSelectedParameters(0).ProductDetails.Portionable %>$loadSelectedParameters(0).ProductDetails.Portion<% else %>1<% end_if %>"
							readonly="readonly"
							value="<% if $loadSelectedParameters(0).Quantity>0 %><% if $loadSelectedParameters(0).ProductDetails.Portionable %>$Top.formattedWeight($loadSelectedParameters(0).Quantity)<% else %>$loadSelectedParameters(0).Quantity<% end_if %><% else %><% if $loadSelectedParameters(0).ProductDetails.Portionable %>{$loadSelectedParameters(0).ProductDetails.PortionMin}g<% else %>0<% end_if %><% end_if %>">
						</div>
					</div>
					<div id="currentlyInBasket" class="col-7 offset-2 <% if $Top.loadSelectedParameters(0).Quantity %>d-block<% else %>d-none<% end_if %>" style="padding:10px 30px 10px 3px;"><p class="pl-3">Bereits im Warenkorb</p>
					</div>
				</div>
				<div class="form-group col-12 messageBox px-0">
					<div class="col-12 pl-3 pr-3 alert  fade" style="display:none;" role="alert">
					</div>
				</div>
				
				<div class="form-group d-flex align-items-center">
					<div class="container">
						<div id="addFunction" class="row" <% if $Top.loadSelectedParameters(0).Quantity>0 %>style="display:none;"<% end_if %>>
						<a class="btn btn-dark btn-shadow btn-block" href="javascript:addToList('$ID','new');"><i class="czi-cart font-size-lg mr-2"></i>In den Warenkorb</a>
						</div>
						<div id="editFunction" class="row" <% if $Top.loadSelectedParameters(0).Quantity==0 || $Top.loadSelectedParameters(0).Quantity=="" %>style="display:none;"<% end_if %>>
							 <div class="col-md-6 pl-md-0">
								<a class="btn btn-dark btn-shadow btn-block" href="javascript:addToList('$ID','edit');"><i class="czi-reload font-size-lg mr-2"></i>Aktualisieren</a>
							</div>

							<div class="col-md-6 pr-md-0 mt-2 mt-sm-0">
								<a class="btn btn-danger btn-shadow btn-block" href="javascript:javascript:removeProductFromBasket('$ID','{$Top.BaseHref}{$Top.Link}');" title="Produkt aus dem Warenkorb entfernen"><i class="font-size-lg mr-2 czi-close-circle"></i>Entfernen</a>
							</div>
							<!--
							<div class="col-12 pl-md-0 pr-md-0 pt-3">
								<a class="btn btn-dark btn-shadow btn-block" href="javascript:addToList('$ID','new');"><i class="czi-cart font-size-lg mr-2"></i>Als neues Produkt in den Warenkorb</a>
							</div>
							-->
						</div>
					</div>
				  </div>