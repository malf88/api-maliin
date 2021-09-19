<?php

namespace App\Modules\Account\Impl\Business;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface CategoryBusinessInterface
{
    public function getCategoriesFromUser(Model $user):Collection;
    public function getCategoriesFromUserLogged():Collection;
    public function getCategoryById(int $id):Model;
    public function insertCategory(User $user,array $dataCategory): Model;
    public function updateCategory(int $id,array $dataCategory): Model;
    public function deleteCategory(int $id): bool;

}
