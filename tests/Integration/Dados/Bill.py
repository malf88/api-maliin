

def create_bill_with_creditcard(creditcardId,categoryId):
    return {
        'description': 'Loren Ipsum',
        'amount': 3.14,
        'date': '2021-10-21',
        'due_date': '2021-10-21',
        'credit_card_id': creditcardId,
        'pay_day': None,
        'category_id': categoryId,
        'portion': 1
    }
def create_bill_without_creditcard(categoryId):
    return {
        'description': 'Loren Ipsum',
        'amount': 3.14,
        'date': '2021-10-21',
        'due_date': '2021-10-22',
        'category_id': categoryId,
        'barcode':'Teste',
        'portion': 1,
        'pay_day': None
    }

def create_bill_without_creditcard_with_portion(categoryId):
    return {
        'description': 'Loren Ipsum',
        'amount': 3.14,
        'date': '2021-10-21',
        'due_date': '2021-10-21',
        'category_id': categoryId,
        'portion': 4,
        'pay_day': None
    }

def create_bill_with_creditcard_with_portion(creditcardId,categoryId):
    return {
        'description': 'Loren Ipsum',
        'amount': 3.14,
        'date': '2021-10-21',
        'due_date': '2021-10-21',
        'credit_card_id': creditcardId,
        'category_id': categoryId,
        'portion': 5,
        'pay_day': None
    }