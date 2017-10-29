<?php 

namespace App\Http\Controllers;

use App\Telefone;
use Illuminate\Http\Request;

class TelefoneController extends Controller{

	public function __construct(){
		$this->middleware('oauth');
		//$this->middleware('authorize:' . __CLASS__, ['except' => ['index', 'show', 'store']]);
	}
    /**
     * Request
     *
     * @var request[offset]
     * @var request[limit]
     * @var request[numero]
    */

    public function index(Request $request){
		$telefones = Telefone::carregaPaginado($this->getUserId(), $request, $request->get('offset', 0), $request->get('limit', 10));
		return $this->successList($telefones, Telefone::carregaTotal($this->getUserId(), $request), 200);
	}

    public function carregar($id){
        $telefone = Telefone::where("user_id", $this->getUserId())->find($id);
        if(!$telefone){
            return $this->error("O telefone com id {$id} nao existe", 404);
        }
        return $this->success($telefone, 200);
    }

	public function adicionar(Request $request){
		$this->validarRequisicao($request);
        $telefone = Telefone::create([
					'numero'        => $request->get('numero'),
                    'user_id'       => $this->getUserId(),
					'token'         => str_random(25),
				]);
		return $this->success("O Telefone com o Id {$telefone->id} foi criado com sucesso!", 201);
	}

	public function atualizar(Request $request, $id){
        $telefone = Telefone::where("user_id", $this->getUserId())->find($id);
		if(!$telefone){
            return $this->error("O telefone com id {$id} nao existe", 404);
		}

		$this->validarRequisicao($request);
        $telefone->numero = $request->get('numero');
        $newToken = str_random(25);
        if($request->get('token')){
            $telefone->token = $request->get('token');
        }
        $telefone->save();
		return $this->success("O Telefone com {$telefone->id} foi atualizado seu novo token é:[{$newToken}]", 200);
	}

	public function deletar($id){
        $telefone = Telefone::where("user_id", $this->getUserId())->find($id);
		if(!$telefone){
            return $this->error("O telefone com id {$id} nao existe", 404);
		}
        $telefone->delete();
		return $this->success("O Telefone com id {$id} foi removido com sucesso", 200);
	}

    public function ativar($id){
        $telefone = Telefone::where("user_id", $this->getUserId())->find($id);
        if(!$telefone){
            return $this->error("O telefone com id {$id} nao existe", 404);
        }
        $telefone->ativo 		= "S";
        $telefone->save();
        return $this->success("O telefone {$telefone->id} foi ativado com sucesso", 200);
    }

    public function desativar($id){
        $telefone = Telefone::where("user_id", $this->getUserId())->find($id);
        if(!$telefone){
            return $this->error("O telefone com id {$id} nao existe", 404);
        }
        $telefone->ativo 		= "N";
        $telefone->save();
        return $this->success("O telefone {$telefone->id} foi ativado com sucesso", 200);
    }

	public function validarRequisicao(Request $request){
		$regras = [
			'numero'  => 'required',
		];
		$this->validate($request, $regras);
	}

	public function estaAutorizado(Request $request){
		$resource = "telefone";
		$post     = Telefone::find($this->getArgs($request)["id"]);

		return $this->authorizeUser($request, $resource, $post);
	}
}