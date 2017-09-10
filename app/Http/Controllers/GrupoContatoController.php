<?php 

namespace App\Http\Controllers;

use App\Contato;
use App\Grupo;
use App\GrupoContato;
use Illuminate\Database\QueryException;
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

    public function contatoGrupo(Request $request, $id_grupo){
        $contatoGrupo = GrupoContato::carregaContatosDoGrupoPaginado($this->getUserId(), $id_grupo, $request);
        return $this->successList($contatoGrupo, GrupoContato::carregaTotalContatosDoGrupo($this->getUserId(), $id_grupo, $request), 200);
	}

	public function grupoContato(Request $request, $id_contato){
        $grupoContato = GrupoContato::carregaGruposDoContatoPaginado($this->getUserId(), $id_contato, $request);
        return $this->successList($grupoContato, GrupoContato::carregaTotalGruposDoContato($this->getUserId(), $id_contato, $request), 200);
	}



    public function associaContatoGrupo($contato_id, $grupo_id){
	    try{
            GrupoContato::insert(['grupo_id' => $grupo_id, 'contato_id' => $contato_id]);
            return $this->successList(['mens'=>'Contato associado com sucesso'], 1, 200);
        }catch (QueryException $e){
            return $this->successList(['mens'=>'O contato ja estava associado'], 1, 200);
        }

    }
    public function desassociaContatoGrupo($contato_id, $grupo_id){
        try{
            GrupoContato::where('grupo_id',$grupo_id)->where('contato_id', $contato_id)->delete();
            return $this->successList(['mens'=>'Contato desassociado com sucesso'], 1, 200);
        }catch (QueryException $e){
            return $this->successList(['mens'=>'O contato ja estava desassociado'], 1, 200);
        }
    }

    public function associaGrupoContato($grupo_id, $contato_id){
        try{
            GrupoContato::insert(['grupo_id' => $grupo_id, 'contato_id' => $contato_id]);
            return $this->successList(['mens'=>'Grupo associado com sucesso'], 1, 200);
        }catch (QueryException $e){
            return $this->successList(['mens'=>'O Grupo ja estava associado'], 1, 200);
        }
    }

    public function desassociaGrupoContato($grupo_id, $contato_id){
        try{
            GrupoContato::where('grupo_id',$grupo_id)->where('contato_id', $contato_id)->delete();
            return $this->successList(['mens'=>'Grupo desassociado com sucesso'], 1, 200);
        }catch (QueryException $e){
            return $this->successList(['mens'=>'O Grupo ja estava desassociado'], 1, 200);
        }

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