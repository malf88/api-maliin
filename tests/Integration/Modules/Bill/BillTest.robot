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

Caso de teste 13 - Deve excluir um lançamento                                  False                      1
    [Template]     Proccess Teste Delete Bill

Caso de teste 14 - Deve excluir um lançamento com parcela                      False                      4
    [Template]     Proccess Teste Delete Bill

Caso de teste 15 - Deve excluir um lançamento cartão                           True                       1
    [Template]     Proccess Teste Delete Bill
Caso de teste 16 - Deve excluir um lançamento cartão e parcela                 True                       4
    [Template]     Proccess Teste Delete Bill
Caso de teste 17 - Deve pagar um lançamento                                    False                      1
    [Template]     Proccess Teste Pay Bill

Caso de teste 18 - Deve pagar um lançamento com parcela                        False                      4
    [Template]     Proccess Teste Pay Bill

Caso de teste 19 - Deve pagar um lançamento cartão                             True                       1
    [Template]     Proccess Teste Pay Bill
Caso de teste 20 - Deve pagar um lançamento cartão e parcela                   True                       4
    [Template]     Proccess Teste Pay Bill
Caso de teste 21 - Deve buscar todos os lançamentos da conta            False                      1
    [Template]     Proccess Teste Get All Bills
Caso de teste 22 - Deve buscar todos os lançamentos da conta com parcela              False                      4
    [Template]     Proccess Teste Get All Bills

Caso de teste 23 - Deve buscar todos os lançamentos da conta por período            False                      1
    [Template]     Proccess Teste Get All Bills Per Period
    [Tags]    Between
Caso de teste 24 - Deve buscar todos os lançamentos da conta por período com parcela              False                      4
    [Template]     Proccess Teste Get All Bills Per Period   
    [Tags]    Between