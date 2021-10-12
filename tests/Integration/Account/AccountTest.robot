** Settings **
Library               RequestsLibrary
Library               Collections
Library               Dados/User.py
Library               Dados/Account.py
Resource              Resources/Resources.robot
Resource              Resources/Cenarios.robot
Resource              Resources/BDDpt_BR.robot

** Test Cases **

Caso de teste 1 - Autenticar na api
    ${USER}             Cenarios.Create User
    ${response}         POST        ${URL_BASE}/token    ${USER}
    Status Should Be    200         ${response}
    Dictionary Should Contain Key   ${response.json()}   token
    Dictionary Should Contain Key   ${response.json()}   token_type

Caso de teste 2 - Inserir uma conta
    ${token}            Resources.Get Token Authenticate
    ${headers}          Resources.Generate Header Authorization ${token.token}
    ${account}           Account.Create Account Xpto Json
    &{account_dict}     Account.Create Account Xpto
    ${response}         POST        ${URL_BASE}/account  ${account}  headers=${headers}
    Status Should Be    201         ${response}
    Dictionary Should Contain Key    ${response.json()}   id
    Dictionary Should Contain Key    ${response.json()}   name
    Dictionary Should Contain Key    ${response.json()}   agency
    Dictionary Should Contain Key    ${response.json()}   account
    Dictionary Should Contain Item   ${response.json()}   name   ${account_dict.name}

Caso de teste 3 - Listar contas 
    ${token}            Resources.Get Token Authenticate
    ${headers}          Resources.Generate Header Authorization ${token.token}
    ${account}           Account.Create Account Xpto Json
    &{account_dict}     Account.Create Account Xpto
    ${response}         POST        ${URL_BASE}/account  ${account}  headers=${headers}

    ${response}         GET        ${URL_BASE}/account  headers=${headers}
    Status Should Be    200         ${response}
    
    FOR  ${item}  IN  @{response.json()}
        Dictionary Should Contain Key    ${item}   id
        Dictionary Should Contain Key    ${item}   name
        Dictionary Should Contain Key    ${item}   agency
        Dictionary Should Contain Key    ${item}   account
        Dictionary Should Contain Item   ${item}   name   ${account_dict.name}
        Dictionary Should Contain Item   ${item}   agency   ${account_dict.agency}
        Dictionary Should Contain Item   ${item}   account   ${account_dict.account}
    END
Caso de teste 4 - Deletar uma conta
    ${token}            Resources.Get Token Authenticate
    ${headers}          Resources.Generate Header Authorization ${token.token}
    ${account}          Account.Create Account Xpto Json
    &{account_dict}     Account.Create Account Xpto
    ${response}         POST        ${URL_BASE}/account  ${account}  headers=${headers}
    &{response_post}    Set Variable    ${response.json()}   
    ${response}         DELETE        ${URL_BASE}/account/${response_post.id}  headers=${headers}
    Status Should Be    200           ${response}
    ${um}   Convert To Integer   1
    Should Be Equal    ${response.json()}  ${um}

Caso de teste 5 - Alterar uma conta
    ${token}                Resources.Get Token Authenticate
    ${headers}              Resources.Generate Header Authorization ${token.token}
    ${account}              Account.Create Account Xpto Json
    &{account_dict}         Account.Create Account Xpto
    ${response}             POST        ${URL_BASE}/account  ${account}  headers=${headers}
    Status Should Be    201          ${response}
    &{response_post}        Set Variable  ${response.json()}
    ${new_account}          Account.Create Account Xyz Json
    &{new_account_dict}     Account.Create Account Xyz 
    ${response}             PUT          ${URL_BASE}/account/${response_post.id}  ${new_account}  headers=${headers}
    Status Should Be    200          ${response}
    Dictionary Should Contain Key    ${response.json()}   id
    Dictionary Should Contain Key    ${response.json()}   name
    Dictionary Should Contain Key    ${response.json()}   agency
    Dictionary Should Contain Key    ${response.json()}   account
    Dictionary Should Contain Item   ${response.json()}   name      ${new_account_dict.name}
    Dictionary Should Contain Item   ${response.json()}   agency    ${new_account_dict.agency}
    Dictionary Should Contain Item   ${response.json()}   account   ${new_account_dict.account}
    Dictionary Should Contain Item   ${response.json()}   id        ${response_post.id} 





