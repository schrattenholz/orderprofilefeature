<!DOCTYPE html>

<html lang="$ContentLocale">
<!--<![endif]-->
<!--[if IE 6 ]><html lang="$ContentLocale" class="ie ie6"><![endif]-->
<!--[if IE 7 ]><html lang="$ContentLocale" class="ie ie7"><![endif]-->
<!--[if IE 8 ]><html lang="$ContentLocale" class="ie ie8"><![endif]-->
<% include Head %>
<body class="$ClassName" <% if $i18nScriptDirection %>dir="$i18nScriptDirection"<% end_if %>>
<% include Header %>
<% include CallToActions %>
<% if $HasRevoSlider %>
	<% include RevoSlider %>
<% else_if $MainImage %>
	<div class="container-fluid headerImage px-0">
		<div class="inner" style="background-image:url(<% if $MainImage %>$MainImage.URL<% else_if $Parent.MainImage %>$Parent.MainImage.URL<% else %>$ThemeDir/images/default.jpg<% end_if %>);"></div>
		<div class="text<% if $MainImage && $DarkImage %> darkBg<% else_if $Parent.MainImage && $DarkImage %>darkBg <% end_if %> ">
			<% if not $NoHeadline %>
				<h1><% if $Headline %>$Headline<% else %>$MenuTitle.XML<% end_if %></h1>
			<% end_if %>
			<% include BreadCrumbs %>
		</div>
	</div>
<% else %>
	<div class="container-fluid headerImage px-0" style="position:relative;">
		<div class="inner" style="background-image:url($ThemeDir/images/default.jpg);"></div>
		<div class="text"<% if $DarkImage %> darkBg<% end_if %>">
		<% if not $NoHeadline %>
			<h1><% if $Headline %>$Headline<% else %>$MenuTitle.XML<% end_if %></h1>
		<% end_if %>
		<% include BreadCrumbs %>

		</div>
	</div>
<% end_if %>
<!-- featuresList -->

$Layout
<% include Footer %>

<% include Javascript %>
<% include OrderIncludes %>
</body>
</html>
