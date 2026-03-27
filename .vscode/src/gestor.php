<?php 
// 1. CONECTAMOS CON LA BASE DE DATOS
include("conexion.php"); 

// 2. PEDIMOS LAS TAREAS (Usando un JOIN para traer el nombre de la categoría)
$query = "SELECT t.*, c.nombre AS cat_nombre, c.color AS cat_color, c.icono AS cat_icono 
          FROM Tareas t 
          LEFT JOIN Categorias c ON t.categoria_id = c.id";

$resultado = mysqli_query($conexion, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestor de Tareas Profesional</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Tu CSS aquí para que se vea como el diseño que hicimos */
        body { font-family: 'Segoe UI', sans-serif; display: flex; margin: 0; background: #f8f9fa; }
        aside { width: 260px; background: #2c3e50; color: white; height: 100vh; padding: 20px; }
        main { flex: 1; padding: 40px; }
        .tarea-card { background: white; padding: 15px; margin-bottom: 10px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; align-items: center; }
        .tag { padding: 4px 10px; border-radius: 15px; font-size: 12px; color: white; margin-left: 10px; }
    </style>
</head>
<body>

    <aside>
        <h2><i class="fas fa-tasks"></i> Mi Gestor</h2>
        <hr>
        <p><i class="fas fa-user"></i> Usuario: <strong>Admin</strong></p>
        </aside>

    <main>
        <h1>Mis Pendientes</h1>
        
        <div id="contenedor-tareas">
            <?php 
            // 3. EL BUCLE MÁGICO: Crea un HTML por cada fila de la BD
            while($tarea = mysqli_fetch_assoc($resultado)) { 
            ?>
                <div class="tarea-card">
                    <input type="checkbox" <?php echo $tarea['completada'] ? 'checked' : ''; ?>>
                    
                    <div style="margin-left: 20px; flex: 1;">
                        <strong><?php echo $tarea['titulo']; ?></strong>
                        <p style="margin: 5px 0; font-size: 14px; color: #666;">
                            <?php echo $tarea['descripcion']; ?>
                        </p>
                        
                        <span class="tag" style="background-color: <?php echo $tarea['cat_color'] ?? '#ccc'; ?>">
                            <i class="fas <?php echo $tarea['cat_icono'] ?? 'fa-tag'; ?>"></i> 
                            <?php echo $tarea['cat_nombre'] ?? 'Sin categoría'; ?>
                        </span>
                    </div>

                    <div class="acciones">
                        <i class="fas fa-trash" style="color: #e74c3c; cursor: pointer;"></i>
                    </div>
                </div>
            <?php } ?>
        </div>
    </main>

</body>
</html>