<?php

namespace App\Console\Commands;

use App\Agendamento;
use App\PreDisparo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FinalizaAgendamento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swesat:finaliza_agendamento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Carrega as mensagens agendadas que a data de termino ja esgotaram';

    public function __construct(){
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
       $this->info("Processando agendamentos finalizados");
        Log::info("Processando agendamentos finalizados");
       /////Carrega as mensagen para ser pre-carregadas
       $agendamentos = Agendamento::getAgendamentosEncerrados();
       foreach ($agendamentos as $agendamento){
           $totalDisparos   = PreDisparo::carregaTotalEnviado($agendamento);
           $agendamento->status_disparo = Agendamento::FZ;
           $agendamento->total_disparo  = $totalDisparos;
           $agendamento->save();
           PreDisparo::atualizaDisparosNaoEnviados($agendamento);
       }
    }

    public function setError($agendamento, $obs){
        Agendamento::where('id',$agendamento->id)
            ->update([
                'status_disparo'=> Agendamento::ERR,
                'obs'           => $obs
            ]);
    }




}

