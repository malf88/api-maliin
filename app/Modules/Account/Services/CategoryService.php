<?php

namespace App\Modules\Account\Services;

use App\Models\Category;
use App\Models\User;
use App\Modules\Account\Impl\Business\CategoryBusinessInterface;
use App\Modules\Account\ServicesLocal\CategoryServiceLocal;
use Illuminate\Database\Eloquent\Collection;

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

    public function updateCategory(int $id, array $dataCategory): Category
    {
        return $this->categoryBusiness->updateCategory($id,$dataCategory);
    }

    public function deleteCategory(int $id): bool
    {
        return $this->categoryBusiness->deleteCategory($id);
    }

    public function getCategoryById(int $id): Category
    {
        return $this->categoryBusiness->getCategoryById($id);
    }

    public function insertCategory(User $user, array $dataCategory): Category
    {
        return $this->categoryBusiness->insertCategory($user,$dataCategory);
    }

}