# Configuración de Base de Datos - Cake Party

## Archivos de Configuración

### 1. `database.php`
Contiene la configuración de la base de datos según el entorno (desarrollo/producción).

**Configuración para desarrollo:**
- Host: localhost
- Base de datos: cake_party
- Usuario: root
- Contraseña: gtasanandreas1

**Configuración para producción:**
- Host: localhost
- Base de datos: cake_party_prod
- Usuario: cake_user
- Contraseña: secure_password_here

### 2. `conexion.php`
Clase singleton para manejar la conexión PDO de manera segura.

### 3. `DatabaseHelper.php`
Clase helper con métodos para consultas seguras y validaciones.

## Uso

### Conexión básica:
```php
require_once '../config/conexion.php';

// Obtener conexión PDO
$pdo = getConexion();

// O usar la variable global (compatibilidad)
global $conexion;
```

### Usando DatabaseHelper:
```php
require_once '../config/DatabaseHelper.php';

$db = new DatabaseHelper();

// Consulta SELECT
$usuarios = $db->select("SELECT * FROM usuario WHERE activo = ?", [1]);

// Consulta INSERT
$id = $db->insert("INSERT INTO usuario (nombre, email) VALUES (?, ?)", 
                  ['Juan', 'juan@email.com']);

// Consulta UPDATE
$success = $db->update("UPDATE usuario SET nombre = ? WHERE id = ?", 
                       ['Pedro', 1]);

// Transacciones
$db->beginTransaction();
try {
    $db->insert("INSERT INTO tabla1 (campo) VALUES (?)", ['valor1']);
    $db->insert("INSERT INTO tabla2 (campo) VALUES (?)", ['valor2']);
    $db->commit();
} catch (Exception $e) {
    $db->rollback();
    throw $e;
}
```

## Ventajas de la nueva configuración:

1. **Seguridad**: Uso de PDO con prepared statements
2. **Flexibilidad**: Configuración por entorno
3. **Mantenibilidad**: Código centralizado y documentado
4. **Compatibilidad**: Mantiene compatibilidad con código existente
5. **Validación**: Métodos de validación incluidos
6. **Manejo de errores**: Errores manejados según el entorno

## Migración del código existente:

El código existente seguirá funcionando sin cambios, pero se recomienda migrar gradualmente a usar `DatabaseHelper` para mayor seguridad. 