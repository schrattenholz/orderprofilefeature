<!DOCTYPE html>
<!--[if !IE]><!-->
<html lang="$ContentLocale">
<!--<![endif]-->
<!--[if IE 6 ]><html lang="$ContentLocale" class="ie ie6"><![endif]-->
<!--[if IE 7 ]><html lang="$ContentLocale" class="ie ie7"><![endif]-->
<!--[if IE 8 ]><html lang="$ContentLocale" class="ie ie8"><![endif]-->
<% include Head %>
<body class="class="toolbar-enabled $ClassName.ShortName<% if not $Menu(2) %> no-sidebar<% end_if %>" <% if $i18nScriptDirection %>dir="$i18nScriptDirection"<% end_if %>>
<% include Modal_SignIn %>
<% include Modal_Info %>
<% include HeaderInactive %>
$Layout
<% include Footer %>
<% include Schrattenholz\OrderProfileFeature\Includes\ShopFields %>
<% include HandheldToolbar %>
<% include ToTopButton %>
<% include Toasts %>
<% require themedJavascript('vendor.min') %>
<% include Javascript %>
<% include OrderIncludes %>
</body>
</html>
