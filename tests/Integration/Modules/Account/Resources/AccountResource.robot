*** Settings ***

Resource    ../../../Resources/Resources.robot

*** Variables ***
${URL_ACCOUNT}    ${URL_BASE}/account
***Keywords***

Insert Account
    [Arguments]    ${ACCOUNT}  ${USER}
    ${response}    Request POST    ${URL_ACCOUNT}    ${USER}    ${ACCOUNT}
    [Return]       ${response}

Update Account
    [Arguments]    ${ACCOUNT_ID}    ${NEWACCOUNT}  ${USER}
    ${response}    Request PUT    ${URL_ACCOUNT}/${ACCOUNT_ID}    ${USER}    ${NEWACCOUNT}
    [Return]       ${response}

Delete Account
    [Arguments]    ${ACCOUNT_ID}  ${USER}
    ${response}    Request DELETE    ${URL_ACCOUNT}/${ACCOUNT_ID}    ${USER}
    [Return]       ${response}

Get Account
    [Arguments]    ${USER}    ${ACCOUNT_ID}=${EMPTY}

    ${response}    Request GET    ${URL_ACCOUNT}/${ACCOUNT_ID}    ${USER}
    [Return]       ${response}

Put User To Account
    [Arguments]    ${USER_OWNER}  ${USER_SHARED}   ${ACCOUNT_ID}

    ${response}    Request PUT    ${URL_ACCOUNT}/${ACCOUNT_ID}/user/${USER_SHARED}   ${USER_OWNER}
    [Return]       ${response}

Delete User To Account
    [Arguments]    ${USER_OWNER}  ${USER_SHARED}   ${ACCOUNT_ID}

    ${response}    Request DELETE    ${URL_ACCOUNT}/${ACCOUNT_ID}/user/${USER_SHARED}   ${USER_OWNER}
    [Return]       ${response}