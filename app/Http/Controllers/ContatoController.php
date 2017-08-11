<?php 

namespace App\Http\Controllers;

use App\Contato;
use Illuminate\Http\Request;

class ContatoController extends Controller{

	public function __construct(){
		$this->middleware('oauth');
		//$this->middleware('authorize:' . __CLASS__, ['except' => ['index', 'show', 'store']]);
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

    public function index(Request $request){
		$contatos = Contato::carregaPaginado($this->getUserId(), $request, $request->get('offset', 0), $request->get('limit', 10));
		return $this->successList($contatos, Contato::carregaTotal($this->getUserId(), $request), 200);
	}

    public function carregar($id){
        $contato = Contato::where("user_id", $this->getUserId())->find($id);
        if(!$contato){
            return $this->error("O contato com id {$id} nao existe", 404);
        }
        return $this->success($contato, 200);
    }

	public function adicionar(Request $request){
		$this->validarRequisicao($request);
        $contato = Contato::create([
					'nome'          => $request->get('nome'),
					'sobre_nome'    => $request->get('sobre_nome'),
					'email'         => $request->get('email'),
					'ddd'           => $request->get('ddd'),
					'telefone'      => $request->get('telefone'),
					'data_nascimento'=> $request->get('data_nascimento'),
					'user_id'       => $this->getUserId()
				]);
		return $this->success("O contato com o Id {$contato->id} foi criado com sucesso!", 201);
	}

	public function atualizar(Request $request, $id){
        $contato = Contato::where("user_id", $this->getUserId())->find($id);
		if(!$contato){
            return $this->error("O contato com id {$id} nao existe", 404);
		}

		$this->validarRequisicao($request);
        $contato->nome 		        = $request->get('nome');
        $contato->email 		    = $request->get('email');
        $contato->telefone 		    = $request->get('telefone');
        $contato->data_nascimento   = $request->get('data_nascimento');
        $contato->save();
		return $this->success("O contato com {$contato->id} foi atualizado", 200);
	}

	public function deletar($id){
        $contato = Contato::where("user_id", $this->getUserId())->find($id);
		if(!$contato){
            return $this->error("O contato com id {$id} nao existe", 404);
		}
        $contato->delete();
		return $this->success("O Contato com id {$id} foi removido com sucesso", 200);
	}

    public function ativar($id){
        $contato = Contato::where("user_id", $this->getUserId())->find($id);
        if(!$contato){
            return $this->error("O contato com id {$id} nao existe", 404);
        }
        $contato->ativo 		= "S";
        $contato->save();
        return $this->success("O contato {$contato->id} foi ativado com sucesso", 200);
    }

    public function desativar($id){
        $contato = Contato::where("user_id", $this->getUserId())->find($id);
        if(!$contato){
            return $this->error("O contato com id {$id} nao existe", 404);
        }
        $contato->ativo 		= "N";
        $contato->save();
        return $this->success("O contato {$contato->id} foi ativado com sucesso", 200);
    }

	public function validarRequisicao(Request $request){
		$regras = [
			'nome'      => 'required',
			'email'     => 'required',
			'telefone'  => 'required',
		];
		$this->validate($request, $regras);
	}

	public function estaAutorizado(Request $request){
		$resource = "contato";
		$post     = Contato::find($this->getArgs($request)["id"]);

		return $this->authorizeUser($request, $resource, $post);
	}
}