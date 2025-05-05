import time
import imaplib
from imap_tools import MailBox, AND
import requests

# Configuración IMAP
IMAP_SERVER = 'imap.dondominio.com'
EMAIL_USER = 'support@usolutions.cat'
EMAIL_PASSWORD = 'P@ssw0rd'

# Configuración GLPI
GLPI_URL = 'http://192.168.5.248/apirest.php'
APP_TOKEN = 'iD2raBxcvPSlZgRU5ooSpdQxBOPr2yPU9WLBDMjQ'
USER_TOKEN = 'vWUfe0X3Zx3MCysWChaJmk9aVqj6Nophbi2vU6Vj'

def procesar_correos():
    try:
        # Autenticar en GLPI
        auth = requests.get(
            f"{GLPI_URL}/initSession",
            headers={"App-Token": APP_TOKEN, "Authorization": f"user_token {USER_TOKEN}"}
        )
        session_token = auth.json()["session_token"]

        # Conectar a la bùstia via IMAP 
        with MailBox(IMAP_SERVER).login(EMAIL_USER, EMAIL_PASSWORD, 'INBOX') as mailbox:
            # Llegir tots els correus NOUS
            for msg in mailbox.fetch(AND(seen=False), mark_seen=False):
                # Crear ticket 
                contenido = f"""
                **Remitente**: {msg.from_}
                **Asunto**: {msg.subject}
                **Mensaje**:
                {msg.text}
                """
                
                response = requests.post(
                    f"{GLPI_URL}/Ticket/",
                    headers={'App-Token': APP_TOKEN, 'Session-Token': session_token, 'Content-Type': 'application/json'},
                    json={
                        "input": {
                            "name": msg.subject,
                            "content": contenido,
                            "entities_id": 0
                        }
                    }
                )
                
                if response.status_code == 201:
                    print(f"Ticket creado para {msg.from_}")
                    # Marca el correu com a llegit després de ser el Ticket Creat (Evitar conflictes de mes tickets del mat. remitent)
                    mailbox.flag([msg.uid], ['\\Seen'], True)  
                else:
                    print(f"Error: {response.text}")

    except Exception as e:
        print(f"Error: {str(e)}")

# MAIN Exec 
if __name__ == "__main__":
    while True:
        procesar_correos()
        time.sleep(0.2)  # Esperar 0.2 seg per cada correu