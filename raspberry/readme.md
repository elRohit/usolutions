# Projecte Final: Sistema de Control de Fichatges amb Raspberry Pi i Aplicació Web

## Introducció al Projecte

Aquest projecte consisteix en desenvolupar un sistema complet per gestionar els fichatges dels treballadors utilitzant targetes RFID. L'objectiu principal és automatitzar i simplificar el control horari del personal d'una empresa, oferint una plataforma web intuïtiva per visualitzar i administrar totes les dades recollides.

(FOTO AQUI: Imatge introductòria del sistema complet funcionant)

## Tecnologia Utilitzada

- **Raspberry Pi:**

  - Model: Raspberry Pi 4
  - Sistema operatiu: Raspberry Pi OS
  - Llenguatge: Python
  - Biblioteques: `RPi.GPIO`, `SimpleMFRC522`, `mariadb` (connector amb MariaDB)

- **Base de Dades:**

  - Sistema gestor: MariaDB
  - Allotjament: bbdd.usolutions.cat

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
   ```
   Host raspberry
   HostName 10.93.255.155
   User ira
   ```

(FOTO AQUI: Configuració SSH a Raspberry Pi)

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

(FOTO AQUI: Diagrama ER o captures de les taules de la base de dades)

---

(A continuació segueix la segona part explicant en profunditat l'aplicació web, que proporcionaré en el següent missatge.)
