# Documentació del projecte web

## Pàgines del lloc web

### `register.php`

Aquesta pàgina permet a nous usuaris crear un compte a la plataforma. Inclou validacions bàsiques i comprova que l’adreça de correu electrònic no estigui ja registrada.

#### Estructura general

1. **Redirecció si l’usuari ja està loguejat**
   ```php
   if (isLoggedIn()) { header("Location: dashboard.php"); }
   ```
2. **Gestió del formulari (POST)**
   - Comprova que tots els camps estiguin plens.
   - Valida el correu electrònic.
   - Comprova que les contrasenyes coincideixin i tinguin una llargada mínima.
   - Comprova si el correu ja existeix a la base de dades.
   - Si tot és correcte, registra l’usuari i l’inicia sessió automàticament.
3. **Formulari de registre**
   - Camps per nom, cognoms, correu, contrasenya i confirmació.
   - Checkbox per acceptar els termes i la política de privacitat.
4. **Redirecció post-registre**
   Si tot ha anat bé, redirigeix directament al `dashboard.php`.

### Exemple visual

A continuació una captura de pantalla que mostra el formulari de registre:

```
![Captura del registre](./img/captura-register.png)
```

---

### `login.php`

Aquesta pàgina permet als usuaris accedir al seu compte amb correu i contrasenya.

#### Estructura general

1. **Redirecció si l’usuari ja està loguejat**
   ```php
   if (isLoggedIn()) { header("Location: dashboard.php"); }
   ```
2. **Gestió del formulari (POST)**
   - Es comprova que s’hagi omplert el correu i la contrasenya.
   - S’utilitza la funció `login($email, $password)` per validar credencials.
   - Si és correcte, redirigeix al `dashboard` o a la pàgina especificada a `redirect`.
3. **Formulari d'inici de sessió**
   - Camps per correu i contrasenya.
   - Opció de "Remember me" i enllaç a recuperació de contrasenya.
4. **Errors**
   Si les credencials són incorrectes, es mostra un missatge d’error.

### Exemple visual

A continuació una captura de pantalla que mostra el formulari de login:

```
![Captura del login](./img/captura-login.png)
```

### `home.php`

Aquesta és la pàgina principal del lloc i serveix com a punt d'entrada públic per als usuaris. Mostra informació essencial sobre els serveis que ofereix USOLUTIONS i convida l’usuari a explorar o contactar amb l’equip.

#### Estructura general

1. **Inicialització de la pàgina**

   ```php
   $pageTitle = "Home";
   include_once '../includes/header.php';
   ```

   - Defineix el títol de la pàgina per ser utilitzat al `<title>` del document HTML.
   - Inclou el fitxer de capçalera comú que conté la barra de navegació i metadades.
2. **Secció Hero**
   Aquesta secció destaca el missatge principal amb un títol atractiu i botons cridats a l'acció.

   - Botó per accedir als serveis (`servers.php`).
   - Botó per contactar (`contact.php`).
3. **Secció de característiques (`features`)**
   Presenta tres avantatges clau:

   - **Secure & Reliable** : S'hi destaca la seguretat i estabilitat.
   - **High Performance** : Es ressalta el rendiment optimitzat.
   - **24/7 Support** : Suport tècnic les 24 hores.
4. **Secció de serveis (`services-preview`)**

   ```php
   <?php foreach (SERVICES as $id => $service): ?>
   ```

   - Aquesta secció és dinàmica i utilitza un bucle `foreach` per mostrar una targeta per a cada servei disponible.
   - Cada targeta mostra una icona, nom del servei, descripció i enllaç directe a la secció corresponent de `servers.php`.
5. **Crida a l'acció final (`cta`)**
   Una secció que anima l’usuari a crear un compte amb un botó cap a `register.php`.
6. **Tancament de la pàgina**

   ```php
   include_once '../includes/footer.php';
   ```

   - Inclou el peu de pàgina comú amb informació addicional o enllaços.

### Exemple visual

A continuació una captura de pantalla que mostra la pàgina d'inici:

