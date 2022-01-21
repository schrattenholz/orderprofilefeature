<% if $ProductDiscountScale($ID,$v).Count>0 %>
<div class="card-body font-size-sm" data-pbe_id="$ID" id="pbe_2">
	<div class="card-body">
	  <div class="widget widget-links cz-filter">
		<!-- Sub-categories -->
		<dl class="row font-size-ms mb-0" wfd-id="323">
		<% loop $ProductDiscountScale($ID,$v) %>
		 <dt class="col-sm-8 font-weight-normal mb-0">$Range</dt><dd class="col-sm-4 mb-0">$Price.RAW</dd>
		<% end_loop %>
</dl>
		
		</ul>
	  </div>
	</div>
</div>
<% else %>
<input type="hidden" value="nodiscount" />
<% end_if %>