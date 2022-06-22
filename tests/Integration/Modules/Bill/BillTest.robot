*** Settings ***
Resource    ../../Modules/Account/Resources/AccountResource.robot
Resource    ../../Modules/Bill/Resources/BillResource.robot
Resource    ../../Resources/Cenarios.robot
Resource    ../Category/Resources/CategoryResource.robot
Resource    ../Creditcard/Resources/CreditcardResource.robot
Library     ../../Dados/Account.py
Library     ../../Dados/User.py
Library     ../../Dados/Category.py
Library     ../../Dados/Bill.py
Library     ../../Dados/Creditcard.py
Library    Collections
Resource    Templates/BillTemplates.robot

*** Test Case ***                                                              Cartão de crédito         Portion
Caso de teste 01 - Deve inserir um lançamento                                  False                      1
    [Template]     Proccess Test Insert Bill
Caso de teste 02 - Deve inserir um lançamento com parcela                      False                      4
    [Template]     Proccess Test Insert Bill
Caso de teste 03 - Deve inserir um lançamento com cartão                       True                       1
    [Template]     Proccess Test Insert Bill
Caso de teste 04 - Deve inserir um lançamento com cartão e parcela             True                       4
    [Template]     Proccess Test Insert Bill

Caso de teste 05 - Deve buscar um lançamento pelo id                           False                      1
    [Template]     Proccess Teste Get Bill
Caso de teste 06 - Deve buscar um lançamento pelo id com parcela               False                      4
    [Template]     Proccess Teste Get Bill
Caso de teste 07 - Deve buscar um lançamento pelo id com cartão                True                       1
    [Template]     Proccess Teste Get Bill
Caso de teste 08 - Deve buscar um lançamento pelo id com cartão e parcela      True                       4
    [Template]     Proccess Teste Get Bill
Caso de teste 09 - Deve alterar um lançamento                                  False                      1
    [Template]     Proccess Teste Update Bill
Caso de teste 10 - Deve alterar um lançamento com parcela                      False                      6
    [Template]     Proccess Teste Update Bill
Caso de teste 11 - Deve alterar um lançamento com cartão                       True                       1
    [Template]     Proccess Teste Update Bill
Caso de teste 12 - Deve alterar um lançamento com cartão e parcela             True                       4
    [Template]     Proccess Teste Update Bill

    