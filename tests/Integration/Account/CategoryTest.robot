** Settings **
Library               RequestsLibrary
Library               Collections
Library               Dados/User.py
Library               Dados/Account.py
Library               Dados/Category.py
Resource              Resources/Resources.robot
Resource              Resources/Cenarios.robot
Resource              Resources/BDDpt_BR.robot

** Test Cases **
Caso de teste 1 - Cadastrar uma categoria
    ${token}            Resources.Get Token Authenticate
    ${headers}          Resources.Generate Header Authorization ${token.token}
    ${category}         Category.Create Category Food Json
    &{category_dict}    Category.Create Category Food
    ${response}         POST        ${URL_BASE}/category  ${category}  headers=${headers}
    Status Should Be    201          ${response}
    Dictionary Should Contain Key    ${response.json()}   id
    Dictionary Should Contain Key    ${response.json()}   is_investiment
    Dictionary Should Contain Key    ${response.json()}   name    
    Dictionary Should Contain Item   ${response.json()}   is_investiment    ${category_dict.is_investiment}
    Dictionary Should Contain Item   ${response.json()}   name              ${category_dict.name}



Caso de teste 2 - Listar categorias 
    ${token}            Resources.Get Token Authenticate
    ${headers}          Resources.Generate Header Authorization ${token.token}
    ${category}         Category.Create Category Food Json
    &{category_dict}    Category.Create Category Food
    ${response}         POST        ${URL_BASE}/category  ${category}  headers=${headers}
    Status Should Be    201          ${response}

    ${response}         GET        ${URL_BASE}/category  headers=${headers}
    Status Should Be    200         ${response}
    
    FOR  ${item}  IN  @{response.json()}
        Dictionary Should Contain Key    ${item}   id
        Dictionary Should Contain Key    ${item}   name
        Dictionary Should Contain Key    ${item}   is_investiment
        Dictionary Should Contain Item   ${item}   name             ${category_dict.name}
        Dictionary Should Contain Item   ${item}   is_investiment   ${category_dict.is_investiment}
    END
Caso de teste 3 - Deletar uma categoria
    ${token}            Resources.Get Token Authenticate
    ${headers}          Resources.Generate Header Authorization ${token.token}
    ${category}         Category.Create Category Food Json
    &{category_dict}    Category.Create Category Food
    ${response}         POST        ${URL_BASE}/category  ${category}  headers=${headers}
    Status Should Be    201          ${response}
    &{response_post}    Set Variable    ${response.json()}   

    ${response}         DELETE        ${URL_BASE}/category/${response_post.id}  headers=${headers}
    Status Should Be    200           ${response}
    ${um}   Convert To Integer   1
    Should Be Equal    ${response.json()}  ${um}

Caso de teste 4 - Alterar uma categoria
    ${token}                 Resources.Get Token Authenticate
    ${headers}               Resources.Generate Header Authorization ${token.token}
    ${category}              Category.Create Category Food Json
    &{category_dict}         Category.Create Category Food
    ${response}              POST        ${URL_BASE}/category  ${category}  headers=${headers}
    &{response_post}         Set Variable  ${response.json()}
    ${new_category}          Category.Create Category Investment Json
    &{new_category_dict}     Category.Create Category Investment   
    ${response}              PUT          ${URL_BASE}/category/${response_post.id}  ${new_category}  headers=${headers}
    Status Should Be         200          ${response}
    Dictionary Should Contain Key    ${response.json()}   is_investiment
    Dictionary Should Contain Key    ${response.json()}   name
    Dictionary Should Contain Item   ${response.json()}   name              ${new_category_dict.name}
    Dictionary Should Contain Item   ${response.json()}   is_investiment    ${new_category_dict.is_investiment}
    Dictionary Should Contain Item   ${response.json()}   id                ${response_post.id} 





