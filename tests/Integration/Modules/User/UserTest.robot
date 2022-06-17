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

