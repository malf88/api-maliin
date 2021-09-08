<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ErrorException;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class GenerateStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criar estrutura de novo módulo';

    protected $directories = [
        'Business'      => 'Business',
        'Controllers'   => 'Controller',
        'Impl'          => 'RepositoryInterface',
        'Providers'     => 'ServiceProvider',
        'Repository'    => 'Repository',
        'Route'         => '',
        'Services'      => 'Service',
        'ServicesLocal' => 'ServiceLocal'
    ];
    protected string $directoryModules = 'Modules';
    public function __construct(Filesystem $files)
    {
        //$this->path = app_path('Modules');
        parent::__construct($files);
    }


    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {

        $moduleName = $this->argument('name');


        //$teste = parent::replaceClass($this->getStub(),'App/Modules/'.$moduleName);
        //$this->info($teste);
        $module = $this->getModuleName($moduleName);
        $class = $this->getClassName($moduleName);
        //$path = app_path('Modules'). DIRECTORY_SEPARATOR . $module. DIRECTORY_SEPARATOR . $class;

        $this->info('Criando diretórios');
        $this->makeDirectories($module);
        $this->info('Diretórios criados');
        $this->info("Criando classes");
        $this->makeClasses($module,$class);
        $this->info('Classes criadas');


    }


    private function checkParameterIsDefaultFormat(string $parameter):bool{
        if($this->getModuleName($parameter) == ''){
            return false;
        }
        if($this->getClassName($parameter) == ''){
            return false;
        }
        return true;
    }
    private function getModuleName(string $name):string
    {
        try {
            return ucfirst(explode('/', $name)[0]);
        }catch (ErrorException $e){
            return '';
        }
    }
    private function getClassName(string $name):string
    {
        try{
            return ucfirst(explode('/',$name)[1]);
        }catch (ErrorException $e){
            return '';
        }

    }
    public function getModulePath(string $moduleName):string
    {
        return app_path($this->directoryModules).DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR;
    }
    public function makeDirectories(string $moduleName):void
    {
        $path = $this->getModulePath($moduleName);
        if(!File::exists($path)){
            File::makeDirectory($path);
        }
        foreach($this->directories as $diretory => $files){
            $newDirectory = $path. $diretory;
            if(!File::exists($newDirectory)) {
                //$check = $this->makeDirectory($newDirectory);
                File::makeDirectory($newDirectory);
                $this->info("Criado diretório $newDirectory!");
            }else {
                $this->info("Diretório $newDirectory já existe!");
            }
        }
    }

    public function makeClasses(string $moduleName, string $baseName):void
    {
        $path = $this->getModulePath($moduleName);
        foreach($this->directories as $directory => $stub){
            $pathClass = $path . $directory . DIRECTORY_SEPARATOR;
            if($stub != '')
                $this->makeClass($pathClass,$moduleName, $baseName, $stub);

        }


    }

    private function makeClass(string $path, string $moduleName, string $baseName, string $stub):void
    {
        $file = $path.$baseName."$stub.php";
        if(!File::exists($file)) {
            $repositoryFile = file_get_contents(app_path('Console/Commands/Stubs/') . $stub . '.stub');
            $repositoryFile = str_replace(['{module}', '{basename}'], [$moduleName, $baseName], $repositoryFile);

            File::put($file, $repositoryFile);
            $this->info("Criado o arquivo $baseName$stub.php no diretório $path");
        }else{
            $this->info("Arquivo $baseName$stub.php já existe no diretório $path!");
        }
    }

}
