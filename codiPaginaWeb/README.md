# Documentació del projecte web

## Introducció

En aquesta secció es documenta detalladament el funcionament intern de la pàgina web **Usolutions**. L’objectiu és explicar el funcionament de cada component a través del codi font i de les bases de dades. Aquesta documentació es complementa amb captures de pantalla de les diferents seccions de la pàgina per entendre millor el seu funcionament i aspecte visual.

## Base de dades `usolutions_db`

La base de dades utilitzada per aquest projecte és `usolutions_db`. Aquesta conté totes les taules necessàries per gestionar:

- Usuaris
- Factures i transaccions
- Pagaments i serveis
- Contenidors virtuals
- Tiquets de suport

### Estructura i contingut detallat

A continuació es descriuen les taules principals de la base de dades:

### `container_templates`

Defineix les plantilles disponibles per crear contenidors. Cada plantilla està associada a un sistema operatiu concret i un fitxer arxivat.

### `invoices`

Conté informació sobre factures generades per als usuaris: imports, estat, dates d’emissió, venciment i pagament.

### `payment_methods`

Emmagatzema els mètodes de pagament registrats per cada usuari: tipus (targeta, PayPal, banc), últimes xifres, dates de caducitat, i si és el predeterminat.

### `proxmox_config`

Configuració de connexió amb el node de Proxmox: URL de l’API, usuari, contrasenya, nom del node i informació d’emmagatzematge.

### `support_tickets`

Sistema de tiquets de suport per part dels usuaris. Inclou l’assumpte, estat, prioritat, dates de creació, actualització i tancament.

### `ticket_messages`

Missatges associats als tiquets de suport. Pot ser enviat per l’usuari o pel personal de suport.

### `transactions`

Historial de transaccions econòmiques. Pot incloure pagaments, devolucions, càrrecs o abonaments.

### `users`

Informació personal dels usuaris registrats: nom, correu, contrasenya, estat del compte, saldo, data de creació i última connexió.

### `services`

Defineix els serveis disponibles al sistema (gestionats o no gestionats). També es detalla l’aplicació associada (WordPress, PrestaShop, etc.), preu base i quota d’instal·lació.

### `server_plans`

Plans predefinits de configuració de recursos per als servidors. Especifica CPU, memòria RAM, emmagatzematge, amplada de banda i multiplicador de preu.

### `user_servers`

Relació entre usuaris i servidors contractats. Defineix informació com el nom del servidor, hostname, estat, dates de compra i renovació, cicle de facturació i notes.

### `server_containers`

Contenidors actius o programats. Inclou el VMID, IP assignada, credencials d'accés, node on està allotjat i estat del contenidor.

### `server_configurations`

Paràmetres addicionals de configuració per a cada servidor. Cada configuració es guarda amb una clau i un valor.

### `server_tasks`

Tasques relacionades amb la gestió del servidor: crear, iniciar, aturar, reiniciar, fer còpies de seguretat, etc. Inclou l’estat i resultat.

### `server_backups`

Informació de les còpies de seguretat realitzades per cada servidor. S’indica el nom, mida, estat i data de finalització.

### `server_stats`

Registre de les estadístiques d’ús dels servidors: CPU, RAM, disc i amplada de banda.

### `invoice_items`

Desglossament dels ítems inclosos dins una factura, amb una descripció, import i relació amb el servidor associat.

### Vista `server_view`

Una vista SQL per facilitar consultes complexas sobre informació completa del servidor, el seu servei, el pla, el contenidor i la plantilla associada.

---

La base de dades està dissenyada per garantir integritat referencial amb claus primàries i estrangeres, assegurant així que les relacions entre les taules són consistents.
