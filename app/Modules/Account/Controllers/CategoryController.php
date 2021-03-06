<?php

namespace App\Modules\Account\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\Impl\Business\CategoryBusinessInterface;
use App\Modules\Account\ServicesLocal\CategoryServiceLocal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends Controller
{
    public function __construct(private CategoryBusinessInterface $categoryServices)
    {
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
        return response($this->categoryServices->getCategoriesFromUserLogged(),200);
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
        try{
            return response($this->categoryServices->getCategoryById($id),200);
        }catch (NotFoundHttpException $e){
            return response($e->getMessage(),404);
        }

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
        return response($this->categoryServices->insertCategory(Auth::user(),$request->all()),201);
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
     *             mediaType="text/json",
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
        try{
            return response($this->categoryServices->updateCategory($id,$request->all()),200);
        }catch (NotFoundHttpException $e){
            return response($e->getMessage(), 404);
        }

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
        try{
            return response($this->categoryServices->deleteCategory($id), 200);
        }catch (NotFoundHttpException $e){
            return response($e->getMessage(),404);
        }

    }
}
