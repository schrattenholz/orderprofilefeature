    <!-- Page Title (Light)-->
    <div class="bg-secondary py-4">
      <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <% include BreadCrumbs Design=""%>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0">$MenuTitle.XML</h1>
        </div>
      </div>
    </div>
    <!-- Page Content-->
    <div class="container pb-4 pb-sm-5">
      <!-- Categories grid-->
      <div class="row pt-5">
		<% if $Children %>
			<% loop $Children.Sort('Date','DESC') %>
			<!-- Catogory-->
			<div class="col-md-4 col-sm-6 mb-3">
			<div class="card border-0">
			<a class="d-block overflow-hidden rounded-lg" href="shop-grid-ls.html">
				<img class="card-img" src="$DefaultImage.Fill(400,266).URL" alt="$DefaultImage.Filename">
			</a>
				<div class="card-body">
				<h2 class="h5">$MenuTitle.XML</h2>
				<% if $Children %>
				<ul class="list-unstyled font-size-sm mb-0">
				<% loop $Children.Limit(7) %>
					<li class="d-flex align-items-center justify-content-between"><a class="nav-link-style" href="$Link" alt="Zu $MenuTitel.XML wechseln"><i class="czi-arrow-right-circle mr-2"></i>$MenuTitle.XML</a><% if $Children %><span class="font-size-ms text-muted">$Children.Count</span><% end_if %></li>
				<% end_loop %>
					<li>...</li>
					<li>
					<hr>
					</li>
					<li class="d-flex align-items-center justify-content-between"><a class="nav-link-style" href="$Link" alt="Zu $MenuTitel.XML wechseln"><i class="czi-arrow-right-circle mr-2"></i>Kategorie anschauen</a>
				</ul>
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


