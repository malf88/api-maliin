** Settings **

Library               Collections
Library               ../../Dados/User.py
Library               ../../Dados/Account.py
Resource              ../../Resources/Resources.robot
Resource              ../../Resources/Cenarios.robot
Resource    Resources/AccountResource.robot

** Test Cases **
Caso de teste 01 - Deve inserir uma conta
    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}
    ${response}    Insert Account    ${ACCOUNT}    ${USER}
    Status Should Be    201
    Dictionary Should Contain Item    ${response.json()}    name      ${ACCOUNT.name}
    Dictionary Should Contain Item    ${response.json()}    bank      ${ACCOUNT.bank}
    Dictionary Should Contain Item    ${response.json()}    agency    ${ACCOUNT.agency}
    Dictionary Should Contain Item    ${response.json()}    account   ${ACCOUNT.account}

Caso de teste 02 - Deve alterar uma conta
    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}

    ${response}   Insert Account    ${ACCOUNT}    ${USER}
    
    &{NEWACCOUNT}    Account.Dados Xyz
    ${response}      Update Account    ${response.json()['id']}    ${NEWACCOUNT}    ${USER}

    Status Should Be    200
    Dictionary Should Contain Item    ${response.json()}    name      ${NEWACCOUNT.name}
    Dictionary Should Contain Item    ${response.json()}    bank      ${NEWACCOUNT.bank}
    Dictionary Should Contain Item    ${response.json()}    agency    ${NEWACCOUNT.agency}
    Dictionary Should Contain Item    ${response.json()}    account   ${NEWACCOUNT.account}

Caso de teste 03 - Deve excluir uma conta
    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}

    ${response}   Insert Account    ${ACCOUNT}    ${USER}

    ${response}      Delete Account    ${response.json()['id']}   ${USER}

    Status Should Be    200

Caso de teste 04 - Deve retornar uma conta
    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}

    ${response}   Insert Account    ${ACCOUNT}    ${USER}

    ${response}    Get Account    ${USER}    ${response.json()['id']} 

    Status Should Be    200
    Dictionary Should Contain Item    ${response.json()}    name      ${ACCOUNT.name}
    Dictionary Should Contain Item    ${response.json()}    bank      ${ACCOUNT.bank}
    Dictionary Should Contain Item    ${response.json()}    agency    ${ACCOUNT.agency}
    Dictionary Should Contain Item    ${response.json()}    account   ${ACCOUNT.account}

Caso de teste 05 - Deve retornar uma lista de contas
    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}

    ${response}   Insert Account    ${ACCOUNT}    ${USER}
    ${response}   Get Account    ${USER} 

    Status Should Be    200
    Dictionary Should Contain Item    ${response.json()[0]}    name      ${ACCOUNT.name}
    Dictionary Should Contain Item    ${response.json()[0]}    bank      ${ACCOUNT.bank}
    Dictionary Should Contain Item    ${response.json()[0]}    agency    ${ACCOUNT.agency}
    Dictionary Should Contain Item    ${response.json()[0]}    account   ${ACCOUNT.account}


Caso de teste 06 - Deve compartilhar conta com outro usuário
    &{ACCOUNT}    Account.Dados Xpto

    &{USER_OWNER}       User.Dados Joao Silva
    ${USER_OWNER}       Cenarios.Create User    ${USER_OWNER}
    
    &{USER_SHARED}       User.Dados Maria Eduarda
    ${USER_SHARED}       Cenarios.Create User    ${USER_SHARED}

    ${response}   Insert Account         ${ACCOUNT}      ${USER_OWNER}
    ${response}   Put User To Account    ${USER_OWNER}   ${USER_SHARED.id}     ${response.json()['id']}

    ${response}   Get Account    ${USER_SHARED} 

    Status Should Be    200
    Dictionary Should Contain Item    ${response.json()[0]}    name      ${ACCOUNT.name}
    Dictionary Should Contain Item    ${response.json()[0]}    bank      ${ACCOUNT.bank}
    Dictionary Should Contain Item    ${response.json()[0]}    agency    ${ACCOUNT.agency}
    Dictionary Should Contain Item    ${response.json()[0]}    account   ${ACCOUNT.account}

Caso de teste 07 - Deve deletar o compartilhamento da conta com outro usuário
    &{ACCOUNT}    Account.Dados Xpto

    &{USER_OWNER}       User.Dados Joao Silva
    ${USER_OWNER}       Cenarios.Create User    ${USER_OWNER}
    
    &{USER_SHARED}       User.Dados Maria Eduarda
    ${USER_SHARED}       Cenarios.Create User    ${USER_SHARED}

    ${response}   Insert Account         ${ACCOUNT}      ${USER_OWNER}

    ${response}   Delete User To Account    ${USER_OWNER}   ${USER_SHARED.id}     ${response.json()['id']}

    ${response}   Get Account    ${USER_SHARED} 

    Status Should Be    200
    Length Should Be    ${response.json()}    0

Caso de teste 08 - Deve listar usuário com os quais a conta é compartilhada
    &{ACCOUNT}    Account.Dados Xpto

    &{USER_OWNER}       User.Dados Joao Silva
    ${USER_OWNER}       Cenarios.Create User    ${USER_OWNER}
    
    &{USER_SHARED}       User.Dados Maria Eduarda
    ${USER_SHARED}       Cenarios.Create User    ${USER_SHARED}

    ${response}   Insert Account         ${ACCOUNT}      ${USER_OWNER}
    ${ACCOUNT_ID}    Set Variable  ${response.json()['id']}

    ${response}   Put User To Account    ${USER_OWNER}   ${USER_SHARED.id}   ${ACCOUNT_ID}

    ${response}   Get Users From Account     ${USER_OWNER}     ${ACCOUNT_ID} 

    Status Should Be    200
    FOR    ${item}    IN    @{response.json()}
        ${USER_SHARE}    Set To Dictionary    ${item}
        Dictionary Should Contain Item    ${USER_SHARE}    first_name   ${USER_SHARED.name}
        Dictionary Should Contain Item    ${USER_SHARE}    last_name   ${USER_SHARED.lastname} 
        Dictionary Should Contain Item    ${USER_SHARE}    email   ${USER_SHARED.email}
    END
