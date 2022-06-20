*** Settings ***
Resource    ../../Modules/Account/Resources/AccountResource.robot
Resource    ../../Modules/Bill/Resources/BillResource.robot
Resource    ../../Resources/Cenarios.robot
Resource    ../Category/Resources/CategoryResource.robot
Resource    ../Creditcard/Resources/CreditcardResource.robot
Library     ../../Dados/Account.py
Library     ../../Dados/User.py
Library     ../../Dados/Category.py
Library     ../../Dados/Bill.py
Library     ../../Dados/Creditcard.py
Library    Collections
Test Template    Proccess Test Insert Bill

*** Test Case ***                                                          Cartão de crédito         Portion
Caso de teste 01 - Deve inserir um lançamento                              False                      1
Caso de teste 02 - Deve inserir um lançamento com parcela                  False                      4
Caso de teste 03 - Deve inserir um lançamento com cartão                   True                       1
Caso de teste 04 - Deve inserir um lançamento com cartão e parcela         True                       4
*** Keywords ***
Proccess Test Insert Bill
    [Arguments]    ${CREDITCARD}    ${PORTION}
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}

    
   
    ${IDS}        Insert Scenario    ${USER}
    &{BILL}       Bill.Create Bill Without Creditcard    ${IDS.category_id}

    IF  ${CREDITCARD} == True
        &{CREDITCARD}    Dados Nubank
        ${response}     Insert Creditcard    ${IDS.account_id}    ${CREDITCARD}    ${USER}
        ${BILL.credit_card_id}  Set Variable  ${response.json()['id']}
    END

    
    ${BILL.portion}    Set Variable  ${PORTION}

    ${response}   Insert Bill    ${BILL}    ${IDS.account_id}    ${USER}

    Status Should Be    201
    IF   ${BILL.portion} > 1
      FOR    ${billItem}    IN    @{response.json()}
            Dictionary Should Contain Key     ${billItem}    date
            Dictionary Should Contain Item    ${billItem}    description      ${BILL.description}
            Dictionary Should Contain Key     ${billItem}    due_date         
            Dictionary Should Contain Item    ${billItem}    category_id      ${BILL.category_id}
            Dictionary Should Contain Item    ${billItem}    portion          ${BILL.portion}
            Dictionary Should Contain Item    ${billItem}    account_id       ${IDS.account_id}
          
      END  
    ELSE
        Dictionary Should Contain Item    ${response.json()}    category_id      ${BILL.category_id}
        Dictionary Should Contain Key     ${response.json()}     date
        Dictionary Should Contain Item    ${response.json()}    description      ${BILL.description}
        Dictionary Should Contain Key     ${response.json()}     due_date         
        Dictionary Should Contain Item    ${response.json()}    category_id      ${BILL.category_id}
        Dictionary Should Contain Item    ${response.json()}    portion          ${BILL.portion}
        Dictionary Should Contain Item    ${response.json()}    account_id       ${IDS.account_id}     
    END
    

    