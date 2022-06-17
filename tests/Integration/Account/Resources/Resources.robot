**Settings**

**Variables**

${URL_BASE}     %{ROBOT_URL}

**Keywords**
Get Token Authenticate
    [Arguments]  ${USER}
    ${response}     POST     ${URL_BASE}/token    ${USER}
    [Return]        &{response.json()}

Generate Header Authorization ${chave}
    ${headers}       Create Dictionary  Authorization=Bearer ${chave}  Content-Type=application/json
    [Return]         ${headers}