	<% include PageTitle_Dark %>
    <!-- Page Content-->
<div class="container pb-4 pb-sm-5">
      <!-- Categories grid-->
	<div class="<% if $HeaderImage %>spacer-white<% end_if %> bg-white">
		<section class="container mb-md-3 mt-2 mt-sm-5">
		  <div class="row">
			<div class="<% if $ContentObjects %>col-md-8<% else %> col-md-12<% end_if %> mb-4">
				<div class="pb-4 my-2 my-md-0 pb-md-5 text-center text-sm-left">
				  $Content
				   $Form
				</div>
			<div class="row">
			<% if $Children.Filter("ClassName","Schrattenholz\\Order\\ProductList") || $Children.Filter("ClassName","Schrattenholz\\Order\\Product")  %>
				<% loop $Children.Sort('Date','DESC') %>
				<!-- Catogory-->
				<div class="col-md-4 col-sm-6 mb-3">
				<div class="card product-card border-0">
				<% if not $Top.HideLabel %>
					<% if $OutOfStock %>
					<span class="badge badge-dark badge-shadow" wfd-id="218">Ausverkauft</span>
					<% end_if %>
				<% end_if %>
				<a class="d-block overflow-hidden rounded-lg" href="$Link">
					<img class="card-img" src="$DefaultImage.Fill(400,266).URL" alt="$DefaultImage.Filename">
				</a>
					<div class="card-body">
					<h2 class="h5">
					<a href="$Link">$MenuTitle.XML</a></h2>									
					<% if $ClassName=="Schrattenholz\\Order\\Product" %>
						<% if $CaPrice %>ca. <% end_if %>$Top.formattedNumber($KiloPrice.Price) &euro;/$Unit.Shortcode
					<% end_if %>				
					
					</div>
				</div>
				</div>
				<% end_loop %>
			<% else %>
				<div class="row mb-4" >
					<article class="card col-12  pl-0 pr-0 pl-sm-0 pr-sm-3">
					$NoEntryText 
					</article>
				</div>
			<% end_if %>
		  </div>
			</div>
			<% if $ContentObjects %>
			<div class="col-md-4 mb-4 align-self-start">

				$ContentObjects.Sort('SortID').First.renderIT

			</div>
			<% end_if %>
		  </div>
		</section>
	</div>
</div>



