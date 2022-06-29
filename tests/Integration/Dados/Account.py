import json
def dados_xpto():
    return {
        'name': 'Banco XPTO',
        'bank': 'Banco XPTO',
        'agency': '00001-0',
        'account': '09000000000'
    }
def dados_xyz():
    return {
        'name': 'Banco xyz',
        'bank': 'Banco xyz',
        'agency': '00021-0',
        'account': '09000000008'
    }
def dados_xpto_json():
    return json.dumps(create_account_xpto())
def dados_xyz_json():
    return json.dumps(create_account_xyz())