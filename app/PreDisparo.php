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




}