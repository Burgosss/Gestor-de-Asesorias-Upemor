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
CREATE TABLE IF NOT EXISTS `gestorasesorias`.`disponibibilad` (
  `id_disponibibilad` INT(11) NOT NULL AUTO_INCREMENT,
  `hora_inicio` TIME NOT NULL,
  `hora_fin` TIME NOT NULL,
  `dia` DATE NOT NULL,
  `id_profesor` INT(11) NOT NULL,
  PRIMARY KEY (`id_disponibibilad`),
    FOREIGN KEY (`id_profesor`)
    REFERENCES `gestorasesorias`.`profesor` (`id_profesor`)
    ON DELETE NO ACTION
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
    
    -- -----------------------------------------------------
-- Table `gestorasesorias`.`historial`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestorasesorias`.`historial` (
  `id_historial` INT NOT NULL AUTO_INCREMENT,
  `mensaje` VARCHAR(100) NOT NULL,
  `id_usuario` INT NOT NULL,
  PRIMARY KEY (`id_historial`),
    FOREIGN KEY (`id_usuario`)
    REFERENCES `gestorasesorias`.`usuario` (`id_usuario`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE);

show tables;
