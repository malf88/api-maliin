<?php

namespace App\Modules\Account\ServicesLocal;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface CategoryServiceLocal
{
    public function getCategoriesFromUser(User $user):Collection;
    public function getCategoriesFromUserLogged():Collection;
    public function insertCategory(User $user,array $dataCategory): Category;
    public function updateCategory(int $id,array $dataCategory): Category;
    public function deleteCategory(int $id): bool;
    public function getCategoryById(int $id):Category;

}
