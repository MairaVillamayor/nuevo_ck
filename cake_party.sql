CREATE DATABASE cake_party;
USE cake_party;

CREATE TABLE estado_decoraciones (ID_estado_decoraciones INT NOT NULL AUTO_INCREMENT,
estado_decoraciones_descri VARCHAR (20) NOT NULL,
PRIMARY KEY (ID_estado_decoraciones));

CREATE TABLE tamaño (ID_tamaño INT NOT NULL AUTO_INCREMENT,
tamaño_nombre VARCHAR (15) NOT NULL,
tamaño_medidas VARCHAR (10) NOT NULL,
tamaño_precio FLOAT(10,2) NOT NULL DEFAULT 0,
PRIMARY KEY (ID_tamaño));

CREATE TABLE sabor (ID_sabor INT NOT NULL AUTO_INCREMENT,
sabor_nombre VARCHAR (20) NOT NULL,
sabor_descripcion VARCHAR (100),
sabor_precio FLOAT(10,2) NOT NULL DEFAULT 0,
RELA_estado_decoraciones INT NOT NULL,
PRIMARY KEY (ID_sabor));

CREATE TABLE relleno (ID_relleno INT NOT NULL AUTO_INCREMENT,
relleno_nombre VARCHAR(20) NOT NULL,
relleno_descripcion VARCHAR(100),
relleno_precio FLOAT(10,2) NOT NULL DEFAULT 0,
RELA_estado_decoraciones INT NOT NULL,
PRIMARY KEY (ID_relleno));

CREATE TABLE tematica (ID_tematica INT NOT NULL AUTO_INCREMENT,
tematica_descripcion VARCHAR (100),
RELA_estado_decoraciones INT NOT NULL,
PRIMARY KEY (ID_tematica));

CREATE TABLE color_pastel (ID_color_pastel INT NOT NULL AUTO_INCREMENT,
color_pastel_nombre VARCHAR (110) NOT NULL,
color_pastel_codigo VARCHAR (20),
RELA_estado_decoraciones INT NOT NULL,
PRIMARY KEY (ID_color_pastel));

CREATE TABLE decoracion(ID_decoracion INT NOT NULL AUTO_INCREMENT,
decoracion_nombre VARCHAR (25),
decoracion_descripcion VARCHAR (150),
decoracion_precio  FLOAT(10,2) NOT NULL DEFAULT 0,
RELA_estado_decoraciones INT NOT NULL,
PRIMARY KEY (ID_decoracion));

CREATE TABLE base_pastel(ID_base_pastel INT NOT NULL AUTO_INCREMENT,
base_pastel_nombre VARCHAR(20) NOT NULL,
base_pastel_descripcion VARCHAR (100),
base_pastel_precio  FLOAT(10,2) NOT NULL DEFAULT 0,
RELA_estado_decoraciones INT NOT NULL,
PRIMARY KEY (ID_base_pastel));

CREATE TABLE pastel_personalizado(ID_pastel_personalizado INT NOT NULL AUTO_INCREMENT,
pastel_personalizado_descripcion VARCHAR (250),
pastel_personalizado_pisos_total INT NOT NULL,
RELA_color_pastel INT NOT NULL,
RELA_decoracion INT NOT NULL,
RELA_base_pastel INT NOT NULL,
PRIMARY KEY (ID_pastel_personalizado));

CREATE TABLE pisos (ID_pisos INT NOT NULL AUTO_INCREMENT,
pisos_numero INT NOT NULL,
RELA_pastel_personalizado INT NOT NULL,
RELA_tamaño INT NOT NULL,
UNIQUE (RELA_pastel_personalizado, pisos_numero),
PRIMARY KEY (ID_pisos));

CREATE TABLE pisos_sabor (ID_pisos_sabor INT NOT NULL AUTO_INCREMENT,
RELA_sabor INT NOT NULL,
RELA_pisos INT NOT NULL,
PRIMARY KEY (ID_pisos_sabor));

