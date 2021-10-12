**Settings**
Library     DatabaseLibrary
Library     ../Dados/User.py
Library     ../Dados/Account.py
Library     ../Dados/Category.py
Library     ../Dados/CreditCard.py
Library     ../Dados/Bill.py
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
    Delete User
    Connect Database    
    &{USER}    User.Create User
    Execute Sql String      INSERT INTO maliin.users (first_name, last_name, email, document, gender, api_token, password) VALUES ('${USER.name}', '${USER.lastname}', '${USER.email}', '${USER.cpf}', '${USER.gender}', '${USER.api_token}', '${USER.password_hash}')
    Disconnect From Database
    [Return]    ${USER}

Delete User
    Connect Database    
    &{USER}                 User.Create User   

    &{CREDITCARD}           CreditCard.Create Creditcard Nubank
    Execute Sql String      DELETE FROM maliin.bills WHERE credit_card_id = (SELECT id FROM maliin.credit_cards WHERE name = '${CREDITCARD.name}' AND due_day = '${CREDITCARD.due_day}' AND close_day='${CREDITCARD.close_day}')
    Execute Sql String      DELETE FROM maliin.invoices WHERE credit_card_id = (SELECT credit_card_id FROM maliin.credit_cards WHERE name = '${CREDITCARD.name}' AND due_day = '${CREDITCARD.due_day}' AND close_day='${CREDITCARD.close_day}')
    Execute Sql String      DELETE FROM maliin.credit_cards WHERE name = '${CREDITCARD.name}' AND due_day = '${CREDITCARD.due_day}' AND close_day='${CREDITCARD.close_day}'

    &{CREDITCARD}           CreditCard.Create Creditcard Itau
    Execute Sql String      DELETE FROM maliin.bills WHERE credit_card_id = (SELECT id FROM maliin.credit_cards WHERE name = '${CREDITCARD.name}' AND due_day = '${CREDITCARD.due_day}' AND close_day='${CREDITCARD.close_day}')
    Execute Sql String      DELETE FROM maliin.invoices WHERE credit_card_id = (SELECT credit_card_id FROM maliin.credit_cards WHERE name = '${CREDITCARD.name}' AND due_day = '${CREDITCARD.due_day}' AND close_day='${CREDITCARD.close_day}')
    Execute Sql String      DELETE FROM maliin.credit_cards WHERE name = '${CREDITCARD.name}' AND due_day = '${CREDITCARD.due_day}' AND close_day='${CREDITCARD.close_day}'

    Execute Sql String      DELETE FROM maliin.bills WHERE credit_card_id IS NULL AND description = 'Loren Ipsum'
    Execute Sql String      COMMIT

    &{CATEGORY}             Category.Create Category Food
    Execute Sql String      DELETE FROM maliin.categories WHERE name = '${CATEGORY.name}' AND is_investiment = '${CATEGORY.is_investiment}'

    &{CATEGORY}             Category.Create Category Investment
    Execute Sql String      DELETE FROM maliin.categories WHERE name = '${CATEGORY.name}' AND is_investiment = '${CATEGORY.is_investiment}' 


    &{ACCOUNT}              Account.Create Account Xpto
    Execute Sql String      DELETE FROM maliin.accounts WHERE name = '${ACCOUNT.name}' AND bank = '${ACCOUNT.bank}' AND agency = '${ACCOUNT.agency}' AND account = '${ACCOUNT.account}'

    &{ACCOUNT}              Account.Create Account Xyz
    Execute Sql String      DELETE FROM maliin.accounts WHERE name = '${ACCOUNT.name}' AND bank = '${ACCOUNT.bank}' AND agency = '${ACCOUNT.agency}' AND account = '${ACCOUNT.account}'
   
    Execute Sql String      DELETE FROM maliin.users WHERE first_name = '${USER.name}' AND last_name = '${USER.lastname}' AND email = '${USER.email}' AND document = '${USER.cpf}' AND gender = '${USER.gender}' AND api_token = '${USER.api_token}' AND password = '${USER.password_hash}'
    Disconnect From Database