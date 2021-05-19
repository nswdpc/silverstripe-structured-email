<style type="text/css" media="all">
    {$Top.EmailDecorator}
</style>

<% if $Top.EmailDecorator.LayoutType == 'basic-full' %>
    <% include NSWDPC/StructuredEmail/Style_basic_full %>
<% else_if $Top.EmailDecorator.LayoutType == 'basic' %>
    <% include NSWDPC/StructuredEmail/Style_basic %>
<% else %>
    <% include NSWDPC/StructuredEmail/Style_plain %>
<% end_if %>
