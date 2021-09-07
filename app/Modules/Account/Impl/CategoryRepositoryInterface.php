<?php

namespace App\Modules\Account\Impl;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function getCategoriesFromUser(User $user):Collection;
    public function saveCategory(User $user,array $categoryData):Category;
    public function updateCategory(int $id,array $categoryData):Category;
    public function deleteCategory(int $id):bool;
    public function getCategoryById(int $id):Category;
}
