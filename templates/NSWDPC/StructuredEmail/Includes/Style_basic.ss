<style type="text/css" rel="stylesheet" media="all">
    /* Base ------------------------------ */

    $Me

    body {
        width: 100% !important;
        height: 100%;
        margin: 0;
        -webkit-text-size-adjust: none;
    }

    <% include NSWDPC/StructuredEmail/Styles_text %>

    .email-wrapper {
        width: 100%;
        margin: 0;
        padding: 0;
        -premailer-width: 100%;
        -premailer-cellpadding: 0;
        -premailer-cellspacing: 0;
        background-color: $BodyBackgroundColor;
    }

    .email-content {
        width: 100%;
        margin: 0;
        padding: 0;
        -premailer-width: 100%;
        -premailer-cellpadding: 0;
        -premailer-cellspacing: 0;
    }

    <% include NSWDPC/StructuredEmail/Styles_masthead %>

    /* Body ------------------------------ */

    .email-body {
        width: 100%;
        margin: 0;
        padding: 0;
        -premailer-width: 100%;
        -premailer-cellpadding: 0;
        -premailer-cellspacing: 0;
    }

    .email-body_inner {
        width: 570px;
        margin: 0 auto;
        padding: 0;
        -premailer-width: 570px;
        -premailer-cellpadding: 0;
        -premailer-cellspacing: 0;
        background-color: #FFFFFF;
    }

    .email-footer {
        width: 570px;
        margin: 0 auto;
        padding: 0;
        -premailer-width: 570px;
        -premailer-cellpadding: 0;
        -premailer-cellspacing: 0;
        text-align: center;
    }

    .email-footer p {
        color: #A8AAAF;
    }

    .body-action {
        width: 100%;
        margin: 30px auto;
        padding: 0;
        -premailer-width: 100%;
        -premailer-cellpadding: 0;
        -premailer-cellspacing: 0;
        text-align: center;
    }

    .body-sub {
        margin-top: 25px;
        padding-top: 25px;
        border-top: 1px solid #EAEAEC;
    }

    .content-cell {
        padding: 45px;
    }

    /*Media Queries ------------------------------ */

    @media only screen and (max-width: 600px) {

        .email-body_inner,
        .email-footer {
            width: 100% !important;
        }
    }

    <% include NSWDPC/StructuredEmail/Styles_darkmode %>

</style>
