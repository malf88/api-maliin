** Settings **

Library               Collections
Library               ../../Dados/User.py
Library               ../../Dados/Account.py
Library    ../../Dados/Category.py
Resource              ../../Resources/Resources.robot
Resource              ../../Resources/Cenarios.robot
Resource              Resources/CategoryResource.robot
Default Tags    Categoria
** Test Cases **

Caso de teste 01 - Inserir uma categoria
    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}
    &{CATEGORY}   Dados Lanches

    ${response}    Insert Category    ${CATEGORY}    ${USER}

    Dictionary Should Contain Item    ${response.json()}    name    ${CATEGORY.name}
    Dictionary Should Contain Item    ${response.json()}    is_investiment    ${CATEGORY.is_investiment}

Caso de teste 02 - Alterar uma categoria

    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}
    &{CATEGORY}   Dados Lanches

    ${response}    Insert Category    ${CATEGORY}    ${USER}
    
    ${CATEGORY.is_investiment}    Set Variable  ${True}

    ${response}    Update Category    ${CATEGORY}    ${USER}    ${response.json()['id']}

    Dictionary Should Contain Item    ${response.json()}    name    ${CATEGORY.name}
    Dictionary Should Contain Item    ${response.json()}    is_investiment    ${CATEGORY.is_investiment}

Caso de teste 03 - Excluir uma categoria
    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}
    &{CATEGORY}   Dados Lanches

    ${response}    Insert Category    ${CATEGORY}    ${USER}
    ${response}    Delete Category    ${USER}    ${response.json()['id']}
    
    Should Be Equal As Integers    ${response.json()}    1

Caso de teste 04 - Buscar uma categoria
    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}
    &{CATEGORY}   Dados Lanches

    ${response}    Insert Category    ${CATEGORY}    ${USER}

    ${response}    Get A Category    ${USER}    ${response.json()['id']}

    Dictionary Should Contain Item    ${response.json()}    name    ${CATEGORY.name}
    Dictionary Should Contain Item    ${response.json()}    is_investiment    ${CATEGORY.is_investiment}

Caso de teste 05 - Buscar todas as categorias
    &{ACCOUNT}    Account.Dados Xpto
    &{USER}       User.Dados Joao Silva
    ${USER}       Cenarios.Create User    ${USER}
    &{CATEGORY}   Dados Lanches

    ${response}    Insert Category    ${CATEGORY}    ${USER}
    ${response}    Insert Category    ${CATEGORY}    ${USER}
    ${response}    Get All Category    ${USER}

    FOR    ${item}    IN    @{response.json()}
        &{CATEGORY_ITEM}    Set To Dictionary    ${item}
        Dictionary Should Contain Item    ${CATEGORY_ITEM}    name    ${CATEGORY.name}
        Dictionary Should Contain Item    ${CATEGORY_ITEM}    is_investiment    ${CATEGORY.is_investiment}

    END

    