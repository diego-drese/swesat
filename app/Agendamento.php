<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Self_;

class Agendamento extends Model{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['mensagem_id',  'user_id', 'contato_id', 'grupo_id', 'data_disparo', 'data_fim', 'tipo'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
	protected $hidden   = ['data_criacao', 'data_atualizacao'];
	protected $table    = "agendamento";
    public $timestamps = false;

    /**
     * Define a one-to-many relationship with App\Comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    protected static function setaCondicoes($query, Request $request){
        if($request->get('contato_id')){
            $query->where('contato_id', '=' , $request->get('contato_id'));
        }
        if($request->get('grupo_id')){
            $query->where('contato_id', '=' , $request->get('contato_id'));
        }
        if($request->get('tipo')){
            $query->where('tipo', '=' , $request->get('tipo'));
        }
        return $query;
    }

    public static function carregaPaginado($userId=0, Request $request, $offset=0, $limit=10){
        $query = self::select("agendamento.*",
            "mensagem.nome as mensagem_nome",
            "mensagem.texto as mensagem_texto",
            "contato.nome as contato_nome",
            "grupo.nome as grupo_nome")
            ->limit($limit)->skip($offset)
            ->join("mensagem", "mensagem.id","mensagem_id")
            ->leftJoin("contato", "contato.id","contato_id")
            ->leftJoin("grupo", "grupo.id","grupo_id")
            ->where("agendamento.user_id", $userId);
        $query = self::setaCondicoes($query, $request);
        return $query->get();
    }

    public static function carregaTotal($userId=0, Request $request){
        $query = self::where('user_id', $userId);
        $query = self::setaCondicoes($query, $request);
        return $query->count();
    }

}