<?php

namespace App;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Slim 2026 API",
 *     version="1.0.0",
 *     description="Dokumentasi API untuk project Slim 2026.",
 *     @OA\Contact(
 *         name="Argo Uchiha"
 *     )
 * )
 * 
 * @OA\Server(
 *     description="Local Server",
 *     url="http://localhost/slim2026/public"
 * )
 */
class OpenApiSpec
{
    /**
     * @OA\Get(
     *     path="/obat",
     *     summary="Mendapatkan daftar obat",
     *     tags={"Obat"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Nomor halaman",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Jumlah data per halaman",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Pencarian berdasarkan SKU",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Berhasil mengambil data obat"
     *     )
     * )
     */
    public function getObat()
    {
    }

    /**
     * @OA\Get(
     *     path="/simulasi-nested-looping",
     *     summary="Simulasi data nested menggunakan looping",
     *     tags={"Simulasi"},
     *     @OA\Response(
     *         response="200",
     *         description="Berhasil mengambil data simulasi nested looping",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id_resep", type="integer", example=101),
     *             @OA\Property(property="pasien", type="string", example="Rizky Fitriani"),
     *             @OA\Property(property="status", type="string", example="Selesai"),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="nama_obat", type="string", example="Paracetamol"),
     *                     @OA\Property(property="jumlah", type="integer", example=10),
     *                     @OA\Property(property="aturan_pakai", type="string", example="3x1")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getSimulasiNestedLooping()
    {
    }

    /**
     * @OA\Post(
     *     path="/obat",
     *     summary="Menambahkan data obat baru",
     *     tags={"Obat"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="sku", type="string", example="OBT-001"),
     *             @OA\Property(property="id_rm", type="integer", example=1),
     *             @OA\Property(property="label_catatan", type="string", example="3x sehari sesudah makan"),
     *             @OA\Property(property="jumlah", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Berhasil menambahkan data obat",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="sku", type="string", example="OBT-001"),
     *             @OA\Property(property="id_rm", type="integer", example=1),
     *             @OA\Property(property="label_catatan", type="string", example="3x sehari sesudah makan"),
     *             @OA\Property(property="jumlah", type="integer", example=10)
     *         )
     *     )
     * )
     */
    public function createObat()
    {
    }

    /**
     * @OA\Put(
     *     path="/obat/{id}",
     *     summary="Memperbarui data obat",
     *     tags={"Obat"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID Obat",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="sku", type="string", example="OBT-001-REV"),
     *             @OA\Property(property="id_rm", type="integer", example=1),
     *             @OA\Property(property="label_catatan", type="string", example="2x sehari sesudah makan"),
     *             @OA\Property(property="jumlah", type="integer", example=15)
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Berhasil memperbarui data obat",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="BERHASIL UPDATE COYY"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="sku", type="string", example="OBT-001-REV"),
     *                 @OA\Property(property="id_rm", type="integer", example=1),
     *                 @OA\Property(property="label_catatan", type="string", example="2x sehari sesudah makan"),
     *                 @OA\Property(property="jumlah", type="integer", example=15)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Data obat tidak ditemukan"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validasi gagal"
     *     )
     * )
     */
    public function updateObat()
    {
    }

