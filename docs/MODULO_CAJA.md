# Módulo de Caja - Sistema Cake Party

## 📋 Descripción General

El módulo de caja es un sistema completo de gestión de caja registradora que se integra con el sistema de pedidos de Cake Party. Permite a los empleados, administradores y gerentes gestionar el flujo de dinero de manera eficiente y controlada.

## 🎯 Funcionalidades Principales

### 1. Apertura de Caja
- **Acceso**: Empleado, Admin, Gerente
- **Funcionalidad**: Permite abrir una nueva caja con monto inicial
- **Validaciones**: 
  - Solo una caja abierta por usuario
  - Monto inicial obligatorio y mayor a 0
- **Archivo**: `controllers/caja/apertura_caja.php`

### 2. Cierre de Caja
- **Acceso**: Empleado, Admin, Gerente
- **Funcionalidad**: Cierra la caja y calcula totales automáticamente
- **Cálculos**:
  - Total de ingresos
  - Total de egresos
  - Saldo final (monto inicial + ingresos - egresos)
- **Archivo**: `controllers/caja/cierre_caja.php`

### 3. Registro de Egresos
- **Acceso**: Empleado, Admin, Gerente
- **Funcionalidad**: Registra gastos menores manualmente
- **Campos**: Monto, descripción obligatoria
- **Archivo**: `controllers/caja/registrar_egreso.php`

### 4. Arqueo de Caja
- **Acceso**: Empleado, Admin, Gerente
- **Funcionalidad**: Consulta el estado actual de la caja abierta
- **Información mostrada**:
  - Monto inicial
  - Total de ingresos y egresos
  - Saldo actual
  - Historial de movimientos
- **Archivo**: `controllers/caja/arqueo_caja.php`

### 5. Historial de Cajas
- **Acceso**: Empleado, Admin, Gerente
- **Funcionalidad**: Consulta el historial de cajas del usuario
- **Filtros**: Por fecha, estado
- **Archivo**: `views/caja/historial_cajas.php`

### 6. Todas las Cajas (Admin/Gerente)
- **Acceso**: Solo Admin y Gerente
- **Funcionalidad**: Vista administrativa de todas las cajas del sistema
- **Filtros**: Por fecha, estado, usuario
- **Archivo**: `views/caja/todas_las_cajas.php`

### 7. Procesamiento de Pedidos
- **Acceso**: Solo Admin y Gerente
- **Funcionalidad**: Integración automática con sistema de pedidos
- **Proceso**: Registra ingresos automáticamente cuando se finaliza un pedido
- **Archivo**: `controllers/caja/procesar_pedidos.php`

## 🗄️ Estructura de Base de Datos

### Tabla: `caja`
```sql
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
```

### Tabla: `movimiento_caja`
```sql
CREATE TABLE movimiento_caja (
  ID_movimiento INT AUTO_INCREMENT PRIMARY KEY,
  RELA_caja INT NOT NULL,
  RELA_usuario INT NOT NULL,
  movimiento_tipo ENUM('ingreso', 'egreso') NOT NULL,
  movimiento_monto DECIMAL(10,2) NOT NULL,
  movimiento_descripcion VARCHAR(255),
  movimiento_fecha DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

## 🔧 Archivos del Sistema

### Controladores
- `controllers/caja/CajaController.php` - Controlador principal
- `controllers/caja/apertura_caja.php` - Apertura de caja
- `controllers/caja/cierre_caja.php` - Cierre de caja
- `controllers/caja/registrar_egreso.php` - Registro de egresos
- `controllers/caja/arqueo_caja.php` - Arqueo de caja
- `controllers/caja/procesar_pedidos.php` - Procesamiento de pedidos
- `controllers/caja/IntegracionPedidosCaja.php` - Integración con pedidos

### Vistas
- `views/caja/dashboard_caja.php` - Dashboard principal
- `views/caja/historial_cajas.php` - Historial de cajas
- `views/caja/todas_las_cajas.php` - Vista administrativa

### Estilos
- `public/css/caja_dashboard.css` - Estilos específicos del módulo

## 🎨 Diseño y Estilo

### Colores Principales
- **Primario**: `#e91e63` (Rosa/Magenta)
- **Secundario**: `#f06292` (Rosa claro)
- **Fondo**: `#ffe6ef` (Rosa muy claro)
- **Éxito**: `#4caf50` (Verde)
- **Peligro**: `#f44336` (Rojo)
- **Advertencia**: `#ff9800` (Naranja)
- **Info**: `#2196f3` (Azul)

