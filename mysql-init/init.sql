CREATE DATABASE IF NOT EXISTS gestortareas;
USE gestortareas;

CREATE TABLE IF NOT EXISTS Categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL,
  color VARCHAR(20) DEFAULT '#3498db',
  icono VARCHAR(80) DEFAULT 'fa-tag'
);

CREATE TABLE IF NOT EXISTS Tareas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  descripcion TEXT,
  categoria_id INT NULL,
  completada TINYINT(1) NOT NULL DEFAULT 0,
  prioridad ENUM('baja','media','alta') DEFAULT 'media',
  fecha_limite DATE NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES Categorias(id) ON DELETE SET NULL
);

INSERT INTO Categorias (nombre, color, icono) VALUES
('Trabajo', '#e67e22', 'fa-briefcase'),
('Personal', '#2ecc71', 'fa-user'),
('Estudio', '#3498db', 'fa-book');

INSERT INTO Tareas (titulo, descripcion, categoria_id, prioridad, fecha_limite) VALUES
('Aprender Docker', 'Completar el tutorial de Docker', 1, 'alta', DATE_ADD(CURDATE(), INTERVAL 2 DAY)),
('Hacer ejercicio', '30 minutos de cardio', 2, 'media', DATE_ADD(CURDATE(), INTERVAL 1 DAY)),
('Estudiar PHP', 'Repasar conexiones a MySQL', 3, 'baja', DATE_ADD(CURDATE(), INTERVAL 3 DAY));