# Documentació del projecte web

## Pàgines del lloc web

## `includes/config.php`

Aquest arxiu estableix la configuració base del projecte web USOLUTIONS. Defineix les constants necessàries per connectar-se a la base de dades i gestionar paràmetres globals del lloc web, així com una llista de serveis disponibles utilitzada a `home.php`.

---

### 🔌 Connexió a la base de dades

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'usolutions_admin');
define('DB_PASS', 'P@ssw0rd');
define('DB_NAME', 'usolutions_db');
```

Aquestes constants defineixen les credencials i la informació de connexió per accedir a la base de dades MySQL que utilitza el projecte.

- `DB_HOST`: Servidor on es troba la base de dades (habitualment `localhost` en entorns locals).
- `DB_USER`: Nom d’usuari per connectar-se a la base de dades.
- `DB_PASS`: Contrasenya de l’usuari de la base de dades.
- `DB_NAME`: Nom de la base de dades que conté les taules del sistema.

---

### 🌐 Configuració general del lloc

```php
define('SITE_NAME', 'USOLUTIONS');
define('SITE_URL', 'http://localhost/');
```

Són constants globals que defineixen:

- `SITE_NAME`: El nom comercial del projecte web.
- `SITE_URL`: La URL base des d’on s’executa el lloc (útil per generar enllaços absoluts o redireccions).

---

### 🧰 Definició de serveis (utilitzada a `home.php`)

```php
define('SERVICES', [
    'web-hosting' => [
        'name' => 'Web Hosting',
        'description' => 'Fast and reliable web hosting for your websites',
        'icon' => 'fas fa-globe'
    ],
    'managed-servers' => [
        'name' => 'Managed Servers',
        'description' => 'Fully managed WordPress, PrestaShop, and NextCloud servers',
        'icon' => 'fas fa-server'
    ],
    'unmanaged-servers' => [
        'name' => 'Unmanaged Servers',
        'description' => 'Unmanaged Debian and Ubuntu servers with root access',
        'icon' => 'fas fa-terminal'
    ]
]);
```

Aquesta constant és una associació de serveis predefinits que es mostren visualment a la pàgina principal (`home.php`). Cada servei inclou:

- `name`: Nom del servei.
- `description`: Descripció curta.
- `icon`: Icona de FontAwesome per representar-lo visualment.

---

### 🔄 Inicialització de la connexió

```php
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
```

S’inicialitza una connexió MySQL mitjançant l’objecte `mysqli`, que serà reutilitzat a tot el projecte per accedir a la base de dades.

---

### 🚨 Verificació de la connexió

```php
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
```

En cas que la connexió falli, el sistema mostrarà un missatge d’error i detindrà l’execució del script.
