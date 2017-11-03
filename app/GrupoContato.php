<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Self_;

class GrupoContato extends Model{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['grupo_contato', 'contato_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

	protected $table    = "grupo_contato";

    /**
     * Define a one-to-many relationship with App\Comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public static function carregaContatosDoGrupoPaginado($userId=0, $grupoId=0, Request $request){
        $query = self::where('grupo_id', $grupoId)
                    ->where('user_id', $userId)
                    ->join('contato', "contato.id", "contato_id")
                    ->limit($request->get('limit',10))
                    ->skip($request->get('offset',0));
        return $query->select("id", "nome", "ativo")->get();
    }

    public static function carregaTotalContatosDoGrupo($userId=0, $grupoId=0, Request $request){
        $query = self::where('grupo_id', $grupoId)
            ->where('user_id', $userId)
            ->join('contato', "contato.id", "contato_id");

        return $query->count();
    }

    public static function carregaGruposDoContatoPaginado($userId=0, $contatoId=0, Request $request){
        $query = self::where('contato_id', $contatoId)
                    ->where('user_id', $userId)
                    ->join('grupo', "grupo.id", "grupo_id")
                    ->limit($request->get('limit',100))
                    ->skip($request->get('offset',0));
        return $query->select("grupo.id", "grupo.nome", "grupo.ativo")->get();
    }

    public static function carregaTotalGruposDoContato($userId=0, $contatoId=0, Request $request){
        $query = self::where('contato_id', $contatoId)
            ->where('user_id', $userId)
            ->join('grupo', "grupo.id", "grupo_id");
        return $query->count();
    }




    public static function carregaTodosOsGruposDoContato($userId=0, $contatoId=0){
        $query = self::where('contato_id', $contatoId);
        $query = self::where('usuario_id', $userId);
        $query = self::join('grupo', "grupo.id", "grupo_id");
        return $query->select("id", "nome", "ativo")->get();
    }



}