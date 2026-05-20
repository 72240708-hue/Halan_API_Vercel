<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//use Illuminate\Database\Capsule\Manager as DB;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;

return function (\Slim\App $app) {



$app->get('/obat-secure', function (Request $request, Response $response) {
        // Ambil kredensial (mendukung Basic Auth atau Custom Header)
        $serverParams = $request->getServerParams();
        $username = $serverParams['PHP_AUTH_USER'] ?? $request->getHeaderLine('Username');
        $password = $serverParams['PHP_AUTH_PW'] ?? $request->getHeaderLine('Password');

        // Pengecekan jika kredensial kosong
        if (empty($username) || empty($password)) {
            $response->getBody()->write(json_encode([
                "status" => "error",
                "message" => "Autentikasi Diperlukan. Harap Sertakan username dan password."
            ]));
            return $response->withStatus(401)
                ->withHeader('WWW-Authenticate', 'Basic realm="Protected Area"')
                ->withHeader('Content-Type', 'application/json');
        }

        // Cek Username di tabel user role
        $db = $this->get('db');
    $user = $db->table('user_role')->where('username', $username)->first();

        // Verifikasi password (password pada database sudah di hash, misal dengan password_verify)
        if (!$user || !password_verify($password, $user->password)) {
            $response->getBody()->write(json_encode([
                "status" => "error",
                "message" => "Username atau password salah. Akses ditolak."
            ]));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }

        // Jika autentikasi berhasil, lanjutkan untuk mengambil data obat
        $dataObat = $db->table('obat')->get();
        $response->getBody()->write(json_encode([
            "status" => "success",
            "message" => "Berhasil mengambil data obat.",
            "data" => $dataObat
        ]));
        return $response->withHeader('Content-Type', 'application/json');

    });

    $app->post('/login-jwt', function (Request $request, Response $response) {
        $data = $request->getParsedBody();
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($username) || empty($password)) {
            $response->getBody()->write(json_encode([
                "status" => "error",
                "message" => "Username dan password harus diisi."
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        $db = $this->get('db');
        $user = $db->table('user_role')->where('username', $username)->first();

        // Verifikasi password salah (password pada database sudah di hash, misal dengan password_verify)
        if (!$user || !password_verify($password, $user->password)) {
            $response->getBody()->write(json_encode([
                "status" => "error",
                "message" => "Username atau password salah."
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        //kunci rahasia untuk JWT (sebaiknya disimpan di .env)
        $secretKey = "halanbaik";

        $payload = [
            "iat" => time(), // Issued at
            "exp" => time() + (60 * 60) ,// Expiration time (1 jam)
            "sub" => $user->username, // Subject (ID pengguna)
            "uid" => $user->id ?? 1 ,// ID user untuk pengecekan resource (asumsi table user_role punya kolom id)
             "role" => $user->role ?? 'apoteker' // Role pengguna untuk manajemen akses 

        ];

        //generate token
        $token = \Firebase\JWT\JWT::encode($payload, $secretKey, 'HS256');
        $response->getBody()->write(json_encode([
            "status" => "success",
            "message" => "Login berhasil",
            "token" => $token
        ]));
        return $response->withHeader('Content-Type', 'application/json');

    });

     $app->get('/obat-jwt', function (Request $request, Response $response) {
      $db = $this->get('db');
      $dataObat = $db->table('obat')->get();

      $response->getBody()->write(json_encode([
          "status" => "success",
          "message" => "Berhasil mengambil data obat.",
          "data" => $dataObat
      ]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
       
    })->add(new \Tuupola\Middleware\JwtAuthentication([
        "secret" => "halanbaik",
        "error" => function ($response, $arguments) {
            $data["status"] = "error";
            $data["message"] = $arguments["message"];
            $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            );
              return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    ]));

     $app->post('/obat-jwt', function (Request $request, Response $response) {
        $data = $request->getParsedBody();
        $db = $this->get('db');
        $id_obat = $db->table('obat')->insertGetId([
            'sku' => $data['sku'],
            'label_catatan' => $data['label_catatan'],
            'jumlah' => $data['jumlah']
        ]);
       $result = $db->table('obat')->where('id', $id_obat)->first();
        $response->getBody()->write(json_encode($result));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    })->add(new \Tuupola\Middleware\JwtAuthentication([
        "secret" => "halanbaik",
        "error" => function ($response, $arguments) {
            $data["status"] = "error";
            $data["message"] = $arguments["message"];
            $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            );
              return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    ]));

    $app->get('/obat-jwt/{id}', function ($request, $response, $args) {

    $id = $args['id'];

    $conn = new mysqli("localhost", "root", "", "api");

    $sql = "SELECT * FROM obat WHERE id = '$id'";

    $result = $conn->query($sql);

    $data = $result->fetch_assoc();

    $response->getBody()->write(json_encode($data));

    return $response->withHeader('Content-Type', 'application/json');

})->add(new \Tuupola\Middleware\JwtAuthentication([
        "secret" => "halanbaik",
        "error" => function ($response, $arguments) {
            $data["status"] = "error";
            $data["message"] = $arguments["message"];
            $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            );
              return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    ]));

    $app->put('/obat-jwt/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $data = $request->getParsedBody();
    $db = $this->get('db');
    $db->table('obat')
    ->where('id', $id)
    ->update([
        'sku' => $data['sku'],
        'label_catatan' => $data['label_catatan'],
        'jumlah' => $data['jumlah']
    ]);
    $result = $db->table('obat')->where('id', $id)->first();
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new \Tuupola\Middleware\JwtAuthentication([
        "secret" => "halanbaik",
        "error" => function ($response, $arguments) {
            $data["status"] = "error";
            $data["message"] = $arguments["message"];
            $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            );
              return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    ]));

    

    // 3. Hanya bisa diakses oleh user dengan role 'apoteker' (Role-Based)
// dan hanya bisa menghapus obat miliknya sendiri (Resource-Based)
$app->delete('/obat-otorisasi/{id}', function (Request $request, Response $response, $args) {

    $obatId = $args['id'];

    $token = $request->getAttribute("token");
    $currentUserId = is_array($token)
        ? ($token['uid'] ?? null)
        : ($token->uid ?? null);

    $db = $this->get('db');
    $obat = $db->table('obat')->where('id', $obatId)->first();

    if (!$obat) {
        $response->getBody()->write(json_encode([
            "error" => "Obat tidak ditemukan"
        ]));

        return $response
            ->withStatus(404)
            ->withHeader('Content-Type', 'application/json');
    }

    // Otorisasi resource
    if ($obat->sku != $currentUserId) {
        $response->getBody()->write(json_encode([
            "error" => "Unauthorized: Anda bukan pemilik data obat ini!"
        ]));

        return $response
            ->withStatus(403)
            ->withHeader('Content-Type', 'application/json');
    }

    // Hapus data
    // 1. Hapus riwayat obat ini di tabel anaknya dulu (detail_resep)
    $db->table('detail_resep')->where('id_obat', $obatId)->delete();

    // 2. Setelah anaknya bersih, baru hapus induknya (obat)
    $db->table('obat')->where('id', $obatId)->delete();
    $response->getBody()->write(json_encode([
        "status" => "success",
        "message" => "Data obat Anda berhasil dihapus."
    ]));

    return $response
        ->withStatus(200)
        ->withHeader('Content-Type', 'application/json');

})

->add(new \App\Application\Middleware\RoleMiddleware('apoteker'))

->add(new \Tuupola\Middleware\JwtAuthentication([
    "secret" => "halanbaik",

    "error" => function ($response, $arguments) {

        $data["status"] = "error";
        $data["message"] = $arguments["message"];

        $response->getBody()->write(
            json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        );

        return $response->withHeader(
            "Content-Type",
            "application/json"
        );
    }
])); 



    


//Route untuk menggenerate swagger JSON
 $app->get('/swagger.json', function (
        Request $request,
        Response $response
    ) {

        $generator = new \OpenApi\Generator();

        $openapi = $generator->generate([
            __DIR__.'/../app',
            __DIR__.'/../src'
        ]);

        if($openapi){
            $response->getBody()->write(
                $openapi->toJson()
            );
        }

        return $response
            ->withHeader(
                'Content-Type',
                'application/json'
            )
            ->withHeader(
                'Access-Control-Allow-Origin',
                '*'
            )
            ->withHeader(
                'Access-Control-Allow-Methods',
                'GET, POST, PUT, DELETE, PATCH, OPTIONS'
            )
            ->withHeader(
                'Access-Control-Allow-Headers',
                'X-Requested-With, Content-Type, Accept, Origin, Authorization'
            );
    });

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
        'tanggal' => $data['tanggal']
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
        'tanggal' => $data['tanggal']
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

// $app->post('/createobat', function (Request $request, Response $response) {
//     $db = $this->get('db');
//     $data = $request->getParsedBody();
//     $id_obat = $db->table('obat')->insertGetId([
//         'sku' => $data['sku'],
//         'label_catatan' => $data['label_catatan'],
//         'jumlah' => $data['jumlah']
//     ]);
//    $result = $db->table('obat')->where('id_obat', $id_obat)->first();
//     $response->getBody()->write(json_encode($result));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// SOAL MINGGU 8 
$app->post('/createobat', function (Request $request, Response $response) {
    $db = $this->get('db');
    $data = $request->getParsedBody() ?? [];

    $validator = v::key('sku', v::stringType()->notEmpty())
        ->key('label_catatan', v::optional(v::stringType()))
        ->key('jumlah', v::intVal()->min(1));

    try {   
        $validator->assert($data);

         $id_obat = $db->table('obat')->insertGetId([
            'sku' => $data['sku'],
            'label_catatan' => $data['label_catatan'] ?? null,
            'jumlah' => $data['jumlah']
        ]);
        $result = $db->table('obat')->where('id_obat', $id_obat)->first();
        $response->getBody()->write(json_encode([
            'message' => "Data obat berhasil ditambahkan",
            'data' => $result
        ]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

    } catch (NestedValidationException $e) {
        $errors = $e->getMessages([
            "sku" => 'SKU tidak boleh kosong',
            "label_catatan" => 'Label ga boleh kosong',
            "jumlah" => 'Jumlah minimal adalah 1'
        ]);
        
        $payload = json_encode([ 
            'status' => 'fail',
            'message' => "Validasi gagal",
             'errors' => array_filter($errors) 
        ]);
        $response->getBody()->write($payload);

        return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
    } catch (\Exception $e){
        $response->getBody()->write(json_encode([
            'message' => "Error: " . $e->getMessage()
        ]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});


// SOAL MINGGU 8 
$app->put('/updateobat/{id}', function (Request $request, Response $response, array $args) {

    //validasi manual
    $data = $request->getParsedBody() ?? [];
    // if (empty($data['sku']) ){
    //     $response->getBody()->write(json_encode([
    //         'debug_info' => 'SKU kosong di level php',
    //         'data_received' => $data,
    //     ]));
    //     return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    // }

    $id = $args['id'];

    //gunakan validator dan set rule
    $validator = v::key('sku', v::stringType()->notEmpty())
        ->key('label_catatan', v::optional(v::stringType()))
        ->key('jumlah', v::intVal()->min(1));

    try {

        //jalankan validasi
        //paksa lempar error jika sku kosong atau tidak sesuai rule

        if(!isset($data['sku']) || $data['sku'] === ''){
            throw new \Exception('SKU tidak boleh kosong');
        }

        $validator->assert($data);
        
        $check = $this->get('db')->table('obat')->where('id_obat', $id)->first();
        if (!$check){
            $response->getBody()->write(json_encode([
                'message' => "Data obat tidak ditemukan" ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $this->get('db')->table('obat')->where('id_obat', $id)->update([
            'sku' => $data['sku'],
            'label_catatan' => $data['label_catatan'] ?? null,
            'jumlah' => $data['jumlah']
        ]);

        $result = $this->get('db')->table('obat')->where('id_obat', $id)->first();
        $response->getBody()->write(json_encode([
            'message' => "Data obat berhasil diupdate",
            'data' => $result
        ]));

        //berikan status code 200 secara eksplisit
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');


    } catch (NestedValidationException $e) {
        //tangkap error validasi
        // $errors = $e->getFullMessage(); // untuk debugging

        $errors = $e->getMessages([
            "sku" => 'SKU tidak boleh kosong',
            "label_catatan" => 'Label ga boleh kosong',
            "jumlah" => 'Jumlah minimal adalah 1'
        ]);
        
        $payload = json_encode([ 
            'status' => 'fail',
            'message' => "Validasi gagal",
            // 'errors' => $e ->getMessages()
             'errors' => array_filter($errors) 
        ]);
        $response->getBody()->write($payload);

        //harus respond dengan status code 422 Unprocessable Entity untuk error validasi
        return $response->withStatus(422)->withHeader('Content-Type', 'application/json');


    } catch (\Exception $e){
        //tangkap error lain yang mungkin terjadi
        $response->getBody()->write(json_encode([
            'message' => "Error: " . $e->getMessage()
        ]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});







// $app->put('/updateobat', function (Request $request, Response $response) {
//     $db = $this->get('db');
//     $data = $request->getParsedBody();
//     $db->table('obat')
//     ->where('id_obat', $data['id_obat'])
//     ->update([
//         'sku' => $data['sku'],
//         'label_catatan' => $data['label_catatan'],
//         'jumlah' => $data['jumlah']
//     ]);
//    $result = $db->table('obat')->where('id_obat', $data['id_obat'])->first();
//     $response->getBody()->write(json_encode($result));
//     return $response->withHeader('Content-Type', 'application/json');
// });

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
    $id_detail = $db->table('detail_resep')->insertGetId([
        'id_resep' => $data['id_resep'],
        'id_obat' => $data['id_obat'],
        'jumlah' => $data['jumlah'],
        'dosis' => $data['dosis'],
        'keterangan' => $data['keterangan']
    ]);
   $result = $db->table('detail_resep')->where('id_detail', $id_detail)->first();
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



//LATIHAN SOAL UTS
// NO 1 menampilkan status API dengan endpoint /status
$app->get('/status', function (Request $request, Response $response) {
        $data = [
            'status' => 'online',
            'message' => 'API Smart Campus Ready',
            "version" => "1.0.0"
        ];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });

// NO 2 menampilkan data mahasiswa dengan endpoint /mahasiswa secara manual tanpa menggunakan ORM
$app->get('/mhs', function (Request $request, Response $response) {
        $data = [
            'nama' => 'Budi Santoso',
            'nim' => '220101',
            "prodi" => "informatika",
            "semester" => "4"

        ];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });

    
// NO 3 endpoint POST /echo yang menerima JSON berisi pesan dan mengembalikan pesantersebut dengan tambahan teks "Terima kasih"
$app->post('/pesan', function (Request $request, Response $response) {
        $data = $request->getParsedBody();
        $responseData = [
            "status" => "success",
            'balasan' => "Terima kasih, pesan anda: " . $data['pesan'],
            "timestamp" => date('Y-m-d H:i:s')
        ];
        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json');
    }); 




// NO 4 endpoint DELETE /hapus/{id} yang mengembalikan pesan konfirmasi bahwa data dengan ID tersebut berhasil dihapus secara simulasi.
$app->delete('/hapus_aja/{id}', function (Request $request, Response $response, $args) {
        $id = $args['id'];
        $responseData = [
            "id_terhapus" => "$id",
            'message' => "Data dengan ID $id berhasil dihapus dari sistem",
            'status' => "deleted"
        ];
        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json');
    }); 

    

// NO 5 Manajemen Data Obat
$app->post('/latihan_kirimobat', function (Request $request, Response $response) {
        $data = $request->getParsedBody();
        $db = $this->get('db');
        $id_obat = $db->table('obat_baru')->insertGetId([
            'id_obat' => $data['id_obat'],
            'nama_obat' => $data['nama_obat'],
            'stok' => $data['stok'],
            'harga' => $data['harga']
        ]);
        $responseData = [
            "message" => "Data obat berhasil diterima",
            "data" => [
                'id_obat' => $data['id_obat'],
                'nama_obat ' => $data['nama_obat'],
                'stok' => $data['stok'],
                'harga' => $data['harga']
            ]
        ];
        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json');
    });

//NO 6  Update Stok Obat Buatlah fungsi Update (PUT) untuk mengubah stok obat berdasarkan ID obat yang diterima dari request. Endpoint ini akan menerima ID obat dan jumlah stok baru, lalu mengupdate data di database dan mengembalikan response konfirmasi.
$app->put('/latihan_updatestok', function (Request $request, Response $response) {
    
        $data = json_decode($request->getBody()->getContents(), true);
        $db = $this->get('db');
        $db->table('obat_baru')
        ->where('id_obat', $data['id_obat'])
        ->update([
            'stok' => $data['stok']
        ]);
        $responseData = [ 
            "status" => "success",
            "message" => "Stok obat ID " . $data['id_obat'] . " berhasil diperbarui",
            "row_affected" => 1
        ];  
        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json');
    });


    //NO 7 Riwayat Pembelian (Join) Buatlah endpoint GET /pembelian yang menggabungkan tabel pembelian dan supplier.
    $app->get('/pembelian', function (Request $request, Response $response) {
        $db = $this->get('db');
        $join = $db->table('pembelian')
                    ->join('supplier', 'pembelian.id_supplier', '=', 'supplier.id_supplier')
                    ->select('pembelian.id_beli', 'pembelian.tgl', 'pembelian.total_bayar', 'supplier.nama_supplier', 'supplier.alamat')
                    ->get();

    $hasil = $join->map(function ($item) {
        return [
            'id_beli' => $item->id_beli,
            'tgl' => $item->tgl,
            'total_bayar' => $item->total_bayar,
            'nama_supplier' => $item->nama_supplier,
            'alamat' => $item->alamat
        ];
    });
        $response->getBody()->write(json_encode($hasil));
        return $response->withHeader('Content-Type', 'application/json');
    }); 

    // NO 8 Detail Resep Dokter (Join) Buatlah endpoint GET /resep/{id} untuk melihat detail satu resep yang mengambil data dari tabel resep dan pasien.
    $app->get('/resep/{id}', function (Request $request, Response $response) {
        $db = $this->get('db');
        $join = $db->table('resep_uts')
                    ->join('pasien', 'resep_uts.id_pasien', '=', 'pasien.id_pasien')
                    ->select('resep_uts.id_resep', 'resep_uts.tgl_resep', 'pasien.id_pasien', 'pasien.nama_pasien', 'resep_uts.dokter_pemeriksa')
                    ->where('resep_uts.id_resep', $request->getAttribute('id'))
                    ->get();

    $hasil = $join->map(function ($item) {
        return [
            'id_resep' => $item->id_resep,
            'tgl_resep' => $item->tgl_resep,
            'id_pasien' => $item->id_pasien,
            'nama_pasien' => $item->nama_pasien,
            'dokter_pemeriksa' => $item->dokter_pemeriksa
        ];
    });
        $response->getBody()->write(json_encode($hasil));
        return $response->withHeader('Content-Type', 'application/json');
    }); 


    //NO 9 Katalog Obat per Kategori Gunakan tabel kategori dan obat. Buatlah output di mana satu Kategori memiliki daftar (array) obat di dalamnya.
     $app->get('/katalog', function (Request $request, Response $response) {
        $db = $this->get('db');
        $join = $db->table('kategori_obat')
                    ->join('obat_latuts', 'kategori_obat.id_kategori', '=', 'obat_latuts.id_kategori')
                    ->select('kategori_obat.id_kategori', 'kategori_obat.nama_kategori', 'obat_latuts.id_obat', 'obat_latuts.nama_obat', 'obat_latuts.harga')
                    ->get();
    
     $grouped = [];

    foreach ($join as $item) {
        $id = $item->id_kategori;

        if (!isset($grouped[$id])) {
            $grouped[$id] = [
                'id_kategori' => $item->id_kategori,
                'nama_kategori' => $item->nama_kategori,
                'daftar_obat' => []
            ];
        }

        $grouped[$id]['daftar_obat'][] = [
            'nama_obat' => $item->nama_obat,
            'harga' => $item->harga
        ];
    }

    $hasil = array_values($grouped);

    $response->getBody()->write(json_encode($hasil));
    return $response->withHeader('Content-Type', 'application/json');
    }); 

    
    // NO 10  Detail Transaksi dengan Item Terjual Gunakan tabel penjualan dan penjualan_detail. Buatlah API untuk melihat satu transaksi yang di dalamnya berisi daftar barang yang dibeli (nesting data).
$app->get('/penjualan/{id}', function (Request $request, Response $response, $args) {
    $db = $this->get('db');
    $id = $args['id'];

    $data = $db->table('penjualan')
        ->join('penjualan_detail', 'penjualan.id_penjualan', '=', 'penjualan_detail.id_penjualan')
        ->select(
            'penjualan.nomor_nota',
            'penjualan.tanggal',
            'penjualan_detail.nama_produk',
            'penjualan_detail.qty',
            'penjualan_detail.subtotal'
        )
        ->where('penjualan.id_penjualan', $id)
        ->get();

    if ($data->isEmpty()) {
        $response->getBody()->write(json_encode([
            "message" => "Data tidak ditemukan"
        ]));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    // Ambil data utama dari baris pertama
    $result = [
        "nomor_nota" => $data[0]->nomor_nota,
        "tanggal" => $data[0]->tanggal,
        "total_item" => $data->count(),
        "items" => []
    ];

    // Isi detail item
    foreach ($data as $item) {
        $result['items'][] = [
            "nama_produk" => $item->nama_produk,
            "qty" => $item->qty,
            "subtotal" => $item->subtotal
        ];
    }

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

//SOAL UJIAN NO 1 
$app->get('/health', function ($request, $response) {
        $data = [
            'code' => 200,
            'message' => 'Library API Server is Healthy',
            'uptime' => '99.9%',
        ];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });

//SOAL UJIAN NO 2
$app->get('/member', function (Request $request, Response $response) {
        $data = [
            'member_id' => 'LIB-9901',
            'full_name' => 'Agus Dermawan',
            'major' => 'Sistem Informasi',
            "active_status" => "true"
        ];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });
    
//SOAL UJIAN NO 3
$app->post('/feedback', function (Request $request, Response $response) {
        $data = $request->getParsedBody();
        $responseData = [
            "info" => "Feedback Received",
            'your_message' => "Terima Sangat Membantu!",
            "received_at" => date('Y-m-d H:i:s')
        ];
        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json');
    }); 

//SOAL UJIAN NO 4
$app->delete('/booking/{id}', function (Request $request, Response $response, $args) {
        $id = $args['id'];
        $responseData = [
            "booking_code" => "$id",
            'status' => "CANCELED",
            'details' => "Booking has been removed from the queue"
        ];
        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json');
    }); 

//SOAL UJIAN NO 5
$app->post('/input_buku', function (Request $request, Response $response) {
        $data = $request->getParsedBody();
        $db = $this->get('db');
        $id_buku = $db->table('buku')->insertGetId([
            'id_buku' => $data['id_buku'],
            'judul_buku' => $data['judul_buku'],
            'penulis' => $data['penulis'],
            'tahun_terbit' => $data['tahun_terbit']
        ]);
        $responseData = [
            "status" => "success",
            "new_entry" => [
                'id_buku' => $data['id_buku'],
                'judul_buku' => $data['judul_buku'],
                'penulis' => $data['penulis'],
                'tahun_terbit' => $data['tahun_terbit']
            ]
        ];
        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json');
    });

//SOAL UJIAN NO 6
$app->put('/updaterakbuku', function (Request $request, Response $response) {
    
        $data = json_decode($request->getBody()->getContents(), true);
        $db = $this->get('db');
        $db->table('buku')
        ->where('id_buku', $data['id_buku'])
        ->update([
            'judul_buku' => $data['judul_buku'],
            'rak' => $data['rak']
        ]);
        $responseData = [ 
            "update_status" => true,
            "message" => "Book location updated for ID ".$data['id_buku'],
            "total_updated" => 1
        ];  
        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json');
    });

//SOAL UJIAN NO 7
 $app->get('/loans', function (Request $request, Response $response) {
        $db = $this->get('db');
        $join = $db->table('peminjaman')
                    ->join('buku', 'peminjaman.id_buku', '=', 'buku.id_buku')
                    ->select('peminjaman.id_pinjam', 'peminjaman.tgl_pinjam', 'buku.judul_buku', 'peminjaman.isbn_code')
                    ->get();

    $hasil = $join->map(function ($item) {
        return [
            'id_pinjam' => $item->id_pinjam,
            'tgl_pinjam' => $item->tgl_pinjam,
            'judul_buku' => $item->judul_buku,
            'isbn_code' => $item->isbn_code
        ];
    });
        $response->getBody()->write(json_encode($hasil));
        return $response->withHeader('Content-Type', 'application/json');
    }); 


//SOAL UJIAN NO 8
$app->get('/shift/{id}', function (Request $request, Response $response) {
        $db = $this->get('db');
        $join = $db->table('petugas')
                    ->join('transaksi', 'petugas.id_petugas', '=', 'transaksi.id_petugas')
                    ->select('petugas.id_petugas', 'petugas.nama_petugas', 'petugas.shift_kerja', 'transaksi.id_transaksi', 'transaksi.jenis_transaksi')
                    ->where('petugas.id_petugas', $request->getAttribute('id'))
                    ->get();

    $hasil = $join->map(function ($item) {
        return [
            'id_transaksi' => $item->id_transaksi,
            'jenis_transaksi' => $item->jenis_transaksi,
            'nama_petugas' => $item->nama_petugas,
            'shift_kerja' => $item->shift_kerja
        ];
    });
        $response->getBody()->write(json_encode($hasil));
        return $response->withHeader('Content-Type', 'application/json');
    }); 

//SOAL UJIAN NO 9
 $app->get('/daftar_penerbit', function (Request $request, Response $response) {
        $db = $this->get('db');
        $join = $db->table('penerbit')
                    ->join('buku', 'penerbit.id_penerbit', '=', 'buku.id_penerbit')
                    ->select('penerbit.id_penerbit', 'penerbit.nama_penerbit', 'penerbit.negara', 'buku.id_buku', 'buku.judul_buku')
                    ->get();

     $grouped = [];

    foreach ($join as $item) {
        $id = $item->id_penerbit;

        if (!isset($grouped[$id])) {
            $grouped[$id] = [
                'publisher_name' => $item->nama_penerbit,
                'origin' => $item->negara,
                'collections' => []
            ];
        }

        $grouped[$id]['collections'][] = [
            'judul_buku' => $item->judul_buku,
            'harga_sewa' => 2000
        ];
    }

    $hasil = array_values($grouped);

    $response->getBody()->write(json_encode($hasil));
    return $response->withHeader('Content-Type', 'application/json');
    }); 


// SOAL NO 10
$app->get('/denda', function (Request $request, Response $response) {
        $db = $this->get('db');
        $join = $db->table('denda')
                    ->join('denda_detail', 'denda.id_denda', '=', 'denda_detail.id_denda')
                    ->select('denda.id_denda', 'denda.nama_anggota','denda.total_tagihan', 'denda_detail.id_detail', 'denda_detail.judul_buku_telat', 'denda_detail.hari_telat', 'denda_detail.denda_per_buku')
                    ->get();

     $grouped = [];

    foreach ($join as $item) {
        $id = $item->id_denda;

        if (!isset($grouped[$id])) {
            $grouped[$id] = [
                'member' => $item->nama_anggota,
                'total_fine' => $item->total_tagihan,
                'overdue_details' => []
            ];
        }

        $grouped[$id]['overdue_details'][] = [
            'book_title' => $item->judul_buku_telat,
            'days' => $item->hari_telat,
            'fine' => $item->denda_per_buku
        ];
    }

    $hasil = array_values($grouped);

    $response->getBody()->write(json_encode($hasil));
    return $response->withHeader('Content-Type', 'application/json');
    }); 



$app->get('/mahasiswa', function (Request $request, Response $response) {
    $db = $this->get('db');
    $hasil = $db->table('Mahasiswa')->get();

     $data = $hasil->map(function ($item) {
        return [
            'Nama' => $item->Nama,
            'Semester' => $item->Semester,
            'Nim' => $item->Nim,
        ];
    }); 

    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

//menambahkan data rekam medis ke database
$app->post('/postmhs', function (Request $request, Response $response) {
    $db = $this->get('db');
    $data = $request->getParsedBody();
    $id = $db->table('Mahasiswa')->insertGetId([
        'Nama' => $data['Nama'],
        'Nim' => $data['Nim'],
        'Semester' => $data['Semester']
    ]);
   $result = $db->table('Mahasiswa')->where('Nim', $id)->first();
    $response->getBody()->write(json_encode($result));
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

    
};