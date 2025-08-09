# Sistema de Gestión para Licorería "El Manguito C.A"

## 📋 Descripción del Proyecto

Sistema web completo para la gestión de una licorería que incluye control de inventario, ventas, clientes, proveedores, reportes y más. Desarrollado con arquitectura MVC en PHP.

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 7.4+** - Lenguaje de programación principal
- **MySQL** - Base de datos relacional
- **PDO** - Capa de abstracción de base de datos para PHP
- **Composer** - Gestor de dependencias de PHP

### Frontend
- **HTML5** - Estructura de las páginas web
- **CSS3** - Estilos y diseño responsivo
- **JavaScript (ES6+)** - Interactividad del lado del cliente
- **Bootstrap 3.2** - Framework CSS para diseño responsivo

### Librerías CSS
- **Bootstrap CSS** - Framework principal de estilos
- **Font Awesome** - Iconografía
- **Zabuto Calendar CSS** - Estilos para calendario
- **jQuery Gritter CSS** - Notificaciones estilo toast
- **LineIcons** - Iconos adicionales
- **Select2 CSS** - Estilos para selectores avanzados

### Librerías JavaScript

#### Core Libraries
- **jQuery 3.6.0** - Biblioteca JavaScript principal
- **Bootstrap JS 3.2** - Componentes interactivos de Bootstrap

#### UI/UX Libraries
- **SweetAlert2** - Alertas y modales elegantes para mensajes al usuario
- **DataTables 1.11.5** - Tablas interactivas con paginación, búsqueda y ordenamiento
- **Select2 4.1.0** - Selectores avanzados con búsqueda
- **jQuery Gritter** - Sistema de notificaciones tipo toast
- **jQuery NiceScroll** - Barras de desplazamiento personalizadas
- **jQuery Sparkline** - Gráficos pequeños en línea

#### Charts & Visualization
- **Chart.js** - Gráficos interactivos (barras, líneas, donut, etc.)
- **Morris Charts** - Gráficos adicionales
- **Easy Pie Chart** - Gráficos circulares de progreso

#### Calendar & Date
- **Zabuto Calendar** - Componente de calendario
- **FullCalendar** - Calendario completo con eventos

#### Form & Input
- **Bootstrap InputMask** - Máscaras de entrada para formularios
- **Bootstrap Switch** - Interruptores toggle
- **jQuery Tags Input** - Entrada de etiquetas

#### Utilities
- **jQuery ScrollTo** - Navegación suave por scroll
- **jQuery Backstretch** - Imágenes de fondo responsivas
- **FancyBox** - Lightbox para imágenes y contenido modal

### Dependencias PHP (Composer)
- **dompdf/dompdf ^3.1** - Generación de PDFs desde HTML
- **tecnickcom/tcpdf ^6.10** - Biblioteca alternativa para PDFs
- **setasign/fpdf ^1.8** - Generación de PDFs ligera

### Funcionalidades Principales

#### Gestión de Inventario
- Control de stock en tiempo real
- Alertas de stock bajo
- Movimientos de inventario (entradas, salidas, ajustes)
- Gestión de productos y categorías

#### Sistema de Ventas
- Registro de ventas con múltiples productos
- Generación automática de facturas en PDF
- Control de clientes y historial de compras
- Descarga automática de facturas

#### Reportes y Analytics
- Gráficos interactivos con Chart.js
- Reportes de inventario
- Análisis de ventas por período
- Productos más vendidos
- Dashboard con métricas clave

#### Gestión de Usuarios
- Sistema de autenticación
- Roles de usuario (Administrador, Empleado)
- Control de acceso por funcionalidades

#### Búsquedas Dinámicas
- **AJAX** - Búsquedas en tiempo real sin recargar página
- **Fetch API** - Peticiones asíncronas modernas
- Filtros dinámicos en todas las tablas
- Autocompletado en formularios

### Características Técnicas

#### Arquitectura
- **Patrón MVC** - Separación clara de responsabilidades
- **Controladores** - Lógica de negocio
- **Modelos** - Acceso a datos
- **Vistas** - Presentación

#### Base de Datos
- Diseño relacional normalizado
- Triggers para control de stock
- Respaldos automáticos
- Integridad referencial

#### Seguridad
- Validación de datos de entrada
- Protección contra SQL Injection (PDO)
- Control de sesiones
- Validación de permisos por rol

#### UI/UX
- Diseño responsivo con Bootstrap
- Notificaciones elegantes con SweetAlert2
- Tablas interactivas con DataTables
- Gráficos dinámicos con Chart.js
- Interfaz intuitiva y moderna

### Estructura del Proyecto

```
licoreria/
├── assets/                 # Recursos estáticos
│   ├── css/               # Hojas de estilo
│   ├── js/                # Scripts JavaScript
│   ├── img/               # Imágenes
│   └── fonts/             # Fuentes
├── config/                # Configuración
├── controllers/           # Controladores MVC
├── models/               # Modelos de datos
├── views/                # Vistas/Templates
├── helpers/              # Funciones auxiliares
├── lib/                  # Librerías locales
├── vendor/               # Dependencias Composer
└── facturas/             # PDFs generados
```

### Instalación y Configuración

1. **Requisitos del servidor:**
   - PHP 7.4+
   - MySQL 5.7+
   - Apache/Nginx
   - Composer

2. **Instalación:**
   ```bash
   composer install
   ```

3. **Configuración:**
   - Configurar base de datos en `config/database.php`
   - Importar `licoreria.sql`
   - Configurar URLs en `config/url_config.php`

### Características Destacadas

- ✅ **Interfaz moderna** con Bootstrap y componentes personalizados
- ✅ **Búsquedas en tiempo real** con AJAX
- ✅ **Notificaciones elegantes** con SweetAlert2
- ✅ **Tablas interactivas** con DataTables
- ✅ **Gráficos dinámicos** con Chart.js
- ✅ **Generación de PDFs** automática
- ✅ **Sistema de respaldos** integrado
- ✅ **Control de stock** en tiempo real
- ✅ **Diseño responsivo** para móviles
- ✅ **Arquitectura escalable** MVC

---

*Desarrollado para la gestión eficiente de licorería con tecnologías web modernas y mejores prácticas de desarrollo.*