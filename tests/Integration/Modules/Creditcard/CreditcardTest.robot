** Settings **

Library               Collections
Library               ../../Dados/User.py
Library               ../../Dados/Account.py
Library               ../../Dados/Bill.py
Library               ../../Dados/Creditcard.py
Resource              ../../Resources/Resources.robot
Resource              ../../Resources/Cenarios.robot
Resource              Resources/CreditcardResource.robot
Resource              ../Account/Resources/AccountResource.robot
Resource    ../Bill/Resources/BillResource.robot
Default Tags    Creditcard
** Test Cases **

Caso de teste 01 - Inserir um cartão de crédito
    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}

    ${response}    Insert Account    ${ACCOUNT}    ${USER}

    &{CREDITCARD}  Dados Nubank

    ${response}    Insert Creditcard    ${response.json()['id']}    ${CREDITCARD}    ${USER}

    Dictionary Should Contain Item     ${response.json()}   name    ${CREDITCARD.name}
    Dictionary Should Contain Item     ${response.json()}   due_day    ${CREDITCARD.due_day}
    Dictionary Should Contain Item     ${response.json()}   close_day    ${CREDITCARD.close_day}

Caso de teste 02 - Alterar um cartão de crédito
    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}

    ${response}    Insert Account    ${ACCOUNT}    ${USER}

    &{CREDITCARD}  Dados Nubank

    ${response}    Insert Creditcard    ${response.json()['id']}    ${CREDITCARD}    ${USER}
    
    ${CREDITCARD_ID}    Set Variable    ${response.json()['id']}
    ${CREDITCARD.name}  Set Variable    Itaú
    
    ${response}    Update Creditcard    ${CREDITCARD_ID}    ${CREDITCARD}    ${USER}

    Dictionary Should Contain Item     ${response.json()}   name    ${CREDITCARD.name}
    Dictionary Should Contain Item     ${response.json()}   due_day    ${CREDITCARD.due_day}
    Dictionary Should Contain Item     ${response.json()}   close_day    ${CREDITCARD.close_day}

Caso de teste 03 - Excluir um cartão de crédito
    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}

    ${response}    Insert Account    ${ACCOUNT}    ${USER}

    &{CREDITCARD}  Dados Nubank

    ${response}    Insert Creditcard    ${response.json()['id']}    ${CREDITCARD}    ${USER}
    
    ${CREDITCARD_ID}    Set Variable    ${response.json()['id']}
    
    ${response}    Delete Creditcard    ${CREDITCARD_ID}    ${USER}

    Should Be Equal As Integers    ${response.json()}    1

Caso de teste 04 - Buscar um cartão de crédito
    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}

    ${response}    Insert Account    ${ACCOUNT}    ${USER}

    &{CREDITCARD}  Dados Nubank

    ${response}    Insert Creditcard    ${response.json()['id']}    ${CREDITCARD}    ${USER}
    
    ${CREDITCARD_ID}    Set Variable    ${response.json()['id']}
    
    ${response}    Get A Creditcard    ${CREDITCARD_ID}    ${USER}

    Dictionary Should Contain Item     ${response.json()}   name    ${CREDITCARD.name}
    Dictionary Should Contain Item     ${response.json()}   due_day    ${CREDITCARD.due_day}
    Dictionary Should Contain Item     ${response.json()}   close_day    ${CREDITCARD.close_day}

Caso de teste 05 - Buscar todos cartão de crédito
    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}

    ${response}    Insert Account    ${ACCOUNT}    ${USER}

    &{CREDITCARD}  Dados Nubank
    ${ACCOUNT_ID}    Set Variable     ${response.json()['id']}

    ${response}    Insert Creditcard    ${response.json()['id']}    ${CREDITCARD}    ${USER}
    ${response}    Insert Creditcard    ${response.json()['id']}    ${CREDITCARD}    ${USER}
    
   
    ${response}    Get All Creditcard    ${ACCOUNT_ID}    ${USER}

    FOR    ${item}    IN    @{response.json()}
        ${CREDITCARD_ITEM}    Set To Dictionary    ${item}
        Dictionary Should Contain Item     ${CREDITCARD_ITEM}   name    ${CREDITCARD.name}
        Dictionary Should Contain Item     ${CREDITCARD_ITEM}   due_day    ${CREDITCARD.due_day}
        Dictionary Should Contain Item     ${CREDITCARD_ITEM}   close_day    ${CREDITCARD.close_day}
    END
    
Caso de teste 06 - Listar faturas
    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}

    ${DADOS_CENARIOS}  Insert Scenario    ${USER}
    ${CREDITCARD}    Dados Nubank

    ${response}    Insert Creditcard    ${DADOS_CENARIOS.account_id}    ${CREDITCARD}    ${USER}    
    ${CREDITCARD_ID}    Set Variable    ${response.json()['id']}

    &{BILL}    Create Bill With Creditcard    ${CREDITCARD_ID}    ${DADOS_CENARIOS.category_id}
    
    ${response}    Insert Bill    ${BILL}    ${DADOS_CENARIOS.account_id}    ${USER}

    ${response}    Get Invoices From Creditcard    ${CREDITCARD_ID}    ${USER}

    FOR    ${item}    IN    @{response.json()}
        ${INVOICE_ITEM}    Set To Dictionary    ${item}
        Dictionary Should Contain Key     ${INVOICE_ITEM}   bills
        Dictionary Should Contain Key     ${INVOICE_ITEM}   start_date
        Dictionary Should Contain Key     ${INVOICE_ITEM}   end_date
        Dictionary Should Contain Key     ${INVOICE_ITEM}   due_date
        Dictionary Should Contain Key     ${INVOICE_ITEM}   pay_day
        Dictionary Should Contain Key     ${INVOICE_ITEM}   month_reference
        
        Dictionary Should Contain Item     ${INVOICE_ITEM}   start_date    2021-10-01T00:00:00.000000Z
        Dictionary Should Contain Item     ${INVOICE_ITEM}   end_date    2021-10-30T00:00:00.000000Z
        Dictionary Should Contain Item     ${INVOICE_ITEM}   due_date    2021-11-06T00:00:00.000000Z
        Dictionary Should Contain Item     ${INVOICE_ITEM}   month_reference    11
        Dictionary Should Contain Item     ${INVOICE_ITEM}   credit_card_id    ${CREDITCARD_ID}
    END