CREATE TABLE pisos_relleno (ID_pisos_relleno INT NOT NULL AUTO_INCREMENT,
RELA_pisos INT NOT NULL,
RELA_relleno INT NOT NULL,
PRIMARY KEY (ID_pisos_relleno));

CREATE TABLE estado_insumos(ID_estado_insumo INT NOT NULL AUTO_INCREMENT,
estado_insumo_descripcion VARCHAR(10),
PRIMARY KEY(ID_estado_insumo));

CREATE TABLE unidad_medida(ID_unidad_medida INT NOT NULL AUTO_INCREMENT,
unidad_medida_nombre VARCHAR (50) NOT NULL,
PRIMARY KEY (ID_unidad_medida));

CREATE TABLE categoria_insumos(ID_categoria_insumo INT NOT NULL AUTO_INCREMENT,
categoria_insumo_nombre VARCHAR (25) NOT NULL,
PRIMARY KEY (ID_categoria_insumo));

CREATE TABLE proveedor(ID_proveedor INT NOT NULL AUTO_INCREMENT,
proveedor_nombre VARCHAR (30) NOT NULL,
proveedor_rubro VARCHAR (20), 
proveedor_observaciones VARCHAR (100),
PRIMARY KEY (ID_proveedor));

CREATE TABLE insumos(ID_insumo INT NOT NULL AUTO_INCREMENT,
insumo_nombre VARCHAR (50)  NOT NULL,
insumo_stock_actual DECIMAL(10,2) NOT NULL DEFAULT 0,
insumo_stock_minimo DECIMAL(10,2) NOT NULL,
insumo_precio_costo FLOAT (10,2),
RELA_unidad_medida INT NOT NULL,
RELA_categoria_insumos INT NOT NULL,
RELA_proveedor INT NOT NULL, 
RELA_estado_insumo INT NOT NULL,
PRIMARY KEY (ID_insumo));

CREATE TABLE perfiles (ID_perfil INT NOT NULL AUTO_INCREMENT,
perfil_rol VARCHAR (50) NOT NULL,
PRIMARY KEY (ID_perfil));

CREATE TABLE modulos(ID_modulos INT NOT NULL AUTO_INCREMENT,
modulos_nombre VARCHAR (50) NOT NULL,
PRIMARY KEY (ID_modulos));

CREATE TABLE modulos_perfiles(ID_modulos_perfiles INT NOT NULL AUTO_INCREMENT,
RELA_perfil INT NOT NULL,
RELA_modulos INT NOT NULL,
PRIMARY KEY (ID_modulos_perfiles));


CREATE TABLE persona (ID_persona INT NOT NULL AUTO_INCREMENT,
persona_nombre VARCHAR (25) NOT NULL,
persona_apellido VARCHAR (25) NOT NULL,
persona_documento VARCHAR(20) NOT NULL,
persona_fecha_nacimiento DATE NOT NULL,
persona_direccion VARCHAR (200) NOT NULL,
PRIMARY KEY (ID_persona));

CREATE TABLE usuarios (ID_usuario INT NOT NULL AUTO_INCREMENT,
usuario_nombre VARCHAR (25) NOT NULL,
usuario_correo_electronico VARCHAR (50) NOT NULL,
usuario_contraseña VARCHAR (255) NOT NULL,
usuario_numero_de_celular VARCHAR (15) NULL,
usuario_fecha_de_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
RELA_persona INT NOT NULL,
RELA_perfil INT NOT NULL,
PRIMARY KEY (ID_usuario));

CREATE TABLE material_extra (ID_material_extra INT NOT NULL AUTO_INCREMENT,
material_extra_nombre VARCHAR(65), 
material_extra_descri  VARCHAR (125), 
material_extra_precio  FLOAT(10,2) NOT NULL DEFAULT 0,
RELA_estado_insumos INT NOT NULL, 
PRIMARY KEY (ID_material_extra));

ALTER TABLE material_extra
ADD COLUMN material_extra_color VARCHAR(100) NULL AFTER material_extra_descri;

