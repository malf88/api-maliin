**Settings**
Library     DatabaseLibrary
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
    ${user_inserted}  Query  SELECT * FROM maliin.users WHERE first_name = '${USER.name}' AND last_name = '${USER.lastname}' AND email = '${USER.email}' AND password = '${USER.password_hash}'
    Disconnect From Database
    ${USER.id}  Set Variable     ${user_inserted[0][0]}
    [Return]    ${USER}

Delete User
    [Arguments]    ${USER}
    Connect Database 
    Execute Sql String      DELETE FROM maliin.invoices WHERE credit_card_id IN (SELECT id FROM maliin.credit_cards WHERE account_id IN (SELECT id FROM maliin.accounts WHERE user_id IN (SELECT id FROM maliin.users WHERE first_name = '${USER.name}' AND last_name = '${USER.lastname}' AND email = '${USER.email}' AND password = '${USER.password_hash}')))

    Execute Sql String      DELETE FROM maliin.bills WHERE account_id IN (SELECT id FROM maliin.accounts WHERE user_id IN (SELECT id FROM maliin.users WHERE first_name = '${USER.name}' AND last_name = '${USER.lastname}' AND email = '${USER.email}' AND password = '${USER.password_hash}')) 
    Execute Sql String      DELETE FROM maliin.credit_cards WHERE account_id IN (SELECT id FROM maliin.accounts WHERE user_id IN (SELECT id FROM maliin.users WHERE first_name = '${USER.name}' AND last_name = '${USER.lastname}' AND email = '${USER.email}' AND password = '${USER.password_hash}')) 
    Execute Sql String      DELETE FROM maliin.categories WHERE user_id IN (SELECT id FROM maliin.users WHERE first_name = '${USER.name}' AND last_name = '${USER.lastname}' AND email = '${USER.email}' AND password = '${USER.password_hash}')
    Execute Sql String      DELETE FROM maliin.accounts_users WHERE account_id IN (SELECT id FROM maliin.accounts WHERE user_id IN (SELECT id FROM maliin.users WHERE first_name = '${USER.name}' AND last_name = '${USER.lastname}' AND email = '${USER.email}' AND password = '${USER.password_hash}'))
    Execute Sql String      DELETE FROM maliin.accounts WHERE user_id IN (SELECT id FROM maliin.users WHERE first_name = '${USER.name}' AND last_name = '${USER.lastname}' AND email = '${USER.email}' AND password = '${USER.password_hash}')
        
    Execute Sql String      DELETE FROM maliin.users WHERE first_name = '${USER.name}' AND last_name = '${USER.lastname}' AND email = '${USER.email}' AND password = '${USER.password_hash}'
    Disconnect From Database