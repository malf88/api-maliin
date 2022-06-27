*** Settings ***

Resource    ../../../Resources/Resources.robot

*** Variables ***
${URL_CATEGORY}    ${URL_BASE}/category
***Keywords***

Insert Category
    [Arguments]    ${CATEGORY}  ${USER}
    ${response}    Request POST    ${URL_CATEGORY}    ${USER}    ${CATEGORY}
    [Return]       ${response}

Update Category
    [Arguments]    ${CATEGORY}  ${USER}  ${CATEGORY_ID}
    ${response}    Request PUT    ${URL_CATEGORY}/${CATEGORY_ID}    ${USER}    ${CATEGORY}
    [Return]       ${response}

Delete Category
    [Arguments]    ${USER}  ${CATEGORY_ID}
    ${response}    Request DELETE    ${URL_CATEGORY}/${CATEGORY_ID}    ${USER}
    [Return]       ${response}

Get A Category
    [Arguments]    ${USER}  ${CATEGORY_ID}
    ${response}    Request GET    ${URL_CATEGORY}/${CATEGORY_ID}    ${USER}
    [Return]       ${response}

Get All Category
    [Arguments]    ${USER} 
    ${response}    Request GET    ${URL_CATEGORY}   ${USER}
    [Return]       ${response}