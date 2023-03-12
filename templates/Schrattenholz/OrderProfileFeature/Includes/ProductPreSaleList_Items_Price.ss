<div class="d-flex justify-content-between">
	<div class="product-price">
		<span class=""><% if $CaPrice %>ca. <% end_if %> $formattedNumber($PriceObject.Brutto) &euro;
		
		</span>
		<span  class="font-weight-normal">($formattedNumber($Product.KiloPrice.Price) &euro;/$Product.Unit.Shortcode)</span>**
	</div>
	<div class="star-rating">
	<!--
	<i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star-filled active"></i><i class="sr-star czi-star"></i>
	-->
	</div>
</div>