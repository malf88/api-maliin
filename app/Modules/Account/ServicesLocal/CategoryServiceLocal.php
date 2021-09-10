<?php

namespace App\Modules\Account\ServicesLocal;


use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface CategoryServiceLocal
{
    public function getCategoriesFromUser(User $user):Collection;
    public function getCategoriesFromUserLogged():Collection;
    public function insertCategory(Model $user,array $dataCategory): Model;
    public function updateCategory(int $id,array $dataCategory): Model;
    public function deleteCategory(int $id): bool;
    public function getCategoryById(int $id):Model;

}
