<% if $EmailMasthead %>
<tr>
    <td class="email-masthead" bgcolor="{$Top.EmailDecorator.PrimaryColor}" align="center">

        <% if $EmailMastheadLink %>
        <a href="{$Top.EmailMastheadLink.XML}" class="f-fallback email-masthead_name">
            {$Top.EmailMasthead.XML}
        </a>
        <% else %>
        <span class="f-fallback email-masthead_name">
            {$Top.EmailMasthead.XML}
        </span>
        <% end_if %>
    </td>
</tr>
<% end_if %>
