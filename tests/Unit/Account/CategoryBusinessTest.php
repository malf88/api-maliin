<?php

namespace Tests\Unit\Account;

use App\Models\Category;
use App\Models\User;
use App\Modules\Account\Bussines\CategoryBusiness;
use App\Modules\Account\Repository\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;
use Tests\TestCase;

class CategoryBusinessTest extends TestCase
{
    private function fatoryCategoryList(){
        $category1 = new Category();
        $category1->id = 1;
        $category1->name = 'Mercado';
        $category1->is_investiment = true;

        $category2 = new Category();
        $category2->id = 2;
        $category2->name = 'Pets';
        $category2->is_investiment = false;

        $category3 = new Category();
        $category3->id = 3;
        $category3->name = 'Lanches';
        $category3->is_investiment = true;

        return Collection::make([$category1,$category2,$category3]);
    }
    /**
     * @test
     */
    public function deveListarCategoriaParaUmUsuario()
    {
        $user = new User();
        $listCategory = $this->fatoryCategoryList();

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository
            ->method('getCategoriesFromUser')
            ->with($user)
            ->willReturn($listCategory);
        $categoryBusiness = new CategoryBusiness($categoryRepository);

        $categories = $categoryBusiness->getCategoriesFromUser($user);
        $this->assertIsIterable($categories);
        $this->assertCount(3,$categories);
        $this->assertEquals('Mercado',$categories->get(0)->name);
        $this->assertTrue($categories->get(0)->is_investiment);
    }
    /**
     * @test
     */
    public function deveListarCategoriaParaUmUsuarioLogado()
    {
        $user = new User();
        Auth::shouldReceive('user')
            ->once()
            ->andReturn($user);
        $listCategory = $this->fatoryCategoryList();

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository
            ->method('getCategoriesFromUser')
            ->with($user)
            ->willReturn($listCategory);
        $categoryBusiness = new CategoryBusiness($categoryRepository);

        $categories = $categoryBusiness->getCategoriesFromUserLogged();
        $this->assertIsIterable($categories);
        $this->assertCount(3,$categories);
        $this->assertEquals('Mercado',$categories->get(0)->name);
        $this->assertTrue($categories->get(0)->is_investiment);
    }

    /**
     * @test
     */
    public function deveSalvarCategoria()
    {
        $user = new User();
        $dataCategory = [
            'name'              => 'Construção',
            'is_investiment'    =>true
        ];
        $category = new Category();
        $category->id = 1;
        $category->fill($dataCategory);

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository
            ->method('saveCategory')
            ->with($user,$dataCategory)
            ->willReturn($category);
        $categoryBusiness = new CategoryBusiness($categoryRepository);

        $newCategory = $categoryBusiness->insertCategory($user,$dataCategory);
        $this->assertEquals('Construção',$category->name);
        $this->assertTrue($category->is_investiment);
    }

    /**
     * @test
     */
    public function deveAtualizarCategoria()
    {

        $dataCategory = [
            'name'              => 'Construção',
            'is_investiment'    =>  false
        ];
        $id = 1;
        $category = new Category();
        $category->id = 1;
        $category->fill($dataCategory);

        $user = $this->createPartialMock(User::class,['categories']);
        $user->method('categories')
            ->willReturn($this->fatoryCategoryList());
        Auth::shouldReceive('user')->andReturn($user);

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository
            ->method('updateCategory')
            ->with($id,$dataCategory)
            ->willReturn($category);
        $categoryBusiness = new CategoryBusiness($categoryRepository);

        $newCategory = $categoryBusiness->updateCategory($id,$dataCategory);
        $this->assertEquals('Construção',$category->name);
        $this->assertFalse($category->is_investiment);
    }
    /**
     * @test
     */
    public function deveRetornarErroAoAtualizarCategoriaInexistente()
    {

        $dataCategory = [
            'name'              => 'Construção',
            'is_investiment'    =>  false
        ];
        $id = 5;
        $category = new Category();
        $category->id = 1;
        $category->fill($dataCategory);

        $user = $this->createPartialMock(User::class,['categories']);
        $user->method('categories')
            ->willReturn($this->fatoryCategoryList());
        Auth::shouldReceive('user')->andReturn($user);

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository
            ->method('updateCategory')
            ->with($id,$dataCategory)
            ->willReturn($category);
        $categoryBusiness = new CategoryBusiness($categoryRepository);
        $this->expectException(ItemNotFoundException::class);
        $newCategory = $categoryBusiness->updateCategory($id,$dataCategory);

    }
    /**
     * @test
     */
    public function deveDeletarCategoria()
    {


        $id = 1;
        $user = $this->createPartialMock(User::class,['categories']);
        $user->method('categories')
            ->willReturn($this->fatoryCategoryList());
        Auth::shouldReceive('user')->andReturn($user);

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository
            ->method('deleteCategory')
            ->with($id)
            ->willReturn(true);
        $categoryBusiness = new CategoryBusiness($categoryRepository);

        $statusDelete = $categoryBusiness->deleteCategory($id);

        $this->assertTrue($statusDelete);
    }

    /**
     * @test
     */
    public function deveDispararExcecaoAoExcluirCategoriaNaoExistente()
    {
        $id = 5;
        $user = $this->createPartialMock(User::class,['categories']);
        $user->method('categories')
            ->willReturn($this->fatoryCategoryList());
        Auth::shouldReceive('user')->andReturn($user);

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository
            ->method('deleteCategory')
            ->with($id)
            ->willReturn(true);
        $categoryBusiness = new CategoryBusiness($categoryRepository);
        $this->expectException(ItemNotFoundException::class);
        $statusDelete = $categoryBusiness->deleteCategory($id);

    }
    /**
     * @test
     */
    public function deveRetornarCategoriaBuscadaPeloId()
    {
        $id = 1;
        $user = $this->createPartialMock(User::class,['categories']);
        $user->method('categories')
            ->willReturn($this->fatoryCategoryList());
        Auth::shouldReceive('user')->andReturn($user);

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository
            ->method('getCategoryById')
            ->with($id)
            ->willReturn($this->fatoryCategoryList()->get(0));
        $categoryBusiness = new CategoryBusiness($categoryRepository);

        $category = $categoryBusiness->getCategoryById($id);

        $this->assertEquals($id,$category->id);

    }

    /**
     * @test
     */
    public function deveDispararExcecaoAoBuscarCategoriaInexistente()
    {
        $id = 5;
        $user = $this->createPartialMock(User::class,['categories']);
        $user->method('categories')
            ->willReturn($this->fatoryCategoryList());
        Auth::shouldReceive('user')->andReturn($user);

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository
            ->method('getCategoryById')
            ->with($id)
            ->willReturn($this->fatoryCategoryList()->get(0));
        $categoryBusiness = new CategoryBusiness($categoryRepository);
        $this->expectException(ItemNotFoundException::class);
        $category = $categoryBusiness->getCategoryById($id);


    }

}