```
![Captura de la pàgina d'inici](./img/captura-home.png)
```

### `about.php`

Aquesta pàgina proporciona informació sobre la història, missió, valors i equip directiu de l’empresa. Està estructurada per oferir una visió clara i propera del projecte USOLUTIONS.

#### Estructura general

1. **Inicialització de la pàgina**

   ```php
   $pageTitle = "About Us";
   include_once '../includes/header.php';
   ```

   - S’estableix el títol com a "About Us".
   - Es carrega el capçal comú del lloc web.
2. **Secció de capçalera (`page-header`)**
   Mostra un títol principal i una breu descripció introductòria.

   - Text: "About USOLUTIONS"
   - Subtítol: "Learn more about our company and mission"
3. **Secció "Our Story" i "Our Mission"**

   ```html
   <div class="about-text">
     <h2>Our Story</h2>
     <p>...</p>
     <h2>Our Mission</h2>
     <ul>
       <li><strong>Reliability:</strong> ...</li>
       ...
     </ul>
   </div>
   ```

   - Contingut històric i objectius de l’empresa des de la seva fundació.
   - Missió basada en la fiabilitat, seguretat, innovació i suport al client.
4. **Imatge complementària**

   ```html
   <div class="about-image">
     <img src="../assets/images/about-us.jpg" alt="USOLUTIONS Team" />
   </div>
   ```

   - Imatge que acompanya el text, probablement de l’equip o l’entorn de treball.
5. **Secció "Our Core Values"**
   Mostra quatre valors fonamentals de l’empresa mitjançant targetes amb icones:

   - **Integrity**
   - **Excellence**
   - **Innovation**
   - **Collaboration**
     Cada targeta inclou una icona, títol i breu descripció.
6. **Secció "Leadership Team"**

   ```html
   <div class="team-member">
     <img src="..." />
     <h3>Nom</h3>
     <p class="member-title">Càrrec</p>
     <p class="member-bio">Descripció</p>
   </div>
   ```

   - Es mostren tres membres fundadors amb imatge, nom, títol i biografia curta.
   - Inclou una nota humorística amb el membre "Cleaner", reforçant la personalitat del projecte.
7. **Crida a l'acció final (`cta`)**
   Anima els usuaris a explorar els serveis amb un botó a `servers.php`.
8. **Tancament de la pàgina**

   ```php
   include_once '../includes/footer.php';
   ```

   - Peu de pàgina comú carregat al final del document.

### Exemple visual

A continuació una captura de pantalla que mostra la pàgina about amb la secció dels fundadors i valors:

```
![Captura de la pàgina about](./img/captura-about.png)
```

### `contact.php`

Aquesta pàgina permet als usuaris posar-se en contacte amb l'equip de USOLUTIONS a través d’un formulari. També mostra informació de contacte i xarxes socials.

#### Estructura general

1. **Inicialització de la pàgina i variables**

   ```php
   $pageTitle = "Contact Us";
   include_once '../includes/header.php';
   $success = false;
   $error = false;
   ```

   - Defineix el títol de la pàgina i carrega el capçal.
   - Inicialitza variables per mostrar missatges d’èxit o error després de l’enviament del formulari.
2. **Gestió de formulari (POST)**

   ```php
   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       $name = ...;
       $email = ...;
       ...
       if (empty(...) || ...) {
           $error = "Please fill in all required fields.";
       } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
           $error = "Please enter a valid email address.";
       } else {
           $success = true;
       }
   }
   ```

   - Es comprova si el mètode de petició és POST.
   - Es valida que tots els camps estiguin plens i que el correu sigui vàlid.
   - S'assigna un missatge d'error o d'èxit segons correspongui.
3. **Capçalera de la pàgina (`page-header`)**
   Mostra un títol i un subtítol introductori.
