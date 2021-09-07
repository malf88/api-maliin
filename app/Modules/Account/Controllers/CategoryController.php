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
    /**
     * @OA\Get(
     *     tags={"Categories"},
     *     summary="Retorna uma lista de categorias",
     *     description="Retornará uma lista de categorias do usuário logado",
     *     path="/category",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *          response="200",
     *          description="Uma lista de categorias"
     *     ),
     * )
     *
     */
    public function index(Request $request)
    {
        return $this->categoryServices->getCategoriesFromUserLogged();
    }
    /**
     * @OA\Get(
     *     tags={"Categories"},
     *     summary="Retorna a a categorias com o {id}",
     *     description="Retorna a categoria com o {id} informado",
     *     path="/category/{id}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do registro buscado",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *         ),
     *         style="form"
     *     ),
     *     @OA\Response(response="200", description="Um objeto de categoria"),
     *     @OA\Response(response="404", description="Categoria não encontrada")
     * ),
     *
     */
    public function show(Request $request, int $id)
    {
        return $this->categoryServices->getCategoryById($id);
    }
    /**
     * @OA\Post(
     *     tags={"Categories"},
     *     summary="Insere uma categoria",
     *     description="Insere uma nova categoria",
     *     path="/category",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="is_investiment",
     *                     type="bool"
     *                 ),
     *                 example={"name": "Lanches", "is_investiment": true}
     *             )
     *         )
     *     ),
     *     @OA\Response(response="201", description="Objeto inserido com sucesso"),
     * ),
     *
     */
    public function insert(Request $request)
    {
        return $this->categoryServices->insertCategory(Auth::user(),$request->all());
    }
    /**
     * @OA\Put(
     *     tags={"Categories"},
     *     summary="Altera uma categoria",
     *     description="Altera uma categoria existente",
     *     path="/category/{id}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do registro a ser alterado",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *         ),
     *         style="form"
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="is_investiment",
     *                     type="bool"
     *                 ),
     *                 example={"name": "Lanches", "is_investiment": true}
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Objeto alterado com sucesso"),
     *     @OA\Response(response="404", description="Objeto não encontrado"),
     * ),
     *
     */
    public function update(Request $request,int $id)
    {
        return $this->categoryServices->updateCategory($id,$request->all());
    }
    /**
     * @OA\Delete(
     *     tags={"Categories"},
     *     summary="Exclui a categoria com o {id}",
     *     description="Exclui uma categoria",
     *     path="/category/{id}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do registro buscado",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *         ),
     *         style="form"
     *     ),
     *     @OA\Response(response="200", description="Excluído com sucesso"),
     *     @OA\Response(response="404", description="Conta não encontrada")
     * ),
     *
     */
    public function delete(Request $request,int $id)
    {
        return $this->categoryServices->deleteCategory($id);
    }
}
