@media (prefers-color-scheme: dark) {

    body,
    .email-body,
    .email-body_inner,
    .email-content,
    .email-wrapper,
    .email-footer {
        background-color: {$DarkModeBackgroundColor} !important;
        color: {$DarkModeColor} !important;
    }

    .email-masthead {
        background-color: {$DarkModeBackgroundSubColor} !important;
        color: {$DarkModeColor} !important;
    }

    p,
    ul,
    ol,
    blockquote,
    h1,
    h2,
    h3,
    span,
    a,
    .email-masthead a.email-masthead_name span,
    .purchase_item {
        color: {$DarkModeColor} !important;
    }

    .attributes_content,
    .discount {
        background-color: {$DarkModeBackgroundColor} !important;
        color : {$DarkModeColor} !important;
    }

    .email-body .button {
        background-color: {$DarkModeButtonColor} !important;
        border-color: {$DarkModeButtonColor} !important;
        color: {$DarkModeColor} !important;
    }

    :root {
        color-scheme: light dark;
        supported-color-schemes: light dark;
    }

}
