<?php

namespace App\Modules\Account\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\Services\AccountService;
use App\Modules\Account\Services\AccountServiceLocal;
use App\Modules\Account\Services\CategoryServiceLocal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    private CategoryServiceLocal $categoryServices;
    public function __construct(CategoryServiceLocal $categoryService)
    {
        $this->categoryServices = $categoryService;
    }
    public function index(Request $request)
    {
        return $this->categoryServices->getCategoriesFromUserLogged();
    }
    public function show(Request $request, int $id)
    {
        return $this->categoryServices->getCategoryById($id);
    }
    public function insert(Request $request)
    {
        return $this->categoryServices->insertCategory(Auth::user(),$request->all());
    }

    public function update(Request $request,int $id)
    {
        return $this->categoryServices->updateCategory($id,$request->all());
    }

    public function delete(Request $request,int $id)
    {
        return $this->categoryServices->deleteCategory($id);
    }
}
