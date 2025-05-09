# Estructura Principal

A la carpeta arrel del projecte trobem els següents elements principals:

- `css/`: Conté els fitxers d'estils per donar aparença a la interfície.
- `js/`: Conté scripts JavaScript per gestionar la interacció amb l'usuari i AJAX.
- `includes/`: Conté fitxers PHP compartits i reutilitzables (connexió, funcions, capçalera...).
- `components/ui/`: Directori reservat per a components d'interfície, actualment no usat intensament.
- `index.php`: Redirecciona o carrega el panell principal segons si l'usuari està loguejat.
- `login.php` i `logout.php`: Gestionen l'inici i el tancament de sessió.
- `panel.php`: Panell principal per a l’usuari autenticat.
- `usuarios.php`: Gestió dels usuaris.
- `tarjetas.php`: Gestió de targetes RFID (escaneig, assignació, registre...).
- `fichar.php`: Endpoint per registrar entrades/sortides via RFID.
- `estadisticas.php`: Mostra gràfiques i estadístiques de fitxatges.
- Altres scripts com `get_fichajes.php`, `get_today_attendance.php`, etc., que donen suport AJAX.
