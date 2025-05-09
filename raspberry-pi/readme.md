# IOT: Sistema de Control de Fichatges amb Raspberry Pi i Aplicació Web

## Introducció al Projecte

Aquest projecte consisteix en desenvolupar un sistema complet per gestionar els fichatges dels treballadors utilitzant targetes RFID. L'objectiu principal és automatitzar i simplificar el control horari del personal d'una empresa, oferint una plataforma web intuïtiva per visualitzar i administrar totes les dades recollides.

## Tecnologia Utilitzada

- **Raspberry Pi:**

  - Model: Raspberry Pi 4
  - Sistema operatiu: Raspberry Pi OS
  - Llenguatge: Python
  - Biblioteques: `RPi.GPIO`, `SimpleMFRC522`, `mariadb` (connector amb MariaDB)

- **Base de Dades:**

  - Sistema gestor: MariaDB

- **Aplicació Web:**
  - Backend: PHP
  - Frontend: HTML, CSS, JavaScript
  - Frameworks i eines: Chart.js (per estadístiques), AJAX (per comunicació asincrònica amb PHP), SSH per execució remota d'scripts en la Raspberry Pi

## Connexió entre Raspberry Pi i Servidor Web

Per establir una comunicació segura i eficient entre la Raspberry Pi i el servidor web, hem configurat una connexió mitjançant SSH amb autenticació per claus públiques i privades. Això permet executar remotament scripts de Python des de l'aplicació web sense necessitat de contrasenyes explícites.

Els passos realitzats han estat els següents:

1. **Generar una parella de claus SSH:**

   ```bash
   ssh-keygen -t rsa -b 4096
   ```

2. **Copiar la clau pública a la Raspberry Pi:**

   ```bash
   ssh-copy-id ira@10.93.255.155
   ```

3. **Configurar un àlies per a facilitar la connexió:**
   S'ha creat un àlies en el fitxer `~/.ssh/config` per simplificar la connexió remota:

   ```:
   Host raspberry
   HostName 10.93.255.155
   User ira
   ```

## Estructura del Sistema de Fichatges

El sistema consisteix en diversos components, cadascun amb funcions específiques:

- **Targetes RFID:** Cada treballador disposa d'una targeta RFID associada al seu perfil en la base de dades.
- **Lectura RFID:** Quan un usuari escaneja la seva targeta a través del lector RFID connectat a la Raspberry Pi, s'executa un script en Python que registra la informació a la base de dades.
- **Scripts Python:**
  - `leer_rfid.py`: Registra entrades i sortides.
  - `registrar_tarjeta_existente.py`: Associa targetes RFID a usuaris existents.

## Disseny i Estructura de la Base de Dades

Hem dissenyat una base de dades robusta que permet gestionar eficaçment les dades generades pel sistema. Aquesta base de dades consta de les següents taules principals:

### Taula `usuaris`

- `id`: Clau primària
- `nombre`, `apellido`: Dades personals
- `email`: Correu electrònic de l'usuari
- `password`: Contrassenya encriptada amb `password_hash`
- `rol_id`: Referència al rol (Administrador, Recursos Humans, Empleat)

### Taula `rols`

- `id`: Clau primària
- `nombre`: Nom del rol (Administrador, Recursos Humans, Empleat)

### Taula `permisos`

- `id`: Clau primària
- `nombre`: Descripció del permís (veure fitxatges propis, veure tots els fitxatges, modificar fitxatges, gestionar usuaris)

### Taula `rols_permisos`

- Relació N:M entre `rols` i `permisos`.

### Taula `tarjetas`

- `id`: Clau primària
- `usuario_id`: Referència a l'usuari que té assignada la targeta
- `rfid_code`: Codi únic de la targeta RFID

### Taula `sesiones_fichaje`

- `id`: Clau primària
- `usuario_id`: Referència a l'usuari que ha fitxat
- `fecha_entrada`: Data i hora de l'entrada
- `fecha_salida`: Data i hora de sortida
- `tiempo_extra`: Temps extra calculat respecte a la jornada laboral estàndard (8 hores)

---

