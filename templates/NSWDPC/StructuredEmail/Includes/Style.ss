<% with $Top.EmailDecorator %>
<% if $Top.EmailDecorator.LayoutType == 'basic-full' %>
    <% include NSWDPC/StructuredEmail/Style_basic_full %>
<% else_if $Top.EmailDecorator.LayoutType == 'basic' %>
    <% include NSWDPC/StructuredEmail/Style_basic %>
<% else %>
    <% include NSWDPC/StructuredEmail/Style_plain %>
<% end_if %>
<!--[if mso]>
<style type="text/css">
    .f-fallback  {
        font-family: {$FontFamily.RAW};
    }
</style>
<![endif]-->
<% end_with %>
