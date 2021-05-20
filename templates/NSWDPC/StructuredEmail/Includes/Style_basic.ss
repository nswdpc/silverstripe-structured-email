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

    /* Masthead ----------------------- */

    .email-masthead {
        padding: 25px 0;
        text-align: center;
        background-color : $PrimaryColor;
        color: $PrimaryTextColor;
    }

    .email-masthead_logo {
        width: 94px;
    }

    .email-masthead_name {
        font-size: 16px;
        font-weight: bold;
        color: $PrimaryTextColor;
        text-decoration: none;
    }

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

    @media (prefers-color-scheme: dark) {

        body,
        .email-body,
        .email-body_inner,
        .email-content,
        .email-wrapper,
        .email-masthead,
        .email-footer {
            background-color: #333333 !important;
            color: #FFF !important;
        }

        p,
        ul,
        ol,
        blockquote,
        h1,
        h2,
        h3,
        span,
        .purchase_item {
            color: #FFF !important;
        }

        .attributes_content,
        .discount {
            background-color: #222 !important;
        }

    }

    :root {
        color-scheme: light dark;
        supported-color-schemes: light dark;
    }
</style>
