<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Self_;

class Telefone extends Model{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['id', 'user_id', 'numero', 'token'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
	protected $hidden   = ['data_criacao', 'data_atualizacao'];

	protected $table    = "telefone";

    public $timestamps = false;

    /**
     * Define a one-to-many relationship with App\Comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    protected static function setaCondicoes($query, Request $request){

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

    public static function pegaUsuarioPorToken($token){
        $query = self::where('token',$token);
        return $query->first();
    }

}