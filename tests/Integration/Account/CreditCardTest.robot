** Settings **
Library               RequestsLibrary
Library               Collections
Library               Dados/User.py
Library               Dados/Account.py
Library               Dados/CreditCard.py
Library               Dados/Category.py
Library               Dados/Bill.py
Resource              Resources/Resources.robot
Resource              Resources/Cenarios.robot
Resource              Resources/BDDpt_BR.robot

** Test Cases **
Caso de teste 1 - Cadastrar um cartão de crédito
    ${token}            Resources.Get Token Authenticate
    ${headers}          Resources.Generate Header Authorization ${token.token}
    ${account}          Account.Create Account Xpto Json
    &{account_dict}     Account.Create Account Xpto
    ${response}         POST            ${URL_BASE}/account  ${account}  headers=${headers}

    &{account}          Set Variable  ${response.json()}
    ${creditcard}       CreditCard.Create Creditcard Nubank Json
    &{creditcard_dict}  CreditCard.Create Creditcard Nubank
    ${response}         POST         ${URL_BASE}/creditcard/account/${account.id}  ${creditcard}  headers=${headers}
    Status Should Be    201          ${response}
    Dictionary Should Contain Key    ${response.json()}   id
    Dictionary Should Contain Key    ${response.json()}   name
    Dictionary Should Contain Key    ${response.json()}   due_day
    Dictionary Should Contain Key    ${response.json()}   close_day
    Dictionary Should Contain Item   ${response.json()}   name        ${creditcard_dict.name}
    Dictionary Should Contain Item   ${response.json()}   due_day     ${creditcard_dict.due_day} 
    Dictionary Should Contain Item   ${response.json()}   close_day   ${creditcard_dict.close_day}   


Caso de teste 2 - Listar cartões de crédito 
    ${token}            Resources.Get Token Authenticate
    ${headers}          Resources.Generate Header Authorization ${token.token}
    ${account}          Account.Create Account Xpto Json
    &{account_dict}     Account.Create Account Xpto
    ${response}         POST            ${URL_BASE}/account  ${account}  headers=${headers}

    &{account}          Set Variable  ${response.json()}
    ${creditcard}       CreditCard.Create Creditcard Nubank Json
    &{creditcard_dict}  CreditCard.Create Creditcard Nubank
    ${response}         POST         ${URL_BASE}/creditcard/account/${account.id}  ${creditcard}  headers=${headers}
    Status Should Be    201          ${response}

    ${response}         GET         ${URL_BASE}/creditcard/account/${account.id}   headers=${headers}
    Status Should Be    200         ${response}
    
    FOR  ${item}  IN  @{response.json()}
        Dictionary Should Contain Key    ${item}   id
        Dictionary Should Contain Key    ${item}   name
        Dictionary Should Contain Key    ${item}   due_day
        Dictionary Should Contain Key    ${item}   close_day
        Dictionary Should Contain Item   ${item}   name      ${creditcard_dict.name}
        Dictionary Should Contain Item   ${item}   due_day   ${creditcard_dict.due_day}
        Dictionary Should Contain Item   ${item}   close_day   ${creditcard_dict.close_day}
    END
Caso de teste 3 - Deletar um cartão de crédito
    ${token}            Resources.Get Token Authenticate
    ${headers}          Resources.Generate Header Authorization ${token.token}
    ${account}          Account.Create Account Xpto Json
    &{account_dict}     Account.Create Account Xpto
    ${response}         POST            ${URL_BASE}/account  ${account}  headers=${headers}

    &{account}          Set Variable  ${response.json()}
    ${creditcard}       CreditCard.Create Creditcard Nubank Json
    &{creditcard_dict}  CreditCard.Create Creditcard Nubank
    ${response}         POST         ${URL_BASE}/creditcard/account/${account.id}  ${creditcard}  headers=${headers}
    Status Should Be    201          ${response}   
    &{response_post}          Set Variable  ${response.json()}

    ${response}         DELETE        ${URL_BASE}/creditcard/${response_post.id}  headers=${headers}
    Status Should Be    200           ${response}
    ${um}   Convert To Integer   1
    Should Be Equal    ${response.json()}  ${um}

