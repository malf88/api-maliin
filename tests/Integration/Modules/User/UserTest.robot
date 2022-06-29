** Settings **
Library               RequestsLibrary
Library               Collections
Library               ../../Dados/User.py
Library               ../../Dados/Account.py
Resource              ../../Resources/Resources.robot
Resource              ../../Resources/Cenarios.robot

** Test Cases **

Caso de teste 1 - Autenticar na api
    &{USER}             User.Dados Joao Silva
    ${USER}             Cenarios.Create User    ${USER}
    ${response}         POST        ${URL_BASE}/token    ${USER}
    Status Should Be    200         ${response}
    Dictionary Should Contain Key   ${response.json()}   token
    Dictionary Should Contain Key   ${response.json()}   token_type

Caso de teste 2 - Fazer logout
    &{USER}             User.Dados Joao Silva
    ${USER}             Cenarios.Create User    ${USER}
    
    ${header}           Resources.Generate Header Authorization     ${USER}
   
    ${response}         Resources.Request PUT   ${URL_BASE}/logout   ${USER}
    Status Should Be   200
    Should Be Equal As Strings   ${response.json()}    1

Caso de teste 3 - Deve trazer dados do usuário
    &{USER}             User.Dados Joao Silva
    ${USER}             Cenarios.Create User    ${USER}
    ${response}         Request GET        ${URL_BASE}/user    ${USER}

    Status Should Be    200         ${response}
    Dictionary Should Contain Key   ${response.json()}   email
    Dictionary Should Contain Key   ${response.json()}   first_name
    Dictionary Should Contain Key   ${response.json()}   last_name
    Dictionary Should Contain Key   ${response.json()}   id
    Dictionary Should Contain Item  ${response.json()}   email        ${USER.email}
    Dictionary Should Contain Item  ${response.json()}   first_name   ${USER.name}
    Dictionary Should Contain Item  ${response.json()}   last_name    ${USER.lastname}