CREATE TABLE pastel_material_extra (ID_pastel_material_extra INT NOT NULL AUTO_INCREMENT,
RELA_pastel_personalizado INT NOT NULL, 
RELA_material_extra INT NOT NULL, 
PRIMARY KEY (ID_pastel_material_extra));

CREATE TABLE estado (ID_estado INT NOT NULL AUTO_INCREMENT, 
estado_descri VARCHAR (12) NOT NULL, 
PRIMARY KEY (ID_estado));

CREATE TABLE metodo_pago (ID_metodo_pago INT NOT NULL AUTO_INCREMENT, 
metodo_pago_descri VARCHAR (25),
PRIMARY KEY (ID_metodo_pago));


CREATE TABLE pedido_envio (ID_pedido_envio INT NOT NULL AUTO_INCREMENT, 
envio_fecha_hora_entrega DATETIME NOT NULL, 
envio_calle_numero VARCHAR(150) NOT NULL, 
envio_piso INT NULL, 
envio_dpto VARCHAR(3) NULL,
envio_barrio VARCHAR(100) NOT NULL, 
envio_localidad VARCHAR(100) NOT NULL, 
envio_cp VARCHAR(15) NOT NULL, 
envio_provincia VARCHAR(15) NOT NULL, 
envio_telefono_contacto VARCHAR(55), 
envio_referencias VARCHAR(150) NULL, 
PRIMARY KEY (ID_pedido_envio)); 

CREATE TABLE pedido (ID_pedido INT NOT NULL AUTO_INCREMENT, 
pedido_fecha DATE, 
pedido_tipo_de_factura VARCHAR(10), 
RELA_pedido_envio INT NOT NULL, 
RELA_usuario INT NOT NULL, 
RELA_estado INT NOT NULL, 
RELA_metodo_pago INT NOT NULL, 
PRIMARY KEY (ID_pedido));

CREATE TABLE pedido_detalle (ID_pedido_detalle INT NOT NULL AUTO_INCREMENT, 
pedido_detalle_cantidad INT, 
pedido_detalle_precio_unitario FLOAT(10,2),
pedido_detalle_subtotal FLOAT (12,2), 
pedido_detalle_precio_total FLOAT(12,2),
RELA_pastel_personalizado INT NOT NULL, 
RELA_pedido INT NOT NULL, 
PRIMARY KEY (ID_pedido_detalle));

ALTER TABLE pedido 
ADD COLUMN pedido_total DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER RELA_pedido_envio;

ALTER TABLE pedido MODIFY RELA_metodo_pago INT NOT NULL DEFAULT 0;

CREATE TABLE tipo_de_operacion (ID_tipo_de_operacion INT NOT NULL AUTO_INCREMENT,
tipo_de_operacion_descripcion VARCHAR (255),
PRIMARY KEY (ID_tipo_de_operacion));

CREATE TABLE operacion (ID_operacion INT NOT NULL AUTO_INCREMENT,
operacion_cantidad_de_productos INT,
operacion_fecha_de_actualizacion DATETIME NOT NULL,
RELA_tipo_de_operacion INT NOT NULL,
RELA_insumos INT NOT NULL,
PRIMARY KEY (ID_operacion));

ALTER TABLE operacion
ADD COLUMN RELA_usuario INT NULL AFTER ID_operacion,
ADD COLUMN operacion_total FLOAT(10,2) NULL AFTER operacion_cantidad_de_productos;

ALTER TABLE operacion
ADD CONSTRAINT fk_operacion_usuario FOREIGN KEY (RELA_usuario) REFERENCES usuarios(ID_usuario);

CREATE TABLE operacion_pastel_personalizado (ID_operacion_pastel INT NOT NULL AUTO_INCREMENT,
RELA_operacion INT NOT NULL,
RELA_pastel_personalizado INT NOT NULL,
operacion_pastel_cantidad_utilizada DECIMAL (10,2),
PRIMARY KEY (ID_operacion_pastel));

