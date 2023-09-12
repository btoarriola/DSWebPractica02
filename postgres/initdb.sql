CREATE USER myuser;
CREATE DATABASE mydb;
GRANT ALL PRIVILEGES ON DATABASE mydb TO myuser;
ALTER DATABASE mydb OWNER TO myuser;

\connect mydb

CREATE TABLE mytable(
	clave SERIAL NOT NULL,
	nombre character varying,
	direccion character varying,
	telefono character varying,
	CONSTRAINT pk_clave PRIMARY KEY (clave)
);

ALTER TABLE mytable OWNER TO myuser;