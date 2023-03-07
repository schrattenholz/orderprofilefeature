<% if not $Availability($Top.CurrentOrderCustomerGroup.ID) %>
	<span class="badge badge-danger badge-shadow">Ausverkauft $Available</span>
<% end_if %>