CREATE TABLE recetas (ID_receta INT NOT NULL AUTO_INCREMENT,
receta_nombre VARCHAR (100),
receta_descripcion TEXT,
receta_rendimiento INT,
receta_unidad_rendimiento VARCHAR (50),
receta_estado TINYINT(1),
PRIMARY KEY(ID_receta));

CREATE TABLE receta_insumos (ID_receta_insumo INT NOT NULL AUTO_INCREMENT,
RELA_insumos INT NOT NULL,
RELA_receta INT NOT NULL,
receta_insumo_cantidad DECIMAL (10,2),
PRIMARY KEY (ID_receta_insumo));

CREATE TABLE categoria_producto_finalizado (ID_categoria_producto_finalizado INT NOT NULL AUTO_INCREMENT, 
categoria_producto_finalizado_nombre VARCHAR (65), 
PRIMARY KEY (ID_categoria_producto_finalizado));

CREATE TABLE producto_finalizado (
ID_producto_finalizado INT NOT NULL AUTO_INCREMENT, 
producto_finalizado_nombre VARCHAR(65) NOT NULL, 
producto_finalizado_descri VARCHAR(125),
producto_finalizado_precio DECIMAL(10,2) NOT NULL, 
disponible_web TINYINT(1) NOT NULL DEFAULT 0,
imagen_url VARCHAR(255) NULL,
stock_actual INT NOT NULL DEFAULT 0,
RELA_tematica INT NULL, 
RELA_categoria_producto_finalizado INT NOT NULL, 
PRIMARY KEY (ID_producto_finalizado));

ALTER TABLE producto_finalizado ALTER COLUMN RELA_categoria_producto_finalizado SET DEFAULT 1;

CREATE TABLE operacion_producto_finalizado (ID_operacion_producto_finalizado INT NOT NULL AUTO_INCREMENT, 
RELA_operacion INT NOT NULL, 
RELA_producto_finalizado INT NOT NULL, 
primary key(ID_operacion_producto_finalizado));

CREATE TABLE caja (
  ID_caja INT AUTO_INCREMENT PRIMARY KEY,
  RELA_usuario INT NOT NULL,
  caja_monto_inicial DECIMAL(10,2) NOT NULL,
  caja_fecha_apertura DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  caja_fecha_cierre DATETIME NULL,
  caja_estado ENUM('abierta', 'cerrada') DEFAULT 'abierta',
  caja_total_ingresos DECIMAL(10,2) DEFAULT 0,
  caja_total_egresos DECIMAL(10,2) DEFAULT 0,
  caja_saldo_final DECIMAL(10,2) DEFAULT 0
);

CREATE TABLE movimiento_caja (
  ID_movimiento INT AUTO_INCREMENT PRIMARY KEY,
  RELA_caja INT NOT NULL,
  RELA_usuario INT NOT NULL,
  movimiento_tipo ENUM('ingreso', 'egreso') NOT NULL,
  movimiento_monto DECIMAL(10,2) NOT NULL,
  movimiento_descripcion VARCHAR(255),
  movimiento_fecha DATETIME DEFAULT CURRENT_TIMESTAMP
);

create table estado_factura (ID_estado_factura INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
estado_factura_descri VARCHAR(10));

INSERT INTO estado_factura (estado_factura_descri) VALUES ('Activo'), ('No Activo');

CREATE TABLE factura (
ID_factura INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
factura_fecha_emision timestamp not null DEFAULT CURRENT_TIMESTAMP, 
factura_subtotal DECIMAL(10,2), 
factura_iva_tasa DECIMAL(5,2),
factura_iva_monto DECIMAL(10,2), 
factura_total DECIMAL(10,2), 
RELA_estado_factura INT NULL,
RELA_persona INT NOT NULL);

CREATE TABLE factura_detalle (
ID_factura_detalle INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
RELA_factura INT,  
RELA_producto_finalizado INT,
factura_detalle_cantidad INT);

