** Settings **

Library               Collections
Library               ../../Dados/User.py
Library               ../../Dados/Account.py
Library               ../../Dados/Bill.py
Library               ../../Dados/Creditcard.py
Library               DateTime
Resource              ../../Resources/Resources.robot
Resource              ../../Resources/Cenarios.robot
Resource              ../Account/Resources/AccountResource.robot
Resource              ../Bill/Resources/BillResource.robot
Resource              Resources/InvoiceResource.robot
Default Tags          Invoice

** Test Cases **

Caso de teste 01 - Buscar uma fatura
    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}

    ${DADOS_CENARIOS}  Insert Scenario    ${USER}

    ${CREDITCARD}    Dados Nubank
    ${response}      Insert Creditcard    ${DADOS_CENARIOS.account_id}    ${CREDITCARD}    ${USER}    
    ${CREDITCARD_ID}    Set Variable      ${response.json()['id']}

    &{BILL}        Create Bill With Creditcard    ${CREDITCARD_ID}    ${DADOS_CENARIOS.category_id}
    ${response}    Insert Bill    ${BILL}    ${DADOS_CENARIOS.account_id}    ${USER}

    ${response}    Get Invoices From Creditcard    ${CREDITCARD_ID}    ${USER}
    ${INVOICE}     Set To Dictionary      ${response.json()[0]}

    ${response}    Get Invoice    ${INVOICE['id']}    ${USER}

    Dictionary Should Contain Key     ${response.json()}   bills
    Dictionary Should Contain Key     ${response.json()}   start_date
    Dictionary Should Contain Key     ${response.json()}   end_date
    Dictionary Should Contain Key     ${response.json()}   due_date
    Dictionary Should Contain Key     ${response.json()}   pay_day
    Dictionary Should Contain Key     ${response.json()}   month_reference
    
    Dictionary Should Contain Item     ${response.json()}   start_date    2021-10-01T00:00:00.000000Z
    Dictionary Should Contain Item     ${response.json()}   end_date    2021-10-30T00:00:00.000000Z
    Dictionary Should Contain Item     ${response.json()}   due_date    2021-11-06T00:00:00.000000Z
    Dictionary Should Contain Item     ${response.json()}   month_reference    11
    Dictionary Should Contain Item     ${response.json()}   credit_card_id    ${CREDITCARD_ID}
    
Caso de teste 02 - Pagar uma fatura

    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}

    ${DADOS_CENARIOS}  Insert Scenario    ${USER}

    ${CREDITCARD}    Dados Nubank
    ${response}    Insert Creditcard    ${DADOS_CENARIOS.account_id}    ${CREDITCARD}    ${USER}    
    ${CREDITCARD_ID}    Set Variable    ${response.json()['id']}

    &{BILL}        Create Bill With Creditcard    ${CREDITCARD_ID}    ${DADOS_CENARIOS.category_id}
    ${response}    Insert Bill    ${BILL}    ${DADOS_CENARIOS.account_id}    ${USER}

    ${response}    Get Invoices From Creditcard    ${CREDITCARD_ID}    ${USER}
    ${INVOICE}     Set To Dictionary      ${response.json()[0]}

    ${response}      Pay Invoice    ${INVOICE['id']}    ${USER}
    ${ATUAL_DATE}    Get Current Date    result_format=%Y-%m-%d
    ${PAY_DAY}       Convert Date     ${response.json()['pay_day']}  date_format=%Y-%m-%dT00:00:00.000000Z    result_format=%Y-%m-%d
    
    Dictionary Should Contain Key     ${response.json()}   start_date
    Dictionary Should Contain Key     ${response.json()}   end_date
    Dictionary Should Contain Key     ${response.json()}   due_date
    Dictionary Should Contain Key     ${response.json()}   pay_day
    Dictionary Should Contain Key     ${response.json()}   month_reference
    
    Dictionary Should Contain Item     ${response.json()}   start_date    2021-10-01T00:00:00.000000Z
    Dictionary Should Contain Item     ${response.json()}   end_date      2021-10-30T00:00:00.000000Z
    Dictionary Should Contain Item     ${response.json()}   due_date      2021-11-06T00:00:00.000000Z
    Dictionary Should Contain Item     ${response.json()}   month_reference    11
    Should Be Equal                    ${PAY_DAY}    ${ATUAL_DATE}
    Dictionary Should Contain Item     ${response.json()}   credit_card_id    ${CREDITCARD_ID}
    