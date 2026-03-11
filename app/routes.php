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

   
    //contoh looping nested
    $app->get('/simuliasi-nested-looping', function ($request, $response) {
        // 1. Simulasi data parent (hasil dari query : SELECT * FROM resep WHERE id = 101)
        $resep = [
            'id_resep' => 101,
            'pasien' => 'Rizky Fitriani',
            'status' => 'Selesai',
        ];


        // 2. Simulasi data child (hasil dari query : JOIN detil_resep & obat)
        // anggap ini hasil dari $details = DB::table(...)->get();
        $rawDetailsFromDB = [
            ['nama' => 'Paracetamol', 'qty' =>10, 'aturan' => '3x1'],
            ['nama' => 'Amoxicillin', 'qty' =>5,'aturan' => '2x1'],
            ['nama' => 'Vitamin C', 'qty' =>30, 'aturan' => '1x1']
        ];

        // 3. Inisialisasi array kosong untuk menampung hasil looping
        $listObat = [];

        //4. proses looping 
        foreach ($rawDetailsFromDB as $row) {
            // kita susun ulang data jika perlu, atau langsung masukan 
            $listObat[]= [
                'nama_obat' => $row['nama'],
                'jumlah' => $row['qty'],
                'aturan_pakai' => $row['aturan']
            ];
        }

            // 5. Masukkan hasil mapping ke dalam array listObat
            $resep['items'] = $listObat;

       // 6. Kembalikan response dalam format JSON         
         $response->getBody()->write(json_encode($resep));
    return $response->withHeader('Content-Type', 'application/json');
        
    });

    //contoh filtering data 
    $app->get('/readobat', function (Request $request, Response $response) {

    $params = $request->getQueryParams();
    $db = $this->get('db');

    $query = $db->table('obat');

    // search sku
    if (!empty($params['search'])) {
        $searchTerm = $params['search'];
        $query->where('sku', 'like', "%$searchTerm%");
    }

    // filter jumlah
    if (!empty($params['jumlah'])) {
        $query->where('jumlah', $params['jumlah']);
    }

    // filter label
    if (!empty($params['label'])) {
        $query->where('label_catatan', $params['label']);
    }

    $hasil = $query->get();

    $response->getBody()->write(json_encode($hasil));
    return $response->withHeader('Content-Type', 'application/json');
});



    $app->get('/obat', function (Request $request, Response $response) {
    $params = $request->getQueryParams();

    //tentukan parameter untuk pagination
    $page = isset($params['page']) ? (int)$params['page'] : 1;
    $perPage = isset($params['per_page']) ? (int)$params['per_page'] : 10;
    $offset = ($page - 1) * $perPage;

    //inisialisasi query builder
    $db = $this->get('db');
    $query = $db->table('obat');

    //filter/search
    if (!empty($params['search'])) {
        $searchTerm = $params['search'];
        $query->where('sku', 'like', "%$searchTerm%");
    }

    //hitung total data untuk pagination
    $totaldata = $query->count();
    $totalpage = ceil($totaldata / $perPage);

    //ambil data dengan limit dan offset untuk pagination
    $data = $query->limit($perPage)
    ->offset($offset)   
    ->orderBy('id_obat', 'desc')
    ->get();   

    //susun response dengan format pagination
    $result = [
        'status' => 'success',
        'pagination' => [
            'current_page' => $page,
            'per_page' => $perPage, 
            'total_data' => $totaldata,
            'total_page' => $totalpage
        ],
        'data' => $data
    ];

    

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});



//     $app->get('/readobat', function (Request $request, Response $response) {
//     $db = $this->get('db');
//     $result = $db->table('obat')->get();

//     $response->getBody()->write(json_encode($result));
//     return $response->withHeader('Content-Type', 'application/json');
// });

