          <h1 class="h3 text-light mb-0">$Title.XML 
		  <% if $AdditionalTitle %><span class="text-light">$AdditionalTitle</span><% end_if %>
		  </h1>
		  <% if $Ingredients || $Addons %>
		  <h6 class="text-light">
				<% loop $Addons %>$Title<% if not $Last %>, <% end_if %><% end_loop %> | <% loop $Ingredients %>$Title<% if not $Last %>, <% end_if %><% end_loop%>

				</h6>
				<% end_if %>
        </div>