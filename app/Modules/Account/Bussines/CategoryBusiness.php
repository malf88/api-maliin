<?php

namespace App\Modules\Account\Bussines;

use App\Models\Category;
use App\Models\User;
use App\Modules\Account\Respository\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;

class CategoryBusiness
{
    private CategoryRepository $categoryRepository;
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getCategoriesFromUser(User $user):Collection
    {
        return $this->categoryRepository->getCategoriesFromUser($user);
    }

    public function getCategoriesFromUserLogged():Collection
    {
        $user = Auth::user();
        return $this->categoryRepository->getCategoriesFromUser($user);
    }

    public function getCategoryById(int $id):Category
    {
        if($this->userHasCategory(Auth::user(),$id)) {
            return $this->categoryRepository->getCategoryById($id);
        }else{
            throw new ItemNotFoundException("Registro não encontrado");
        }
    }
    public function insertCategory(User $user,array $dataCategory): Category
    {
        return $this->categoryRepository->saveCategory($user,$dataCategory);
    }

    public function updateCategory(int $id,array $dataCategory): Category
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
    private function userHasCategory(User $user,int $id):bool{
        return $user->categories()->find($id) != null;
    }
}