$app->post('/createobat', function (Request $request, Response $response) {
    $db = $this->get('db');
    $data = $request->getParsedBody();
    $id_obat = $db->table('obat')->insertGetId([
        'sku' => $data['sku'],
        'label_catatan' => $data['label_catatan'],
        'jumlah' => $data['jumlah']
    ]);
   $result = $db->table('obat')->where('id_obat', $id_obat)->first();
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->put('/updateobat', function (Request $request, Response $response) {
    $db = $this->get('db');
    $data = $request->getParsedBody();
    $db->table('obat')
    ->where('id_obat', $data['id_obat'])
    ->update([
        'sku' => $data['sku'],
        'label_catatan' => $data['label_catatan'],
        'jumlah' => $data['jumlah']
    ]);
   $result = $db->table('obat')->where('id_obat', $data['id_obat'])->first();
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->delete('/deleteobat', function (Request $request, Response $response) {
    $db = $this->get('db');
    $data = $request->getParsedBody();
    $db->table('obat')
    ->where('id_obat', $data['id_obat'])
    ->delete();
    $response->getBody()->write(json_encode(['message' => 'Data berhasil dihapus']));
    return $response->withHeader('Content-Type', 'application/json');
});


$app->get('/readresep', function (Request $request, Response $response) {
    $db = $this->get('db');
    $result = $db->table('resep')->get();

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});



$app->post('/createresep', function (Request $request, Response $response) {
    $db = $this->get('db');
    $data = $request->getParsedBody();
    $id_resep = $db->table('resep')->insertGetId([
        'id_pasien' => $data['id_pasien'],
        'id_dokter' => $data['id_dokter'],
        'tgl_resep' => $data['tgl_resep'],
        'status' => $data['status']
    ]);
   $result = $db->table('resep')->where('id_resep', $id_resep)->first();
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});


$app->post('/createdtl_resep', function (Request $request, Response $response) {
    $db = $this->get('db');
    $data = $request->getParsedBody();
    $id_dtl_resep = $db->table('detail_resep')->insertGetId([
        'id_resep' => $data['id_resep'],
        'id_obat' => $data['id_obat'],
        'jumlah' => $data['jumlah'],
        'dosis' => $data['dosis'],
        'keterangan' => $data['keterangan']
    ]);
   $result = $db->table('detail_resep')->where('id_detail', $id_dtl_resep)->first();
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});



$app->get('/readdtl_resep', function (Request $request, Response $response) {
    $db = $this->get('db');
    $resep = $db->table('resep')
                ->where('id_resep', 1)
                ->first();

    $rawDetails = 
    $db->table('detail_resep')
    ->join('obat', 'detail_resep.id_obat', '=', 'obat.id_obat')
    ->where('detail_resep.id_resep', 1)
    ->select('detail_resep.id_detail as id_detail', 'detail_resep.jumlah as jumlah', 'detail_resep.dosis as dosis', 'detail_resep.keterangan as keterangan', 'obat.id_obat as id_obat', 'obat.sku as nama_obat', 'obat.label_catatan as label_catatan')
    ->get();

    $listdetail = [];

        //4. proses looping 
        foreach ($rawDetails as $row) {

    $listdetail[] = [
        'id_detail' => $row->id_detail,
        'jumlah' => $row->jumlah,
        'dosis' => $row->dosis,
        'keterangan' => $row->keterangan,
        'obat' => [
            'id_obat' => $row->id_obat,
            'nama_obat' => $row->nama_obat,
            'label_catatan' => $row->label_catatan
        ]
    ];
}
        $resep->items = $listdetail;
       // 6. Kembalikan response dalam format JSON         
         $response->getBody()->write(json_encode($resep));
    return $response->withHeader('Content-Type', 'application/json');
        
});

$app->delete('/deletedtl_resep', function (Request $request, Response $response) {
    $db = $this->get('db');
    $data = $request->getParsedBody();
    $db->table('detail_resep')
    ->where('id_detail', $data['id_detail'])
    ->delete();
    $response->getBody()->write(json_encode(['message' => 'Data berhasil dihapus']));
    return $response->withHeader('Content-Type', 'application/json');
});





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
