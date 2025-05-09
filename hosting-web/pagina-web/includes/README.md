# includes/

Fitxers PHP que es carreguen a diverses pàgines per evitar duplicar codi.

## Contingut típic

- **config.php**  
  Configuració de la base de dades i constants globals.

- **header.php / footer.php**  
  Capçalera i peu de pàgina comuns.

- **functions.php**  
  Funcions reutilitzables (sanitització, validació, helpers…).

### Ús

A cada pàgina PHP principal inclou:

```php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
include __DIR__ . '/header.php';
include __DIR__ . '/footer.php';
