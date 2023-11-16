--15/11/2023 ; 15:45
ALTER TABLE `socios` CHANGE `Imagen` `Imagen` LONGBLOB NULL;
ALTER TABLE `socios` CHANGE `Email` `Email` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;
ALTER TABLE `socios` CHANGE `fecha_ingreso` `fecha_ingreso` DATE NOT NULL;