    /**
     * @OA\Delete(
     *     path="/obat/{id}",
     *     summary="Menghapus data obat",
     *     tags={"Obat"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID Obat",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Berhasil menghapus data obat",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="deleted")
     *         )
     *     )
     * )
     */
    public function deleteObat()
    {
    }
/**
 * @OA\Get(
 *     path="/rekam-medis",
 *     summary="Mendapatkan daftar rekam medis",
 *     tags={"Rekam Medis"},
 *     
 *     @OA\Parameter(
 *         name="no_rm",
 *         in="query",
 *         description="Pencarian berdasarkan Nomor Rekam Medis",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *
 *     @OA\Parameter(
 *         name="dokter",
 *         in="query",
 *         description="Pencarian Nama Dokter",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data rekam medis"
 *     )
 * )
 */
    public function getRekamMedis()
    {
    }

    
    /**
 * @OA\Post(
 *     path="/rekam-medis",
 *     summary="Menambahkan data rekam medis baru",
 *     tags={"Rekam Medis"},
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"no_rm","keluhan","tinggi","berat"},
 *             @OA\Property(property="no_rm", type="string", example="0001"),
 *             @OA\Property(property="keluhan", type="string", example="Pusing"),
 *             @OA\Property(property="tinggi", type="integer", example=170),
 *             @OA\Property(property="berat", type="integer", example=70),
 *             @OA\Property(property="dokter", type="string", example="Andi"),
 *             @OA\Property(property="status_obat", type="string", example="Ada"),
 *             @OA\Property(property="tanggal", type="string", format="date", example="2023-01-01")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="Berhasil menambahkan data rekam medis",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id_rm", type="integer", example=1),
 *                 @OA\Property(property="no_rm", type="string", example="0001"),
 *                 @OA\Property(property="keluhan", type="string", example="Pusing"),
 *                 @OA\Property(property="tinggi", type="integer", example=170),
 *                 @OA\Property(property="berat", type="integer", example=70),
 *                 @OA\Property(property="dokter", type="string", example="Andi"),
 *                 @OA\Property(property="status_obat", type="string", example="Ada"),
 *                 @OA\Property(property="tanggal", type="string", format="date", example="2023-01-01")
 *             )
 *         )
 *     )
 * )
 */
    public function postRekamMedis()
    {
    }

      /**
 * @OA\Put(
 *     path="/rekam-medis/{id_rm}",
 *     summary="Memperbarui data rekam medis",
 *     tags={"Rekam Medis"},
 *     
 *     @OA\Parameter(
 *         name="id_rm",
 *         in="path",
 *         description="ID Rekam Medis",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"no_rm","keluhan","tinggi","berat"},
 *             @OA\Property(property="no_rm", type="string", example="0001"),
 *             @OA\Property(property="keluhan", type="string", example="Pusing"),
 *             @OA\Property(property="tinggi", type="integer", example=170),
 *             @OA\Property(property="berat", type="integer", example=70),
 *             @OA\Property(property="dokter", type="string", example="Andi"),
 *             @OA\Property(property="status_obat", type="string", example="Ada"),
 *             @OA\Property(property="tanggal", type="string", format="date", example="2023-01-01")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil memperbarui data rekam medis",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id_rm", type="integer", example=1),
 *                 @OA\Property(property="no_rm", type="string", example="0001"),
 *                 @OA\Property(property="keluhan", type="string", example="Pusing"),
 *                 @OA\Property(property="tinggi", type="integer", example=170),
 *                 @OA\Property(property="berat", type="integer", example=70),
 *                 @OA\Property(property="dokter", type="string", example="Andi"),
 *                 @OA\Property(property="status_obat", type="string", example="Ada"),
 *                 @OA\Property(property="tanggal", type="string", format="date", example="2023-01-01")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Data rekam medis tidak ditemukan"
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal"
 *     )
 * )
 */
    public function updateRekamMedis()
    {
    }

    /**
     * @OA\Delete(
     *     path="/rekam-medis/{id_rm}",
     *     summary="Menghapus data rekam medis",
     *     tags={"Rekam Medis"},
     *     @OA\Parameter(
     *         name="id_rm",
     *         in="path",
     *         description="ID Rekam Medis",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Berhasil menghapus data rekam medis",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="deleted")
     *         )
     *     )
     * )
     */
    public function deleteRekamMedis()
    {
    }


