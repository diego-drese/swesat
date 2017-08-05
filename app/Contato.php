<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Self_;

class Contato extends Model{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['id', 'user_id', 'nome', 'email', 'telefone', 'celular'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
	protected $hidden   = ['created_at', 'updated_at'];

	protected $table    = "contato";

    /**
     * Define a one-to-many relationship with App\Comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    protected static function setaCondicoes($query, Request $request){
        if($request->get('name')){
            $query->where('nome', 'like' , "%".$request->get('name')."%");
        }
        if($request->get('telefone')){
            $query->where('telefone', 'like' , "%".$request->get('telefone')."%");
        }
        if($request->get('email')){
            $query->where('email', 'like' , "%".$request->get('email')."%");
        }
        if($request->get('celular')){
            $query->where('celular', 'like' , "%".$request->get('celular')."%");
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

}