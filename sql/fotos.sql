-- Importar en phpMyAdmin sobre la base `otomi_rnn` (pestaña SQL). Se puede ejecutar varias veces.
-- Foto de perfil: se guarda la RUTA del archivo (ej. uploads/perfiles/u15_ab12cd.jpg), NO la imagen dentro de la BD.
USE `otomi_rnn`;

-- Si tu tabla ya trae Foto_perfil (la del dump original), esta línea no hace nada.
ALTER TABLE `usuarios` ADD COLUMN IF NOT EXISTS `Foto_perfil` varchar(255) DEFAULT NULL;

-- Por si alguna fila quedó con cadena vacía en vez de NULL
UPDATE `usuarios` SET `Foto_perfil` = NULL WHERE `Foto_perfil` = '';
