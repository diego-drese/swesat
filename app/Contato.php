<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Contato extends Model{
	protected $fillable = ['id', 'user_id', 'nome', 'sobre_nome', 'email', 'telefone', 'data_nascimento'];
	protected $hidden   = ['data_criacao', 'data_atualizacao'];
	protected $table    = "contato";
    public $timestamps = false;
    protected static function setaCondicoes($query, Request $request){
        if($request->get('nome')){
            $query->where('nome', 'like' , "%".$request->get('nome')."%");
        }
        if($request->get('sobre_nome')){
            $query->where('sobre_nome', 'like' , "%".$request->get('sobre_nome')."%");
        }
        if($request->get('telefone')){
            $query->where('telefone', 'like' , "%".$request->get('telefone')."%");
        }
        if($request->get('email')){
            $query->where('email', 'like' , "%".$request->get('email')."%");
        }
        if($request->get('data_nascimento')){
            $query->where('data_nascimento', 'like' , "%".$request->get('data_nascimento')."%");
        }
        if($request->get('ativo')){
            $query->where('ativo', '=' , $request->get('ativo'));
        }
        return $query;
    }

    public static function carregaPaginado($userId=0, Request $request, $offset=0, $limit=10){
        $query = self::limit($limit)->skip($offset)->where("user_id", $userId);
        $query = self::setaCondicoes($query, $request);
        return $query->get();
    }

    public static function carregaTotal($userId=0, Request $request){
        $query = self::where('user_id',$userId);
        $query = self::setaCondicoes($query, $request);
        return $query->count();
    }

}