     /**
 * @OA\Get(
 *     path="/resep",
 *     summary="Mendapatkan daftar resep",
 *     tags={"Resep"},
 *     
 *     @OA\Parameter(
 *         name="id_dokter",
 *         in="query",
 *         description="Filter berdasarkan ID dokter",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Parameter(
 *         name="id_pasien",
 *         in="query",
 *         description="Filter berdasarkan ID pasien",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         description="Status resep",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *
 *     @OA\Parameter(
 *         name="tgl_resep",
 *         in="query",
 *         description="Tanggal resep (YYYY-MM-DD)",
 *         required=false,
 *         @OA\Schema(type="string", format="date")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data resep"
 *     )
 * )
 */
    public function getResep()
    {
    }

    /**
 * @OA\Get(
 *     path="/resep/{id_resep}",
 *     summary="Mendapatkan detail resep berdasarkan ID",
 *     tags={"Resep"},
 *     
 *     @OA\Parameter(
 *         name="id_resep",
 *         in="path",
 *         description="ID Resep",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil detail resep",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id_resep", type="integer", example=1),
 *             @OA\Property(property="tgl_resep", type="string", format="date", example="2026-05-02"),
 *             @OA\Property(property="id_pasien", type="integer", example=10),
 *             @OA\Property(property="nama_pasien", type="string", example="Budi"),
 *             @OA\Property(property="dokter_pemeriksa", type="string", example="dr. Andi")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Resep tidak ditemukan"
 *     )
 * )
 */
    public function getResepById()
    {
    }


    /**
 * @OA\Post(
 *     path="/resep",
 *     summary="Menambahkan data resep baru",
 *     tags={"Resep"},
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"id_pasien","id_dokter","tgl_resep","status"},
 *             @OA\Property(property="id_pasien", type="integer", example=1),
 *             @OA\Property(property="id_dokter", type="integer", example=1),
 *             @OA\Property(property="tgl_resep", type="string", format="date", example="2023-01-01"),
 *             @OA\Property(property="status", type="enum", enum={"Pending", "Disetujui", "Ditolak"}, example="Pending")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="Berhasil menambahkan data resep",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id_resep", type="integer", example=1),
 *                 @OA\Property(property="id_pasien", type="integer", example=1),
 *                 @OA\Property(property="id_dokter", type="integer", example=1),
 *                 @OA\Property(property="tgl_resep", type="string", format="date", example="2023-01-01"),
 *                 @OA\Property(property="status", type="enum", enum={"Pending", "Disetujui", "Ditolak"}, example="Pending")
 *             )
 *         )
 *     )
 * )
 */
    
    public function postResep()
    {
    }


      /**
 * @OA\Post(
 *     path="/detail-resep",
 *     summary="Menambahkan data detail resep baru",
 *     tags={"Detail Resep"},
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"id_resep","id_obat","jumlah","dosis","keterangan"},
 *             @OA\Property(property="id_resep", type="integer", example=1),
 *             @OA\Property(property="id_obat", type="integer", example=1),
 *             @OA\Property(property="jumlah", type="integer", example=1),
 *             @OA\Property(property="dosis", type="string", example="1x sehari"),
 *             @OA\Property(property="keterangan", type="string", example="Minum setelah makan")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="Berhasil menambahkan data detail resep",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id_detail_resep", type="integer", example=1),
 *                 @OA\Property(property="id_resep", type="integer", example=1),
 *                 @OA\Property(property="id_obat", type="integer", example=1),
 *                 @OA\Property(property="jumlah", type="integer", example=1),
 *                 @OA\Property(property="dosis", type="string", example="1x sehari"),
 *                 @OA\Property(property="keterangan", type="string", example="Minum setelah makan"),
 *                 @OA\Property(property="status", type="enum", enum={"Pending", "Disetujui", "Ditolak"}, example="Pending")
 *             )
 *         )
 *     )
 * )
 */
    
