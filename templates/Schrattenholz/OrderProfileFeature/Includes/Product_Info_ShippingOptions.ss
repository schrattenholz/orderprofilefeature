								
								
								
								<% if $loadSelectedParameters.ProductDetails.PreSaleMode=="openpresale" %>
					<div class="row">
					<div class="col-12 font-size-sm">
					<% if not $v %>
					$BasketDeliverySetup($ID,$GroupPreise.Sort('SortID','ASC').First.ID).DeliverySetup.ContentProductShippingInfo
					<% else %>
					$BasketDeliverySetup($ID,$v).DeliverySetup.ContentProductShippingInfo
					<% end_if %>
					</div>
					</div>
				<% else %>
				<% loop BasketDeliverySetup($ID,$v).DeliverySetup %>
					<% if $Up.DeliverySpecial %>
					<div class="card-body font-size-sm">
					<p class="font-size-md">Die Lieferoptionen werden durch ein Produkt in deinem Warenkorb vorgegeben, dass spezielle Lieferoptionen ($Title) hat.</p>
					</div>
					<% end_if %>
					<!-- Abholtage -->
						<% if $CollectionDays %>
							<div class="card-body font-size-sm" data-pbe_id="$ID" id="pbe_1">
							<h4 class="font-size-md">Abholtage</h4>
							<% if $MinOrderValue($Top.CurrentOrderCustomerGroup.ID,"collection")>0 %>
								<h5 class="font-size-md"><i class="text-body czi-announcement"></i> Mindesbestellwert: $Top.FormattedNumber($MinOrderValue($Top.CurrentOrderCustomerGroup.ID,"collection")) €</h5>
							<% end_if %>
							
						
							<% loop $getNextCollectionDays($Top.CurrentOrderCustomerGroup.ID,$ID) %>
								
								<div class="d-flex justify-content-between">
								  <div>
									<div class="font-weight-semibold">$DayTranslated, {$Date.Short}: $Time.From bis $Time.To Uhr</div>
									
									
								  </div>
								</div>
							<% end_loop %>
							</div>
						<% end_if %>
					<!-- Ende Abholtage -->
					
					
					<!-- Lieferorte -->
					<% if $getCities($Top.CurrentOrderCustomerGroup.ID) %>
					<div class="card-body font-size-sm" data-pbe_id="$ID" id="pbe_2">
						<h4 class="font-size-md">Lieferorte</h4>
					<% if $MinOrderValue($Top.CurrentOrderCustomerGroup.ID,"delivery")>0 %>
								<h5 class="font-size-md"><i class="text-body czi-announcement"></i> Mindesbestellwert: $Top.FormattedNumber($MinOrderValue($Top.CurrentOrderCustomerGroup.ID,"delivery")) €</h5>
							<% end_if %>
						<div class="card-body">
						  <div class="widget widget-links cz-filter">
							<div class="input-group-overlay input-group-sm mb-2">
							  <input type="text" class="cz-filter-search form-control form-control-sm appended-form-control" placeholder="Suche">
							  <div class="input-group-append-overlay">
								<span class="input-group-text">
								  <i class="czi-search"></i>
								</span>
							  </div>
							</div>
							<!-- Sub-categories -->
							<ul class="widget-list cz-filter-list pt-1" style="height: 12rem;" data-simplebar data-simplebar-auto-hide="false">
							<% loop $getCities($Top.CurrentOrderCustomerGroup.ID).Sort('Title') %>
							  <li class="widget-list-item cz-filter-item">						
								<% loop $Top.DeliveryDatesForCity($Top.CurrentOrderCustomerGroup.ID, $Delivery_ZIPCodes.First.Title,$Title).Dates %>
								  <% if $First %><span class="cz-filter-item-text">$Up.ZIPs.First.Title, $Up.Title</span><% end_if %>
								  <% if $First %><span class="font-size-xs text-muted ml-3"><% end_if %>$Short<% if not $Last %>, <% end_if %><% if $Last %></span><% end_if %>
								<% end_loop %>
							  </li>
								<% end_loop %>
							</ul>
						  </div>
						</div>
					</div>
						<% end_if %>
					<!- Ende Lieferorte -->
					
					
					<% end_loop %>
					<% end_if %>