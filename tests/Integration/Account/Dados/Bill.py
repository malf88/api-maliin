import json
def create_bill_with_creditcard(creditcardId,categoryId):
    return {
        'description': 'Loren Ipsum',
        'amount': 3.14,
        'date': '2021-10-21',
        'due_date': '2021-10-21',
        'credit_card_id': creditcardId,
        'category_id': categoryId,
        'portion': 1
    }
def create_bill_without_creditcard(categoryId):
    return {
        'description': 'Loren Ipsum',
        'amount': 3.14,
        'date': '2021-10-21',
        'due_date': '2021-10-21',
        'category_id': categoryId,
        'portion': 1
    }
def create_bill_with_creditcard_json(creditcardId,categoryId):
    return json.dumps(create_bill_with_creditcard(creditcardId,categoryId))

def create_bill_without_creditcard_json(categoryId):
    return json.dumps(create_bill_without_creditcard(categoryId))