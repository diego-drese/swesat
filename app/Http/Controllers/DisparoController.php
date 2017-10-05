<?php

namespace App\Http\Controllers;


use App\PreDisparo;
use App\Telefone;
use Illuminate\Http\Request;
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

    public function pegarMensagem($token = null,Request $request){
        $token = $token ? $token : $request->get("token");
        if(empty($token)){
            return $this->error("Token para disparo nao encontrado", 400);
        }
        $usuario = Telefone::pegaUsuarioPorToken($token);
        if(empty($usuario)){
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
        $disparos = PreDisparo::carregaPaginado($usuario->user_id, $request, $request->get('offset', 0), $request->get('limit', 10));
        unlink($pathFile);
        return $this->successList($disparos, PreDisparo::carregaTotal($usuario->user_id, $request), 200);
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