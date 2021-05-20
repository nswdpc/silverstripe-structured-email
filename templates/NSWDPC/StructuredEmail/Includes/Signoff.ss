<% with $Top.EmailDecorator %>
<table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td class="content-cell" align="center">

            <% if $Copyright %>
            <p class="f-fallback sub align-center" align="center">
                {$Copyright.XML}
            </p>
            <% end_if %>

            <% if $PhysicalAddress %>
            <p class="f-fallback sub align-center" align="center">
                {$PhysicalAddress}
            </p>
            <% end_if %>

        </td>
    </tr>
</table>
<% end_with %>
