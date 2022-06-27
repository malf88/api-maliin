*** Settings ***

Resource    ../../../Resources/Resources.robot

*** Variables ***
${URL_INVOICE}    ${URL_BASE}/invoice
***Keywords***

Get Invoice
    [Arguments]    ${INVOICE_ID}  ${USER}
    ${response}    Request GET    ${URL_INVOICE}/${INVOICE_ID}    ${USER}
    [Return]       ${response}

Pay Invoice
    [Arguments]    ${INVOICE_ID}  ${USER}
    ${response}    Request PATCH    ${URL_INVOICE}/pay/${INVOICE_ID}    ${USER}
    [Return]       ${response}