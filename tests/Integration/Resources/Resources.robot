**Settings**
Library               RequestsLibrary
**Variables**

${URL_BASE}     %{ROBOT_URL}

**Keywords**
Get Token Authenticate
    [Arguments]  ${USER}
    ${response}     POST     ${URL_BASE}/token    ${USER}
    [Return]        &{response.json()}

Generate Header Authorization 
    [Arguments]      ${USER}
    ${chave}         Get Token Authenticate    ${USER}
    ${headers}       Create Dictionary  Authorization=Bearer ${chave.token}  Content-Type=application/json
    [Return]         ${headers}

Request PUT
    [Arguments]  ${URL}     ${USER}    ${DATA}={} 
    ${header}      Generate Header Authorization     ${USER}
    ${response}    PUT    ${URL}  json=${DATA}    headers=${header}    expected_status=any
    [Return]     ${response}

Request PATCH
    [Arguments]  ${URL}     ${USER}    ${DATA}={} 
    ${header}      Generate Header Authorization     ${USER}
    ${response}    PATCH    ${URL}  json=${DATA}    headers=${header}    expected_status=any
    [Return]     ${response}
Request GET
    [Arguments]  ${URL}     ${USER}    ${PARAMS}=${EMPTY} 
    ${header}      Generate Header Authorization     ${USER}
    ${response}    GET    ${URL}  params=${PARAMS}   headers=${header}   expected_status=any
    [Return]     ${response}

Request POST
    [Arguments]  ${URL}     ${USER}    ${DATA}={} 
    ${header}      Generate Header Authorization     ${USER}
    ${response}    POST    ${URL}  json=${DATA}    headers=${header}    expected_status=any
    [Return]     ${response}

Request DELETE
    [Arguments]  ${URL}     ${USER}    ${DATA}={} 
    ${header}      Generate Header Authorization     ${USER}
    ${response}    DELETE    ${URL}  json=${DATA}    headers=${header}    expected_status=any
    [Return]     ${response}