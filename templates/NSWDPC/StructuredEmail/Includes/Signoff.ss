<table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td class="content-cell" align="center">

            <% if $Top.EmailCopyright %>
            <p class="f-fallback sub align-center" align="center">
                {$Top.EmailCopyright.XML}
            </p>
            <% end_if %>

            <% if $Top.EmailPhysical %>
            <p class="f-fallback sub align-center" align="center">
                {$Top.EmailPhysical.XML}
            </p>
            <% end_if %>

        </td>
    </tr>
</table>
