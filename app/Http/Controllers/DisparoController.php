<?php

namespace App\Http\Controllers;


use App\Agendamento;
use App\PreDisparo;
use App\Telefone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller as BaseController;

class DisparoController extends BaseController{

    public function __construct(){

    }
    /**
     * Request
     *
     * @var request[offset]
     * @var request[limit]
     * @var request[nome]
     * @var request[sobre_nome]
     * @var request[telefone]
     * @var request[email]
     * @var request[ativo] = S para sim N para nao
     * @var request[data_nascimento]
     */

    public function pegarMensagem($token = null, Request $request){
        $token = $token ? $token : $request->get("token");
        Log::info("Pegando mensagem pelo token[$token]");
        if(empty($token)){
            return $this->error("Token para disparo nao encontrado", 400);
        }
        $usuario = Telefone::pegaUsuarioPorToken($token);
        if(empty($usuario)){
            Log::error("Usuario nao encontrado para o token[{$token}]");
            return $this->error("Usuario nao encontrado para o token[{$token}]", 200);
        }

        $total = 0;
        $pathFile = storage_path("framework/lock_table-user-{$usuario->user_id}.lock");
        while (file_exists($pathFile)){
            $total++;
            sleep(1);
            if($total>=10){
                return $this->successList(null, 0, 200);
                unlink($pathFile);
            }
        }
        file_put_contents($pathFile, "Carregando");
        $disparos = PreDisparo::carregaParaEnvio($usuario->user_id, $request, $request->get('offset', 0), $request->get('limit', 10));
        unlink($pathFile);
        Log::info("Disparos encontrados", [$disparos]);
        return $this->successList($disparos, PreDisparo::carregaTotal($usuario->user_id, $request), 200);
    }

    public function notificarMensagem($notificacao, $token = null, $id = null, Request $request){
        $token = $token ? $token : $request->get("token");
        Log::info("Notificando envio pelo[$token]");
        if(empty($token)){
            return $this->error("Token para disparo nao encontrado", 400);
        }
        $usuario = Telefone::pegaUsuarioPorToken($token);
        if(empty($usuario)){
            Log::error("Usuario nao encontrado para o token[{$token}]");
            return $this->error("Usuario nao encontrado para o token[{$token}]", 200);
        }

        if(!$id){
            Log::error("Id nao encontrado[{$id}]");
            return $this->error("Id nao encontrado[{$id}]", 200);
        }

        if(!$notificacao || ($notificacao!="ENVIADO" || $notificacao!="NAOENVIADO")){
            Log::error("Notificacao invalida[{$notificacao}]");
            return $this->error("Notificacao invalida[{$notificacao}]", 200);
        }

        PreDisparo::where("id", $id)->update(['status_envio' => $notificacao]);
        if($notificacao=="ENVIADO"){
            $predisparo = PreDisparo::where("id", $id)->first();
            Agendamento::where('id', $predisparo->agendamento_id)->increment('total_disparo');
        }

        return $this->successList(['mens'=>"PreDisparo atualizado com sucesso"], 0, 200);
    }

    public function successList($data, $recordsTotal=0, $code){

        return response()->json(['data' => $data, 'recordsTotal' => $recordsTotal, 'recordsFiltered' => count($data)], $code);
    }
    /**
     * Return a JSON response for error.
     *
     * @param  array  $message
     * @param  string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($message, $code){
        return response()->json(['message' => $message], $code);
    }


}