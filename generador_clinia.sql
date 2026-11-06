-- SCRIPT de Generacion de BBDD en MySQL
CREATE DATABASE IF NOT EXISTS Clinica;
USE Clinica;
-- DROPS

DROP TABLE IF EXISTS citasMedicas;
DROP TABLE IF EXISTS doctores;
-- CREATE TABLES
CREATE TABLE doctores(n_licencia INT primary key,
						nombre_doctor VARCHAR(50) NOT NULL,
                        apellidos_doctor VARCHAR(50) NOT NULL
                        );
CREATE TABLE citasMedicas(id_cita INT primary key auto_increment,
						   nombre_paciente VARCHAR(50) NOT NULL,
                           especialidad  ENUM('Medicina General',
											'Pediatría',
                                            'Traumatología',
                                            'Dermatología'),
						   fecha DATETIME,
                           fk_doctor INT NOT NULL,
			FOREIGN KEY (fk_doctor) REFERENCES doctores(n_licencia)
                           );

-- ALTER TABLE citasMedicas ADD COLUMN(fk_doctor INT NOT NULL);
-- ALTER TABLE citasMedicas ADD CONSTRAINT FOREIGN FOREIGN KEY (fk_doctor) REFERENCES doctores(n_licencia);

DROP PROCEDURE IF EXISTS ListaDoctores;
DELIMITER //
CREATE PROCEDURE ListaDoctores()
BEGIN
	-- CASO 0 - Todo OK. Instruccion nuclear 
		SELECT * FROM doctores;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS DameDoctor;
DELIMITER //
CREATE PROCEDURE DameDoctor(IN _n_licencia INT)
BEGIN
	-- CASO 0 - Todo OK. Instruccion nuclear 
		SELECT * FROM doctores
        WHERE n_licencia = _n_licencia;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS AltaDoctor;
DELIMITER //
CREATE PROCEDURE AltaDoctor(IN _n_licencia INT,
						  IN _nombre_doctor VARCHAR(50),
						  IN _apellidos_doctor VARCHAR(50),
                          OUT _res INT)
BEGIN

	--  CASO -1 - Caso de Nombre vacio
    IF (_nombre_doctor LIKE "" or _nombre_doctor IS NULL) THEN
		SET _res = -1;
    --  CASO -2 - Caso de apellidos vacios
	ELSEIF (_apellidos_doctor LIKE "" or _apellidos_doctor IS NULL) THEN
		SET _res = -2;
	-- CASO -3 - Caso de doctor duplicado
	ELSEIF ( (SELECT n_licencia FROM doctores WHERE n_licencia LIKE _n_licencia) IS NOT NULL) THEN
		SET _res = -3;
	ELSE
	-- CASO 0 - Todo OK. Instruccion nuclear 
		INSERT INTO doctores(n_licencia,nombre_doctor,apellidos_doctor)
						VALUES(_n_licencia, _nombre_doctor,_apellidos_doctor);
		SET _res = 0;
	END IF;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS AltaCita;
DELIMITER //
CREATE PROCEDURE AltaCita(IN _nombre_paciente VARCHAR(50),
						  IN _especialidad VARCHAR(20),
						  IN _fecha DATETIME,
                          IN _doctor_asignado INT,
                          OUT _res INT)
BEGIN

	--  CASO -1 - Nombre vacio
    IF (_nombre_paciente LIKE "" or _nombre_paciente IS NULL) THEN
		SET _res = -1;
    --  CASO -2 - Especialidad vacio
	ELSEIF (_especialidad LIKE "" or _especialidad IS NULL) THEN
		SET _res = -2;
    --  CASO -3 - Fecha vacia
    ELSEIF (_fecha IS NULL or _fecha LIKE "") THEN
		SET _res = -3;
    --  CASO -4 - Fecha es anterior al presente
	ELSEIF (_fecha <= NOW()) THEN
		SET _res = -4;
    -- CASO -5 - El doctor no existe
    ELSEIF NOT EXISTS (SELECT 1 FROM doctores WHERE n_licencia = _doctor_asignado) THEN
        SET _res = -5;
    ELSE
	-- CASO 0 - Todo OK. Instruccion nuclear 
		INSERT INTO citasmedicas(nombre_paciente,especialidad,fecha, fk_doctor)
						VALUES(_nombre_paciente,_especialidad,_fecha,_doctor_asignado);
		SET _res = 0;
	END IF;
END//
DELIMITER ;







