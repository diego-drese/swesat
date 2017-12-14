<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Mensagem extends Model{
	protected $fillable = ['id',  'user_id', 'nome', 'texto'];
	protected $hidden   = ['data_criacao', 'data_atualizacao'];
	protected $table    = "mensagem";
    public $timestamps = false;

    protected static function setaCondicoes($query, Request $request){
        if($request->get('texto')){
            $query->where('texto', 'like' , "%".$request->get('nome')."%");
        }
        if($request->get('tipo')){
            $query->where('tipo', '=' , $request->get('tipo'));
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