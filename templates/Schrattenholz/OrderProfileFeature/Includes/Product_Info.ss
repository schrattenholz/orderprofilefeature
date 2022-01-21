<div class="accordion mb-4" id="productPanels">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="accordion-heading">
					  <a href="#productInfo" data-target="#productInfo" role="button" data-toggle="collapse" aria-expanded="true" aria-controls="productInfo"><i class="czi-announcement text-muted font-size-lg align-middle mt-n1 mr-2"></i>Produktinfos<span class="accordion-indicator"><i data-feather="chevron-up"></i></span></a></h3>
                    </div>
                    <div class="collapse show" id="productInfo" data-parent="#productPanels">
                      <div class="card-body">
                       $Content
                      </div>
                    </div>
                  </div>
				  
                 <div class="card <% if $ProductDiscountScale($ID,$v).Count==0 %>d-none<% end_if %>" id="discountScaleCard">
                    <div class="card-header">
                      <h3 class="accordion-heading"><a class="collapsed" href="#discountScale" data-target="#discountScale" role="button" data-toggle="collapse" aria-expanded="true" aria-controls="discountScale"><i class="czi-discount text-muted lead align-middle mt-n1 mr-2"></i>Rabatte<span class="accordion-indicator"><i data-feather="chevron-up"></i></span></a></h3>
                    </div>
					
                    <div class="collapse" id="discountScale"  data-parent="#productPanels">
						<% include Schrattenholz\OrderProfileFeature\Includes\Product_Info_DiscountScale %>
					 </div>
                  </div>
				  
				  <div class="card">
                    <div class="card-header">
                      <h3 class="accordion-heading"><a class="collapsed" href="#shippingOptions" data-target="#shippingOptions" role="button" data-toggle="collapse" aria-expanded="true" aria-controls="shippingOptions"><i class="czi-delivery text-muted lead align-middle mt-n1 mr-2"></i>Liefer/ Abholtermine<span class="accordion-indicator"><i data-feather="chevron-up"></i></span></a></h3>
                    </div>
					
                    <div class="collapse" id="shippingOptions"  data-parent="#productPanels">
						<% include Schrattenholz\OrderProfileFeature\Includes\Product_Info_ShippingOptions %>
					 </div>
                  </div>
				  <!--
                  <div class="card">
                    <div class="card-header">
                      <h3 class="accordion-heading"><a class="collapsed" href="#localStore" data-target="#localStore"  role="button" data-toggle="collapse" aria-expanded="true" aria-controls="localStore"><i class="czi-location text-muted font-size-lg align-middle mt-n1 mr-2"></i>Find in local store<span class="accordion-indicator"><i data-feather="chevron-up"></i></span></a></h3>
                    </div>
                    <div class="collapse" id="localStore" data-parent="#productPanels">
                      <div class="card-body">
                        <select class="custom-select">
                          <option value>Select your country</option>
                          <option value="Argentina">Argentina</option>
                          <option value="Belgium">Belgium</option>
                          <option value="France">France</option>
                          <option value="Germany">Germany</option>
                          <option value="Spain">Spain</option>
                          <option value="UK">United Kingdom</option>
                          <option value="USA">USA</option>
                        </select>
                      </div>
                    </div>
                  </div>
				  -->
				  
                </div>