<p>
<%t StructuredEmail.THANKS 'Thanks '%>
<br>
<% if $SiteConfig %>
<br>
{$SiteConfig.Title}
<% end_if %>
<% with $Top.EmailDecorator %>
<% if $SignOffLink %>
<br>
{$SignOffLink.XML}
<% end_if %>
<% end_with %>
</p>
