/* Buttons ------------------------------ */

.email-body .button {
    background-color: {$PrimaryButtonColor} !important;
    border-width: 14px 28px;
    border-color: {$PrimaryButtonColor} !important;
    border-style: solid;
    display: inline-block;
    color: {$PrimaryButtonTextColor} !important;
    text-decoration: none;
    border-radius: 4px;
    box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
    -webkit-text-size-adjust: none;
    box-sizing: border-box;
}

.email-body .button--green {
    background-color: #22BC66 !important;
    border-color: #22BC66 !important;
    color: #FFFFFF !important;
}

.email-body .button--red {
    background-color: #FF6136 !important;
    border-color: #FF6136 !important;
    color: #FFFFFF !important;
}

@media only screen and (max-width: 500px) {
    .email-body .button {
        width: 100% !important;
        text-align: center !important;
    }
}
