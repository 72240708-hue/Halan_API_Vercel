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
