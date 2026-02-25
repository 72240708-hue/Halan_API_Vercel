<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//use Illuminate\Database\Capsule\Manager as DB;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    
//menampilkan data rekam medis dari database
$app->get('/crud', function (Request $request, Response $response) {
    $db = $this->get('db');
    $result = $db->table('rekam_medis')->get();

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

//menambahkan data rekam medis ke database
$app->post('/resep', function (Request $request, Response $response) {
    $db = $this->get('db');
    $data = $request->getParsedBody();
    $id = $db->table('rekam_medis')->insertGetId([
        'no_rm' => $data['no_rm'],
        'keluhan' => $data['keluhan'],
        'tinggi' => $data['tinggi'],
        'berat' => $data['berat'],
        'dokter' => $data['dokter'],
        'status_obat' => $data['status_obat'],
        'tanggal_date' => $data['tanggal_date']
    ]);
   $result = $db->table('rekam_medis')->where('id_rm', $id)->first();
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});


//mengubah data rekam medis di database
$app->put('/update', function (Request $request, Response $response) {
    $db = $this->get('db');
    $data = $request->getParsedBody();
    $db->table('rekam_medis')
    ->where('id_rm', $data['id_rm'])
    ->update([
        'no_rm' => $data['no_rm'],
        'keluhan' => $data['keluhan'],
        'tinggi' => $data['tinggi'],
        'berat' => $data['berat'],
        'dokter' => $data['dokter'],
        'status_obat' => $data['status_obat'],
        'tanggal_date' => $data['tanggal_date']
    ]);
   $result = $db->table('rekam_medis')->where('id_rm', $data['id_rm']  )->first();
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

//menghapus data rekam medis dari database
$app->delete('/delete', function (Request $request, Response $response) {
    $db = $this->get('db');
    $data = $request->getParsedBody();
    $db->table('rekam_medis')
    ->where('id_rm', $data['id_rm'])
    ->delete();
    $response->getBody()->write(json_encode(['message' => 'Data berhasil dihapus']));
    return $response->withHeader('Content-Type', 'application/json');
});




    // //farmasi fiks

    // //resep dokter
    // $app->get('/rekam_medis', function (Request $request, Response $response) {
    //     $result = DB::table('rekam_medis')->get();
    //     $response->getBody()->write(json_encode($result));
    //     return $response->withHeader('Content-Type', 'application/json');
    // });

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->get('/health', function ($request, $response) {
        $data = [
            'status' => 'ok',
            'message' => 'API is running'
        ];


        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });
};
