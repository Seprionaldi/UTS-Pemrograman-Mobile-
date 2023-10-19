<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

return function (App $app) {
    $app->get("/petani", function (Request $request, Response $response){
        $sql = "SELECT * FROM mahasiswa";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $response->getBody()->write(json_encode(["status" => "success", "data" => $result]));

         return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    });

    $app->get("/petani/{id}", function (Request $request, Response $response, $args){
        $idpetani = $args["id"];
        $sql = "SELECT * FROM mahasiswa WHERE idmahasiswa =:idmahasiswa";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":idmahasiswa" => $idpetani]);
        $result = $stmt->fetchAll();
         
        $response->getBody()->write(json_encode(["status" => "success", "data" => $result]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    });

    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });
    
    $app->add(function ($req, $res, $next) {
        $response = $next($req, $res);
        return $response
                ->withHeader('Access-Control-Allow-Origin', 'http://mysite')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    });
    // Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

};
