<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    // get
    $app->get('/buku', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('SELECT * FROM buku');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    // get by id_buku
    $app->get('/buku/{id_buku}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('SELECT * FROM buku WHERE id_buku=?');
        $query->execute([$args['id_buku']]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    // post data
    $app->post('/buku', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();

        $id_buku = $parsedBody["id_buku"]; // menambah dengan kolom baru
        $judul = $parsedBody["judul"];
        $nama_pengarang = $parsedBody["nama_pengarang"];
        $tahun_terbit = $parsedBody["tahun_terbit"];
        $jumlah_halaman = $parsedBody["jumlah_halaman"];
        $ISBN = $parsedBody["ISBN"];
        $nama_penerbit = $parsedBody["nama_penerbit"];
        $alamat_penerbit = $parsedBody["alamat_penerbit"];

        $db = $this->get(PDO::class);

        $query = $db->prepare('INSERT INTO buku (id_buku, judul, nama_pengarang, tahun_terbit, jumlah_halaman, ISBN, nama_penerbit, alamat_penerbit) values (?, ?, ?, ?, ?, ?, ?, ?)');

        // urutan harus sesuai dengan values
        $query->execute([$id_buku, $judul, $nama_pengarang, $tahun_terbit, $jumlah_halaman, $ISBN, $nama_penerbit, $alamat_penerbit,]);

        $response->getBody()->write(json_encode(
            [
                'message' => 'buku disimpan dengan id ' . $id_buku
            ]
        ));

        return $response->withHeader("Content-Type", "application/json");
    });

    // put data
    $app->put('/buku/{id_buku}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();

        $currentid_buku = $args['id_buku'];
        $countryName = $parsedBody["name"];
        $db = $this->get(PDO::class);

        $query = $db->prepare('UPDATE buku SET name = ? WHERE id_buku = ?');
        $query->execute([$countryName, $currentid_buku]);

        $response->getBody()->write(json_encode(
            [
                'message' => 'country dengan id_buku ' . $currentid_buku . ' telah diupdate dengan nama ' . $countryName
            ]
        ));

        return $response->withHeader("Content-Type", "application/json");
    });

    // delete data
    $app->delete('/buku/{id_buku}', function (Request $request, Response $response, $args) {
        $currentid_buku = $args['id_buku'];
        $db = $this->get(PDO::class);

        try {
            $query = $db->prepare('DELETE FROM buku WHERE id_buku = ?');
            $query->execute([$currentid_buku]);

            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Data tidak ditemukan'
                    ]
                ));
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'buku dengan id_buku ' . $currentid_buku . ' dihapus dari database'
                    ]
                ));
            }
        } catch (PDOException $e) {
            $response = $response->withStatus(500);
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Database error ' . $e->getMessage()
                ]
            ));
        }

        return $response->withHeader("Content-Type", "application/json");
    });
};
