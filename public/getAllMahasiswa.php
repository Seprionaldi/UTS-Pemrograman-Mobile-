<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/api/progmob/mhs/{nim_progmob}', function (Request $request, Response $response, $args) {
    // Dapatkan nim_progmob dari argumen rute
    $nim_progmob = $args['nim_progmob'];
    
    // Contoh sumber data dalam bentuk array (gantilah ini dengan sumber data yang sesuai)
    $mahasiswaData = [
        [
            "id" => 1,
            "nama" => "Reinald",
            "nim" => "72150001",
            "alamat" => "Babarsari",
            "email" => "reinald@si.ukdw.ac.id",
            "foto" => "Yes",
        ],
        [
            "id" => 2,
            "nama" => "Salsa",
            "nim" => "72150002",
            "alamat" => "Babarsari",
            "email" => "salsa@si.ukdw.ac.id",
            "foto" => "Yes",
        ],
    ];

    // Cari mahasiswa dengan nim_progmob yang sesuai
    $mahasiswa = null;
    foreach ($mahasiswaData as $data) {
        if ($data['nim'] === $nim_progmob) {
            $mahasiswa = $data;
            break;
        }
    }

    if ($mahasiswa) {
        return $response->withJson($mahasiswa);
    } else {
        // Jika mahasiswa tidak ditemukan, berikan respons yang sesuai
        $responseData = [
            "message" => "Mahasiswa dengan NIM Progmob $nim_progmob tidak ditemukan",
        ];
        return $response->withJson($responseData, 404); // Gunakan status 404 Not Found
    }
});