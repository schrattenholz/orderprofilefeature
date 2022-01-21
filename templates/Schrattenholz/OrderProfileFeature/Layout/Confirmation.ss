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
 .table td {
  border-top: 1px solid #dddddd;
  line-height: 20px;
  padding: 8px;
  text-align: left;
  vertical-align: top;
}
.table-striped tbody > tr:nth-child(2n+1) > td, .table-striped tbody > tr:nth-child(2n+1) > th {
  background-color: #f5ca46;
}
.table-striped tbody tr:nth-child(2n+1) td, .table-striped tbody tr:nth-child(2n+1) th {
  background-color: #99b473 ;
}
</style>
<div style="margin:0 20px 0 20px";>
<h3>Guten Tag</h3>
<p>
folgende Bestellung ist soeben eingegangen:
</p>
<h3>Produkte auf der Bestellliste</h3>
<table class="table table-striped">
          <thead>
            <tr>
              <th style="border-top: 1px solid #dddddd;  line-height: 20px;  padding: 8px;  text-align: left;  vertical-align: top;">Produkt </th>
			   <th style="border-top: 1px solid #dddddd;  line-height: 20px;  padding: 8px;  text-align: left;  vertical-align: top;"></th>
              <th style="border-top: 1px solid #dddddd;  line-height: 20px;  padding: 8px;  text-align: left;  vertical-align: top;">Anzahl</th>
			  <th style="border-top: 1px solid #dddddd;  line-height: 20px;  padding: 8px;  text-align: left;  vertical-align: top;">Ca. Preis</th>
            </tr>
          </thead>
		   <tbody>
		 <% loop $Basket.ProductContainers.Sort('ProductSort') %>
			<tr id="product_{$ID}" style="<% if $Even %>background-color: #f5ca46;<% else %>background-color: #99b473;<% end_if %>">
				<td style="border-top: 1px solid #dddddd;  line-height: 20px;  padding: 8px;  text-align: left;  vertical-align: top;">$Product.SummaryTitle</td>
				
				<td style="border-top: 1px solid #dddddd;  line-height: 20px;  padding: 8px;  text-align: left;  vertical-align: top;">
				<% if $PriceBlockElement %>
					<div class="font-size-sm">
					<span class="text-muted mr-2"><% loop $PriceBlockElement %>$FullTitle<% end_loop %>
					</span>
					</div>					
				<% end_if %>
				</td>
				<td style="border-top: 1px solid #dddddd;  line-height: 20px;  padding: 8px;  text-align: left;  vertical-align: top;"><% if $PriceBlockElement.Portionable %>$formattedWeight($Quantity)<% else %>{$Quantity}stk<% end_if %></td>
				<td><% if $CompletePrice.CaPrice %>ca. <% end_if %>$Up.Page.formattedNumber($CompletePrice.Price) &euro;</td>
			</tr>
		  <% end_loop %>
			<tr style="background-color:transparent!important;">
			<td style="background-color:transparent!important; border-top: 1px solid #dddddd;  line-height: 20px;  padding: 8px;  text-align: left;  vertical-align: top;"></td>
			<td style="background-color:transparent!important; border-top: 1px solid #dddddd;  line-height: 20px;  padding: 8px;  text-align: left;  vertical-align: top;"></td>
			<td style="background-color:transparent!important; border-top: 1px solid #dddddd;  line-height: 20px;  padding: 8px;  text-align: left;  vertical-align: top;">Gesamt</td>
			<td style="background-color:transparent!important;"><% if $Basket.TotalPrice.CaPrice %>ca. <% end_if %>$Page.formattedNumber($Basket.TotalPrice.Price) &euro;</td>
			</tr>
			<tr style="background-color:transparent;">
			<td style="background-color:transparent!important; border-top: 1px solid #dddddd;  line-height: 20px;  padding: 8px;  text-align: left;  vertical-align: top;"></td>
			<td style="background-color:transparent!important; border-top: 1px solid #dddddd;  line-height: 20px;  padding: 8px;  text-align: left;  vertical-align: top;"></td>
			<td style="background-color:transparent!important; border-top: 1px solid #dddddd;  line-height: 20px;  padding: 8px;  text-align: left;  vertical-align: top;"><% if $Top.CurrentOrderCustomerGroup.VatExluded %>
			  zzgl. 
			  <% else %>
			  inkl. 
			  <% end_if %>
			  MwSt.({$Page.CurrentOrderCustomerGroup.Vat}%):</span></td>
			<td style="background-color:transparent!important;"><% if $Basket.TotalPrice.CaPrice %>ca. <% end_if %>$Page.formattedNumber($Basket.TotalPrice.Vat) &euro;</td> 
		  </tr>
		  
		 </tbody>
        </table>
		<h3>Anmerkungen zur Bestellung</h3>
		$Basket.AdditionalNotes
				<h3>Lieferart</h3>
				<p>$Basket.DeliveryType.Title $Basket.VersandInfo.RAW</p>
				<h3>Zahlart</h3>
				<p>$Basket.PaymentMethod.Title</p>
		<h3>Angaben zum Auftraggeber</h3>
		
		 <div class="span4" style="margin-left:0;">
			<table class="table table-condensed">
				<thead>
				<tr>
				  <th>Kontaktdaten</th>
				  <th></th>
				  
				</tr>
			  </thead>
				<tbody>
					<tr><td width="120px">Firmenname:</td><td>$CheckoutAddress.Company</td></tr>
					<tr><td width="120px">Strasse:</td><td class="noBorder">$CheckoutAddress.Street</td></tr>
					<tr><td width="120px">PLZ:</td><td class="noBorder">$CheckoutAddress.ZIP</td></tr>
					<tr><td width="120px">Ort:</td><td class="noBorder">$CheckoutAddress.City</td></tr>
				</tbody>
			</table>
          </div>
          <div class="span4">
		  <table class="table table-condensed">
			<thead>
				<tr>
				  <th>Ansprechpartner</th>
				  <th></th>
				  
				</tr>
			  </thead>
				<tbody>
					<tr><td width="120px">Vorname:</td><td width="80%">$CheckoutAddress.FirstName</td></tr>
					<tr><td width="120px">Nachname:</td><td class="noBorder">$CheckoutAddress.Surname</td></tr>
					<tr><td width="120px">Telefon:</td><td class="noBorder">$CheckoutAddress.PhoneNumber</td></tr>
					<tr><td width="120px">Email:</td><td class="noBorder">$CheckoutAddress.Email</td></tr>
				</tbody>
			</table>
		</div>
		
$OrderConfig.EmailSignature
</div>