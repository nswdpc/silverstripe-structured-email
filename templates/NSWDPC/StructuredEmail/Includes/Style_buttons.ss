/* Buttons ------------------------------ */

.button {
    background-color: $PrimaryColor;
    border-top: 10px solid $PrimaryColor;
    border-right: 18px solid $PrimaryColor;
    border-bottom: 10px solid $PrimaryColor;
    border-left: 18px solid $PrimaryColor;
    display: inline-block;
    color: $PrimaryTextColor;
    text-decoration: none;
    border-radius: 3px;
    box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
    -webkit-text-size-adjust: none;
    box-sizing: border-box;
}

.button--green {
    background-color: #22BC66;
    border-top: 10px solid #22BC66;
    border-right: 18px solid #22BC66;
    border-bottom: 10px solid #22BC66;
    border-left: 18px solid #22BC66;
}

.button--red {
    background-color: #FF6136;
    border-top: 10px solid #FF6136;
    border-right: 18px solid #FF6136;
    border-bottom: 10px solid #FF6136;
    border-left: 18px solid #FF6136;
}

@media only screen and (max-width: 500px) {
    .button {
        width: 100% !important;
        text-align: center !important;
    }
}
