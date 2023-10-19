<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__.'/../vendor/autoload.php';

$app = new Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false,
        'db' => [
            'host' => 'localhost',
            'user' => 'root',
            'pass' => '',
            'dbname' => 'slims',
        ],
    ],
]);

// Menghubungkan ke database
$container = $app->getContainer();
$container['db'] = function ($c) {
    $dbConfig = $c->get('settings')['db'];
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}",
        $dbConfig['user'],
        $dbConfig['pass']
    );
    return $pdo;
};

// Mengambil data semua petani
$app->get('/petani', function ($request, $response) {
    $sql = "SELECT * FROM master_petani";
    $stmt = $this->db->query($sql);
    $petani = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $response->withJson(["status" => "success", "data" => $petani]);
});

// Mengambil data petani berdasarkan ID
$app->get('/petani/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $sql = "SELECT * FROM master_petani WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam("id", $id);
    $stmt->execute();
    $petani = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($petani) {
        return $response->withJson(["status" => "success", "data" => $petani]);
    } else {
        return $response->withJson(["status" => "error", "message" => "Petani not found"])->withStatus(404);
    }
});

// Menambahkan petani baru
$app->post('/petani', function ($request, $response) {
    $data = $request->getParsedBody();
    $sql = "INSERT INTO master_petani (nama, alamat, provinsi, kabupaten, kecamatan, kelurahan, nama_istri, jumlah_lahan, foto) VALUES (:nama, :alamat, :provinsi, :kabupaten, :kecamatan, :kelurahan, :nama_istri, :jumlah_lahan, :foto)";
    $stmt = $this->db->prepare($sql);
    $stmt->execute($data);
    return $response->withJson(["status" => "success", "message" => "Petani added"])->withStatus(201);
});

// Memperbarui data petani berdasarkan ID
$app->put('/petani/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $data = $request->getParsedBody();
    $sql = "UPDATE master_petani SET nama = :nama, alamat = :alamat, provinsi = :provinsi, kabupaten = :kabupaten, kecamatan = :kecamatan, kelurahan = :kelurahan, nama_istri = :nama_istri, jumlah_lahan = :jumlah_lahan, foto = :foto WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $data['id'] = $id;
    $stmt->execute($data);
    return $response->withJson(["status" => "success", "message" => "Petani updated"]);
});

// Menghapus petani berdasarkan ID
$app->delete('/petani/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $sql = "DELETE FROM master_petani WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam("id", $id);
    $stmt->execute();
    return $response->withJson(["status" => "success", "message" => "Petani deleted"]);
});

// Menjalankan aplikasi Slim
$app->run();