    public function postDetailResep()
    {
    }
    

    
    /**
 * @OA\Get(
 *     path="/readdtl_resep",
 *     summary="Menampilkan detail resep beserta obat",
 *     tags={"Detail Resep"},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data resep beserta detail obat",
 *         @OA\JsonContent(
 *             type="object",
 *
 *             @OA\Property(property="id_resep", type="integer", example=1),
 *
 *             @OA\Property(
 *                 property="items",
 *                 type="array",
 *
 *                 @OA\Items(
 *                     type="object",
 *
 *                     @OA\Property(property="id_detail", type="integer", example=1),
 *                     @OA\Property(property="jumlah", type="integer", example=10),
 *                     @OA\Property(property="dosis", type="string", example="3x1"),
 *                     @OA\Property(property="keterangan", type="string", example="Setelah makan"),
 *
 *                     @OA\Property(
 *                         property="obat",
 *                         type="object",
 *                         @OA\Property(property="id_obat", type="integer", example=5),
 *                         @OA\Property(property="nama_obat", type="string", example="Paracetamol"),
 *                         @OA\Property(property="label_catatan", type="string", example="Obat keras")
 *                     )
 *                 )
 *             )
 *         )
 *     )
 * )
 */
    public function getDetailResep()
    {
    }

     /**
     * @OA\Delete(
     *     path="/detail-resep/{id_dtl_resep}",
     *     summary="Menghapus data detail resep",
     *     tags={"Detail Resep"},
     *     @OA\Parameter(
     *         name="id_dtl_resep",
     *         in="path",
     *         description="ID Detail Resep",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Berhasil menghapus data detail resep",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="deleted")
     *         )
     *     )
     * )
     */
    public function deleteDetailResep()
    {
    }

          /**
 * @OA\Post(
 *     path="/obat-baru",
 *     summary="Menambahkan data obat baru",
 *     tags={"Obat Baru"},
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"nama_obat","harga","stok"},
 *             @OA\Property(property="nama_obat", type="string", example="Paracetamol"),
 *            @OA\Property(property="harga", type="string", example="Rp 10.000"),
 *             @OA\Property(property="stok", type="integer", example=100)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="Berhasil menambahkan data obat baru",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id_obat", type="integer", example=1),
 *                 @OA\Property(property="nama_obat", type="string", example="Paracetamol"),
 *                @OA\Property(property="harga", type="string", example="Rp 10.000"),
 *                 @OA\Property(property="stok", type="integer", example=100)
 *             )
 *         )
 *     )
 * )
 */
    
    public function postObatBaru()
    {
    }

     /**
 * @OA\Put(
 *     path="/obat-baru/{id_obat}",
 *     summary="Memperbarui data obat baru melalui ID Obat",
 *     tags={"Obat Baru"},
 *     
 *     @OA\Parameter(
 *         name="id_obat",
 *         in="path",
 *         description="ID Obat Baru",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"nama_obat","harga","stok"},
 *             @OA\Property(property="nama_obat", type="string", example="Paracetamol"),
 *             @OA\Property(property="harga", type="string", example="Rp 10.000"),
 *             @OA\Property(property="stok", type="integer", example=100)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil memperbarui data obat baru",
 *         @OA\JsonContent(
 *             type="object",
 *            @OA\Property(property="id_obat", type="integer", example=1),
 *           @OA\Property(property="nama_obat", type="string", example="Paracetamol"),
 *             @OA\Property(property="harga", type="string", example="Rp 10.000"),
 *             @OA\Property(property="stok", type="integer", example=100)
 *         )
 *     ),
 *
 *   
 *
 *     @OA\Response(
 *         response=404,
 *         description="Data obat baru tidak ditemukan"
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal"
 *     )
 * )
 */
    public function updateObatBaru()
    {
    }


     /**
 * @OA\Get(
 *     path="/pembelian",
 *     summary="Mendapatkan data pembelian dari supplier",
 *     tags={"Pembelian"},
 *     
 *     @OA\Parameter(
 *         name="id_beli",
 *         in="query",
 *         description="Filter berdasarkan ID Beli",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Parameter(
 *         name="id_supplier",
 *         in="query",
 *         description="Filter berdasarkan ID Supplier",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data Pembelian"
 *     )
 * )
 */
    public function getPembelian()
    {
    }

