<?php

namespace App\Modules\Account\Services;

use App\Models\Category;
use App\Models\User;
use App\Modules\Account\Impl\Business\CategoryBusinessInterface;
use App\Modules\Account\ServicesLocal\CategoryServiceLocal;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CategoryService implements CategoryServiceLocal
{
    private CategoryBusinessInterface $categoryBusiness;
    public function __construct(CategoryBusinessInterface $categoryBusiness)
    {
        return $this->categoryBusiness = $categoryBusiness;
    }

    public function getCategoriesFromUser(User $user): Collection
    {
        return $this->categoryBusiness->getCategoriesFromUser($user);
    }

    public function getCategoriesFromUserLogged(): Collection
    {
        return $this->categoryBusiness->getCategoriesFromUserLogged();
    }

    public function updateCategory(int $id, array $dataCategory): Model
    {
        return $this->categoryBusiness->updateCategory($id,$dataCategory);
    }

    public function deleteCategory(int $id): bool
    {
        return $this->categoryBusiness->deleteCategory($id);
    }

    public function getCategoryById(int $id): Model
    {
        return $this->categoryBusiness->getCategoryById($id);
    }

    public function insertCategory(Model $user, array $dataCategory): Model
    {
        return $this->categoryBusiness->insertCategory($user,$dataCategory);
    }

}
