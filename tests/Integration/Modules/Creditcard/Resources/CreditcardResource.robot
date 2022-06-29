*** Settings ***

Resource    ../../../Resources/Resources.robot

*** Variables ***
${URL_CREDITCARD}    ${URL_BASE}/creditcard
***Keywords***

Insert Creditcard
    [Arguments]    ${ACCOUNT_ID}  ${CREDITCARD}  ${USER}
    ${response}    Request POST    ${URL_CREDITCARD}/account/${ACCOUNT_ID}    ${USER}    ${CREDITCARD}
    [Return]       ${response}

Update Creditcard
    [Arguments]    ${CREDITCARD_ID}  ${CREDITCARD}  ${USER}
    ${response}    Request PUT    ${URL_CREDITCARD}/${CREDITCARD_ID}    ${USER}    ${CREDITCARD}
    [Return]       ${response}

Delete Creditcard
    [Arguments]    ${CREDITCARD_ID}  ${USER}
    ${response}    Request DELETE    ${URL_CREDITCARD}/${CREDITCARD_ID}    ${USER}
    [Return]       ${response}

Get A Creditcard
    [Arguments]    ${CREDITCARD_ID}  ${USER}
    ${response}    Request GET    ${URL_CREDITCARD}/${CREDITCARD_ID}    ${USER}
    [Return]       ${response}

Get All Creditcard
    [Arguments]    ${ACCOUNT_ID}  ${USER}
    ${response}    Request GET    ${URL_CREDITCARD}/account/${ACCOUNT_ID}    ${USER}
    [Return]       ${response}

Get Invoices From Creditcard
    [Arguments]    ${CREDITCARD_ID}  ${USER}
    ${response}    Request GET    ${URL_CREDITCARD}/${CREDITCARD_ID}/invoices    ${USER}
    [Return]       ${response}