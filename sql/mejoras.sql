-- Importar en phpMyAdmin sobre la base `otomi_rnn` (pestaña SQL). Se puede ejecutar más de una vez.
-- Requiere MariaDB 10.4+ (la que trae XAMPP).
USE `otomi_rnn`;

-- 1) Fecha de la última práctica (para calcular la racha de días seguidos)
ALTER TABLE `progreso` ADD COLUMN IF NOT EXISTS `Ultima_actividad` date DEFAULT NULL AFTER `Fecha_registro`;

-- 2) Lecciones terminadas por usuario (para calcular el porcentaje del curso)
CREATE TABLE IF NOT EXISTS `leccion_completada` (
  `Id_usuario` smallint(4) NOT NULL,
  `Id_leccion` varchar(30) NOT NULL,
  `Fecha` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`Id_usuario`, `Id_leccion`),
  CONSTRAINT `fk_leccion_usuario` FOREIGN KEY (`Id_usuario`) REFERENCES `usuarios` (`Id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3) Unidades de las lecciones nuevas
INSERT IGNORE INTO `unidad` (`Id_unidad`, `Titulo`, `Descripcion`, `Id_curso`) VALUES
(3, 'Frutas',   'Frutas en otomi',   100),
(4, 'Numeros',  'Numeros en otomi',  100),
(5, 'Familia',  'Familia en otomi',  100),
(6, 'Animales', 'Animales en otomi', 100);

-- 4) respuesta_usuario no tenía AUTO_INCREMENT y sus columnas eran muy cortas
ALTER TABLE `respuesta_usuario`
  MODIFY `Id_respuesta` smallint(4) NOT NULL AUTO_INCREMENT,
  MODIFY `RespuestaDelUsuario` varchar(100) NOT NULL,
  MODIFY `Respuesta_correcta` varchar(100) NOT NULL;

-- 5) Perfil: avatar y meta diaria del usuario
ALTER TABLE `usuarios`
  ADD COLUMN IF NOT EXISTS `Avatar` tinyint(2) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `Meta_diaria` smallint(4) NOT NULL DEFAULT 20;

-- 6) XP ganado por día (para la gráfica "Actividad de la semana" y los días practicados)
CREATE TABLE IF NOT EXISTS `actividad_diaria` (
  `Id_usuario` smallint(4) NOT NULL,
  `Fecha` date NOT NULL,
  `Xp` int(5) NOT NULL DEFAULT 0,
  PRIMARY KEY (`Id_usuario`, `Fecha`),
  CONSTRAINT `fk_actividad_usuario` FOREIGN KEY (`Id_usuario`) REFERENCES `usuarios` (`Id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7) Foto de perfil (ruta del archivo guardado en /uploads/perfiles)
ALTER TABLE `usuarios` ADD COLUMN IF NOT EXISTS `Foto_perfil` varchar(255) DEFAULT NULL;
