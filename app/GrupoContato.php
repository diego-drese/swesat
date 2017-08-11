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
    protected static function setaCondicoes($query, Request $request){
        if($request->get('grupo_id')){
            if(is_array($request->get('grupo_id'))){
                $query->whereIn('grupo_id', $request->get('grupo_id'));
            }else{
                $query->where('grupo_id', $request->get('grupo_id'));
            }
        }
        if($request->get('contato_id')){
            if(is_array($request->get('contato_id'))){
                $query->whereIn('contato_id', $request->get('contato_id'));
            }else{
                $query->where('contato_id', $request->get('contato_id'));
            }
        }
        return $query;
    }

    public static function carregaPaginado($userId=0, Request $request, $offset=0, $limit=10){
        $query = self::limit($limit, $offset)->where("user_id", $userId);
        $query = self::setaCondicoes($query, $request);
        return $query->get();
    }

    public static function carregaTotal($userId=0, Request $request){
        $query = self::where('user_id',$userId);
        $query = self::setaCondicoes($query, $request);
        return $query->count();
    }

    public static function carregaTodosOsGruposDoContato($userId=0, $contatoId=0){
        $query = self::where('contato_id', $contatoId);
        $query = self::where('usuario_id', $userId);
        $query = self::join('grupo', "grupo.id", "grupo_id");
        return $query->get();
    }

}