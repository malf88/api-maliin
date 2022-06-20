*** Settings ***

Resource    ../../../Resources/Resources.robot

*** Variables ***
${URL_CATEGORY}    ${URL_BASE}/category
***Keywords***

Insert Category
    [Arguments]    ${CATEGORY}  ${USER}
    ${response}    Request POST    ${URL_CATEGORY}    ${USER}    ${CATEGORY}
    [Return]       ${response}