4. **Formulari de contacte (`contact-form`)**

   ```html
   <form action="/pages/contact.php" method="post">
     <input type="text" id="name" name="name" />
     <input type="email" id="email" name="email" />
     <input type="text" id="subject" name="subject" />
     <textarea id="message" name="message"></textarea>
   </form>
   ```

   - Conté camps per nom, correu, assumpte i missatge.
   - Mostra un missatge si el formulari s’ha enviat correctament o amb errors:
     ```php
     <?php if ($success): ?> ... <?php endif; ?>
     <?php if ($error): ?> ... <?php endif; ?>
     ```
5. **Informació de contacte (`contact-info`)**
   Presenta dades de contacte reals:

   - Adreça física
   - Número de telèfon
   - Correu electrònic
   - Horari d'atenció
6. **Xarxes socials**
   Mostra enllaços amb icones cap a Facebook, Twitter, LinkedIn i Instagram.
7. **Tancament de la pàgina**

   ```php
   include_once '../includes/footer.php';
   ```

### Exemple visual

A continuació una captura de pantalla que mostra el formulari i la informació de contacte:

```
![Captura de la pàgina contact](./img/captura-contact.png)
```

### `create-ticket.php`

Aquesta pàgina permet als usuaris autenticats enviar una nova sol·licitud de suport al sistema.

#### Estructura general

1. **Control d’accés i inicialització**

   ```php
   requireLogin();
   $userId = $_SESSION['user_id'];
   ```

   - L’usuari ha d’estar connectat per accedir a aquesta pàgina.
   - S'obté l’ID de l’usuari a través de la sessió.
2. **Gestió del formulari (POST)**

   ```php
   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       $subject = ...;
       $message = ...;
       $priority = ...;

       if (empty($subject) || empty($message)) {
           $error = "Subject and message are required.";
       } else {
           $ticketId = createTicket(...);

           if ($ticketId) {
               header("Location: ticket-details.php?id=...&created=1");
               exit;
           } else {
               $error = "Failed to create ticket. Please try again.";
           }
       }
   }
   ```

   - Es comprova si el mètode és POST.
   - Es validen els camps obligatoris (`subject` i `message`).
   - Si tot és correcte, es crida a la funció `createTicket()` i es redirigeix l’usuari al detall del tiquet.
3. **Secció del formulari**

   ```html
   <form action="create-ticket.php" method="post">
     <input type="text" name="subject" />
     <select name="priority">
       <option value="low">Low</option>
       ...
     </select>
     <textarea name="message"></textarea>
   </form>
   ```

   - Camps:
     - **Subjecte** : títol del tiquet
     - **Prioritat** : selector amb valors `low`, `medium`, `high`
     - **Missatge** : descripció detallada del problema
   - Botons d’acció per cancel·lar o enviar el tiquet.
4. **Missatges d’error**
   Si hi ha un error durant l'enviament, aquest es mostra dins d’un `div` amb la classe `alert-error`.

### Exemple visual

A continuació una captura de pantalla que mostra el formulari de creació de tiquets de suport:

```
![Captura de la pàgina create-ticket](./img/captura-create-ticket.png)
```

### `ticket-details.php`

Aquesta pàgina mostra el detall d’un tiquet específic creat per l’usuari i permet enviar respostes si aquest no està tancat.

#### Estructura general

1. **Verificació i obtenció del tiquet**

   ```php
   $ticketId = $_GET['id'];
   $ticket = getTicketDetails($ticketId, $userId);
   ```

   - Es verifica que l’usuari estigui autenticat.
   - Es recupera el tiquet corresponent. Si no existeix o no pertany a l’usuari, es redirigeix al dashboard.
2. **Enviament de respostes (POST)**

   ```php
   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       $message = $_POST['message'];
       if (empty($message)) {
           $error = ...;
       } else {
           replyToTicket(...);
       }
   }
   ```

   - Si el formulari ha estat enviat, es valida el contingut del missatge i s’afegeix una resposta mitjançant `replyToTicket()`.
   - S’actualitzen les dades del tiquet per incloure la nova resposta.
3. **Capçalera del tiquet**
   Mostra el número, l’estat actual i dades com la prioritat i la data de creació.

   ```php
   <h1>Ticket #<?php echo $ticketId; ?></h1>
   <span class="status-badge ...">...</span>
   ```
