/* Buttons ------------------------------ */

.button {
    background-color: $PrimaryColor;
    border-width: 14px 18px;
    border-color: $PrimaryColor;
    border-style: solid;
    display: inline-block;
    color: $PrimaryTextColor;
    text-decoration: none;
    border-radius: 4px;
    box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
    -webkit-text-size-adjust: none;
    box-sizing: border-box;
}

.button--green {
    background-color: #22BC66;
    border-color: #22BC66;
    color: #FFFFFF !important;
}

.button--red {
    background-color: #FF6136;
    border-color: #FF6136;
    color: #FFFFFF !important;
}

@media only screen and (max-width: 500px) {
    .button {
        width: 100% !important;
        text-align: center !important;
    }
}