### Características del Diseño
- Diseño responsivo con sidebar colapsable
- Tarjetas con sombras y efectos hover
- Gradientes en headers y elementos importantes
- Animaciones suaves y transiciones
- Iconos emoji para mejor UX
- Colores consistentes con el sistema Cake Party

## 🔐 Sistema de Permisos

### Empleado (ID: 2)
- ✅ Abrir su propia caja
- ✅ Cerrar su propia caja
- ✅ Registrar egresos en su caja
- ✅ Ver arqueo de su caja
- ✅ Ver historial de sus cajas
- ❌ Ver cajas de otros usuarios

### Administrador (ID: 1)
- ✅ Todas las funciones del empleado
- ✅ Ver todas las cajas del sistema
- ✅ Procesar pedidos pendientes
- ✅ Acceso completo al sistema

### Gerente (ID: 4)
- ✅ Todas las funciones del empleado
- ✅ Ver todas las cajas del sistema
- ✅ Procesar pedidos pendientes
- ✅ Acceso completo al sistema

### Cliente (ID: 3)
- ❌ Sin acceso al módulo de caja

## 🔄 Integración con Pedidos

### Proceso Automático
1. Cuando un pedido cambia a estado "Finalizado"
2. El sistema busca la caja abierta del usuario que procesó el pedido
3. Si existe caja abierta, registra automáticamente el ingreso
4. Si no existe caja abierta, genera error para procesamiento manual

### Procesamiento Manual
- Los administradores pueden procesar pedidos pendientes manualmente
- Se muestran estadísticas de ventas del día
- Se evita duplicar ingresos del mismo pedido

## 📊 Funcionalidades de Reportes

### Dashboard Principal
- Estadísticas en tiempo real
- Estado de caja actual
- Acciones rápidas disponibles
- Historial reciente

### Arqueo de Caja
- Resumen completo de la caja
- Historial detallado de movimientos
- Cálculos automáticos
- Opción de impresión

### Historial y Filtros
- Filtros por fecha y estado
- Búsqueda por usuario (admin/gerente)
- Estadísticas generales
- Exportación de datos

## 🚀 Instalación y Configuración

### 1. Base de Datos
Las tablas ya están creadas en el archivo `cake_party.sql` (líneas 231-251).

### 2. Permisos de Archivos
```bash
chmod 755 controllers/caja/
chmod 755 views/caja/
```

### 3. Configuración de Sesiones
Asegúrate de que las sesiones estén configuradas correctamente en PHP.

### 4. Integración con Navegación
Agregar enlaces al módulo de caja en el menú principal del sistema.

## 🔧 Mantenimiento

### Limpieza de Datos
- Los movimientos se mantienen para auditoría
- Las cajas cerradas conservan todos los datos
- No se eliminan registros automáticamente

### Respaldo
- Respaldar regularmente las tablas `caja` y `movimiento_caja`
- Mantener logs de auditoría de cambios importantes

### Monitoreo
- Verificar regularmente cajas abiertas sin cerrar
- Revisar movimientos con montos inusuales
- Monitorear integración con pedidos

## 🐛 Solución de Problemas

### Problemas Comunes

1. **Error: "Ya tienes una caja abierta"**
   - Solución: Cerrar la caja existente antes de abrir una nueva

2. **Error: "No hay caja abierta para registrar la venta"**
   - Solución: El usuario debe abrir una caja antes de procesar pedidos

3. **Problemas de permisos**
   - Verificar que el usuario tenga el perfil correcto (1, 2, o 4)

4. **Errores de conexión a BD**
   - Verificar configuración en `config/conexion.php`

### Logs y Debugging
- Revisar logs de PHP para errores
- Verificar transacciones de base de datos
- Comprobar permisos de archivos

## 📈 Futuras Mejoras

### Funcionalidades Propuestas
- Reportes avanzados con gráficos
- Integración con sistema de facturación
- Notificaciones automáticas
- Backup automático de datos
- API REST para integraciones externas
- Dashboard móvil optimizado

### Optimizaciones
- Cache de consultas frecuentes
- Paginación en listados grandes
- Compresión de datos históricos
- Indexación optimizada en BD

---

**Desarrollado para Cake Party** 🎂  
**Versión**: 1.0  
**Fecha**: 2024  
**Autor**: Sistema de Gestión Cake Party
