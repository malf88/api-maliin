import json
def create_account_xpto():
    return {
        'name': 'Banco XPTO',
        'bank': 'Banco XPTO',
        'agency': '00001-0',
        'account': '09000000000'
    }
def create_account_xyz():
    return {
        'name': 'Banco xyz',
        'bank': 'Banco xyz',
        'agency': '00021-0',
        'account': '09000000008'
    }
def create_account_xpto_json():
    return json.dumps(create_account_xpto())
def create_account_xyz_json():
    return json.dumps(create_account_xyz())