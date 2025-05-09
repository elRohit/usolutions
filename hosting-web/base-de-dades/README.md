# Estructura de la Base de Dades

La base de dades `usolutions_db` està dissenyada per gestionar de manera eficient els serveis i configuracions del sistema. A continuació es presenta una descripció de les seves taules principals amb detalls basats en l'estructura SQL proporcionada:

- **`container_templates`**: Conté plantilles de contenidors amb informació detallada com el nom, descripció, tipus de sistema operatiu (`debian`, `ubuntu`), versió i estat d'activació.
- **`count_administrative`**: Registra el recompte de serveis administratius classificats per tipus, amb actualitzacions automàtiques de la data.
- **`invoices`**: Gestiona les factures dels usuaris, incloent-hi l'import, estat (`pending`, `paid`, `cancelled`, `refunded`), dates de venciment i pagament.
- **`payment_methods`**: Emmagatzema informació sobre els mètodes de pagament disponibles, com targetes de crèdit, PayPal o comptes bancaris, amb opcions per defecte.
- **`proxmox_config`**: Inclou la configuració necessària per integrar i gestionar servidors mitjançant l'API de Proxmox, com l'URL de l'API, credencials i estat d'activació.
- **`server_configurations`**: Defineix configuracions específiques associades a cada servidor, amb claus i valors personalitzats.
- **`server_containers`**: Proporciona informació sobre els contenidors vinculats als servidors, incloent-hi l'adreça IP, estat (`pending`, `creating`, `running`, etc.), i detalls d'administració.
- **`server_plans`**: Defineix els plans de servidors amb especificacions tècniques com CPU, RAM, emmagatzematge, amplada de banda i multiplicadors de preu.
- **`services`**: Detalla els serveis disponibles, especificant si són gestionats (`managed`) o autogestionats (`unmanaged`), amb opcions per aplicacions com WordPress o PrestaShop.
- **`support_tickets`**: Gestiona els tiquets de suport creats pels usuaris, amb estats (`open`, `in_progress`, `resolved`, `closed`) i prioritats (`low`, `medium`, `high`, `critical`).
- **`ticket_messages`**: Conté els missatges associats als tiquets de suport per facilitar el seguiment de les comunicacions entre usuaris i personal.
- **`users`**: Emmagatzema informació dels usuaris, incloent-hi el seu estat (`active`, `inactive`, `suspended`), dades de contacte, saldo i imatge de perfil.
- **`user_servers`**: Estableix la relació entre els usuaris i els servidors adquirits, incloent-hi informació sobre l'assignació, estat (`pending`, `active`, etc.), i cicle de facturació (`monthly`, `quarterly`, `annually`).

Aquest disseny permet una gestió centralitzada i eficient de tots els aspectes relacionats amb els serveis, usuaris i configuracions del sistema.