## Aplicació Web

L'aplicació web desenvolupada serveix com a interfície principal per interactuar amb les dades generades pel sistema RFID. Aquesta aplicació permet gestionar els usuaris, visualitzar estadístiques, administrar les targetes RFID, així com realitzar configuracions generals del sistema.

### Estructura de l'aplicació web

L'aplicació web està estructurada en diverses pàgines i seccions:

#### **Login**

Permet als usuaris iniciar sessió amb el seu correu electrònic i contrasenya. La contrasenya es gestiona de manera segura mitjançant la funció `password_hash` i es verifica amb `password_verify`.

#### **Panel d'Administració**

És la pàgina principal després d'iniciar sessió, adaptada segons el rol de l'usuari:

- **Administrador**: pot veure i gestionar tots els aspectes del sistema.
- **Recursos Humans**: pot veure fitxatges i estadístiques generals.
- **Empleat**: té accés limitat només als seus propis registres.

#### **Gestió d'Usuaris**

Permet a l'administrador crear, modificar i eliminar usuaris. També pot assignar rols (Administrador, Recursos Humans, Empleat).

#### **Gestió de Targetes RFID**

Des d'aquesta secció es gestionen les targetes RFID:

- Registrar noves targetes.
- Assignar o revocar targetes a usuaris existents.
- Escanejar targetes directament des de la interfície web mitjançant una execució remota a la Raspberry Pi.

#### **Estadístiques**

Ofereix gràfiques interactives que mostren estadístiques clau com:

- Hores totals treballades mensualment.
- Hores extra realitzades.
- Percentatge d'assistència per dia de la setmana.
- Rànquing de puntualitat dels empleats.

Aquestes estadístiques estan generades dinàmicament amb Chart.js i alimentades mitjançant dades processades en PHP amb consultes SQL eficients.

### Millores implementades

- **Validació en temps real:** Ús de JavaScript per a la validació immediata dels formularis, millorant l'experiència d'usuari.
- **Animacions i Estètica:** Millores visuals amb animacions CSS, efectes d'interacció i utilització de biblioteques com tsParticles per donar un toc modern.
- **Eficiència i Rendiment:** Optimització del codi SQL, implementació d'índexs a la base de dades, i càrrega asincrònica de dades amb AJAX.
- **Seguretat millorada:** Prevenció d’injeccions SQL utilitzant declaracions preparades amb MySQLi, sessions segures i protecció contra CSRF en formularis crítics.

### Funcionament tècnic de la comunicació amb la Raspberry Pi

La comunicació entre l'aplicació web i la Raspberry Pi es realitza de manera segura via SSH:

- **Execució remota d'scripts Python:** des de PHP, utilitzem la funció `shell_exec()` amb comandes SSH per executar scripts Python específics per llegir i registrar targetes RFID.

Exemple d'execució des de PHP:

```php
$output = shell_exec('ssh raspberry "python3 /home/ira/leer_rfid.py"');
```

### Flux complet del procés

1. **Registre d'entrada/sortida:**

   - L'usuari escaneja la seva targeta RFID.
   - La Raspberry Pi registra aquest esdeveniment executant `leer_rfid.py` i introdueix les dades a la base de dades remota.

2. **Consulta de fitxatges:**

   - Des de l'aplicació web, els usuaris amb permisos adients consulten els registres guardats.

3. **Administració de targetes:**
   - L'administrador pot registrar noves targetes RFID o associar-les a usuaris existents.

## Futurs desenvolupaments

Per futures iteracions, es consideren implementar les següents millores:

- **Notificacions automàtiques:** per informar administradors o RRHH de certs esdeveniments.
- **Integració mòbil:** desenvolupament d'una aplicació mòbil per facilitar encara més l'accés i gestió dels registres.
- **Còpies de seguretat automatitzades:** de les dades generades per garantir-ne la seguretat.


---

Aquest projecte, per tant, combina hardware (Raspberry Pi), software personalitzat (scripts Python) i una interfície web moderna per aconseguir un sistema robust, segur i intuïtiu per al control d'assistència en entorns empresarials.