drop table factura_pagos;
CREATE TABLE factura_pagos (
  ID_pago INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  RELA_factura INT NOT NULL,
  RELA_metodo_pago INT NOT NULL,
  pago_interes DECIMAL(5,2) DEFAULT 0,
  pago_monto DECIMAL(10,2) DEFAULT 0,
  FOREIGN KEY (RELA_factura) REFERENCES factura(ID_factura)
);
select * from metodo_pago;

INSERT INTO estado_decoraciones (estado_decoraciones_descri)
VALUES ('Activo'), ('Inactivo');

INSERT INTO estado_insumos (estado_insumo_descripcion)
VALUES ('Activo'), ('Baja');

INSERT INTO tamaño (tamaño_nombre, tamaño_medidas, tamaño_precio)
VALUES ('Pequeño', '10x10', '7000.00'), ('Mediano', '20x20', '14000.00'), ('Grande', '30x30', '21000.00');

INSERT INTO sabor (sabor_nombre, sabor_descripcion, sabor_precio, RELA_estado_decoraciones)
VALUES ('Chocolate', 'Sabor intenso a cacao', '1500.00', 1),
       ('Vainilla', 'Clásico sabor dulce', '1000.0', 1);

INSERT INTO relleno (relleno_nombre, relleno_descripcion, relleno_precio, RELA_estado_decoraciones)
VALUES ('Dulce de leche', 'Relleno argentino', '3000.00', 1),
       ('Crema pastelera', 'Suave y cremosa', '200000', 1);
       
INSERT INTO tematica (tematica_descripcion, RELA_estado_decoraciones) VALUES ('Boda', 1), ('Infantil',1), ('Recepciones', 1);

INSERT INTO color_pastel (color_pastel_nombre, color_pastel_codigo, RELA_estado_decoraciones)
VALUES  ('Rosa', '#FFC0CB', 1),
('Celeste', '#87CEEB', 1),
('Verde menta', '#98FF98', 1),
('Lavanda', '#E6E6FA', 1),
('Amarillo pastel', '#FFFACD', 1);


INSERT INTO decoracion (decoracion_nombre, decoracion_descripcion, decoracion_precio, RELA_estado_decoraciones)
VALUES ('Plano', 'Diseño liso de un solo color', '7000.00', 1),
('Duyas', 'Diseño de duyas de distinto tamaño', '7000.00', 1),
('Aerografo', 'Diseño de aerogrado mezclando distintas tonalidades', '7000.00', 1),
('Perlas', 'Diseño de perlas pequeñas de rodean todo el pastel', '7000.00', 1);

INSERT INTO base_pastel (base_pastel_nombre, base_pastel_descripcion, base_pastel_precio, RELA_estado_decoraciones)
VALUES ('Cartón duro', 'Base circular resistente', '1500.00', 1);

INSERT INTO pastel_personalizado (pastel_personalizado_descripcion, pastel_personalizado_pisos_total, RELA_color_pastel, RELA_decoracion, RELA_base_pastel)
VALUES ('Pastel de prueba con 2 pisos', 2, 1, 1, 1);

-- Piso 1: Mediano
INSERT INTO pisos (pisos_numero, RELA_pastel_personalizado, RELA_tamaño)
VALUES (1, 1, 2);

-- Piso 2: Grande
INSERT INTO pisos (pisos_numero, RELA_pastel_personalizado, RELA_tamaño)
VALUES (2, 1, 3);

-- Asociar sabores a los pisos
INSERT INTO pisos_sabor (RELA_sabor, RELA_pisos)
VALUES (1, 1), -- Chocolate al piso 1
       (2, 2); -- Vainilla al piso 2

-- Asociar rellenos a los pisos
INSERT INTO pisos_relleno (RELA_relleno, RELA_pisos)
VALUES (1, 1), -- Dulce de leche al piso 1
       (2, 2); -- Crema pastelera al piso 2

INSERT INTO unidad_medida (unidad_medida_nombre) VALUES('Kilogramos'),('Litros'),('Unidad'),('Gramos'),('Mililitros');

