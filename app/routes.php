<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Api\Models\Conexion;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    // 1. GET: Recuperar todos los Doctores
    $app->get('/doctores', function (Request $request, Response $response) {
        $db = new Conexion();
        $conn = $db->getConnection();
        
        $stmt = $conn->query("SELECT * FROM doctores");
        $doctores = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($doctores));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    });

    // 2. POST: Adicionar un nuevo Doctor
    $app->post('/doctores', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();
        $db = new Conexion();
        $conn = $db->getConnection();

        $sql = "INSERT INTO doctores (idDoctor, nombresDoctor, apellidosDoctor, especialidad, turnoAtencion, pacientesMinDiarios, nSueldo, idHospital) 
                VALUES (:id, :nombres, :apellidos, :especialidad, :turno, :pacientes, :sueldo, :idHospital)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':id' => $parsedBody['idDoctor'],
            ':nombres' => $parsedBody['nombresDoctor'],
            ':apellidos' => $parsedBody['apellidosDoctor'],
            ':especialidad' => $parsedBody['especialidad'],
            ':turno' => $parsedBody['turnoAtencion'],
            ':pacientes' => $parsedBody['pacientesMinDiarios'],
            ':sueldo' => $parsedBody['nSueldo'],
            ':idHospital' => $parsedBody['idHospital']
        ]);

        $response->getBody()->write(json_encode(["mensaje" => "Doctor registrado exitosamente"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    });

    // 3. POST: Adicionar un nuevo Hospital
    $app->post('/hospitales', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();
        $db = new Conexion();
        $conn = $db->getConnection();

        $sql = "INSERT INTO hospitales (idHospital, nomHospital, capacidadAtencion, especialidades) 
                VALUES (:id, :nombre, :capacidad, :especialidades)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':id' => $parsedBody['idHospital'],
            ':nombre' => $parsedBody['nomHospital'],
            ':capacidad' => $parsedBody['capacidadAtencion'],
            ':especialidades' => $parsedBody['especialidades']
        ]);

        $response->getBody()->write(json_encode(["mensaje" => "Hospital registrado exitosamente"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    });

    // 4. GET: Recuperar un Hospital en específico
    $app->get('/hospitales/{id}', function (Request $request, Response $response, array $args) {
        $id = $args['id'];
        $db = new Conexion();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM hospitales WHERE idHospital = :id");
        $stmt->execute([':id' => $id]);
        $hospital = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($hospital) {
            $response->getBody()->write(json_encode($hospital));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(["error" => "Hospital no encontrado"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    });
};