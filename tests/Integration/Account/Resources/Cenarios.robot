**Settings**
Library     DatabaseLibrary
Library     ../Dados/User.py
Library     ../Dados/Account.py
Resource    Resources.robot

**Variables**

**Keywords**
Connect Database
    Connect To Database     psycopg2    
    ...                     %{DB_DATABASE}    
    ...                     %{DB_USERNAME}    
    ...                     %{DB_PASSWORD}      
    ...                     %{DB_HOST}
    ...                     %{DB_PORT}

Create User
    [Arguments]         ${USER}
    Delete User         ${USER}
    Connect Database
    Execute Sql String      INSERT INTO maliin.users (first_name, last_name, email, password) VALUES ('${USER.name}', '${USER.lastname}', '${USER.email}', '${USER.password_hash}')
    Disconnect From Database
    [Return]    ${USER}

Delete User
    [Arguments]    ${USER}
    Connect Database        
    Execute Sql String      DELETE FROM maliin.users WHERE first_name = '${USER.name}' AND last_name = '${USER.lastname}' AND email = '${USER.email}' AND password = '${USER.password_hash}'
    Disconnect From Database