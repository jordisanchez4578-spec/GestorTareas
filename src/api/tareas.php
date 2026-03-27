<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers");

require_once '../conexion.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $sql = "SELECT t.*, c.nombre as categoria_nombre, c.color as categoria_color 
                FROM Tareas t 
                LEFT JOIN Categorias c ON t.categoria_id = c.id 
                ORDER BY t.created_at DESC";
        $result = $conexion->query($sql);
        $tareas = [];
        while($row = $result->fetch_assoc()) {
            $row['completada'] = (int)$row['completada'];
            $tareas[] = $row;
        }
        echo json_encode($tareas);
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $titulo = $conexion->real_escape_string($data['titulo']);
        $descripcion = $conexion->real_escape_string($data['descripcion'] ?? '');
        $categoria_id = $data['categoria_id'] ? (int)$data['categoria_id'] : 'NULL';
        $prioridad = $conexion->real_escape_string($data['prioridad'] ?? 'media');
        
        $sql = "INSERT INTO Tareas (titulo, descripcion, categoria_id, prioridad) 
                VALUES ('$titulo', '$descripcion', $categoria_id, '$prioridad')";
        
        if($conexion->query($sql)) {
            echo json_encode(['success' => true, 'id' => $conexion->insert_id]);
        } else {
            echo json_encode(['success' => false, 'error' => $conexion->error]);
        }
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = (int)$_GET['id'];
        $completada = (int)$data['completada'];
        
        $sql = "UPDATE Tareas SET completada = $completada WHERE id = $id";
        
        if($conexion->query($sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conexion->error]);
        }
        break;
        
    case 'DELETE':
        $id = (int)$_GET['id'];
        $sql = "DELETE FROM Tareas WHERE id = $id";
        
        if($conexion->query($sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conexion->error]);
        }
        break;
}

$conexion->close();
?>