4. **Missatges del tiquet**

   ```php
   <div class="message customer">...</div>
   <div class="message admin">...</div>
   ```

   - Es mostren els missatges ordenats cronològicament, diferenciant si són de l’usuari o del personal de suport.
   - Utilitza icones i classes CSS per distingir rols.
5. **Formulari de resposta**

   - Si el tiquet no està tancat, es mostra un formulari per afegir una nova resposta.
   - En cas contrari, es mostra un missatge indicant que està tancat amb un botó per crear-ne un de nou.

### Exemple visual

A continuació una captura de pantalla que mostra els detalls d’un tiquet i el sistema de missatgeria entre usuari i suport:

```
![Captura de la pàgina ticket-details](./img/captura-ticket-details.png)
```

### `dashboard.php`

Aquesta pàgina és el centre de control de l’usuari un cop ha iniciat sessió. Des d’aquí pot visualitzar el seu perfil, gestionar servidors, veure factures i tiquets de suport.

#### Estructura general

1. **Recollida de dades**

   ```php
   $user = getUserProfile($userId);
   $servers = getUserServers($userId);
   $invoices = getUserInvoices($userId);
   $tickets = getUserTickets($userId);
   ```

   - S'obtenen totes les dades rellevants de l'usuari des de la base de dades mitjançant funcions específiques.
2. **Benvinguda**
   Es mostra una secció personalitzada amb el nom de l’usuari i una descripció breu del que pot fer al dashboard.
3. **Menú lateral**

   - Enllaços cap a seccions internes com servidors, facturació, tiquets i configuració del compte.
   - Mostra el perfil i el correu electrònic de l’usuari amb una icona d’usuari.
4. **Resum d’estadístiques**
   Mostra targetes amb dades clau:

   - Servidors actius
   - Factures pendents
   - Tiquets oberts
   - Antiguitat del compte (en dies)
     Aquestes dades es calculen amb `count()` i condicions específiques sobre els arrays.
5. **Secció de servidors**

   - Si l’usuari no té servidors, es mostra un missatge d’estat buit amb enllaç a `servers.php`.
   - En cas contrari, es mostren targetes amb informació del servidor: nom, estat, tipus de servei, pla contractat, IP i data de venciment.
   - Cada servidor té un botó per anar a `server-details.php`.
6. **Secció de facturació**

   - Si no hi ha factures, es mostra un estat buit.
   - Si n’hi ha, s’utilitza una taula per llistar-les: número, data, import, estat i accions (veure o pagar).
   - S’inclou un accés directe a mètodes de pagament.
7. **Secció de tiquets de suport**

   - S’utilitza una taula per mostrar el número, assumpte, estat i data de creació dels tiquets.
   - Cada fila inclou un botó per veure el detall (`ticket-details.php`).
   - També s’inclou un botó per crear un nou tiquet.

### Exemple visual

A continuació una captura de pantalla que mostra el panell principal amb estadístiques, servidors, facturació i tiquets:

```
![Captura de la pàgina dashboard](./img/captura-dashboard.png)
```

### `profile.php`

Aquesta pàgina permet als usuaris gestionar la seva informació personal i canviar la contrasenya. És una secció important dins de l’experiència d’usuari perquè garanteix el control de dades i seguretat del compte.

#### Estructura general

1. **Obtenció de dades de l’usuari**

   ```php
   $user = getUserProfile($userId);
   ```

   - Es recuperen les dades del perfil a partir de la sessió activa (`$_SESSION['user_id']`).
2. **Gestió del formulari (POST)**
   Segons el valor del camp `action`, la pàgina gestiona dues funcionalitats:

   - **Actualització de perfil:**

     ```php
     if ($action === 'update_profile') { ... }
     ```

     - Requereix el nom i cognom.
     - Utilitza `updateUserProfile()` per guardar canvis.
     - Si tot va bé, es refresca la informació i es mostra un missatge d’èxit.
   - **Canvi de contrasenya:**

     ```php
     if ($action === 'change_password') { ... }
     ```

     - Comprova que tots els camps estiguin plens.
     - Valida la llargada mínima i coincidència.
     - Si tot és correcte, s’utilitza `updateUserPassword()` per guardar la nova contrasenya.
