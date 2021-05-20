<% with $Top.EmailDecorator %>
    <% if $Masthead %>
    <tr>
        <td class="email-masthead" bgcolor="{$PrimaryColor}" align="center">

            <% if $MastheadLink %>
            <a href="{$MastheadLink.XML}" class="f-fallback email-masthead_name">
                <% if $MastheadLogo %>
                <img class="email-masthead_logo" src="{$MastheadLogo}" width="90" height="90" border="0">
                <% end_if %>
                <span>{$Masthead.XML}</span>
            </a>
            <% else %>
            <span class="f-fallback email-masthead_name">
                <% if $MastheadLogo %>
                <img class="email-masthead_logo" src="{$MastheadLogo}" width="90" height="90" border="0">
                <% end_if %>
                <span>{$Masthead.XML}</span>
            </span>
            <% end_if %>
        </td>
    </tr>
    <% else %>
        <!-- no masthead -->
    <% end_if %>
<% end_with %>
