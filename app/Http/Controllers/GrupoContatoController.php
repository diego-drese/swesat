<?php 

namespace App\Http\Controllers;

use App\Contato;
use App\Grupo;
use App\GrupoContato;
use Illuminate\Http\Request;

class GrupoContatoController extends Controller{

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
     * @var request[id_grupo]
     * @var request[id_contato]
    */

    public function grupoContato($id_contato){
        $contato = Contato::find($id_contato);
        if(isset($contato->id)){
            $contato->grupos= GrupoContato::carregaTodosOsGruposDoContato($this->getUserId(), $contato->id);
        }
        return $this->successList($contato, 1, 200);
	}

    public function contatoGrupo(Request $request){

    }

    public function associa_contato_ao_grupo($contato_id, $grupo_id){

    }
    public function associa_grupo_ao_contato($grupo_id,$contato_id){


    }

	public function adicionar(Request $request){
		$this->validarRequisicao($request);
        $contato = Grupo::create([
					'nome'          => $request->get('nome'),
					'user_id'       => $this->getUserId()
				]);
		return $this->success("O grupo com o Id {$contato->id} foi criado com sucesso!", 201);
	}

	public function atualizar(Request $request, $id){
        $contato = Grupo::where("user_id", $this->getUserId())->find($id);
		if(!$contato){
            return $this->error("O grupo com id {$id} nao existe", 404);
		}

		$this->validarRequisicao($request);
        $contato->nome 		        = $request->get('nome');
        $contato->save();
		return $this->success("O grupo com {$contato->id} foi atualizado", 200);
	}

	public function deletar($id){
        $contato = Grupo::where("user_id", $this->getUserId())->find($id);
		if(!$contato){
            return $this->error("O grupo com id {$id} nao existe", 404);
		}
        $contato->delete();
		return $this->success("O Grupo com id {$id} foi removido com sucesso", 200);
	}

    public function ativar($id){
        $contato = Grupo::where("user_id", $this->getUserId())->find($id);
        if(!$contato){
            return $this->error("O grupo com id {$id} nao existe", 404);
        }
        $contato->ativo 		= "S";
        $contato->save();
        return $this->success("O grupo {$contato->id} foi ativado com sucesso", 200);
    }

    public function desativar($id){
        $contato = Grupo::where("user_id", $this->getUserId())->find($id);
        if(!$contato){
            return $this->error("O grupo com id {$id} nao existe", 404);
        }
        $contato->ativo 		= "N";
        $contato->save();
        return $this->success("O grupo {$contato->id} foi ativado com sucesso", 200);
    }

	public function validarRequisicao(Request $request){
		$regras = [
			'nome'      => 'required',
		];
		$this->validate($request, $regras);
	}

	public function estaAutorizado(Request $request){
		$resource = "grupos";
		$post     = Grupo::find($this->getArgs($request)["id"]);

		return $this->authorizeUser($request, $resource, $post);
	}
}