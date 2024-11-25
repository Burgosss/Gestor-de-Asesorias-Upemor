drop database if exists gestorAsesorias;
create database gestorAsesorias;
use gestorAsesorias;

-- -----------------------------------------------------
-- Table `gestorasesorias`.`usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestorasesorias`.`usuario` (
  `id_usuario` INT(11) NOT NULL AUTO_INCREMENT,
  `usuario` VARCHAR(45) NOT NULL,
  `password` VARCHAR(45) NOT NULL,
  `nombre` VARCHAR(45) NOT NULL,
  `apellido` VARCHAR(45) NOT NULL,
  `genero` VARCHAR(45) NOT NULL,
  `fec_nac` DATE NOT NULL,
  `correo_electronico` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_usuario`));

-- -----------------------------------------------------
-- Table `gestorasesorias`.`admin`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestorasesorias`.`admin` (
  `id_admin` INT(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` INT(11) NOT NULL,
  PRIMARY KEY (`id_admin`),
    FOREIGN KEY (`id_usuario`)
    REFERENCES `gestorasesorias`.`usuario` (`id_usuario`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE);


-- -----------------------------------------------------
-- Table `gestorasesorias`.`alumno`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestorasesorias`.`alumno` (
  `id_alumno` INT(11) NOT NULL AUTO_INCREMENT,
  `cuatrimestre` INT(11) NOT NULL,
  `id_usuario` INT(11) NOT NULL,
  PRIMARY KEY (`id_alumno`),
    FOREIGN KEY (`id_usuario`)
    REFERENCES `gestorasesorias`.`usuario` (`id_usuario`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE);


-- -----------------------------------------------------
-- Table `gestorasesorias`.`materias`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestorasesorias`.`materia` (
  `id_materia` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  `cuatrimestre` INT(11) NOT NULL,
  `Creditos` INT(11) NOT NULL,
  `Descripcion` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_materia`));


-- -----------------------------------------------------
-- Table `gestorasesorias`.`profesor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestorasesorias`.`profesor` (
  `id_profesor` INT(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` INT(11) NOT NULL,
  PRIMARY KEY (`id_profesor`),
    FOREIGN KEY (`id_usuario`)
    REFERENCES `gestorasesorias`.`usuario` (`id_usuario`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE);


-- -----------------------------------------------------
-- Table `gestorasesorias`.`asesoria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestorasesorias`.`asesoria` (
  `id_asesoria` INT(11) NOT NULL AUTO_INCREMENT,
  `concepto` VARCHAR(45) NOT NULL,
  `fecha` DATE NOT NULL,
  `hora` TIME NOT NULL,
  `estado` VARCHAR(45) NOT NULL,
  `observaciones` VARCHAR(100) NOT NULL,
  `id_materia`  INT(11) NOT NULL ,
  `id_alumno` INT(11) NOT NULL ,
  `id_profesor` INT(11) NOT NULL ,
  PRIMARY KEY (`id_asesoria`),
    FOREIGN KEY (`id_materia`)
    REFERENCES `gestorasesorias`.`materia` (`id_materia`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
    FOREIGN KEY (`id_alumno`)
    REFERENCES `gestorasesorias`.`alumno` (`id_alumno`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
    FOREIGN KEY (`id_profesor`)
    REFERENCES `gestorasesorias`.`profesor` (`id_profesor`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE);


-- -----------------------------------------------------
-- Table `gestorasesorias`.`archivo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestorasesorias`.`archivo` (
  `id_archivo` INT(11) NOT NULL AUTO_INCREMENT,
  `ruta` VARCHAR(45) NOT NULL,
  `id_asesoria` INT(11) NOT NULL,
  PRIMARY KEY (`id_archivo`),
    FOREIGN KEY (`id_asesoria`)
    REFERENCES `gestorasesorias`.`asesoria` (`id_asesoria`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE);


-- -----------------------------------------------------
-- Table `gestorasesorias`.`disponibibilad`
-- -----------------------------------------------------
CREATE TABLE `gestorasesorias`.`disponibilidad` (
  `iddisponibilidad` INT NOT NULL AUTO_INCREMENT,
  `dia` VARCHAR(45) NULL,
  `hora_inicio` TIME NULL,
  `hora_fin` TIME NULL,
  `id_profesor` INT NULL,
  PRIMARY KEY (`iddisponibilidad`),
    FOREIGN KEY (`id_profesor`)
    REFERENCES `gestorasesorias`.`profesor` (`id_profesor`)
    ON DELETE SET NULL
    ON UPDATE CASCADE);


-- -----------------------------------------------------
-- Table `gestorasesorias`.`mensaje`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestorasesorias`.`mensaje` (
  `id_mensaje` INT(11) NOT NULL AUTO_INCREMENT,
  `contenido` TEXT NOT NULL,
  `fecha_hora` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_profesor` INT(11) NOT NULL,
  `id_alumno` INT(11) NOT NULL,
  `remitente` INT(11) NOT NULL,  -- Nuevo campo para almacenar el ID del remitente
  PRIMARY KEY (`id_mensaje`),
  FOREIGN KEY (`id_profesor`)
    REFERENCES `gestorasesorias`.`profesor` (`id_profesor`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`id_alumno`)
    REFERENCES `gestorasesorias`.`alumno` (`id_alumno`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE
);


-- -----------------------------------------------------
-- Table `gestorasesorias`.`observaciones`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestorasesorias`.`observacion` (
  `id_observacion` INT(11) NOT NULL AUTO_INCREMENT,
  `calificacion_inicial` FLOAT NOT NULL,
  `calificacion_final` FLOAT NOT NULL,
  `comentarios` TEXT NOT NULL,
  `id_asesoria` INT(11) NOT NULL,
  PRIMARY KEY (`id_observacion`),
    FOREIGN KEY (`id_asesoria`)
    REFERENCES `gestorasesorias`.`asesoria` (`id_asesoria`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE);


-- -----------------------------------------------------
-- Table `gestorasesorias`.`profesor_materia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestorasesorias`.`profesor_materia` (
  `id_profesor_materia` INT(11) NOT NULL AUTO_INCREMENT,
  `id_profesor` INT(11) NOT NULL,
  `id_materia` INT(11) NOT NULL,
  PRIMARY KEY (`id_profesor_materia`, `id_profesor`, `id_materia`),
    FOREIGN KEY (`id_profesor`)
    REFERENCES `gestorasesorias`.`profesor` (`id_profesor`),
    FOREIGN KEY (`id_materia`)
    REFERENCES `gestorasesorias`.`materia` (`id_materia`));
    

    


show tables;

-- Cosas mias:


    
-- Inserciones en la tabla usuario
INSERT INTO gestorasesorias.usuario 
(usuario, password, nombre, apellido, genero, fec_nac, correo_electronico) 
VALUES 
('JXPO220899', 'pass123', 'Juan', 'Pérez', 'Maculino', '2003-05-10', 'juan.perez@upemor.edu.mx'),
('AXLO229211', 'pass123', 'Ana', 'López', 'Femenino', '2002-08-15', 'ana.lopez@upemor.edu.mx'),
('CXRO223462', 'profpass', 'Carlos', 'Ramírez', 'Maculino', '1980-04-12', 'carlos.ramirez@upemor.edu.mx'),
('MXSO229362', 'profpass', 'María', 'Sánchez', 'Femenino', '1985-07-20', 'maria.sanchez@upemor.edu.mx'),
('BGMO220610', 'adminpass', 'Mario', 'Burgos', 'Maculino', '1990-01-30', 'BGMO220610@upemor.edu.mx');

-- Inserciones en la tabla alumno, usando los ID de los usuarios alumno
INSERT INTO `gestorasesorias`.`alumno` (`cuatrimestre`, `id_usuario`) 
VALUES 
(4, 1), -- Juan Pérez
(6, 2); -- Ana López

-- Inserciones en la tabla profesor, usando los ID de los usuarios profesor
INSERT INTO `gestorasesorias`.`profesor` (`id_usuario`) 
VALUES 
(3), -- Carlos Ramírez
(4); -- María Sánchez

-- Inserción en la tabla admin, usando el ID de usuario del administrador
INSERT INTO `gestorasesorias`.`admin` (`id_usuario`) 
VALUES 
(5); -- Luis Morales

-- Inserciones en la tabla materia
INSERT INTO `gestorasesorias`.`materia` (`nombre`, `cuatrimestre`,`creditos`, `descripcion`) 
VALUES 
('Matemáticas', 1,5,'Introducción a matematicas básicas'),
('Programación', 2,2,'Introducción a programacion basica'),
('Bases de Datos', 3,7,'Introducción a Bases de Datos');

-- Inserciones en la tabla usuario para tres profesores adicionales
INSERT INTO gestorasesorias.usuario 
(usuario, password, nombre, apellido, genero, fec_nac, correo_electronico) 
VALUES 
('LXGO231412', 'profpass3', 'Laura', 'Gómez', 'Femenino', '1978-03-05', 'laura.gomez@upemor.edu.mx'),
('MXHO232232', 'profpass4', 'Miguel', 'Hernández', 'Maculino', '1975-11-12', 'miguel.hernandez@upemor.edu.mx'),
('SXTO245255', 'profpass5', 'Sofía', 'Torres', 'Femenino', '1982-09-28', 'sofia.torres@upemor.edu.mx');

-- Inserciones en la tabla profesor usando los ID de los usuarios profesor adicionales
INSERT INTO `gestorasesorias`.`profesor` (`id_usuario`) 
VALUES 
(6), -- Laura Gómez
(7), -- Miguel Hernández
(8); -- Sofía Torres

-- Inserciones en la tabla profesor_materia para asignar materias a cada profesor
INSERT INTO `gestorasesorias`.`profesor_materia` (`id_profesor`, `id_materia`) 
VALUES 
(1, 1), -- Primer profesor enseña Matemáticas
(2, 2), -- Segundo profesor enseña Programación
(5, 3); -- Laura Gómez enseña Bases de Datos

select * from profesor_materia;


select * from asesoria;
select * from disponibilidad;
select * from usuario;
select * from profesor;
select * from profesor_materia;

-- Asesorías de prueba para el profesor Carlos Ramírez (id_profesor = 1) en Matemáticas (id_materia = 1)

INSERT INTO asesoria (concepto, fecha, hora, estado, observaciones, id_materia, id_alumno, id_profesor)
VALUES 
-- Asesorías Aprobadas
('Asesoría Matemáticas - Álgebra', '2024-11-20', '10:00:00', 'Aprobada', '', 1, 1, 1), -- Alumno Juan Pérez
('Asesoría Matemáticas - Cálculo', '2024-11-21', '14:30:00', 'Aprobada', '', 1, 2, 1), -- Alumna Ana López

-- Asesorías Reservadas
('Asesoría Matemáticas - Geometría', '2024-11-22', '12:00:00', 'Reservada', '', 1, 1, 1), -- Alumno Juan Pérez
('Asesoría Matemáticas - Estadística', '2024-11-23', '16:00:00', 'Reservada', '', 1, 2, 1); -- Alumna Ana López

select * from asesoria;
select * from disponibilidad;

CREATE TABLE material (
    id_material INT AUTO_INCREMENT PRIMARY KEY,
    id_asesoria INT NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_asesoria) REFERENCES asesoria(id_asesoria)
);

CREATE TABLE `gestorasesorias`.`notas_reunion` (
  `idnotas_reunion` INT NOT NULL AUTO_INCREMENT,
  `calificacionP1` FLOAT NULL,
  `calificacionP2` FLOAT NULL,
  `hora_salida` TIME NULL,
  `recomendaciones` VARCHAR(45) NULL,
  `id_asesoria` INT NULL,
  PRIMARY KEY (`idnotas_reunion`),
    FOREIGN KEY (`id_asesoria`)
    REFERENCES `gestorasesorias`.`asesoria` (`id_asesoria`)
    ON DELETE SET NULL
    ON UPDATE CASCADE);
    
    CREATE TABLE `gestorasesorias`.`extensionesArchivos` (
  `idextensionesArchivos` INT NOT NULL AUTO_INCREMENT,
  `extension` VARCHAR(45) NULL,
  PRIMARY KEY (`idextensionesArchivos`));
  
  CREATE TABLE `gestorasesorias`.`pesoarchivos` (
  `idpesoArchivos` INT NOT NULL,
  `peso` FLOAT NULL,
  PRIMARY KEY (`idpesoArchivos`));
    
 INSERT INTO `gestorasesorias`.`pesoarchivos` (`idpesoArchivos`, `peso`) VALUES ('1', '50');


