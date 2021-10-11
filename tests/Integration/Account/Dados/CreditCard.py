import json
def create_creditcard_nubank():
    return {
        'name': 'Nubank Teste',
        'due_day': 15,
        'close_day': 7
    }
def create_creditcard_itau():
    return {
        'name': 'Itaú Teste',
        'due_day': 7,
        'close_day': 30
    }

def create_creditcard_nubank_json():
    return json.dumps(create_creditcard_nubank());

def create_creditcard_itau_json():
    return json.dumps(create_creditcard_itau());