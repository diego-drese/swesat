<?php 

namespace App\Http\Controllers;


use App\Agendamento;
use Illuminate\Http\Request;

class AgendamentoController extends Controller{

	public function __construct(){
		$this->middleware('oauth');
		//$this->middleware('authorize:' . __CLASS__, ['except' => ['index', 'show', 'store']]);
	}
    /**
     * Request
     *
     * @var request[offset]
     * @var request[limit]
     * @var request[contato_id]
     * @var request[grupo_id]
     * @var request[tipo]
    */

    public function index(Request $request){
		$agendamentos = Agendamento::carregaPaginado($this->getUserId(), $request, $request->get('offset', 0), $request->get('limit', 10));
		return $this->successList($agendamentos, Agendamento::carregaTotal($this->getUserId(), $request), 200);
	}

    public function carregar($id){
        $contato = Agendamento::select("agendamento.*",
            "mensagem.nome as mensagem_nome",
            "mensagem.texto as mensagem_texto",
            "contato.nome as contato_nome",
            "grupo.nome as grupo_nome")
            ->join("mensagem", "mensagem.id","mensagem_id")
            ->leftJoin("contato", "contato.id","contato_id")
            ->leftJoin("grupo", "grupo.id","grupo_id")
            ->where("agendamento.user_id", $this->getUserId())
            ->where("agendamento.id", $id)
            ->first();
        if(!$contato){
            return $this->error("A Agendamento com id {$id} nao existe", 404);
        }
       
        return $this->success($contato, 200);
    }

	public function adicionar(Request $request){
		$this->validarRequisicao($request);
		$dadosAgendamento = [
            'data_disparo'  => $request->get('data_disparo'),
            'data_fim'      => $request->get('data_fim'),
            'mensagem_id'   => $request->get('mensagem_id'),
            'tipo'          => $request->get('tipo'),
            'contato_id'    => $request->get('contato_id'),
            'grupo_id'      => $request->get('grupo_id'),
            'obs'           => $request->get('obs'),
            'user_id'       => $this->getUserId()
        ];

        $Agendamento = Agendamento::create($dadosAgendamento);
		return $this->success("A Agendamento {$Agendamento->id} foi criado com sucesso!", 201);
	}

	public function atualizar(Request $request, $id){
        $Agendamento = Agendamento::where("user_id", $this->getUserId())->where("status_disparo",'AGUARDANDO')->find($id);
		if(!$Agendamento){
            return $this->error("O Agendamento com id {$id} nao existe ou nao esta com os status AGUARDANDO", 404);
		}

		$this->validarRequisicao($request);
        $Agendamento->data_disparo  = $request->get('data_disparo');
        $Agendamento->data_fim 		= $request->get('data_fim');
        $Agendamento->mensagem_id   = $request->get('mensagem_id');
        $Agendamento->tipo          = $request->get('tipo');
        $Agendamento->contato_id    = $request->get('contato_id');
        $Agendamento->grupo_id      = $request->get('grupo_id');
        $Agendamento->obs           = $request->get('obs');
        $Agendamento->save();
		return $this->success("O Agendamento com {$Agendamento->id} foi atualizado", 200);
	}

	public function deletar($id){
        $Agendamento = Agendamento::where("user_id", $this->getUserId())->find($id);
        /**
         * Verifica se a Agendamento possui um agendamento ativo
         */
		if(!$Agendamento){
            return $this->error("O Agendamento com id {$id} nao existe", 404);
		}
        $Agendamento->delete();
		return $this->success("O Agendamento com id {$id} foi removido com sucesso", 200);
	}

    public function ativar($id){
        $Agendamento = Agendamento::where("user_id", $this->getUserId())->find($id);
        if(!$Agendamento){
            return $this->error("O Agendamento com id {$id} nao existe", 404);
        }
        $Agendamento->ativo 		= "S";
        $Agendamento->save();
        return $this->success("O Agendamento {$Agendamento->id} foi ativado com sucesso", 200);
    }

    public function desativar($id){
        $Agendamento = Agendamento::where("user_id", $this->getUserId())->find($id);
        if(!$Agendamento){
            return $this->error("O Agendamento com id {$id} nao existe", 404);
        }
        $Agendamento->ativo 		= "N";
        $Agendamento->save();
        return $this->success("O Agendamento {$Agendamento->id} foi ativado com sucesso", 200);
    }

	public function validarRequisicao(Request $request){
		$regras = [
			'data_disparo'  => 'required',
			'data_fim'      => 'required',
			'mensagem_id'   => 'required',
			'tipo'          => 'required',
		];

		$this->validate($request, $regras);
	}

	public function estaAutorizado(Request $request){
		$resource = "Agendamento";
		$post     = Agendamento::find($this->getArgs($request)["id"]);

		return $this->authorizeUser($request, $resource, $post);
	}
}