Caso de teste 4 - Alterar um cartão de crédito
    ${token}            Resources.Get Token Authenticate
    ${headers}          Resources.Generate Header Authorization ${token.token}
    ${account}          Account.Create Account Xpto Json
    &{account_dict}     Account.Create Account Xpto
    ${response}         POST            ${URL_BASE}/account  ${account}  headers=${headers}

    &{account}          Set Variable  ${response.json()}
    ${creditcard}       CreditCard.Create Creditcard Nubank Json
    &{creditcard_dict}  CreditCard.Create Creditcard Nubank
    ${response}         POST         ${URL_BASE}/creditcard/account/${account.id}  ${creditcard}  headers=${headers}
    Status Should Be    201          ${response}
    &{response_post}    Set Variable  ${response.json()}

    ${new_creditcard}          CreditCard.Create Creditcard Itau Json
    &{new_creditcard_dict}     CreditCard.Create Creditcard Itau
    ${response}                PUT          ${URL_BASE}/creditcard/${response_post.id}  ${new_creditcard}  headers=${headers}
    Status Should Be           200          ${response}
    Dictionary Should Contain Key    ${response.json()}   name
    Dictionary Should Contain Key    ${response.json()}   due_day
    Dictionary Should Contain Key    ${response.json()}   close_day
    Dictionary Should Contain Item   ${response.json()}   name       ${new_creditcard_dict.name}
    Dictionary Should Contain Item   ${response.json()}   due_day    ${new_creditcard_dict.due_day}
    Dictionary Should Contain Item   ${response.json()}   close_day  ${new_creditcard_dict.close_day}
    Dictionary Should Contain Item   ${response.json()}   id         ${response_post.id} 

Caso de teste 5 - Listar faturas do cartão de crédito
    ${token}            Resources.Get Token Authenticate
    ${headers}          Resources.Generate Header Authorization ${token.token}
    ${account}          Account.Create Account Xpto Json
    &{account_dict}     Account.Create Account Xpto
    ${response}         POST            ${URL_BASE}/account  ${account}  headers=${headers}

    &{account}          Set Variable  ${response.json()}
    ${creditcard}       CreditCard.Create Creditcard Nubank Json
    &{creditcard_dict}  CreditCard.Create Creditcard Nubank
    ${response}         POST         ${URL_BASE}/creditcard/account/${account.id}  ${creditcard}  headers=${headers}
    &{response_creditcard}   Set Variable  ${response.json()}
    Status Should Be    201          ${response}

    ${category}              Category.Create Category Food Json
    &{category_dict}         Category.Create Category Food
    ${response}              POST        ${URL_BASE}/category  ${category}  headers=${headers}
    &{response_category}     Set Variable  ${response.json()}

    ${bill}                  Bill.Create Bill With Creditcard Json   ${response_creditcard.id}  ${response_category.id}

    ${response}         POST        ${URL_BASE}/bill/account/${account.id}   ${bill}  headers=${headers}
    Status Should Be    201         ${response}

    ${response}         GET         ${URL_BASE}/creditcard/${response_creditcard.id}/invoices   headers=${headers}
    Status Should Be    200         ${response}
    
    FOR  ${item}  IN  @{response.json()}
        Dictionary Should Contain Key    ${item}   id
        Dictionary Should Contain Key    ${item}   start_date
        Dictionary Should Contain Key    ${item}   end_date
        Dictionary Should Contain Key    ${item}   due_date
        Dictionary Should Contain Key    ${item}   month_reference
        Dictionary Should Contain Key    ${item}   pay_day
        Dictionary Should Contain Key    ${item}   credit_card_id
        Dictionary Should Contain Item   ${item}   credit_card_id      ${response_creditcard.id}
    END