     /**
 * @OA\Get(
 *     path="/kategori",
 *     summary="Mendapatkan data kategori obat",
 *     tags={"Kategori Obat"},
 *     
 *     @OA\Parameter(
 *         name="id_kategori",
 *         in="query",
 *         description="Filter berdasarkan ID Kategori",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Parameter(
 *         name="nama_kategori",
 *         in="query",
 *         description="Filter berdasarkan Nama Kategori",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data Kategori Obat"
 *     )
 * )
 */
    public function getKategori()
    {
    }

    
    
     /**
 * @OA\Get(
 *     path="/penjualan/{id_penjualan}",
 *     summary="Mendapatkan detail penjualan berdasarkan ID",
 *     tags={"Penjualan"},
 *     
 *     @OA\Parameter(
 *         name="id_penjualan",
 *         in="path",
 *         description="ID Penjualan",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data penjualan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id_penjualan", type="integer", example=1),
 *             @OA\Property(property="nomor_nota", type="string", example="TRX-001"),
 *             @OA\Property(property="tanggal", type="string", format="date", example="2026-05-02"),
 *           @OA\Property(property="nama_produk", type="string", example="Taro"),
 *          @OA\Property(property="qty", type="integer", example=10),
 *          @OA\Property(property="subtotal", type="integer", example=10)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Data penjualan tidak ditemukan"
 *     )
 * )
 */
    public function getPenjualan()
    {
    }

/**
 * @OA\Post(
 *     path="/buku",
 *     summary="Menambahkan data buku",
 *     tags={"Buku"},
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"judul_buku","penulis","tahun_terbit"},
 *             @OA\Property(property="judul_buku", type="string", example="Pemrograman PHP"),
 *             @OA\Property(property="penulis", type="string", example="John Doe"),
 *             @OA\Property(property="tahun_terbit", type="integer", example=2020)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="Berhasil menambahkan data buku",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id_buku", type="integer", example=1),
 *                 @OA\Property(property="judul_buku", type="string", example="Pemrograman PHP"),
 *                 @OA\Property(property="penulis", type="string", example="John Doe"),
 *                 @OA\Property(property="tahun_terbit", type="integer", example=2020)
 *             )
 *         )
 *     )
 * )
 */
    
    public function postBuku()
    {
    }

     /**
 * @OA\Put(
 *     path="/buku/{id_buku}",
 *     summary="Memperbarui data buku",
 *     tags={"Buku"},
 *     
 *     @OA\Parameter(
 *         name="id_buku",
 *         in="path",
 *         description="ID Buku",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"judul_buku","penulis","tahun_terbit"},
 *             @OA\Property(property="judul_buku", type="string", example="Pemrograman PHP"),
 *             @OA\Property(property="penulis", type="string", example="John Doe"),
 *             @OA\Property(property="tahun_terbit", type="integer", example=2020)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil memperbarui data buku",
 *         @OA\JsonContent(
 *             type="object",
 *            @OA\Property(property="id_buku", type="integer", example=1),
 *           @OA\Property(property="judul_buku", type="string", example="Pemrograman PHP"),
 *             @OA\Property(property="penulis", type="string", example="John Doe"),
 *             @OA\Property(property="tahun_terbit", type="integer", example=2020)
 *         )
 *     ),
 *
 *   
 *
 *     @OA\Response(
 *         response=404,
 *         description="Data buku tidak ditemukan"
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal"
 *     )
 * )
 */
    public function updateBuku()
    {
    }

    
     /**
 * @OA\Get(
 *     path="/peminjaman/{id_pinjam}",
 *     summary="Mendapatkan data Peminjaman",
 *     tags={"Peminjaman"},
 *     
 *     @OA\Parameter(
 *         name="id_pinjam",
 *         in="query",
 *         description="Filter berdasarkan ID Pinjam",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Parameter(
 *         name="id_buku",
 *         in="query",
 *         description="Filter berdasarkan ID Buku",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data Peminjaman",
 *           @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id_pinjam", type="integer", example=1),
 *             @OA\Property(property="tgl_pinjam", type="string", format="date", example="2026-05-02"),
 *             @OA\Property(property="judul_buku", type="string", example="Power"),
 *             @OA\Property(property="isbn_code", type="string", example="001")
 *         )
 *     ),
 * 
 * )
 */
    public function getPeminjaman()
    {
    }

