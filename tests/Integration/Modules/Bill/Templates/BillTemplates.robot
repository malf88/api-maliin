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

    ${response}           Insert Bill    ${BILL}    ${IDS.account_id}    ${USER}   ${PORTION}   ${CREDITCARD}
    ${BILL.account_id}    Set Variable  ${IDS.account_id}

    Status Should Be    201
    IF   ${BILL.portion} > 1
      FOR   ${item}    IN    @{response.json()}
            &{billItem}     Set To Dictionary     ${item}            
            Should Be Validate Fields    ${billItem}    ${BILL}          
      END  
    ELSE
        &{billItem}     Set To Dictionary     ${response.json()}  
        Should Be Validate Fields    ${billItem}    ${BILL}  
    END
    
Proccess Teste Get Bill
    [Arguments]    ${CREDITCARD}    ${PORTION}
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER} 
    ${IDS}        Insert Scenario    ${USER}
    &{BILL}       Bill.Create Bill Without Creditcard    ${IDS.category_id}           

    ${response}    Insert Bill    ${BILL}    ${IDS.account_id}    ${USER}  ${PORTION}  ${CREDITCARD}
    
    IF    ${PORTION} > 1
        &{INSERTED_BILL}     Set To Dictionary     ${response.json()[0]}
    ELSE
        &{INSERTED_BILL}     Set To Dictionary   ${response.json()}
    END
    ${response}    Get A Bill    ${INSERTED_BILL.id}    ${USER}
    ${BILL.account_id}    Set Variable    ${IDS.account_id}
    &{billItem}     Set To Dictionary     ${response.json()}  
    Should Be Validate Fields    ${billItem}    ${BILL}

Proccess Teste Update Bill
    [Arguments]    ${CREDITCARD}    ${PORTION}
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER} 
    ${IDS}        Insert Scenario    ${USER}
    &{BILL}       Bill.Create Bill Without Creditcard    ${IDS.category_id} 

    ${BILL.account_id}    Set Variable    ${IDS.account_id}       

    ${response}    Insert Bill    ${BILL}    ${IDS.account_id}    ${USER}  ${PORTION}  ${CREDITCARD}
    IF    ${PORTION} > 1
        &{INSERTED_BILL}     Set To Dictionary     ${response.json()[0]}
    ELSE
        &{INSERTED_BILL}     Set To Dictionary   ${response.json()}
    END

    ${BILL_ID}     Set Variable  ${INSERTED_BILL.id} 
    ${BILL.amount}  Set Variable  12.32
    ${response}    Update Bill    ${BILL}    ${BILL_ID}    ${USER}
    
    &{billItem}     Set To Dictionary     ${response.json()}  
    Should Be Validate Fields    ${billItem}    ${BILL}
