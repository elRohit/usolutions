## Taula de Continguts

1. [Ús d'un Hipervisor i Proxmox](#ús-dun-hipervisor-i-proxmox)
    - [Per què s'utilitza un hipervisor?](#per-què-sutilitza-un-hipervisor)
    - [Què és Proxmox?](#què-és-proxmox)
2. [Què és pfSense?](#què-és-pfsense)
3. [Què és un servidor NAS?](#què-és-un-servidor-nas)
    - [Avantatges d'un servidor NAS](#avantatges-dun-servidor-nas)
4. [Què és TrueNAS?](#què-és-truenas)
    - [Característiques principals de TrueNAS](#característiques-principals-de-truenas)
5. [Què és una VPN i Tailscale?](#què-és-una-vpn-i-tailscale)
    - [Avantatges d'una VPN](#avantatges-duna-vpn)
    - [Què és Tailscale?](#què-és-tailscale)
6. [Monitorització amb Zabbix i Netdata](#monitorització-amb-zabbix-i-netdata)
    - [Què és Zabbix?](#què-és-zabbix)
    - [Què és Netdata?](#què-és-netdata)
7. [Alertes](#alertes)
    - [Alertes amb Zabbix](#alertes-amb-zabbix)
    - [Alertes amb Netdata](#alertes-amb-netdata)


    ## Manuals Pràctics

    1. [Com configurar Proxmox pas a pas](#com-configurar-proxmox-pas-a-pas)
    2. [Instal·lació i configuració de pfSense](#instal·lació-i-configuració-de-pfsense)
    3. [Com muntar un servidor NAS](#com-muntar-un-servidor-nas)
    4. [Guia per instal·lar i configurar TrueNAS](#guia-per-instal·lar-i-configurar-truenas)


# Ús d'un Hipervisor i Proxmox

## Per què s'utilitza un hipervisor?

Un hipervisor és un programari que permet crear i gestionar màquines virtuals (VMs). És especialment útil en entorns de hosting per les següents raons:

- **Optimització de recursos**: Permet executar múltiples VMs en un sol servidor físic, aprofitant millor els recursos disponibles.
- **Aïllament**: Cada VM funciona de manera independent, garantint que els problemes d'una no afectin les altres.
- **Escalabilitat**: Facilita l'escalat de recursos segons les necessitats del client.
- **Flexibilitat**: Permet executar diferents sistemes operatius en el mateix servidor físic.

## Què és Proxmox?

Proxmox és una plataforma de virtualització de codi obert que combina la gestió de màquines virtuals (basades en KVM) i contenidors (LXC). És molt utilitzada en hosting per les seves característiques:

- **Interfície intuïtiva**: Ofereix una interfície web fàcil d'utilitzar per gestionar VMs i contenidors.
- **Alta disponibilitat**: Suporta clústers per garantir la continuïtat del servei.
- **Còpies de seguretat**: Inclou eines integrades per fer snapshots i còpies de seguretat.
- **Codi obert**: És gratuït i té una comunitat activa que contribueix al seu desenvolupament.

Proxmox és una solució robusta i eficient per gestionar entorns virtualitzats en hosting i altres aplicacions empresarials.


## Què és pfSense?

pfSense és un firewall i router de codi obert basat en FreeBSD, àmpliament utilitzat per protegir i gestionar xarxes. És una solució molt flexible i potent, ideal tant per a petites empreses com per a grans entorns empresarials. Algunes de les seves característiques principals són:

- **Firewall avançat**: Ofereix un sistema de regles molt configurable per controlar el trànsit entrant i sortint.
- **VPN integrat**: Suporta protocols com IPsec i OpenVPN per establir connexions segures entre xarxes.
- **Gestió de banda ampla**: Permet prioritzar el trànsit amb QoS (Quality of Service).
- **Monitoratge i informes**: Proporciona eines per supervisar l'ús de la xarxa i generar informes detallats.
- **Extensible**: Admet plugins i paquets addicionals per ampliar les seves funcionalitats, com bloqueig de publicitat o detecció d'intrusions.

pfSense és una opció excel·lent per a aquells que busquen una solució de seguretat de xarxa fiable i personalitzable.


## Què és un servidor NAS?

Un servidor NAS (Network Attached Storage) és un dispositiu d'emmagatzematge connectat a una xarxa que permet als usuaris emmagatzemar i accedir a dades de manera centralitzada. És ideal per a entorns domèstics, petites empreses o grans organitzacions que necessiten compartir fitxers i fer còpies de seguretat de manera eficient.

### Avantatges d'un servidor NAS:
- **Centralització de dades**: Permet emmagatzemar tots els fitxers en un sol lloc accessible des de qualsevol dispositiu connectat a la xarxa.
- **Còpies de seguretat automatitzades**: Facilita la creació de còpies de seguretat regulars per protegir les dades.
- **Accés remot**: Ofereix la possibilitat d'accedir als fitxers des de qualsevol lloc mitjançant internet.
- **Escalabilitat**: Es pot ampliar fàcilment la capacitat d'emmagatzematge afegint més discos.
- **Compartició de fitxers**: Suporta protocols com SMB, NFS o FTP per compartir fitxers entre diferents sistemes operatius.
- **Eficiència energètica**: Consumeix menys energia que un servidor tradicional.

## Què és TrueNAS?

TrueNAS és una solució de codi obert per a servidors NAS basada en FreeBSD. És molt popular per la seva fiabilitat, flexibilitat i característiques avançades. TrueNAS està disponible en dues versions principals: **TrueNAS CORE** (gratuïta) i **TrueNAS Enterprise** (amb suport comercial).

### Característiques principals de TrueNAS:
- **Sistema de fitxers ZFS**: Ofereix integritat de dades, compressió, snapshots i replicació.
- **Còpies de seguretat i recuperació**: Permet fer snapshots i replicar dades a altres servidors per garantir la seguretat.
- **Interfície web intuïtiva**: Facilita la configuració i gestió del servidor NAS.
- **Suport per a múltiples protocols**: Compatible amb SMB, NFS, iSCSI, FTP i més.
- **Virtualització**: Inclou suport per executar màquines virtuals i contenidors.
- **Extensible**: Permet instal·lar plugins per afegir funcionalitats com Plex, Nextcloud o serveis de descàrrega.

TrueNAS és una opció excel·lent per a aquells que busquen una solució NAS robusta, segura i personalitzable, tant per a ús domèstic com empresarial.


## Què és una VPN i Tailscale?

Una VPN (Virtual Private Network) és una tecnologia que permet establir una connexió segura i xifrada entre dispositius a través d'internet. Això és especialment útil per accedir a recursos de xarxa de manera remota o protegir la privacitat en connexions públiques.

### Avantatges d'una VPN:
- **Seguretat**: Xifra les dades per protegir-les de possibles interceptacions.
- **Privacitat**: Oculta l'adreça IP i la ubicació de l'usuari.
- **Accés remot**: Permet connectar-se a xarxes privades des de qualsevol lloc.
- **Superació de restriccions geogràfiques**: Facilita l'accés a continguts bloquejats en determinades regions.

### Què és Tailscale?

Tailscale és una solució de VPN moderna que simplifica la connexió entre dispositius mitjançant una xarxa privada basada en WireGuard. És ideal per a accés remot i col·laboració en equips distribuïts.

#### Característiques principals de Tailscale:
- **Configuració senzilla**: No requereix configuracions complexes de xarxa o tallafocs.
- **Xarxa privada automàtica**: Crea una xarxa privada entre dispositius amb autenticació basada en identitats (com Google o Microsoft).
- **Accés remot segur**: Permet accedir a recursos de xarxa de manera segura des de qualsevol lloc.
- **Compatibilitat multiplataforma**: Funciona en Windows, macOS, Linux, Android i iOS.
- **Eficiència**: Utilitza WireGuard per oferir connexions ràpides i segures.
- **Gestió centralitzada**: Proporciona una interfície web per gestionar dispositius i permisos.

Tailscale és una opció excel·lent per a aquells que necessiten una VPN fàcil d'usar i eficient per accedir a recursos de xarxa de manera remota i segura.



## Monitorització amb Zabbix i Netdata

### Què és Zabbix?

Zabbix és una plataforma de monitorització de codi obert dissenyada per supervisar el rendiment i la disponibilitat de servidors, aplicacions, xarxes i altres dispositius. És àmpliament utilitzada per la seva flexibilitat i capacitat d'escalabilitat.

#### Característiques principals de Zabbix:
- **Supervisió en temps real**: Permet monitoritzar mètriques com ús de CPU, memòria, disc, trànsit de xarxa, entre d'altres.
- **Alertes configurables**: Envia notificacions per correu electrònic, SMS o altres canals quan es detecten problemes.
- **Panells personalitzables**: Ofereix visualitzacions gràfiques i informes detallats.
- **Escalabilitat**: Pot gestionar des de petites infraestructures fins a grans entorns empresarials.
- **Integracions**: Compatible amb una àmplia varietat de sistemes i aplicacions mitjançant agents o protocols com SNMP.

Zabbix és ideal per a aquells que necessiten una solució robusta i completa per supervisar infraestructures complexes.

### Què és Netdata?

Netdata és una eina de monitorització en temps real que proporciona informació detallada sobre el rendiment del sistema i les aplicacions. És coneguda per la seva facilitat d'ús i visualitzacions interactives.

#### Característiques principals de Netdata:
- **Monitorització en temps real**: Mostra mètriques amb una resolució d'un segon.
- **Interfície web interactiva**: Ofereix gràfics dinàmics i fàcils d'entendre.
- **Instal·lació senzilla**: Es pot instal·lar ràpidament en una gran varietat de sistemes operatius.
- **Baix impacte en el sistema**: Consumeix pocs recursos mentre recull dades detallades.
- **Integracions**: Es pot utilitzar conjuntament amb altres eines com Prometheus, Grafana o Elasticsearch.

Netdata és una excel·lent opció per a aquells que busquen una solució lleugera i fàcil d'implementar per monitoritzar sistemes en temps real.

Amb Zabbix i Netdata, es pot obtenir una visió completa i detallada de l'estat i el rendiment de la infraestructura, permetent una gestió proactiva i eficient.

## ALERTES

### Alertes amb Zabbix

Zabbix permet configurar alertes personalitzades per notificar als administradors quan es detecten problemes o anomalies en la infraestructura monitoritzada. Aquestes alertes són essencials per garantir una resposta ràpida i minimitzar l'impacte dels problemes.

#### Característiques de les alertes de Zabbix:
- **Notificacions multi-canal**: Les alertes es poden enviar per correu electrònic, SMS, Slack, o altres integracions.
- **Condicions configurables**: Es poden definir regles específiques per desencadenar alertes basades en mètriques o esdeveniments.
- **Escalabilitat**: Suporta l'escalat d'alertes a diferents nivells d'equip segons la gravetat del problema.
- **Historial d'alertes**: Manté un registre detallat de totes les alertes generades per facilitar l'anàlisi posterior.

### Alertes amb Netdata

Netdata també ofereix funcionalitats d'alertes per informar en temps real sobre problemes detectats en el sistema o aplicacions monitoritzades. Les alertes de Netdata són fàcils de configurar i estan dissenyades per ser immediates.

#### Característiques de les alertes de Netdata:
- **Alertes en temps real**: Notifica immediatament quan es detecta un problema.
- **Configuració senzilla**: Les regles d'alerta es poden definir fàcilment mitjançant fitxers de configuració.
- **Integracions**: Compatible amb sistemes de notificació com Slack, PagerDuty, o correu electrònic.
- **Flexibilitat**: Permet ajustar els llindars i condicions per a cada mètrica monitoritzada.

Amb aquestes eines, es pot garantir una supervisió proactiva i una resposta ràpida davant qualsevol incidència, millorant la fiabilitat i disponibilitat de la infraestructura.



