**Settings**

**Variables**

${URL_BASE}     %{ROBOT_URL}

**Keywords**
Get Token Authenticate
    [Arguments]  ${USER}
    ${response}     POST     ${URL_BASE}/token    ${USER}
    [Return]        &{response.json()}

Generate Header Authorization 
    [Arguments]    ${USER}
    ${chave}    Get Token Authenticate    ${USER}
    ${headers}       Create Dictionary  Authorization=Bearer ${chave.token}  Content-Type=application/json
    [Return]         ${headers}

Request PUT
    [Arguments]  ${URL}     ${USER}    ${DATA}={} 
    ${header}      Generate Header Authorization     ${USER}
    ${response}    PUT    ${URL}  ${DATA}    headers=${header}
    [Return]     ${response}