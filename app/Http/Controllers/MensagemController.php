<?php 

namespace App\Http\Controllers;


use App\Mensagem;
use Illuminate\Http\Request;

class MensagemController extends Controller{

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
		$mensagens = Mensagem::carregaPaginado($this->getUserId(), $request, $request->get('offset', 0), $request->get('limit', 10));
		return $this->successList($mensagens, Mensagem::carregaTotal($this->getUserId(), $request), 200);
	}

    public function carregar($id){
        $contato = Mensagem::where("user_id", $this->getUserId())->find($id);
        if(!$contato){
            return $this->error("A mensagem com id {$id} nao existe", 404);
        }
       
        return $this->success($contato, 200);
    }

	public function adicionar(Request $request){
		$this->validarRequisicao($request);
		$dadosMensagem = [
            'nome'         => $request->get('nome'),
            'texto'        => $request->get('texto'),
            'user_id'   => $this->getUserId()
        ];

        $mensagem = Mensagem::create($dadosMensagem);
		return $this->success("A Mensagem foi criada {$mensagem->id} foi criada com sucesso!", 201);
	}

	public function atualizar(Request $request, $id){
        $mensagem = Mensagem::where("user_id", $this->getUserId())->find($id);
		if(!$mensagem){
            return $this->error("O mensagem com id {$id} nao existe", 404);
		}

		$this->validarRequisicao($request);
        $mensagem->nome 		    = $request->get('nome');
        $mensagem->texto 		    = $request->get('texto');
        $mensagem->save();
		return $this->success("O mensagem com {$mensagem->id} foi atualizada", 200);
	}

	public function deletar($id){
        $mensagem = Mensagem::where("user_id", $this->getUserId())->find($id);
        /**
         * Verifica se a mensagem possui um agendamento ativo
         */
		if(!$mensagem){
            return $this->error("O mensagem com id {$id} nao existe", 404);
		}
        $mensagem->delete();
		return $this->success("O mensagem com id {$id} foi removido com sucesso", 200);
	}

    public function ativar($id){
        $mensagem = Mensagem::where("user_id", $this->getUserId())->find($id);
        if(!$mensagem){
            return $this->error("O mensagem com id {$id} nao existe", 404);
        }
        $mensagem->ativo 		= "S";
        $mensagem->save();
        return $this->success("O mensagem {$mensagem->id} foi ativado com sucesso", 200);
    }

    public function desativar($id){
        $mensagem = Mensagem::where("user_id", $this->getUserId())->find($id);
        if(!$mensagem){
            return $this->error("O mensagem com id {$id} nao existe", 404);
        }
        $mensagem->ativo 		= "N";
        $mensagem->save();
        return $this->success("O mensagem {$mensagem->id} foi ativado com sucesso", 200);
    }

	public function validarRequisicao(Request $request){
		$regras = [
			'nome'      => 'required',
			'texto'      => 'required',
		];

		$this->validate($request, $regras);
	}

	public function estaAutorizado(Request $request){
		$resource = "mensagem";
		$post     = Mensagem::find($this->getArgs($request)["id"]);

		return $this->authorizeUser($request, $resource, $post);
	}
}