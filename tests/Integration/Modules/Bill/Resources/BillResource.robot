*** Settings ***

Resource    ../../../Resources/Resources.robot
Resource    ../../../Modules/Account/Resources/AccountResource.robot
Resource    ../../../Modules/Bill/Resources/BillResource.robot
Resource    ../../../Resources/Cenarios.robot
Resource    ../../Category/Resources/CategoryResource.robot
Resource    ../../Creditcard/Resources/CreditcardResource.robot
Library     ../../../Dados/Account.py
Library     ../../../Dados/User.py
Library     ../../../Dados/Category.py
Library     ../../../Dados/Bill.py
Library    ../../../Dados/Creditcard.py

*** Variables ***
${URL_BILL}    ${URL_BASE}/bill
***Keywords***

Insert Bill
    [Arguments]    ${BILL}  ${ACCOUNT_ID}  ${USER}  ${PORTION}=1  ${CREDITCARD}=False

    ${IDS}        Insert Scenario    ${USER}
    

    IF  ${CREDITCARD} == True
        ${CREDITCARD_ID}    Insert CreditCard Scenario    ${IDS}    ${USER}
        ${BILL.credit_card_id}  Set Variable  ${CREDITCARD_ID}
    END

    ${BILL.portion}    Set Variable  ${PORTION}
    ${response}    Request POST    ${URL_BILL}/account/${ACCOUNT_ID}    ${USER}    ${BILL}
    [Return]       ${response}

Get A Bill
    [Arguments]    ${BILL_ID}  ${USER}
    ${response}    Request GET    ${URL_BILL}/${BILL_ID}   ${USER}
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

Insert CreditCard Scenario
    [Arguments]    ${IDS}    ${USER}
    &{CREDITCARD}    Dados Nubank
    ${response}     Insert Creditcard    ${IDS.account_id}    ${CREDITCARD}    ${USER}
    [Return]     ${response.json()['id']}