<?php

namespace App\Console\Commands;

use App\Agendamento;
use App\Contato;
use App\Mensagem;
use App\PreDisparo as PreDisparoEntidade;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
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
       /////Carrega as mensagen para ser pre-carregadas
       $agendamentos = Agendamento::getAgendamentosParaProcessar();
       foreach ($agendamentos as $agendamento){
           $agora           = new \DateTime();
           $data_disparo    = new \DateTime($agendamento->dat_disparo);
           $data_fim        = new \DateTime($agendamento->data_fim);

           if($data_fim->getTimestamp() < $agora->getTimestamp()){
               $this->setError($agendamento,'A data de processo é maior que a data fim: DataProcesso['.$agora->format('Y-m-d H:i:s').'] DataFim['.$data_fim->format('Y-m-d H:i:s').']');
               continue;
           }

           $contatos = [];
           $mensagem =  Mensagem::find($agendamento->mensagem_id);
           if(!$mensagem){
               $this->setError($agendamento,'A mensagem com id['.$agendamento->mensagem_id.'] não foi encontrada');
           }


           if($agendamento->tipo=="GRUPO"){
               $contatos = Contato::where("grupo_id", $agendamento->grupo_id)->join('grupo_contato','grupo_contato.contato_id', 'contato.id')->get();
           }else if($agendamento->tipo=="UNICO"){
               $contatos = Contato::where("id",$agendamento->contato_id)->get();
           }else{
               $this->setError($agendamento,'O tipo['.$agendamento->tipo.'] não está definido');
           }

           foreach ($contatos as $contato){

               $preDisparo = [
                   'mensagem'           => $mensagem->texto,
                   'telefone'           => $contato->telefone,
                   'mensagem_id'        => $mensagem->id,
                   'agendamento_id'    => $agendamento->id,
                   'contato_id'         => $contato->id,
               ];

               try{
                   PreDisparoEntidade::create($preDisparo);
               }catch (QueryException $e){
                   Log::warning("Esse pre disparo já foi cadastrado: MensId[{$mensagem->id}] AgendamentoId[{$agendamento->id}] ContatoId[{$contato->id}]");
               }
           }

           $agendamento->status_disparo = Agendamento::PR;
           $agendamento->total_agendamento = count($contatos);
           $agendamento->save();



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

