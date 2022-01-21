
					<% loop BasketDeliverySetup($ID,$v).DeliverySetup %>
					<% if $Top.getBasket().DeliverySpecial && not $IsPrimary%>
					<div class="card-body font-size-sm">
					<p class="font-size-md">Die Lieferoptionen werden durch ein anderes Produkt vorgegeben, dass bereits in deinem Warenkorb liegt</p>
					</div>
					<% end_if %>
					<!-- Abholtage -->
						<% if $CollectionDays %>
						<div class="card-body font-size-sm" data-pbe_id="$ID" id="pbe_1">
						<h4 class="font-size-md">Abholtage</h4>
						
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
					<% if $getCities($Top.CurrentOrderCustomerGroup.ID).First.Routes.First.DeliveryType.MinOrderValue %>
					<h6><i class="text-body czi-announcement"></i> Mindesbestellwert: $Top.FormattedNumber($getCities($Top.CurrentOrderCustomerGroup.ID).First.Routes.First.DeliveryType.MinOrderValue) €</h6>
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
								
								  <span class="cz-filter-item-text">$Delivery_ZIPCodes.First.Title, $Title</span>
								  <span class="font-size-xs text-muted ml-3">$Routes.First.getNextDeliveryDates($Top.CurrentOrderCustomerGroup.ID,$Up.Up.ID)</span>
								
							  </li>
								<% end_loop %>
							</ul>
						  </div>
						</div>
					</div>
						<% end_if %>
					<!- Ende Lieferorte -->
					
					
					<% end_loop %>