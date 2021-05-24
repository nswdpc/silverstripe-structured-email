<!--[if !mso]><!-->
<!DOCTYPE html>
<html>
 <!--<![endif]-->
 <!--[if mso]>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<![endif]-->

    <head>
        <% include NSWDPC/StructuredEmail/Head %>
    </head>

    <body itemscope itemtype="http://schema.org/EmailMessage" bgcolor="{$Top.EmailDecorator.BodyBackgroundColor}">

        <!-- preheader -->
        <% include NSWDPC/StructuredEmail/PreHeader %>
        <!-- end preheader -->

        <table class="email-wrapper" width="<% if $Top.EmailDecorator.LayoutType == 'basic-full %>100%<% else %>570<% end_if %>" cellpadding="0" cellspacing="0" role="presentation">

            <tr>

                <td align="center">

                    <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">

                        <!-- masthead -->
                        <% include NSWDPC/StructuredEmail/Masthead %>
                        <!-- end masthead -->

                        <!-- body -->
                        <tr>

                            <td class="email-body" width="570" cellpadding="0" cellspacing="0">

                                <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">

                                    <% with $Top.EmailDecorator %>
                                        <% if $ContentLogo %>
                                        <tr>
                                            <td class="email-body_inner" align="center">
                                                <img class="email-body_logo" src="{$ContentLogo}" border="0">
                                            </td>
                                        </tr>
                                        <% end_if %>
                                    <% end_with %>

                                    <%-- Body content --%>
                                    <tr>

                                        <td class="content-cell">

                                            <div class="f-fallback">
