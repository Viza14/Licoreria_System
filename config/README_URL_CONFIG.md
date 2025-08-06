# Configuración de URL Base

## ¿Cómo cambiar la URL de tu aplicación?

### 1. Archivo de configuración
Edita el archivo `config/url_config.php` para configurar tu URL base.

### 2. Opciones de configuración

#### Opción A: Detección automática (por defecto)
Si no defines nada manualmente, la aplicación detecta automáticamente:
- `http://localhost:8000/` (servidor PHP integrado)
- `http://localhost/licoreria/` (XAMPP/WAMP)

#### Opción B: Configuración manual
Modifica la variable `$MANUAL_BASE_URL` en `config/url_config.php`:

```php
// Para dominio propio
$MANUAL_BASE_URL = 'https://midominio.com/';

// Para subdirectorio
$MANUAL_BASE_URL = 'https://midominio.com/licoreria/';

// Para IP en red local
$MANUAL_BASE_URL = 'http://192.168.1.100/licoreria/';

// Para subdominio
$MANUAL_BASE_URL = 'https://licoreria.midominio.com/';
```

### 3. Ejemplos prácticos

#### Desarrollo local con XAMPP:
```php
$MANUAL_BASE_URL = null; // Detección automática
// URL resultante: http://localhost/licoreria/
```

#### Servidor en red local:
```php
$MANUAL_BASE_URL = 'http://192.168.1.100/licoreria/';
```

#### Hosting compartido:
```php
$MANUAL_BASE_URL = 'https://tudominio.com/public_html/licoreria/';
```

#### Dominio propio:
```php
$MANUAL_BASE_URL = 'https://www.tudominio.com/';
```

#### Subdominio:
```php
$MANUAL_BASE_URL = 'https://licoreria.tudominio.com/';
```

### 4. Pasos para cambiar la URL

1. Abre `config/url_config.php`
2. Busca la variable `$MANUAL_BASE_URL`
3. Cambia `null` por tu URL específica
4. Guarda el archivo

### 5. Verificación
Después de cambiar la configuración, verifica que:
- Los enlaces funcionen correctamente
- Las imágenes y CSS se carguen
- Los formularios redirijan bien

### 6. Troubleshooting

**Problema**: Los enlaces no funcionan
**Solución**: Verifica que la URL termine en `/`

**Problema**: CSS/JS no cargan
**Solución**: Revisa que la ruta sea correcta y accesible

**Problema**: Formularios no redirigen
**Solución**: Asegúrate de que la URL base sea la correcta