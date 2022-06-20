*** Settings ***

Resource    ../../../Resources/Resources.robot

*** Variables ***
${URL_CREDITCARD}    ${URL_BASE}/creditcard
***Keywords***

Insert Creditcard
    [Arguments]    ${ACCOUNT_ID}  ${CREDITCARD}  ${USER}
    ${response}    Request POST    ${URL_CREDITCARD}/account/${ACCOUNT_ID}    ${USER}    ${CREDITCARD}
    [Return]       ${response}