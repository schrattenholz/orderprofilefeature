<style>

.noBorder{
border:0;
}
h3, legend {
  font-size: 1.22em;
  font-weight: 400;
  line-height: 1.375em;
  margin-bottom: 0.6815em;
}
table {
  background-color: transparent;
  border-collapse: collapse;
  border-spacing: 0;
  max-width: 100%;
}
.table {
  margin-bottom: 20px;
  width: 100%;
}
.table th, .table td {
  border-top: 1px solid #dddddd;
  line-height: 20px;
  padding: 8px;
  text-align: left;
  vertical-align: top;
}
.table-striped tbody > tr:nth-child(2n+1) > td, .table-striped tbody > tr:nth-child(2n+1) > th {
  background-color: #f9f9f9;
}
.table-striped tbody tr:nth-child(2n+1) td, .table-striped tbody tr:nth-child(2n+1) th {
  background-color: #f5f5f5;
}
</style>
<h3>Guten Tag $CheckoutAddress.FirstName $CheckoutAddress.Surname</h3>
<% if $Message=="confirmed" %>
<h4>Dein Status als Wiederverkäufer wurde bestätigt.</h4>
<p>
Nach dem  <a href="$OrderConfig.AcountRoot.Link" target="_blank">LogIn</a> werden Dir nun unsere Wiederverkäuferpreise angezeigt und du kannst die Vorteile des Kundenkontos nutzen.
</p>
<% else %>
<h4>Dein Status als Wiederverkäufer wurde abgelehnt. Du behälst weiterhin den Status Privatkunde. Sollte es sich um ein Missverständis handeln, sprich uns bitte an.</h4>
<p>
<a href="$OrderConfig.AcountRoot.Link" target="_blank">Hier</a> kannst Du dich auf unserer Webseite anmelden und die Vorteile des Kundenkontos nutzen.
</p>
<% end_if %>
		  <div>
		  $OrderConfig.EmailSignature
		  
		  </div>
