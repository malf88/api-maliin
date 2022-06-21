*** Settings ***
Resource    ../../../Modules/Account/Resources/AccountResource.robot
Resource    ../../../Modules/Bill/Resources/BillResource.robot
Resource    ../../../Resources/Cenarios.robot
Resource    ../../Category/Resources/CategoryResource.robot
Resource    ../../Creditcard/Resources/CreditcardResource.robot
Library     ../../../Dados/Account.py
Library     ../../../Dados/User.py
Library     ../../../Dados/Category.py
Library     ../../../Dados/Bill.py
Library     ../../../Dados/Creditcard.py
Library    Collections


*** Keywords ***
Proccess Test Insert Bill
    [Arguments]    ${CREDITCARD}    ${PORTION}
    
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER} 
    ${IDS}        Insert Scenario    ${USER}
    &{BILL}       Bill.Create Bill Without Creditcard    ${IDS.category_id}           

    ${response}    Insert Bill    ${BILL}    ${IDS.account_id}    ${USER}

    Status Should Be    201
    IF   ${BILL.portion} > 1
      FOR   ${item}    IN    @{response.json()}
            &{billItem}     Set To Dictionary     ${item}
            
            Dictionary Should Contain Key     ${billItem}    date
            Dictionary Should Contain Item    ${billItem}    description      ${BILL.description} [${billItem.portion}/${BILL.portion}]
            Dictionary Should Contain Key     ${billItem}    due_date         
            Dictionary Should Contain Item    ${billItem}    category_id      ${BILL.category_id}
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
    
Proccess Teste Get Bill
    [Arguments]    ${CREDITCARD}    ${PORTION}
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER} 
    ${IDS}        Insert Scenario    ${USER}
    &{BILL}       Bill.Create Bill Without Creditcard    ${IDS.category_id}           

    ${response}    Insert Bill    ${BILL}    ${IDS.account_id}    ${USER}  ${PORTION}  ${CREDITCARD}
    IF  ${PORTION} > 1
        ${response}    Get A Bill    ${response.json()[0]['id']}    ${USER}
        Dictionary Should Contain Item    ${response.json()}    description      ${BILL.description} [1/${PORTION}]
    ELSE
        ${response}    Get A Bill    ${response.json()['id']}    ${USER}
        Dictionary Should Contain Item    ${response.json()}    description      ${BILL.description}
    END

    Dictionary Should Contain Item    ${response.json()}    category_id      ${BILL.category_id}
    Dictionary Should Contain Key     ${response.json()}    date
    Dictionary Should Contain Key     ${response.json()}    due_date    
    Dictionary Should Contain Item    ${response.json()}    portion          1   
    Dictionary Should Contain Item    ${response.json()}    category_id      ${BILL.category_id}
    Dictionary Should Contain Item    ${response.json()}    account_id       ${IDS.account_id}     
