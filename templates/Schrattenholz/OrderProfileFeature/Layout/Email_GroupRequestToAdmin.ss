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
<h3>Guten Tag $CheckoutAddress.Gender $CheckoutAddress.FirstName $CheckoutAddress.Surname</h3>
<p>
möchte sich als Wiederverkäufer registrieren.

Möchten Sie den Kunden als Wiederverkäufer bestätigen?
<a href="{$BaseHref}groupconfirmation/confirm/$CheckoutAddress.ID">Als Wiederverkäufer bestätigen</a>
Möchten Sie das der Kunde weiterhin as Prvatkunde geführt wird?
<a href="{$BaseHref}groupconfirmation/deny/$CheckoutAddress.ID">Als Privatkunden führen</a>
</p>
<p>Der Kunde wird nach der Bestätigun per E-Mail informiert.
		  <div>
		  $OrderConfig.EmailSignature
		  
		  </div>