3. **Navegació interna**
   Un menú lateral permet moure’s entre seccions de la configuració:

   - Informació personal
   - Canvi de contrasenya
   - Tornar al dashboard
4. **Secció: Informació personal**
   Formulari editable amb:

   - Nom i cognoms (obligatoris)
   - Correu electrònic (només lectura)
   - Número de telèfon (opcional)
     El correu mostra un avís que no pot ser modificat des d’aquí.
5. **Secció: Canvi de contrasenya**
   Formulari amb camps per:

   - Contrasenya actual
   - Nova contrasenya
   - Confirmació de nova contrasenya
     Inclou un missatge amb recomanacions de seguretat.
6. **Missatges de retroacció**

   - Missatges d’èxit o error es mostren després de cada acció, segons correspongui.

### Exemple visual

A continuació una captura de pantalla que mostra les seccions de perfil i canvi de contrasenya:

```
![Captura de la pàgina profile](./img/captura-profile.png)
```

### `servers.php`

Aquesta pàgina mostra tots els serveis de servidors disponibles (tants gestionats com no gestionats) i permet als usuaris iniciar el procés de contractació d’un servidor.

#### Estructura general

1. **Filtrat de tipus de servidor via paràmetre GET**

   ```php
   $serverType = $_GET['type'] ?? 'all';
   ```

   - Permet a l’usuari canviar entre pestanyes: "All Servers", "Managed Servers" i "Unmanaged Servers".
   - El valor es reflecteix tant en la consulta SQL com en l’estil actiu dels botons.
2. **Obtenció de serveis i plans des de la base de dades**

   ```php
   $query = "SELECT * FROM services WHERE is_active = 1";
   $plansQuery = "SELECT * FROM server_plans WHERE is_active = 1";
   ```

   - Els serveis es poden filtrar per tipus (gestionat o no).
   - Els plans estan ordenats pel multiplicador de preu per oferir primer els més econòmics.
3. **Visualització de serveis**
   Per cada servei:

   - Es mostra el nom, descripció i preu base.
   - Si és un servei gestionat, també es mostra el tipus d'aplicació (WordPress, etc.).
   - Característiques diferenciades segons el tipus:
     - Gestionats: backups, actualitzacions, suport 24/7...
     - No gestionats: accés root, tria d’OS, SSH...
4. **Visualització de plans**

   ```php
   foreach ($plans as $plan) { ... }
   ```

   - Per cada servei es mostren totes les opcions de pla disponibles.
   - Cada pla detalla recursos (CPU, RAM, emmagatzematge, amplada de banda).
   - El preu final es calcula multiplicant el preu base del servei pel multiplicador del pla.
   - Si l’usuari està loguejat, pot fer la comanda directament. Si no, es redirigeix al login.
5. **Secció FAQ (Preguntes Freqüents)**

   - Explica diferències entre serveis gestionats i no.
   - Informa sobre la possibilitat de millorar plans.
   - Explica com accedir als servidors (panell vs SSH).
6. **Crida a l’acció final**
   Botó per contactar amb l’equip si l’usuari necessita ajuda per escollir un servei.

### Exemple visual

A continuació una captura de pantalla que mostra la llista de servidors disponibles i els seus plans associats:

```
![Captura de la pàgina servers](./img/captura-servers.png)
```

## servers-config.php

La pàgina `servers-config.php` és el pas intermedi abans de la creació d’un servidor, on l’usuari pot revisar el resum de la comanda i introduir la configuració addicional necessària, especialment en el cas dels servidors  **unmanaged** .

### Resum de la comanda

Es mostra un resum dels detalls de la comanda:

* Servei escollit (ex: WordPress Managed Server)
* Tipus de servei (Managed o Unmanaged)
* Aplicació (si és managed)
* Pla escollit (ex: Starter, Business, etc.)
* Recursos assignats: CPU, RAM, emmagatzematge i amplada de banda
* Preu total mensual calculat amb el `price_multiplier` del pla

