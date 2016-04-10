# Seguir instrucciones iniciales en el `forro.sql`
# - Crear base de datos
# - Crear el usuario
# - Le brindamos privilegios al usuario

# use imagenes;

# Primera estrategia, almacenando las imagenes en el servidor y guardando únicamente la ruta
CREATE TABLE imagenes_en_servidor(
  id INT NOT NULL AUTO_INCREMENT,
  titulo VARCHAR(255),
  url_imagen VARCHAR(255),
  PRIMARY KEY (id)
);

# Segunda estrategia, almacenando las imagenes en la base de datos
CREATE TABLE imagenes_en_bd(
  id INT NOT NULL AUTO_INCREMENT,
  titulo VARCHAR(100),
  imagen_tipo VARCHAR(40),
  imagen LONGBLOB,
  PRIMARY KEY (id)
);

# Tercera estrategia, almacenando las imagenes en un servicio servicio
CREATE TABLE imagenes_en_servicio_externo(
  id INT NOT NULL AUTO_INCREMENT,
  id_externo VARCHAR(255),
  titulo VARCHAR(255),
  url_imagen VARCHAR(100),
  PRIMARY KEY (id)
);

# SHOW tables;