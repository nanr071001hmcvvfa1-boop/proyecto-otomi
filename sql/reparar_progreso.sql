-- REPARACIÓN: la tabla `progreso` está dañada ("Tablespace is missing for a table").
-- Pega esto en phpMyAdmin > base otomi_rnn > pestaña SQL. Se pierden solo XP/racha/% guardados (las lecciones
-- completadas viven en otra tabla). Las cuentas y contraseñas NO se tocan.
USE `otomi_rnn`;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `progreso`;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `progreso` (
  `Id_usuario` smallint(4) NOT NULL,
  `Id_curso` smallint(4) NOT NULL,
  `Experiencia` int(8) NOT NULL DEFAULT 0,
  `Racha` smallint(4) NOT NULL DEFAULT 0,
  `Porcentaje` smallint(3) NOT NULL DEFAULT 0,
  `Fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `Ultima_actividad` date DEFAULT NULL,
  PRIMARY KEY (`Id_usuario`, `Id_curso`),
  CONSTRAINT `fk_progreso_usuario` FOREIGN KEY (`Id_usuario`) REFERENCES `usuarios` (`Id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_progreso_curso` FOREIGN KEY (`Id_curso`) REFERENCES `curso` (`Id_curso`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Fila inicial para los usuarios que ya existen
INSERT IGNORE INTO `progreso` (`Id_usuario`, `Id_curso`) SELECT `Id_usuario`, 100 FROM `usuarios`;
