<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Tareas Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            position: relative;
        }
        .header { background: #2c3e50; color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 2.5em; margin-bottom: 10px; }
        .header h1 i { margin-right: 10px; color: #3498db; }
        .stats { display: flex; justify-content: center; gap: 30px; margin-top: 15px; }
        .stat .numero { font-size: 1.8em; font-weight: bold; color: #3498db; }
        .stat .label { font-size: 0.9em; opacity: 0.8; }
        
        .tareas-lista { padding: 20px; min-height: 200px; }
        .tarea-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 5px solid #3498db;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s;
        }
        .tarea-card:hover { transform: translateX(5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .tarea-card.completada { opacity: 0.6; border-left-color: #2ecc71; background: #e9ecef; }
        .tarea-card.completada .tarea-titulo { text-decoration: line-through; color: #95a5a6; }

        .btn-nueva {
            display: block; width: calc(100% - 40px); margin: 20px; padding: 15px;
            background: #2ecc71; color: white; border: none; border-radius: 10px;
            font-size: 1.1em; font-weight: 600; cursor: pointer; transition: 0.3s;
        }
        .btn-nueva:hover { background: #27ae60; transform: translateY(-2px); }

        /* Estilos del Modal */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); backdrop-filter: blur(5px); }
        .modal-content { background: white; margin: 5% auto; padding: 30px; border-radius: 20px; max-width: 500px; width: 90%; animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #2c3e50; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1em; }
        
        .prioridad-tag { padding: 4px 12px; border-radius: 20px; font-size: 0.8em; font-weight: 600; text-transform: capitalize; }
        .prioridad-alta { background: #fee; color: #e74c3c; }
        .prioridad-media { background: #fff3cd; color: #856404; }
        .prioridad-baja { background: #d4edda; color: #155724; }
        
        .btn-eliminar { color: #e74c3c; background: none; border: none; cursor: pointer; font-size: 1.2em; padding: 10px; transition: 0.2s; }
        .btn-eliminar:hover { color: #c0392b; transform: scale(1.2); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-tasks"></i> Mis Tareas</h1>
            <?php
            require_once 'conexion.php';
            // Consulta de estadísticas
            $stats = $conexion->query("SELECT COUNT(*) as total, SUM(completada) as completadas FROM Tareas")->fetch_assoc();
            $total = $stats['total'] ?? 0;
            $completadas = $stats['completadas'] ?? 0;
            $pendientes = $total - $completadas;
            ?>
            <div class="stats">
                <div class="stat"><div class="numero"><?php echo $total; ?></div><div class="label">Total</div></div>
                <div class="stat"><div class="numero"><?php echo $completadas; ?></div><div class="label">Hechas</div></div>
                <div class="stat"><div class="numero"><?php echo $pendientes; ?></div><div class="label">Pendientes</div></div>
            </div>
        </div>

        <button class="btn-nueva" onclick="abrirModal()">
            <i class="fas fa-plus-circle"></i> Añadir Nueva Tarea
        </button>

        <div class="tareas-lista">
            <?php
            // Listado de tareas con categorías
            $sql = "SELECT t.*, c.nombre as cat_nombre, c.color as cat_color 
                    FROM Tareas t LEFT JOIN Categorias c ON t.categoria_id = c.id 
                    ORDER BY t.completada ASC, t.created_at DESC";
            $result = $conexion->query($sql);
            
            if ($result && $result->num_rows > 0):
                while($tarea = $result->fetch_assoc()):
            ?>
            <div class="tarea-card <?php echo $tarea['completada'] ? 'completada' : ''; ?>">
                <div class="tarea-checkbox">
                    <input type="checkbox" <?php echo $tarea['completada'] ? 'checked' : ''; ?> 
                           onchange="actualizarEstado(<?php echo $tarea['id']; ?>, this.checked)">
                </div>
                <div class="tarea-contenido">
                    <div class="tarea-titulo"><?php echo htmlspecialchars($tarea['titulo']); ?></div>
                    <div class="tarea-descripcion"><?php echo htmlspecialchars($tarea['descripcion'] ?? ''); ?></div>
                    <div class="tarea-metadata">
                        <span class="prioridad-tag prioridad-<?php echo $tarea['prioridad']; ?>">
                            <i class="fas fa-flag"></i> <?php echo $tarea['prioridad']; ?>
                        </span>
                        <?php if($tarea['cat_nombre']): ?>
                        <span style="font-size: 0.8em; color: <?php echo $tarea['cat_color']; ?>; font-weight: bold;">
                            <i class="fas fa-tag"></i> <?php echo $tarea['cat_nombre']; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <button class="btn-eliminar" onclick="eliminarTarea(<?php echo $tarea['id']; ?>)">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
            <?php endwhile; else: ?>
            <div style="text-align: center; padding: 40px; color: #95a5a6;">
                <i class="fas fa-mug-hot" style="font-size: 3em; margin-bottom: 10px;"></i>
                <p>¡Todo limpio! No tienes tareas pendientes.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="modalTarea" class="modal">
        <div class="modal-content">
            <h2 style="margin-bottom: 20px; color: #2c3e50;">Crear Tarea</h2>
            <form id="formTarea">
                <div class="form-group">
                    <label>Título</label>
                    <input type="text" id="titulo" required placeholder="Ej: Comprar pan">
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea id="descripcion" rows="3" placeholder="Detalles adicionales..."></textarea>
                </div>
                <div class="form-group">
                    <label>Prioridad</label>
                    <select id="prioridad">
                        <option value="baja">Baja</option>
                        <option value="media" selected>Media</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="button" onclick="cerrarModal()" style="flex: 1; padding: 12px; border-radius: 8px; border: 1px solid #ddd; cursor: pointer;">Cancelar</button>
                    <button type="submit" style="flex: 2; padding: 12px; border-radius: 8px; background: #3498db; color: white; border: none; cursor: pointer; font-weight: bold;">Guardar Tarea</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modalTarea');

        function abrirModal() { modal.style.display = 'block'; }
        function cerrarModal() { modal.style.display = 'none'; }

        // Crear Tarea
        document.getElementById('formTarea').onsubmit = function(e) {
            e.preventDefault();
            const data = {
                titulo: document.getElementById('titulo').value,
                descripcion: document.getElementById('descripcion').value,
                prioridad: document.getElementById('prioridad').value,
                categoria_id: 1 // Usamos la categoría 'Trabajo' por defecto
            };

            fetch('api/tareas.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                if(res.success) location.reload();
                else alert("Error al guardar la tarea");
            });
        };

        // Actualizar Estado (Check)
        function actualizarEstado(id, completada) {
            fetch(`api/tareas.php?id=${id}`, {
                method: 'PUT',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({completada: completada ? 1 : 0})
            }).then(() => location.reload());
        }

        // Eliminar Tarea
        function eliminarTarea(id) {
            if(confirm('¿Seguro que quieres borrar esta tarea?')) {
                fetch(`api/tareas.php?id=${id}`, { method: 'DELETE' })
                .then(() => location.reload());
            }
        }
    </script>
</body>
</html>