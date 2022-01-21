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
<h3>$CheckoutAddress.FullSalutation</h3>
<p>
wenn du dich auf der Webseite von $SiteConfig.Title registriert hast, kannst du die Registrierung nun abschließen.

Klicke hierzu auf diesen Link: <a href="{$BaseHref}doubleoptin/confirm/$CheckoutAddress.ID">Registrierung abschließen</a></p>
<p>Wenn du dich nicht registriert hast, kannst du diese E-Mail ignorieren, die Registrierung findet dann nicht statt!</p>

<p>Herzliche Grüße</br>Das $SiteConfig.Title Team 
</p>

		  <div>
		  $OrderConfig.EmailSignature
		  
		  </div>
