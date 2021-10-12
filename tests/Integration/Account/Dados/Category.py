import json
def create_category_food():
    return {
        'name': 'Food',
        'is_investiment': False,
    }
def create_category_investment():
    return {
        'name': 'Investment',
        'is_investiment': True,
    }

def create_category_food_json():
    return json.dumps(create_category_food());

def create_category_investment_json():
    return json.dumps(create_category_investment());