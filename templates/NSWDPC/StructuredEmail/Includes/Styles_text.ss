a {
    color: $PrimaryColor;
}

a img {
    border: none;
}

td {
    word-break: break-word;
}

p {
    color: $Color;
}

p.sub {
    color: $SubColor;
}

.preheader {
    display: none !important;
    visibility: hidden;
    mso-hide: all;
    font-size: 1px;
    line-height: 1px;
    max-height: 0;
    max-width: 0;
    opacity: 0;
    overflow: hidden;
}

/* Type ------------------------------ */

body,
td,
th {
    font-family: $FontFamily;
}

h1 {
    margin-top: 0;
    color: #333333;
    font-size: 22px;
    font-weight: bold;
    text-align: left;
}

h2 {
    margin-top: 0;
    color: $HeadingColor;
    font-size: 16px;
    font-weight: bold;
    text-align: left;
}

h3 {
    margin-top: 0;
    color: $HeadingColor;
    font-size: 14px;
    font-weight: bold;
    text-align: left;
}

td,
th {
    font-size: 16px;
}

p,
ul,
ol,
blockquote {
    margin: .4em 0 1.1875em;
    font-size: 16px;
    line-height: 1.625;
}

p.sub {
    font-size: 13px;
}

/* Utilities ------------------------------ */

.align-right {
    text-align: right;
}

.align-left {
    text-align: left;
}

.align-center {
    text-align: center;
}

<% include NSWDPC/StructuredEmail/Style_buttons %>

/* Attribute list ------------------------------ */

.attributes {
    margin: 0 0 21px;
}

.attributes_content {
    background-color: #F4F4F7;
    padding: 16px;
}

.attributes_item {
    padding: 0;
}

/* Related Items ------------------------------ */

.related {
    width: 100%;
    margin: 0;
    padding: 25px 0 0 0;
    -premailer-width: 100%;
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
}

.related_item {
    padding: 10px 0;
    color: #CBCCCF;
    font-size: 15px;
    line-height: 18px;
}

.related_item-title {
    display: block;
    margin: .5em 0 0;
}

.related_item-thumb {
    display: block;
    padding-bottom: 10px;
}

.related_heading {
    border-top: 1px solid #CBCCCF;
    text-align: center;
    padding: 25px 0 10px;
}

/* Discount Code ------------------------------ */

.discount {
    width: 100%;
    margin: 0;
    padding: 24px;
    -premailer-width: 100%;
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    background-color: #F4F4F7;
    border: 2px dashed #CBCCCF;
}

.discount_heading {
    text-align: center;
}

.discount_body {
    text-align: center;
    font-size: 15px;
}

/* Social Icons ------------------------------ */

.social {
    width: auto;
}

.social td {
    padding: 0;
    width: auto;
}

.social_icon {
    height: 20px;
    margin: 0 8px 10px 8px;
    padding: 0;
}

/* Data table ------------------------------ */

.purchase {
    width: 100%;
    margin: 0;
    padding: 35px 0;
    -premailer-width: 100%;
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
}

.purchase_content {
    width: 100%;
    margin: 0;
    padding: 25px 0 0 0;
    -premailer-width: 100%;
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
}

.purchase_item {
    padding: 10px 0;
    color: #51545E;
    font-size: 15px;
    line-height: 18px;
}

.purchase_heading {
    padding-bottom: 8px;
    border-bottom: 1px solid #EAEAEC;
}

.purchase_heading p {
    margin: 0;
    color: #85878E;
    font-size: 12px;
}

.purchase_footer {
    padding-top: 15px;
    border-top: 1px solid #EAEAEC;
}

.purchase_total {
    margin: 0;
    text-align: right;
    font-weight: bold;
    color: #333333;
}

.purchase_total--label {
    padding: 0 15px 0 0;
}
