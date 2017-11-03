<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// Home page

$app->get('/', function () use ($app) {
    return response()->json(['app'=>'swesat', 'version' => "0.0.1", "last_update"=> '2017-08-01'], 200);
});
// Contatos
$app->get('/contato','ContatoController@index');
$app->get('/contato/{id}','ContatoController@carregar');
$app->get('/contato/ativar/{id}','ContatoController@ativar');
$app->get('/contato/desativar/{id}','ContatoController@desativar');
$app->post('/contato','ContatoController@adicionar');
$app->put('/contato/{id}', 'ContatoController@atualizar');
$app->post('/contato/{id}', 'ContatoController@atualizar');
//$app->delete('/contato/{id}', 'ContatoController@deletar');

// Grupos
$app->get('/grupo','GrupoController@index');
$app->get('/grupo/{id}','GrupoController@carregar');
$app->get('/grupo/ativar/{id}','GrupoController@ativar');
$app->get('/grupo/desativar/{id}','GrupoController@desativar');
$app->post('/grupo','GrupoController@adicionar');
$app->put('/grupo/{id}', 'GrupoController@atualizar');
$app->post('/grupo/{id}', 'GrupoController@atualizar');
//$app->delete('/grupo/{id}', 'GrupoController@deletar');

// Grupos Contatos
$app->get('/contato-grupo/{id}','GrupoContatoController@contatoGrupo');
$app->get('/grupo-contato/{id}','GrupoContatoController@grupoContato');
$app->get('/associa-contato-grupo/{contato_id}/{grupo_id}','GrupoContatoController@associaContatoGrupo');
$app->get('/desassocia-contato-grupo/{contato_id}/{grupo_id}','GrupoContatoController@desassociaContatoGrupo');
$app->get('/associa-grupo-contato/{grupo_id}/{contato_id}','GrupoContatoController@associaGrupoContato');
$app->get('/desassocia-grupo-contato/{grupo_id}/{contato_id}','GrupoContatoController@desassociaGrupoContato');


// Mensagens
$app->get('/mensagem','MensagemController@index');
$app->get('/mensagem/{id}','MensagemController@carregar');
$app->post('/mensagem','MensagemController@adicionar');
$app->put('/mensagem/{id}','MensagemController@atualizar');
$app->post('/mensagem/{id}','MensagemController@atualizar');


// Agendamento
$app->get('/agendamento','AgendamentoController@index');
$app->get('/agendamento/{id}','AgendamentoController@carregar');
$app->post('/agendamento','AgendamentoController@adicionar');
$app->put('/agendamento/{id}','AgendamentoController@atualizar');
$app->post('/agendamento/{id}','AgendamentoController@atualizar');

// Telefone
$app->get('/telefone','TelefoneController@index');
$app->get('/telefone/{id}','TelefoneController@carregar');
$app->post('/telefone','TelefoneController@adicionar');
$app->put('/telefone/{id}','TelefoneController@atualizar');
$app->post('/telefone/{id}','TelefoneController@atualizar');

// Request an access token
$app->post('/oauth/access_token', function() use ($app){
    return response()->json($app->make('oauth2-server.authorizer')->issueAccessToken());
});

$app->get('/ultimos-disparos','DisparoControllerOauth@index');

$app->get('/pegar-mensagem/{token}','DisparoController@pegarMensagem');
$app->post('/pegar-mensagem/{token}','DisparoController@pegarMensagem');

