# 📦 Sistema de Control de Stock - Cake Party

## 🎯 Descripción General

El sistema de control de stock permite gestionar automáticamente el inventario de insumos utilizados en la producción de pasteles personalizados. Se integra completamente con el sistema de pedidos existente.

## 🏗️ Arquitectura del Sistema

### Tablas Principales

1. **`insumos`** - Almacena información de cada insumo
   - `insumo_stock_actual` - Stock disponible (se actualiza automáticamente)
   - `insumo_stock_minimo` - Stock mínimo para alertas
   - `insumo_precio_costo` - Costo del insumo
   - `insumo_unidad_medida` - Unidad de medida (kg, litros, unidad, etc.)

2. **`operacion`** - Registra cada movimiento de stock
   - `operacion_cantidad_de_productos` - Cantidad movida
   - `operacion_fecha_de_actualizacion` - Fecha del movimiento
   - `RELA_tipo_de_operacion` - Tipo (1=Ingreso, 2=Egreso)

3. **`tipo_de_operacion`** - Define tipos de operaciones
   - "Ingreso de stock"
   - "Egreso de stock"

4. **`recetas`** y **`receta_insumos`** - Define qué insumos necesita cada pastel

## 🔄 Flujo de Funcionamiento

### 1. Ingreso de Stock
- Se registra manualmente desde la interfaz web
- Se suma automáticamente al `insumo_stock_actual`
- Se registra en la tabla `operacion` como tipo "Ingreso"

### 2. Egreso de Stock (Automático)
- Se ejecuta automáticamente cuando un pedido cambia a estado "En proceso" o "Finalizado"
- Se descuenta según la receta del pastel
- Se registra en la tabla `operacion` como tipo "Egreso"

### 3. Control de Estados
```sql
CASE
    WHEN insumo_stock_actual = 0 THEN 'Sin stock'
    WHEN insumo_stock_actual <= insumo_stock_minimo THEN 'Bajo stock'
    ELSE 'Stock normal'
END AS estado_stock
```

## 📁 Archivos del Sistema

### Controladores
- `controllers/stock/StockController.php` - Clase principal con toda la lógica
- `controllers/stock/ingreso_stock.php` - Interfaz para ingresar stock manualmente
- `controllers/stock/movimientos_stock.php` - Ver movimientos por fecha
- `controllers/stock/alertas_stock.php` - Ver alertas de stock bajo
- `controllers/stock/historial_insumo.php` - Ver historial de un insumo específico

### Vistas
- `views/stock/gestion_stock.php` - Dashboard principal de stock

### Integración
- `controllers/admin/estado_pedido.php` - Modificado para descontar stock automáticamente

## 🚀 Cómo Usar el Sistema

### 1. Acceso al Sistema
- Solo administradores y gerentes pueden acceder
- Enlaces disponibles en el dashboard de administrador

### 2. Gestión de Stock
1. **Ver estado general**: `views/stock/gestion_stock.php`
2. **Ingresar stock**: `controllers/stock/ingreso_stock.php`
3. **Ver alertas**: `controllers/stock/alertas_stock.php`
4. **Ver movimientos**: `controllers/stock/movimientos_stock.php`

### 3. Flujo de Trabajo Típico
1. **Configurar insumos**: Definir insumos con stock mínimo
2. **Ingresar stock inicial**: Registrar compras de insumos
3. **Procesar pedidos**: El sistema descuenta automáticamente
4. **Monitorear alertas**: Revisar stock bajo y sin stock
5. **Reponer stock**: Ingresar nuevos insumos cuando sea necesario

## ⚙️ Configuración Inicial

### 1. Datos de Prueba
El sistema incluye datos de ejemplo:
- Insumos: Crema, Flores comestibles, Harina, Azúcar
- Recetas: Pastel Chocolate, Pastel Vainilla
- Tipos de operación: Ingreso, Egreso

### 2. Personalización
- Agregar más categorías de insumos
- Crear nuevas recetas con sus ingredientes
- Ajustar stock mínimo según necesidades

## 🔧 Funciones Principales

### StockController
```php
// Ingresar stock
$stockController->registrarIngreso($insumo_id, $cantidad, $observaciones);

// Descontar stock por pedido
$stockController->descontarStockPorPedido($pedido_id);

// Obtener estado de stock
$stockController->obtenerEstadoStock($insumo_id);

// Obtener alertas
$stockController->obtenerInsumosStockBajo();
```

## 📊 Reportes Disponibles

1. **Dashboard General**: Estadísticas de stock por estado
2. **Alertas**: Lista de insumos con stock bajo o sin stock
3. **Movimientos**: Historial de ingresos y egresos por fecha
4. **Historial por Insumo**: Movimientos detallados de un insumo específico

## 🎨 Características de la Interfaz

- **Responsive**: Funciona en desktop y móvil
- **Colores consistentes**: Usa la paleta de colores de Cake Party
- **Alertas visuales**: Indicadores de estado con colores
- **Navegación intuitiva**: Enlaces claros entre secciones

## 🔒 Seguridad

- Verificación de permisos en cada controlador
- Validación de datos de entrada
- Transacciones de base de datos para consistencia
- Manejo de errores con mensajes informativos

## 🐛 Resolución de Problemas

### Stock no se descuenta
1. Verificar que el pedido tenga estado "En proceso" o "Finalizado"
2. Revisar que existan recetas configuradas
3. Verificar que los insumos de la receta estén disponibles

### Alertas no aparecen
1. Verificar que el stock actual sea menor o igual al stock mínimo
2. Revisar que los insumos estén activos

### Error en transacciones
1. Verificar conexión a base de datos
2. Revisar logs de errores del servidor
3. Verificar permisos de usuario

## 📈 Próximas Mejoras

- [ ] Alertas por email
- [ ] Reportes en PDF
- [ ] Integración con sistema de compras
- [ ] Predicción de demanda
- [ ] API REST para integraciones externas