 /**
 * @OA\Get(
 *     path="/petugas/{id_petugas}",
 *     summary="Mendapatkan data Shift Petugas & Transaksi",
 *     tags={"Petugas"},
 *     
 *     @OA\Parameter(
 *         name="id_petugas",
 *         in="path",
 *         description="ID Petugas",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data Petugas",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id_transaksi", type="integer", example=1),
 *             @OA\Property(property="jenis_transaksi", type="string", example="Peminjaman"),
 *             @OA\Property(property="nama_petugas", type="string", example="Budi"),
 *             @OA\Property(property="shift_kerja", type="string", example="Pagi")
 *         )
 *     )
 * )
 */
    public function getPetugasById()
    {
    }


    /**
 * @OA\Get(
 *     path="/daftar_penerbit",
 *     summary="Menampilkan daftar penerbit beserta koleksi buku",
 *     tags={"Penerbit"},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data penerbit",
 *         @OA\JsonContent(
 *             type="array",
 *
 *             @OA\Items(
 *                 type="object",
 *
 *                 @OA\Property(property="publisher_name", type="string", example="Gramedia"),
 *                 @OA\Property(property="origin", type="string", example="Indonesia"),
 *
 *                 @OA\Property(
 *                     property="collections",
 *                     type="array",
 *
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="judul_buku", type="string", example="Laskar Pelangi"),
 *                         @OA\Property(property="harga_sewa", type="number", example=2000)
 *                     )
 *                 )
 *             )
 *         )
 *     )
 * )
 */
    public function getPenerbitById()
    {
    }
 
    /**
 * @OA\Get(
 *     path="/denda",
 *     summary="Menampilkan daftar denda beserta detail keterlambatan buku",
 *     tags={"Denda"},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data denda",
 *         @OA\JsonContent(
 *             type="array",
 *             
 *             @OA\Items(
 *                 type="object",
 *                 
 *                 @OA\Property(property="member", type="string", example="Agus"),
 *                 @OA\Property(property="total_fine", type="number", example=5000),
 *                 
 *                 @OA\Property(
 *                     property="overdue_details",
 *                     type="array",
 *                     
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="book_title", type="string", example="Laskar Pelangi"),
 *                         @OA\Property(property="days", type="integer", example=2),
 *                         @OA\Property(property="fine", type="number", example=2000)
 *                     )
 *                 )
 *             )
 *         )
 *     )
 * )
 */
    public function getDendaById()
    {
    }

    /**
 * @OA\Get(
 *     path="/status",
 *     summary="Mengecek status API",
 *     tags={"System"},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Status API",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="online"),
 *             @OA\Property(property="message", type="string", example="API Smart Campus Ready"),
 *             @OA\Property(property="version", type="string", example="1.0.0")
 *         )
 *     )
 * )
 */

    public function getStatus()
    {
    }


    /**
 * @OA\Get(
 *     path="/mhs",
 *     summary="Menampilkan data mahasiswa (static)",
 *     tags={"Mahasiswa"},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Data mahasiswa",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="nama", type="string", example="Budi Santoso"),
 *             @OA\Property(property="nim", type="string", example="220101"),
 *             @OA\Property(property="prodi", type="string", example="informatika"),
 *             @OA\Property(property="semester", type="string", example="4")
 *         )
 *     )
 * )
 */
      public function getMahasiswa()
      {

      }

      /**
 * @OA\Delete(
 *     path="/hapus_aja/{id}",
 *     summary="Menghapus data berdasarkan ID (simulasi)",
 *     tags={"Data Hapus"},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID data yang akan dihapus",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Data berhasil dihapus",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id_terhapus", type="string", example="1"),
 *             @OA\Property(property="message", type="string", example="Data dengan ID 1 berhasil dihapus dari sistem"),
 *             @OA\Property(property="status", type="string", example="deleted")
 *         )
 *     )
 * )
 */

