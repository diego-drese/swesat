<?php 

namespace App\Http\Controllers;


use App\Agendamento;
use App\PreDisparo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisparoControllerOauth extends Controller{

	public function __construct(){
		$this->middleware('oauth');
	}
    /**
     * Request
     *
     * @var request[offset]
     * @var request[limit]
     * @var request[contato_id]
     * @var request[grupo_id]
     * @var request[tipo]
    */

    public function index(Request $request){
        $data_inicio    = new \DateTime();
        $data_inicio->sub(new \DateInterval("P30D"));
        $agora          = new \DateTime();
        $agora->add(new \DateInterval("P1D"));


        $disparos = PreDisparo::select( DB::raw("DATE_FORMAT(data_cadastro,'%m-%d') as dia"),
                                        'status_envio',
                                        DB::raw("COUNT(1) as total"))
                            ->where('data_cadastro','>=',$data_inicio->format('Y-m-d H:i:s'))
                            ->where('data_cadastro','<=',$agora->format('Y-m-d H:i:s'))
                            ->orderBy('dia', 'asc')
                            ->groupBy('dia', 'status_envio')
                            ->get();

        $disparos_formated  = [];
        $chaveArray         = 0;
        while ($data_inicio->getTimestamp()<=$agora->getTimestamp()){
            $disparos_formated[$chaveArray]=
                [
                    'Dia'       =>$data_inicio->format('m-d'),
                    'Aguardando'=>0,
                    'Solicitado'=>0,
                    'Enviado'   =>0
                ];
            foreach ($disparos as $key => $disparo){
                if($disparo->dia == $disparos_formated[$chaveArray]['Dia']){
                    if($disparo->status_envio=="AGUARDANDO"){
                        $disparos_formated[$chaveArray]['Aguardando'] = $disparo->total;
                    }
                    if($disparo->status_envio=="SOLICITADO"){
                        $disparos_formated[$chaveArray]['Solicitado'] = $disparo->total;
                    }
                    if($disparo->status_envio=="ENVIADO"){
                        $disparos_formated[$chaveArray]['Enviado'] = $disparo->total;
                    }
                    unset($disparo[$key]);
                }
            }

            $data_inicio->add(new \DateInterval("P1D"));
            $chaveArray++;
        }


        return $this->success($disparos_formated, 200);
	}

}