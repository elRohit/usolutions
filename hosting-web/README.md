# Hosting Web

## Pàgina Web

### Directori `pagina-web`

El directori `pagina-web` conté els diretoris i recursos necessaris per a la implementació de la pàgina web. Aquí trobaràs:

- **`assets/`**: Conté recursos estàtics com imatges, icones, fonts i altres fitxers multimèdia.
- **`handlers/`**: Inclou scripts o fitxers responsables de gestionar la lògica del servidor o la interacció amb l'usuari.
- **`includes/`**: Conté fragments de codi reutilitzables, com capçaleres, peus de pàgina o components comuns.
- **`pages/`**: Emmagatzema les diferents pàgines HTML o plantilles que formen part de la pàgina web.
- **`index.php`**: Redireccionament a home.php dins de pages/.

## Base de dades

### Estructura de la Base de Dades

La base de dades `usolutions_db` conté diverses taules i vistes per gestionar els serveis i configuracions del sistema. A continuació es detalla l'estructura principal:

- **`container_templates`**: Defineix plantilles de contenidors amb informació com el nom, descripció, tipus de sistema operatiu i versió.
- **`count_administrative`**: Emmagatzema el recompte de serveis administratius per tipus.
- **`invoices`**: Gestiona les factures dels usuaris, incloent-hi l'estat i les dates de venciment.
- **`payment_methods`**: Conté informació sobre els mètodes de pagament dels usuaris.
- **`proxmox_config`**: Configuració de l'API de Proxmox per a la gestió de servidors.
- **`server_configurations`**: Configuracions específiques dels servidors.
- **`server_containers`**: Informació sobre els contenidors associats als servidors.
- **`server_plans`**: Defineix els plans de servidors amb especificacions com CPU, RAM, emmagatzematge i amplada de banda.
- **`services`**: Detalla els serveis disponibles, incloent-hi si són gestionats o no.
- **`support_tickets`**: Gestiona els tiquets de suport dels usuaris.
- **`ticket_messages`**: Missatges associats als tiquets de suport.
- **`users`**: Informació dels usuaris, incloent-hi el seu estat i dades de contacte.
- **`user_servers`**: Relació entre els usuaris i els servidors adquirits.
