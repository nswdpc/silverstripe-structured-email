<% with $Top.EmailDecorator %>
    <% if $Masthead %>
    <tr>
        <td class="email-masthead" align="center">
            <div class="f-fallback email-masthead_name">
            <% if $MastheadLink %>
                <% if $MastheadLogo %>
                <img class="email-masthead_logo" src="{$MastheadLogo}" border="0" height="48">
                <% end_if %>
                <p><a href="{$MastheadLink.XML}"><span>{$Masthead.XML}<% if $Top.EmailReason %> - {$Top.EmailReason.XML}<% end_if %></span></a></p>
            <% else %>
                <% if $MastheadLogo %>
                <img class="email-masthead_logo" src="{$MastheadLogo}" border="0" height="48">
                <% end_if %>
                <p><span>{$Masthead.XML}<% if $Top.EmailReason %> - {$Top.EmailReason.XML}<% end_if %></span></p>
            <% end_if %>
            </div>
        </td>
    </tr>
    <% else %>
        <!-- no masthead -->
    <% end_if %>
<% end_with %>
