<?php

namespace App\Modules\Account\Respository;

use App\Models\Category;
use App\Models\User;
use App\Modules\Account\Impl\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getCategoriesFromUser(User $user):Collection{
        return $user->categories()->get();
    }

    public function saveCategory(User $user,array $categoryData):Category
    {
        $category = new Category();
        $category->fill($categoryData);
        $category->user_id = $user->id;
        $category->save();
        return $category;
    }

    public function updateCategory(int $id,array $categoryData):Category
    {
        $category = Category::find($id);
        $category->fill($categoryData);
        $category->update();
        return $category;
    }
    public function deleteCategory(int $id):bool
    {
        $category = Category::find($id);
        return $category->delete();
    }
    public function getCategoryById(int $id):Category{
        return Category::find($id);
    }
}
