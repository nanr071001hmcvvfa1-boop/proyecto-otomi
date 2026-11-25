-- Plan B: pega esto en phpMyAdmin > base otomi_rnn > pestaña SQL > Continuar.
-- Si alguna línea da error "Duplicate column" o "already exists", ignórala: ya estaba hecha.
ALTER TABLE `progreso` ADD COLUMN `Ultima_actividad` date DEFAULT NULL;
ALTER TABLE `usuarios` ADD COLUMN `Avatar` tinyint(2) NOT NULL DEFAULT 0;
ALTER TABLE `usuarios` ADD COLUMN `Meta_diaria` smallint(4) NOT NULL DEFAULT 20;
CREATE TABLE IF NOT EXISTS `leccion_completada` (
  `Id_usuario` smallint(4) NOT NULL,
  `Id_leccion` varchar(30) NOT NULL,
  `Fecha` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`Id_usuario`, `Id_leccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE IF NOT EXISTS `actividad_diaria` (
  `Id_usuario` smallint(4) NOT NULL,
  `Fecha` date NOT NULL,
  `Xp` int(5) NOT NULL DEFAULT 0,
  PRIMARY KEY (`Id_usuario`, `Fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
ALTER TABLE `usuarios` ADD COLUMN `Foto_perfil` varchar(255) DEFAULT NULL;