### Formulari de configuració

Depenent del tipus de servei, la pàgina ofereix diferents opcions:

* **Managed** : No requereix configuració per part de l’usuari. Es mostra un missatge informatiu i un botó per continuar.
* **Unmanaged** : L’usuari ha d’introduir:
* `hostname` → nom identificatiu del servidor
* `username` → usuari administrador
* `password` i `confirm_password` → contrasenya d’accés

Si les dades són vàlides, es desa la configuració a `$_SESSION['server_config']` i es redirigeix a `servers-payment.php`.

## servers-creation.php

Aquest fitxer és el que  **executa finalment la creació del servidor** . A partir de la configuració desada a la sessió, genera un fitxer `main.tf` amb la configuració necessària per Terraform i el desplega en un entorn  **Proxmox** .

### Validació prèvia

Si no hi ha dades a `$_SESSION['server_config']`, mostra un error i no continua.

### Cas unmanaged

En funció del sistema operatiu seleccionat (Debian o Ubuntu), es genera un `main.tf` personalitzat:

* Configura el `hostname`, `username` i `password`
* Assigna els recursos (CPU, RAM, disc...)
* Fa ús de `sshpass` per connectar-se i executar instruccions dins el contenidor
* Crea l’usuari, li assigna permisos `sudo`, i activa el servei amb alta disponibilitat (`ha-manager`)

Després de la creació:

* Es netegen els arxius generats per Terraform
* Es mostra un missatge de confirmació o d’error

### Cas managed

Depenent de l’aplicació (`WordPress`, `PrestaShop`...):

* El `main.tf` generat inclou ordres per pujar i executar scripts d’instal·lació específics dins del contenidor creat
* També s’instal·la l’agent de monitoratge `Zabbix`

💡  **Nota** : El cas `NextCloud Managed Hosting` apareix com a estructura preparada però sense contingut dins del codi actual.

## servers-payment.php

Aquesta pàgina finalitza el procés de contractació d’un servidor. Mostra un resum detallat de la comanda i permet escollir el mètode de pagament abans de passar a la creació del servidor.

### Funcionament principal

* **Autenticació** : Comprova que l’usuari estigui autenticat. En cas contrari, es redirigeix a `login.php`.
* **Validació de configuració** : Si no hi ha configuració prèvia de servidor emmagatzemada a la sessió (`$_SESSION['server_config']`), redirigeix a `servers.php`.
* **Consulta de dades** : Obté de la base de dades les dades del servei i el pla escollits per mostrar el resum de comanda.
* **Càlcul del preu total** : Multiplica el preu base del servei pel multiplicador del pla (`$totalPrice`).
* **Processament del formulari** :
* Comprova que s’ha seleccionat un mètode de pagament vàlid (`card`, `bank`, o `paypal`).
* Si s’escull `paypal`, es simula un pagament automàtic amb l’estat `completed`.
* La informació es guarda a `$_SESSION['server_config']`.
* Redirigeix a `servers-creation.php` per continuar amb la creació del servidor.

### Contingut de la pàgina

* **Resum de la comanda** : Mostra informació com:
* Nom del servei i tipus
* Aplicació (en cas de servidor gestionat)
* Pla escollit, CPU, RAM, emmagatzematge, ample de banda
* Informació addicional com hostname i username (per a servidors no gestionats)
* Preu total mensual
* **Formulari de pagament** :
* Mostra tres opcions: targeta, transferència bancària i PayPal
* En entorns de test, PayPal és gratuït i no realitza cap transacció real
* Missatge d’advertència indicant que és un entorn de proves

### Exemple visual

A continuació una captura de pantalla que mostra el resum de comanda i el formulari de selecció de mètode de pagament per completar la contractació:

![Exemple de pàgina de pagament amb resum i mètodes](https://chatgpt.com/g/g-p-67ed45cb3e6881919862f4d504d2be91-usolutions/c/exemple-pagament.png)
