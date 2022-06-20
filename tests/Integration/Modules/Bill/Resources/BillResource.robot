*** Settings ***

Resource    ../../../Resources/Resources.robot
Resource    ../../../Modules/Account/Resources/AccountResource.robot
Resource    ../../../Modules/Bill/Resources/BillResource.robot
Resource    ../../../Resources/Cenarios.robot
Resource    ../../Category/Resources/CategoryResource.robot
Library     ../../../Dados/Account.py
Library     ../../../Dados/User.py
Library     ../../../Dados/Category.py
Library     ../../../Dados/Bill.py

*** Variables ***
${URL_BILL}    ${URL_BASE}/bill
***Keywords***

Insert Bill
    [Arguments]    ${BILL}  ${ACCOUNT_ID}  ${USER}
    ${response}    Request POST    ${URL_BILL}/account/${ACCOUNT_ID}    ${USER}    ${BILL}
    [Return]       ${response}

Insert Scenario
    [Arguments]    ${USER}
    &{CATEGORY}   Category.Dados Lanches
    ${response}   Insert Category     ${CATEGORY}   ${USER}
    ${CATEGORY_ID}    Set Variable    ${response.json()['id']} 

    &{ACCOUNT}    Account.Dados Xpto
    ${response}   Insert Account      ${ACCOUNT}    ${USER}
    ${ACCOUNT_ID}    Set Variable     ${response.json()['id']}

    &{IDS}    Create Dictionary   account_id=${ACCOUNT_ID}    category_id=${CATEGORY_ID}

    [Return]  ${IDS}