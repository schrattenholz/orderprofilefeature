<style>
<% if $MainImage %>
.headerbg{
	background-image:url("$MainImage.ScaleWidth(2000).URL");
	background-position:center center;
	background-size:cover;
}
.headerbg .container{
height:180px;
}
<% end_if %>
</style>
<!-- Page Title (Shop)-->

<div class="bg-dark pt-4 headerbg py-5">
	<div class="container d-lg-flex justify-content-between py-2 py-lg-3">
		<div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
		 <% include BreadCrumbsMenu Design="-light" %>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
		<% include Schrattenholz\OrderProfileFeature\Includes\ProductTitle %>
		</div>
	</div>
</div>
    <!-- Page Content-->
	
<div class="container" id="p_$ID" data-productid="$ID" data-producttitle="$Title">
      <!-- Gallery + details-->
      <div class="bg-white  px-4 py-3 mb-5">
        <div class="px-lg-3">
          <div class="row">
            <!-- Product gallery-->
			
            <div class="col-lg-5 pr-lg-0 pt-lg-4">
              <div class="cz-product-gallery">
                <div class=" order-sm-2">
				<% if $ProductImages %>
					<% loop $ProductImages.Sort('SortOrder') %>
					  <div class=" <% if $First %>active<% end_if %>" id="ProductImage_$ID">
					  <% if $Top.ShowQualityLabel %>
					  <div class="picLabel"><img src="/_resources/themes/sehnenmuehle/img/bioland.png" /></div>
					  <% end_if %>
						  <img class="" src="$ScaleWidth(600).URL"  alt="$Filename">
						
					  </div>
					<% end_loop %>
				 <% else %>
				 <div class=" <% if $First %>active<% end_if %>" id="ProductImage_$ID">
				 <% if $Top.ShowQualityLabel %>
				 <div class="picLabel"><img src="/_resources/themes/sehnenmuehle/img/bioland.png" /></div>
				   <% end_if %>
				      <img class="cz-image" src="$CoverImage.ScaleWidth(600).URL"  alt="$CoverImage.URL">
                    <!--<div class="cz-image-zoom-pane"></div>-->
                  </div>
                
				<% end_if %>
				</div>
                <div class="cz-thumblist order-sm-1">
				<% if $ProductImages.Count>1 %>
				<% loop $ProductImages.Sort('SortOrder') %>
				<a class="cz-thumblist-item <% if $First %>active<% end_if %>" href="#ProductImage_$ID"><img src="$Fill(100,100).URL" alt="$Filename"></a>
				<% end_loop %>
				<% end_if %>
				<!--
				<a class="cz-thumblist-item video-item" href="https://www.youtube.com/watch?v=1vrXpMLLK14">
					<div class="cz-thumblist-item-text">
						<i class="czi-video"></i>Video
					</div>
				</a>
				-->
				</div>
              </div>
			 
                
			
				<% if not $ProductImages %>
					<img src="$Fill(100,100).URL" alt="$Filename">
				<% end_if %>
                <!-- Sharing-->
				<% include Schrattenholz\OrderProfileFeature\Includes\Product_Sharing %>
            </div>
			
                        <!-- Product details-->
			<div class="col-lg-6 pt-4 pt-lg-4 offset-lg-1">
				<div class="ml-auto pb-3">

					<div classs="mb-3">
						<span id="single_price" class="h3 font-weight-normal text-accent mr-1">
						<% include Schrattenholz\OrderProfileFeature\Includes\ProductPrice %>
						<% if not $ShowBasePrice %>*<% end_if %>
						</span>
						<% if $ShowBasePrice %>
							<span>($formattedNumber($KiloPrice.Price) &euro;/$Unit.Shortcode)</span>*
						<% end_if %>
						<span class="productbadge">
							$getProductBadge(0)
						</span>

					</div>

					<% include Schrattenholz\OrderProfileFeature\Includes\Product_Shopping %>
				</div>
                
				
              </div>
            </div>
          </div>
		  			<div class="row">
				<div class="col-12 " %>
				 <!-- Product panels-->
					<% include Schrattenholz\OrderProfileFeature\Includes\Product_Info %>
				</div>
			</div>

        </div>
      </div>
	  

	  
	  </div>
	  		<div class="bg-light p-5">
			<div class="container">
	  			<div class="row">
				<div class="col-12 font-size-xs text-justify" >
				 <!-- Product panels-->
					<% include Schrattenholz\OrderProfileFeature\Includes\Product_Law %>
				</div>
			</div>
		</div>
	  
    </div>
<% include Schrattenholz\OrderProfileFeature\Layout\Product_JavaScript %>