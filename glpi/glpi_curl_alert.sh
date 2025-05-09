#!/bin/bash

TO="$1"
SUBJECT="$2"
MESSAGE="$3"

LOGFILE="/var/log/zabbix/glpi_alert.log"

echo "[$(date)] === NUEVO ENVÍO ===" >> "$LOGFILE"
echo "TO: $TO" >> "$LOGFILE"
echo "SUBJECT: $SUBJECT" >> "$LOGFILE"
echo "MESSAGE: $MESSAGE" >> "$LOGFILE"

APP_TOKEN="EL_TEU_APP_TOKEN"
USER_TOKEN="EL_TEU_USER_TOKEN"
GLPI_API_URL="http://192.168.5.248/apirest.php"  # ← CAMBIA ESTA URL

CURL="/usr/bin/curl"
JQ="/usr/bin/jq"

# Obtener session_token
SESSION_JSON=$($CURL -s -X GET \
  -H "App-Token: $APP_TOKEN" \
  -H "Authorization: user_token $USER_TOKEN" \
  "$GLPI_API_URL/initSession")

echo "RESPUESTA initSession:" >> "$LOGFILE"
echo "$SESSION_JSON" >> "$LOGFILE"

SESSION_TOKEN=$(echo "$SESSION_JSON" | $JQ -r '.session_token')

if [[ "$SESSION_TOKEN" == "null" || -z "$SESSION_TOKEN" ]]; then
  echo "ERROR: No se pudo obtener session_token" >> "$LOGFILE"
  exit 1
fi

# Construir JSON limpio con JQ
PAYLOAD=$(jq -n \
  --arg name "$SUBJECT" \
  --arg content "$MESSAGE" \
  '{
    input: {
      name: $name,
      content: $content,
      urgency: 5,
      priority: 5,
      impact: 5
    }
  }')

echo "PAYLOAD JSON:" >> "$LOGFILE"
echo "$PAYLOAD" >> "$LOGFILE"

# Enviar ticket
RESPONSE=$($CURL -s -X POST "$GLPI_API_URL/Ticket" \
  -H "App-Token: $APP_TOKEN" \
  -H "Session-Token: $SESSION_TOKEN" \
  -H "Content-Type: application/json" \
  -d "$PAYLOAD")

echo "RESPUESTA Ticket:" >> "$LOGFILE"
echo "$RESPONSE" >> "$LOGFILE"

# Cerrar sesión
$CURL -s -X GET "$GLPI_API_URL/killSession" \
  -H "App-Token: $APP_TOKEN" \
  -H "Session-Token: $SESSION_TOKEN" >> "$LOGFILE"

echo "=== FIN ===" >> "$LOGFILE"
