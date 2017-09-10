<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Self_;

class Mensagem extends Model{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['id', 'text', 'tipo'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
	protected $hidden   = ['data_criacao', 'data_atualizacao'];
	protected $table    = "mensagem";

    /**
     * Define a one-to-many relationship with App\Comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
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
        $query = self::limit($limit)->skip($offset)->where("usuario_id", $userId);
        $query = self::setaCondicoes($query, $request);
        return $query->get();
    }

    public static function carregaTotal($userId=0, Request $request){
        $query = self::where('usuario_id',$userId);
        $query = self::setaCondicoes($query, $request);
        return $query->count();
    }

}