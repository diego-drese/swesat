<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Self_;

class PreDisparo extends Model{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['id', 'mensagem', 'telefone','mensagem_id', 'agendamento_id', 'contato_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

	protected $table    = "pre_disparo";

    public $timestamps = false;

    protected static function setaCondicoes($query, Request $request){

        return $query;
    }

    public static function carregaPaginado($userId=0, Request $request, $offset=0, $limit=10){
        $query = self::where("agendamento.user_id", $userId)
            ->whereNull('data_requisicao')
            ->select("pre_disparo.*")
            ->join("agendamento","agendamento.id","pre_disparo.agendamento_id")
            ->limit($limit)->skip($offset);
        $query = self::setaCondicoes($query, $request);
        $return = $query->get();
        foreach ($return as $key=>$pre_disparo){
            $update_pre_disparo = self::find($pre_disparo->id);
            $update_pre_disparo->data_requisicao = date("Y-m-d H:i:s");
            $update_pre_disparo->save();
            $return[$key]->next_message = 300;
        }

        return $return;
    }

    public static function carregaTotal($userId=0, Request $request){
        $query = self::where('user_id', $userId)->join("agendamento","agendamento.id","agendamento_id");
        $query = self::setaCondicoes($query, $request);
        return $query->count();
    }

}