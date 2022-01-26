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
Caso de teste 1 - Cadastrar uma compra
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
    &{bill_dict}             Bill.Create Bill With Creditcard   ${response_creditcard.id}  ${response_category.id}

    ${response}         POST        ${URL_BASE}/bill/account/${account.id}   ${bill}  headers=${headers}
    Status Should Be    201         ${response}

    Dictionary Should Contain Key    ${response.json()}   description
    Dictionary Should Contain Key    ${response.json()}   date
    Dictionary Should Contain Key    ${response.json()}   due_date
    Dictionary Should Contain Key    ${response.json()}   credit_card_id
    Dictionary Should Contain Key    ${response.json()}   category_id


Caso de teste 2 - Listar compras com cartão de crédito
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
    &{bill_dict}             Bill.Create Bill With Creditcard        ${response_creditcard.id}  ${response_category.id}

    ${response}         POST        ${URL_BASE}/bill/account/${account.id}   ${bill}  headers=${headers}
    Status Should Be    201         ${response}

    ${response}         GET        ${URL_BASE}/bill/account/${account.id}   headers=${headers}
    Status Should Be    200         ${response}

    FOR  ${item}  IN  @{response.json()}
        Dictionary Should Contain Key    ${item}   description
        Dictionary Should Contain Key    ${item}   date
        Dictionary Should Contain Key    ${item}   credit_card_id
        Dictionary Should Contain Key    ${item}   category_id
        Dictionary Should Contain Item   ${item}   description     Fatura do cartão de crédito ${response_creditcard.name}
        Dictionary Should Contain Item   ${item}   credit_card_id  ${bill_dict.credit_card_id}
        Dictionary Should Contain Item   ${item}   amount          ${bill_dict.amount}
    END


Caso de teste 3 - Listar compras normais
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
    ${bill}                  Bill.Create Bill Without Creditcard Json   ${response_category.id}
    &{bill_dict}             Bill.Create Bill Without Creditcard        ${response_category.id}
    ${response}         POST        ${URL_BASE}/bill/account/${account.id}   ${bill}  headers=${headers}
    Status Should Be    201         ${response}



    ${response}         GET        ${URL_BASE}/bill/account/${account.id}   headers=${headers}
    Status Should Be    200         ${response}

    FOR  ${item}  IN  @{response.json()}
        Dictionary Should Contain Key    ${item}   description
        Dictionary Should Contain Key    ${item}   date
        Dictionary Should Contain Key    ${item}   credit_card_id
        Dictionary Should Contain Key    ${item}   category_id
        Dictionary Should Contain Item   ${item}   description     ${bill_dict.description}
        Dictionary Should Contain Item   ${item}   category_id     ${bill_dict.category_id}
    END

Caso de teste 4 - Deletar uma compras
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

    ${bill}                  Bill.Create Bill Without Creditcard Json   ${response_category.id}
    &{bill_dict}             Bill.Create Bill Without Creditcard        ${response_category.id}

    ${response}         POST        ${URL_BASE}/bill/account/${account.id}   ${bill}  headers=${headers}
    Status Should Be    201         ${response}

    &{response_post}    Set Variable  ${response.json()}

    ${response}         DELETE        ${URL_BASE}/bill/${response_post.id}  headers=${headers}
    Status Should Be    200           ${response}
    ${um}   Convert To Integer   1
    Should Be Equal    ${response.json()}  ${um}

