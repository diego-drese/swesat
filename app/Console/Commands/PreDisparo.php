<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PreDisparo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swesat:pre_disparo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Carrega as mensagens agendadas para o pre disparo';

    public function __construct(){
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
       $this->info("Processando");
       Log::info("Testando");
    }
}

