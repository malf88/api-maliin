<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class Swagger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swagger:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gerar arquivo do swagger';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $openAPI = \OpenApi\Generator::scan([
            base_path('app'),
            base_path('config')
        ]);
        Storage::disk('public')->put('openapi.json', $openAPI->toJson());
        $this->info("Arquivo swagger criado com sucesso");
    }
}
