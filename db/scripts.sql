--17/06/2023 18:43
ALTER TABLE socios ADD COLUMN Id_Clase int(11) NOT NULL, ADD CONSTRAINT `fk_relacion` 
FOREIGN KEY (Id_Clase) REFERENCES clases (IdClase);

--17/06/2023 11:07
ALTER TABLE socios 
ADD fecha_ingreso DATE, ADD fecha_vencimiento DATE;

