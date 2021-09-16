<?php

namespace App\Modules\Account\Business;

use App\Models\User;
use App\Modules\Account\Impl\Business\CategoryBusinessInterface;
use App\Modules\Account\Impl\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;

class CategoryBusiness implements CategoryBusinessInterface
{
    private CategoryRepositoryInterface $categoryRepository;
    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getCategoriesFromUser(Model $user):Collection
    {
        return $this->categoryRepository->getCategoriesFromUser($user);
    }

    public function getCategoriesFromUserLogged():Collection
    {
        $user = Auth::user();
        return $this->categoryRepository->getCategoriesFromUser($user);
    }

    public function getCategoryById(int $id):Model
    {
        if($this->userHasCategory(Auth::user(),$id)) {
            return $this->categoryRepository->getCategoryById($id);
        }else{
            throw new ItemNotFoundException("Registro não encontrado");
        }
    }
    public function insertCategory(User $user,array $dataCategory): Model
    {
        return $this->categoryRepository->saveCategory($user,$dataCategory);
    }

    public function updateCategory(int $id,array $dataCategory): Model
    {
        if($this->userHasCategory(Auth::user(),$id)) {
            return $this->categoryRepository->updateCategory($id,$dataCategory);
        }else{
            throw new ItemNotFoundException("Registro não encontrado");
        }
    }
    public function deleteCategory(int $id): bool
    {
        if($this->userHasCategory(Auth::user(),$id)) {
            return $this->categoryRepository->deleteCategory($id);
        }else{
            throw new ItemNotFoundException("Registro não encontrado");
        }
    }
    /**
     * Método que verifica se o registro que está sendo acessado é do usuário autenticado.
     * @param User $user
     * @param int $id
     * @return bool
     */
    private function userHasCategory(Model $user,int $id):bool
    {
        return $user->categories()->find($id) != null;
    }
}
