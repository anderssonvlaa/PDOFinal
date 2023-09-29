CREATE DATABASE IF NOT EXISTS db_resultado;

USE db_resultado;
create table medicamentos (
    code int PRIMARY KEY AUTO_INCREMENT,
    nombre varchar(50),
    existencia int,
    fecharegistro date,
    precio float
)