INSERT INTO categoria_insumos (categoria_insumo_nombre)
VALUES ('Lácteos'), ('Decoración'), ('Harinas/Cereales'), ('Azúcares/Endulzantes'), ('Huevos'), ('Frutas/Frutos secos'), ('Decoración comestible'), ('Bebidas y esencias'), ('Utensilios'), ('Embalajes');

INSERT INTO proveedor (proveedor_nombre, proveedor_rubro, proveedor_observaciones)
VALUES ('La Vaquita', 'Alimentos', 'Proveedor confiable');

INSERT INTO insumos 
(insumo_nombre, insumo_stock_minimo, RELA_unidad_medida, RELA_categoria_insumos, RELA_proveedor, RELA_estado_insumo)
VALUES 
('Crema', 2, 4, 1, 1,1),
('Flores comestibles', 10, 3, 2, 2,1),
('Harina', 5, 1, 3, 3,1),
('Azúcar', 3, 1, 4, 1,1);


INSERT INTO perfiles (perfil_rol)
VALUES ('Administrador'), ('Empleado'), ('Cliente'), ('Gerente');

INSERT INTO persona (persona_nombre, persona_apellido, persona_documento, persona_fecha_nacimiento, persona_direccion)
VALUES ('Gastón', 'Gómez', '44222333', '2000-01-29', 'Calle falsa 123');

INSERT INTO usuarios (usuario_nombre, usuario_correo_electronico, usuario_contraseña, usuario_numero_de_celular, RELA_persona, RELA_perfil)
VALUES ('gaston_user', 'gaston@mail.com', 'clave_segura', '1123456789', 1, 1);

insert into estado (estado_descri) values 
('Pendiente'), ('En proceso'), ('Finalizado'), ('Cancelado');

INSERT INTO pedido_detalle (pedido_detalle_cantidad, pedido_detalle_precio_unitario, pedido_detalle_subtotal, pedido_detalle_precio_total, RELA_pastel_personalizado, RELA_pedido)
VALUES (1, 350.50, 350.50, 420.60, 10, 1);

INSERT INTO estado_decoraciones (estado_decoraciones_descri) VALUES
('Disponible'), ('No Disponible');


INSERT INTO material_extra (material_extra_nombre, material_extra_descri, material_extra_precio, RELA_estado_insumos)
VALUES
('Flores', 'Flor individual', '1500.00', 1), 
('Flores', 'Flores de diferentes tamaño', '3000.00', 1);

INSERT INTO tipo_de_operacion (tipo_de_operacion_descripcion)
VALUES 
('Ingreso de stock'),
('Egreso de stock');


INSERT INTO recetas (receta_nombre, receta_descripcion, receta_rendimiento, receta_unidad_rendimiento, receta_estado)
VALUES
('Pastel Chocolate', 'Pastel de chocolate clásico', 1, 'unidad', 1),
('Pastel Vainilla', 'Pastel de vainilla', 1, 'unidad', 1);


-- Pastel Chocolate (receta_id = 1)
INSERT INTO receta_insumos (RELA_insumos, RELA_receta, receta_insumo_cantidad)
VALUES
(1, 1, 200), -- Harina 200 g
(2, 1, 100), -- Azúcar 100 g
(3, 1, 50);  -- Cacao 50 g

-- Pastel Vainilla (receta_id = 2)
INSERT INTO receta_insumos (RELA_insumos, RELA_receta, receta_insumo_cantidad)
VALUES
(1, 2, 200), -- Harina 200 g
(2, 2, 100), -- Azúcar 100 g
(4, 2, 50);  -- Vainilla 50 g

INSERT INTO producto_finalizado 
(producto_finalizado_nombre, producto_finalizado_descri, producto_finalizado_precio, 
disponible_web, imagen_url, stock_actual, RELA_tematica, RELA_categoria_producto_finalizado)
VALUES
('Torta de Chocolate' , 'Torta de 10 x 10 cm sabor chocolate' , '12000.00', '1', 'ruta/por/defecto.jpg', 
'4', 1, 1);

INSERT INTO metodo_pago (metodo_pago_descri) VALUES
('Efectivo'), ('Transferencia');