Caso de teste 5 - Alterar uma compra
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

    ${bill}                  Bill.Create Bill Without Creditcard Json   ${response_category.id}
    &{bill_dict}             Bill.Create Bill Without Creditcard        ${response_category.id}

    ${response}         POST        ${URL_BASE}/bill/account/${account.id}   ${bill}  headers=${headers}
    Status Should Be    201         ${response}
    &{response_post}     Set Variable  ${response.json()}


    ${new_bill}          Bill.Create Bill With Creditcard Json   ${response_creditcard.id}  ${response_category.id}
    &{new_bill_dict}     Bill.Create Bill With Creditcard        ${response_creditcard.id}  ${response_category.id}
    ${response}          PUT          ${URL_BASE}/bill/${response_post.id}  ${new_bill}  headers=${headers}

    Status Should Be     200          ${response}
    Dictionary Should Contain Key    ${response.json()}   description
    Dictionary Should Contain Key    ${response.json()}   date
    Dictionary Should Contain Key    ${response.json()}   due_date
    Dictionary Should Contain Key    ${response.json()}   credit_card_id
    Dictionary Should Contain Key    ${response.json()}   category_id
    Dictionary Should Contain Item   ${response.json()}   description     ${new_bill_dict.description}
    Dictionary Should Contain Item   ${response.json()}   credit_card_id  ${new_bill_dict.credit_card_id}
    Dictionary Should Contain Item   ${response.json()}   category_id     ${new_bill_dict.category_id}

Caso de teste 6 - Pagar uma fatura
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
    &{bill_dict}             Bill.Create Bill With Creditcard        ${response_creditcard.id}  ${response_category.id}

    ${response}         POST        ${URL_BASE}/bill/account/${account.id}   ${bill}  headers=${headers}
    Status Should Be    201         ${response}

    ${response}         GET        ${URL_BASE}/bill/account/${account.id}   headers=${headers}
    Status Should Be    200         ${response}

    &{response_invoice}    Set Variable     ${response.json()[0]}

    ${response}         PATCH        ${URL_BASE}/invoice/pay/${response_invoice.id}   headers=${headers}


    Dictionary Should Contain Key    ${response.json()}   pay_day
    Dictionary Should Contain Key    ${response.json()}   credit_card_id
    Dictionary Should Contain Key    ${response.json()}   due_date
    Dictionary Should Contain Key    ${response.json()}   end_date
    Dictionary Should Contain Key    ${response.json()}   start_date

Caso de teste 7 - Listar compras normais por data
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
    FOR  ${key}  IN  1  2  3
        ${bill}                  Bill.Create Bill Without Creditcard Json   ${response_category.id}
        &{bill_dict}             Bill.Create Bill Without Creditcard        ${response_category.id}
        ${response}         POST        ${URL_BASE}/bill/account/${account.id}   ${bill}  headers=${headers}
        Status Should Be    201         ${response}
    END


    ${response}         GET        ${URL_BASE}/bill/account/${account.id}/between/2021-10-01/2021-10-30   headers=${headers}
    Status Should Be    200         ${response}

    Dictionary Should Contain Key   ${response.json()['total']}     total_cash_in
    Dictionary Should Contain Key   ${response.json()['total']}     total_cash_out
    Dictionary Should Contain Key   ${response.json()['total']}     total_estimated
    Dictionary Should Contain Key   ${response.json()['total']}     total_paid
    FOR  ${item}  IN  @{response.json()['bills']}
        Dictionary Should Contain Key    ${item}   description
        Dictionary Should Contain Key    ${item}   date
        Dictionary Should Contain Key    ${item}   credit_card_id
        Dictionary Should Contain Key    ${item}   category_id
        Dictionary Should Contain Item   ${item}   description     ${bill_dict.description}
        Dictionary Should Contain Item   ${item}   category_id     ${bill_dict.category_id}
    END

Caso de teste 8 - Listar periodos com compras
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
    FOR  ${key}  IN  1  2  3
        ${bill}                  Bill.Create Bill Without Creditcard Json   ${response_category.id}
        &{bill_dict}             Bill.Create Bill Without Creditcard        ${response_category.id}
        ${response}         POST        ${URL_BASE}/bill/account/${account.id}   ${bill}  headers=${headers}
        Status Should Be    201         ${response}
    END

    ${response}     GET    ${URL_BASE}/bill/account/${account.id}/periods   headers=${headers}

    FOR  ${item}  IN  @{response.json()}
        Dictionary Should Contain Key    ${item}   month
        Dictionary Should Contain Key    ${item}   year
    END


