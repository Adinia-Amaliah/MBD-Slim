<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    // get buku
    $app->get('/buku', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL GetBook()');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    // post buku
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

        $query = $db->prepare('CALL InsertBuku(?, ?, ?, ?, ?, ?, ?, ?)');

        // urutan harus sesuai dengan values
        $query->execute([$id_buku, $judul, $nama_pengarang, $tahun_terbit, $jumlah_halaman, $ISBN, $nama_penerbit, $alamat_penerbit,]);

        $response->getBody()->write(json_encode(
            [
                'message' => 'buku disimpan dengan id ' . $id_buku
            ]
        ));

        return $response->withHeader("Content-Type", "application/json");
    });

    // put buku
    $app->put('/buku/{id_buku}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        $currentId = $args['id_buku'];
        $judul = $parsedBody["judul"];
        $nama_pengarang = $parsedBody["nama_pengarang"];
        $tahun_terbit = $parsedBody["tahun_terbit"];
        $jumlah_halaman = $parsedBody["jumlah_halaman"];
        $ISBN = $parsedBody["ISBN"];
        $nama_penerbit = $parsedBody["nama_penerbit"];
        $alamat_penerbit = $parsedBody["alamat_penerbit"];
        
        $db = $this->get(PDO::class);
        
        $query = $db->prepare('CALL UpdateBuku(?, ?, ?, ?, ?, ?, ?, ?)');
        $query->bindParam(1, $currentId, PDO::PARAM_INT);
        $query->bindParam(2, $judul, PDO::PARAM_STR);
        $query->bindParam(3, $nama_pengarang, PDO::PARAM_STR);
        $query->bindParam(4, $tahun_terbit, PDO::PARAM_STR);
        $query->bindParam(5, $jumlah_halaman, PDO::PARAM_STR);
        $query->bindParam(6, $ISBN, PDO::PARAM_STR);
        $query->bindParam(7, $nama_penerbit, PDO::PARAM_STR);
        $query->bindParam(8, $alamat_penerbit, PDO::PARAM_STR);
        
        $query->execute();
        
        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'buku dengan id ' . $currentId . ' telah diupdate'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal mengupdate buku dengan id ' . $currentId
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });

    // delete buku
    $app->delete('/buku/{id_buku}', function (Request $request, Response $response, $args) {
        $currentid_buku = $args['id_buku'];
        $db = $this->get(PDO::class);

        try {
            $query = $db->prepare('CALL DeleteBuku(?)');
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

///////////////////////////////////////////////////////////////////////////////////////////////////

    // get Pengarang
    $app->get('/pengarang', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL GetPengarang()');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    // post pengarang
    $app->post('/pengarang', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();

        $id_pengarang = $parsedBody["id_pengarang"]; 
        $nama_pengarang = $parsedBody["nama_pengarang"];

        $db = $this->get(PDO::class);

        $query = $db->prepare('CALL InsertPengarang(?, ?)');

        // urutan harus sesuai dengan values
        $query->execute([$id_pengarang, $nama_pengarang]);

        $response->getBody()->write(json_encode(
            [
                'message' => 'pengarang disimpan dengan id ' . $id_pengarang
            ]
        ));

        return $response->withHeader("Content-Type", "application/json");
    });

     // put pengarang
     $app->put('/pengarang/{id_pengarang}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        $currentId = $args['id_pengarang'];
        $nama_pengarang = $parsedBody["nama_pengarang"];
        
        $db = $this->get(PDO::class);
        
        $query = $db->prepare('CALL UpdatePengarang(?, ?)');
        $query->bindParam(1, $currentId, PDO::PARAM_INT);
        $query->bindParam(2, $nama_pengarang, PDO::PARAM_STR);
        
        $query->execute();
        
        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'pengarang dengan id ' . $currentId . ' telah diupdate'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal mengupdate pengarang dengan id ' . $currentId
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });

    // delete pengarang
    $app->delete('/pengarang/{id_pengarang}', function (Request $request, Response $response, $args) {
        $currentid_pengarang = $args['id_pengarang'];
        $db = $this->get(PDO::class);

        try {
            $query = $db->prepare('CALL DeletePengarang(?)');
            $query->execute([$currentid_pengarang]);

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
                        'message' => 'pengarang dengan id_pengarang ' . $currentid_pengarang . ' dihapus dari database'
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

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    // get Penerbit
       $app->get('/penerbit', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL GetPenerbit()');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    // post penerbit
        $app->post('/penerbit', function (Request $request, Response $response) {
            $parsedBody = $request->getParsedBody();
    
            $id_penerbit = $parsedBody["id_penerbit"]; 
            $nama_penerbit = $parsedBody["nama_penerbit"];
            $alamat_penerbit = $parsedBody["alamat_penerbit"];
    
            $db = $this->get(PDO::class);
    
            $query = $db->prepare('CALL InsertPenerbit(?, ?, ?)');
    
            // urutan harus sesuai dengan values
            $query->execute([$id_penerbit, $nama_penerbit, $alamat_penerbit]);
    
            $response->getBody()->write(json_encode(
            [
                'message' => 'penerbit disimpan dengan id ' . $id_penerbit
            ]
        ));
    
        return $response->withHeader("Content-Type", "application/json");
    });

     // put penerbit
     $app->put('/penerbit/{id_penerbit}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        $currentId = $args['id_penerbit'];
        $nama_penerbit = $parsedBody["nama_penerbit"];
        $alamat_penerbit = $parsedBody["alamat_penerbit"];
        
        $db = $this->get(PDO::class);
        
        $query = $db->prepare('CALL UpdatePenerbit(?, ?, ?)');
        $query->bindParam(1, $currentId, PDO::PARAM_INT);
        $query->bindParam(2, $nama_penerbit, PDO::PARAM_STR);
        $query->bindParam(3, $alamat_penerbit, PDO::PARAM_STR);
        
        $query->execute();
        
        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'penerbit dengan id ' . $currentId . ' telah diupdate'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal mengupdate penerbit dengan id ' . $currentId
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });

    // delete penerbit
    $app->delete('/penerbit/{id_penerbit}', function (Request $request, Response $response, $args) {
        $currentid_penerbit = $args['id_penerbit'];
        $db = $this->get(PDO::class);

        try {
            $query = $db->prepare('CALL DeletePenerbit(?)');
            $query->execute([$currentid_penerbit]);

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
                        'message' => 'penerbit dengan id_penerbit ' . $currentid_penerbit . ' dihapus dari database'
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

/////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // get stok
    $app->get('/stok', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL GetStok()');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    }); 
    

    // post stok
    $app->post('/stok', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();

        $id_stok = $parsedBody["id_stok"]; 
        $judul = $parsedBody["judul"];
        $nama_toko = $parsedBody["nama_toko"];
        $jumlah_stok = $parsedBody["jumlah_stok"];

        $db = $this->get(PDO::class);

        $query = $db->prepare('CALL InsertStok(?, ?, ?, ?)');

        // urutan harus sesuai dengan values
        $query->execute([$id_stok, $judul, $nama_toko, $jumlah_stok]);

        $response->getBody()->write(json_encode(
        [
            'message' => 'stok disimpan dengan id ' . $id_stok
        ]
    ));

        return $response->withHeader("Content-Type", "application/json");
    });

     // put stok
     $app->put('/stok/{id_stok}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        $currentId = $args['id_stok'];
        $judul = $parsedBody["judul"];
        $nama_toko = $parsedBody["nama_toko"];
        $jumlah_stok = $parsedBody["jumlah_stok"];
        
        $db = $this->get(PDO::class);
        
        $query = $db->prepare('CALL UpdateStok(?, ?, ?, ?)');
        $query->bindParam(1, $currentId, PDO::PARAM_INT);
        $query->bindParam(2, $judul, PDO::PARAM_STR);
        $query->bindParam(3, $nama_toko, PDO::PARAM_STR);
        $query->bindParam(4, $jumlah_stok, PDO::PARAM_STR);
        
        $query->execute();
        
        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'stok dengan id ' . $currentId . ' telah diupdate'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal mengupdate penerbit dengan id ' . $currentId
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });

    // delete stok
    $app->delete('/stok/{id_stok}', function (Request $request, Response $response, $args) {
        $currentid_stok = $args['id_stok'];
        $db = $this->get(PDO::class);

        try {
            $query = $db->prepare('CALL DeleteStok(?)');
            $query->execute([$currentid_stok]);

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
                        'message' => 'penerbit dengan id_penerbit ' . $currentid_stok . ' dihapus dari database'
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