      public function deleteData()
      {

      }

      /**
 * @OA\Post(
 *     path="/pesan",
 *     summary="Mengirim pesan dan menerima balasan",
 *     tags={"Pesan"},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"pesan"},
 *             @OA\Property(property="pesan", type="string", example="Halo API")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Balasan pesan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="balasan", type="string", example="Terima kasih, pesan anda: Halo API"),
 *             @OA\Property(property="timestamp", type="string", example="2026-05-03 10:00:00")
 *         )
 *     )
 * )
 */
      public function postPesan()
      {

      }

      /**
 * @OA\Get(
 *     path="/health",
 *     summary="Cek kesehatan server API",
 *     tags={"System Health"},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Server dalam kondisi sehat",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="code", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Library API Server is Healthy"),
 *             @OA\Property(property="uptime", type="string", example="99.9%")
 *         )
 *     )
 * )
 */
      public function getHealth()
      {
      }
      
      /**
 * @OA\Get(
 *     path="/member",
 *     summary="Menampilkan data member perpustakaan",
 *     tags={"Member"},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Data member",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="member_id", type="string", example="LIB-9901"),
 *             @OA\Property(property="full_name", type="string", example="Agus Dermawan"),
 *             @OA\Property(property="major", type="string", example="Sistem Informasi"),
 *             @OA\Property(property="active_status", type="string", example="true")
 *         )
 *     )
 * )
 */

      public function getMember()
      {
      }

      /**
 * @OA\Post(
 *     path="/feedback",
 *     summary="Mengirim feedback pengguna",
 *     tags={"Feedback"},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"message"},
 *             @OA\Property(property="message", type="string", example="Aplikasi sangat membantu")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Feedback berhasil diterima",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="info", type="string", example="Feedback Received"),
 *             @OA\Property(property="your_message", type="string", example="Terima Sangat Membantu!"),
 *             @OA\Property(property="received_at", type="string", example="2026-05-03 10:00:00")
 *         )
 *     )
 * )
 */
      public function postFeedback()
      {
      }

      /**
 * @OA\Delete(
 *     path="/booking/{id}",
 *     summary="Membatalkan booking berdasarkan ID",
 *     tags={"Booking"},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Kode booking",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Booking berhasil dibatalkan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="booking_code", type="string", example="BK-001"),
 *             @OA\Property(property="status", type="string", example="CANCELED"),
 *             @OA\Property(property="details", type="string", example="Booking has been removed from the queue")
 *         )
 *     )
 * )
 */
      public function deleteBooking()
      {
      }

      /**
 * @OA\Get(
 *     path="/mahasiswa",
 *     summary="Menampilkan daftar mahasiswa",
 *     tags={"Mahasiswa"},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil data mahasiswa",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="Nama", type="string", example="Budi"),
 *                 @OA\Property(property="Semester", type="integer", example=4),
 *                 @OA\Property(property="Nim", type="string", example="220101")
 *             )
 *         )
 *     )
 * )
 */

      public function getDaftarMahasiswa()
      {
      }

       
      /**
 * @OA\Post(
 *     path="/postmhs",
 *     summary="Menambahkan data mahasiswa",
 *     tags={"Mahasiswa"},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"Nama","Nim","Semester"},
 *             @OA\Property(property="Nama", type="string", example="Budi"),
 *             @OA\Property(property="Nim", type="string", example="220101"),
 *             @OA\Property(property="Semester", type="integer", example=4)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil menambahkan data mahasiswa",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="Nama", type="string", example="Budi"),
 *             @OA\Property(property="Nim", type="string", example="220101"),
 *             @OA\Property(property="Semester", type="integer", example=4)
 *         )
 *     )
 * )
 */

      public function postMahasiswa()
      {    
      }

      





}
    

