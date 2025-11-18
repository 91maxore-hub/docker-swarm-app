I detta projekt har jag byggt en skalbar och robust miljö för en webbapplikation med Docker Swarm på AWS. Miljön består av tre virtuella EC2-servrar, där en fungerar som manager och två som worker-noder. Applikationen, som är utvecklad med HTML, PHP och CSS, körs i tre separata containrar – en på varje server – vilket ger hög tillgänglighet och enkel skalning.

För att hantera inkommande trafik och säkerställa säkra anslutningar har jag implementerat Traefik som reverse proxy med stöd för HTTPS. För övervakning och visualisering av klustret används Docker Visualizer, vilket ger en tydlig överblick över vilka containrar som körs på vilka noder. Dessutom har jag kopplat CI/CD via GitHub, vilket gör att uppdateringar av applikationen automatiskt byggs och distribueras till klustret.

Denna lösning visar hur containerteknologi och molninfrastruktur kan kombineras för att skapa en flexibel, skalbar och lättunderhållen webbmiljö, samtidigt som den säkerställer säkerhet, pålitlighet och tydlig översikt över klustrets status.

Noterbart är att i detta projekt har jag utnyttjat följande molntjänster från AWS:

* **EC2 (Elastic Compute Cloud):** Tre virtuella servrar används för att köra Docker Swarm – en som manager och två som worker-noder.
* **GitHub:** För CI/CD, vilket möjliggör automatiska bygg och deployment av webbapplikationen.

Tillsammans skapar dessa tjänster en skalbar, flexibel och säker miljö för webbapplikationen.

| **Komponent**              | **Beskrivning**                                                             | **Användningsområde**                                 | **Kommentar**                                                            |
| -------------------------- | --------------------------------------------------------------------------- | ----------------------------------------------------- | ------------------------------------------------------------------------ |
| **EC2-servrar**            | Virtuella servrar i AWS som utgör infrastrukturen för Docker Swarm-klustret | Körning av applikationens containrar och Swarm-noder  | En server som manager, två som workers för skalbarhet och redundans      |
| **Docker Swarm**           | Containerorkestreringssystem som hanterar distribution av containrar        | Säkerställer att applikationen körs i flera noder     | Gör applikationen skalbar och tillgänglig även vid noder som går ner     |
| **Applikationscontainrar** | Containeriserad webbapplikation (HTML, PHP, CSS)                            | Kör själva webbapplikationen på Swarm-servrarna       | En container per server för hög tillgänglighet                           |
| **Traefik**                | Reverse proxy och load balancer med HTTPS-stöd                              | Hantering av inkommande trafik och säkra anslutningar | Automatiserar certifikat via HTTPS och styr trafiken till rätt container |
| **GitHub CI/CD**           | Automatiserat bygg- och deployflöde                                         | Uppdateringar och deployment av applikationen         | Säkerställer att nya versioner distribueras snabbt och pålitligt         |
| **Docker Visualizer**      | Grafiskt verktyg som visar status och fördelning av containrar i Swarm      | Övervakning och visualisering av Swarm-klustret       | Hjälper till att se vilka containrar som körs på vilka noder i realtid   |

**Regler för säkerhetsgruppen `docker-swarm-sg`**

| **Tillämpning / Resurser**   | **Tillåtna portar** | **Protokoll** | **Syfte**                                          |
| ---------------------------- | ------------------- | ------------- | -------------------------------------------------- |
| EC2-servrar i Swarm-klustret | 22                  | TCP           | SSH-åtkomst för administration                     |
| EC2-servrar i Swarm-klustret | 80                  | TCP           | HTTP-trafik till webbapplikationen                 |
| EC2-servrar i Swarm-klustret | 443                 | TCP           | HTTPS-trafik till webbapplikationen                |
| EC2-servrar i Swarm-klustret | 4789                | UDP           | Overlay Network för Swarm-tjänster                 |
| EC2-servrar i Swarm-klustret | 7946                | TCP           | Swarm intern kommunikation                         |
| EC2-servrar i Swarm-klustret | 7946                | UDP           | Swarm intern kommunikation                         |
| EC2-servrar i Swarm-klustret | 2377                | TCP           | Swarm management trafik mellan manager och workers |
| EC2-servrar i Swarm-klustret | 8080                | TCP           | Traefik – reverse proxy med dashboard              |
| EC2-servrar i Swarm-klustret | 8081                | TCP           | Docker Visualizer dashboard                        |

**Mapp struktur**

| Katalog / Fil                    | Typ  | Beskrivning                                          |
| -------------------------------- | ---- | ---------------------------------------------------- |
| **docker-swarm-app/**            | Mapp | Rotmappen för hela projektet.                        |
| ├── **Dockerfile**               | Fil  | Bygger Docker-imagen och definierar miljö/beroenden. |
| ├── **index.html**               | Fil  | Huvudsidan för webbapplikationen.                    |
| ├── **contact_form.html**        | Fil  | Sida med kontaktformulär.                            |
| ├── **process_contact_form.php** | Fil  | PHP-script som hanterar formulärdata.                |
| ├── **style.css**                | Fil  | CSS-stilmall för webbplatsen.                        |
| ├── **.github/workflows/**       | Mapp | Mapp för GitHub Actions workflows.                   |
|      └── **deploy.yml**          | Fil  | Workflow som hanterar CI/CD och deployment.          |


# Provisionera Amazon EC2-server

Denna guide beskriver hur man provisionerar Amazon EC2-instanser som ska ingå i ett Docker Swarm-kluster. Målet är att skapa en stabil och skalbar miljö med en Swarm Manager och två Swarm Workers. EC2-instanserna kommer att konfigureras med nödvändig nätverksåtkomst, säkerhetsgrupper och grundläggande systemkrav för att stödja containerorkestrering med Docker Swarm.

**Steg 1: Bege dig till aws.amazon.com**

![alt text](image.png)

**Steg 2: Ange EC2 i sökrutan och välj "EC2 - Virtual Servers in the Cloud"**

![alt text](image-1.png)

**Steg 3: Välj "Launch Instance"**

![alt text](image-2.png)

**Steg 4: Ange ett namn för din server, operativsystem (AMI), instanstyp, samt skapa SSH-nyckel för säker åtkomst.**

![alt text](image-3.png)

**Steg 5: Välj sedan säkerhetsgruppen (docker-swarm-sg) som ansvarar för vilka portar som ska användas för vårt Docker Swarm-kluster. Resten kan lämnas som det är.**

![alt text](image-4.png)

**Steg 6: Gå sedan längst ner till Advanced details -> User data och klistra in följande:**

```bash
#!/bin/bash
dnf update -y
dnf install -y docker
systemctl enable --now docker
usermod -aG docker ec2-user
```

![alt text](image-5.png)

**Steg 6: Du får sedan en kort översikt över EC2-servern längst upp till höger. Välj Launch instance.**

![alt text](image-8.png)

**Repetera nu likadant för swarm-worker-1 och swarm-worker-2**

**Steg 7: Du bör nu se en översikt som nedan för samtliga EC2-servrar som kommer används i vårt Docker Swarm-kluster.**

![alt text](image-6.png)

# Skapandet av ett Docker Hub-repository

Innan vi initierar Docker Swarm-klustret är det viktigt att vi förbereder den applikations-image som klustret ska använda. Eftersom min app är PHP-baserad behöver projektet först paketeras i en Docker-image och publiceras på Docker Hub, så att Swarm-noderna kan dra ned samma version av imagen oavsett vilken nod som kör tjänsten.

När initieringen av Docker Swarm-klustret senare är klar kommer vi att kunna deploya stacken direkt från denna image. Men för att detta ska fungera måste vi först skapa ett repository på Docker Hub som ska lagra och distribuera Docker-imagen. I mitt fall döpte jag detta repository till **docker-swarm-app** (se bilden nedan).

Därefter behöver vi skapa en **Dockerfile** som använder en PHP-image med inbyggd webbserver, vi kommer att använda `php:8.2-apache`, och som kopierar in alla projektets filer. Denna image byggs sedan lokalt och pushas upp till Docker Hub så att den kan användas av hela Swarm-klustret vid deployment.

![alt text](image-10.png)

## Följ stegen nedan för att skapa ett **Docker Hub-repository**

**Steg 1: Logga in på Docker Hub:**

Gå till [https://hub.docker.com/repositories/ditt-användarnamn](https://hub.docker.com/repositories/ditt-användarnamn)

**Steg 2: Navigera till dina repositories:**

Du kommer direkt till listan över repositories under ditt konto. 

![alt text](image-11.png)

**Steg 3: Skapa ett nytt repository:**

Klicka på **"Create a Repository"** längst bort till höger.

![alt text](image-12.png)

**Steg 4: Fyll i repository-information:**

- **Repository Name:** Ange ett namn för ditt repo, t.ex. `docker-swarm-app` kommer bli **ditt-användarnamn**/`docker-swarm-app` senare när du ska bygga och pusha Docker-image  
- **Visibility:** Välj om ditt repo ska vara **Public** eller **Private**  
- **Description:** Lägg till en kort beskrivning om av vad repot innehåller  
- Klicka på **"Create"**

![alt text](image-13.png)

# Skapandet av Dockerfile

Jag skapade därefter en Dockerfile som använder PHP 8.2 med Apache och kopierar in mina applikationsfiler från projektmappen.
**Kortfattat:** en Dockerfile är en fil som beskriver hur ens Docker-image ska byggas.

**Dockerfile** (docker-swarm-app/Dockerfile) gör följande:

1. Använder officiell PHP 8.2 med Apache som grundimage.
2. Aktiverar Apache-modulen `mod_rewrite` för att möjliggöra URL-omskrivningar.
3. Kopierar alla applikationsfiler från projektmappen till Apache:s webbroot (`/var/www/html/`).
4. Exponerar port 80 så att webbservern kan ta emot HTTP-trafik.

# Byggandet av Docker Image och uppladdning till Docker Hub

### Nu är det dags att gå igenom stegen för att paketera projektet i en Docker-image och publicera den på Docker Hub

**Steg 1: Byggandet av Docker Image**

Jag använde terminalen i Visual Studio Code och angav följande kommando utifrån projektmappen (där appens samtliga filer finns) för att bygga mina applikations-filer till en Docker-image och ge den en tagg.  

**91maxore** = användarnamn  
**docker-swarm-app** = repo på Docker Hub

```bash
docker build -t 91maxore/docker-swarm-app:latest .
```

**Steg 2: Logga in på Docker Hub**

Logga in på Docker Hub via terminalen:
```bash
docker login
```

- Angav mitt användarnamn och lösenord som jag använder till Docker Hub.

**Steg 3: Pusha Docker-image till Docker Hub**

När imagen är byggd och du är inloggad, pusha imagen till Docker Hub med:
```bash
docker push 91maxore/docker-swarm-app:latest
```

Detta pushar min nyskapade Docker-image till Docker Hub och är redo för användning.  
Nu ligger den på Docker Hub:

🔗 https://hub.docker.com/repository/docker/91maxore/docker-swarm-app

När man skapar eller uppdaterar en Docker Swarm-service skickar manager-noden instruktionen till alla workers.
Om ens worker inte har den image-version som behövs, hämtar den automatiskt (pull) imagen från Docker Hub eller den angivna registry.
Man behöver alltså inte göra pull manuellt på varje worker.

# Initiering av Docker Swarm

För att initiera ett Docker Swarm-kluster, anslut till din swarm-manager via SSH och kör kommandot nedan som startar klustret och utser noden till manager.

**Steg 1: Börja med att SSHa in på vår nyskapade EC2 genom att ange:**
```bash
ssh -i ~Downloads/swarm-manager-key.pem ec2-user@34.246.185.128
```
**Notera att du får ändra sökvägen till din SSH-nyckel, samt den publika IP-adress till din EC2-instans**

![alt text](image-7.png)

**Steg 2: Initiera Docker Swarm-kluster på manager genom att köra följande:**

```bash
docker swarm init --advertise-addr 34.246.185.128
```

**Notera att du får byta ut IP-adressen mot den publika IP som din swarm-manager har***
- Kopiera nu kommandot med dess token som skrivs ut för att ansluta våra övriga worker-noder, du bör få något som ser ut så här:

```bash
docker swarm join --token SWMTKN-1-1qb2x87bw5wx75p5opwk8qqqoy513l2piskjrcze19acy8da3c-ec79bgjfs3q8doy3cpw3306js 172.31.23.10:2377
```

# Anslutning av worker-noder via SSH på Swarm Worker 1 och Swarm Worker 2

**Steg 1: Kör nu följande för att ansluta swarm-worker-1 och swarm-worker-2 till Docker Swarm-klustret:**

```bash
docker swarm join --token SWMTKN-1-1qb2x87bw5wx75p5opwk8qqqoy513l2piskjrcze19acy8da3c-ec79bgjfs3q8doy3cpw3306js 172.31.23.10:2377
```

- Notera att du får byta ut den token du får

**Steg 2: Verifera sedan på swarm-manager att worker-noderna har lagts till i klustret genom att ange:**

```bash
docker node ls
```

**Steg 3: Du bör då se något liknande:**

![alt text](image-9.png)

- Detta bekräftar att vårt Docker Swarm-kluster är nu skapad med 1 manager och 2 workers.

**Steg 4: Skapa Docker Compose-fil**

- En **docker-stack.yml** behövs för att definiera hela applikationens tjänster, nätverk och inställningar på ett och samma ställe, så att Docker Swarm kan deploya och hantera allt som en komplett stack.
- På swarm-manager, skapa stackfilen **docker-stack.yml** och klistra in följande:

```bash
version: "3.8"
services:
  web:
    image: 91maxore/docker-swarm-app:latest
    deploy:
      replicas: 3
      restart_policy:
        condition: on-failure
      update_config:
        parallelism: 1
        delay: 5s
    ports:
      - "80:80"
    networks: [webnet]

networks:
  webnet:
    driver: overlay
```

**Beskrivning**

* Kör `91maxore/docker-swarm-app` som en Swarm-tjänst med 3 repliker.
* Startar om repliker automatiskt vid fel.
* Exponerar tjänsten på port 80.
* Använder overlay-nätverk (`webnet`) så att tjänsten kan kommunicera med andra tjänster i klustret.

**Steg 5: Distribuera Docker Swarm-stacken genom att ange följande:**

```bash
sudo docker stack deploy -c docker-stack.yml docker-swarm-app
```

- docker-swarm-app blir namnet på stacken eftersom vår stack kommer i slutändan innehålla flera tjänster: web, viz och traefik
- samtliga tjänster kommer befinnas sig på följande benämningar: docker-swarm-app_web, docker-swarm-app_viz och docker-swarm-app_traefik

**Steg 6: Vi kan nu kontrollera statusen för varje instans av webbapplikationen, se på vilken nod de körs och verifiera att alla tre repliker fungerar som de ska. Detta görs med följande kommando:**

```bash
sudo docker service ps docker-swarm-app_web
```

- Webbapplikationen kör nu stabilt och som förväntat på alla tre noder i Swarm-klustret, vilket bekräftar att deploymenten fungerar korrekt.
- Kort sagt: bilden visar var och hur min web-app körs inom Swarm-klustret
- Det fungerar på samma sätt genom att senare ange docker-swarm-app_viz för Docker Vizualizer och docker-swarm-app_traefik för Traefik som hanterar reverse proxy + https

![alt text](image-17.png)

**Steg 7: Vi kan även verifiera att swarm-worker 1 och swarm-worker 2 tagit del av samma docker-image och att webbapplikationen är replikerad utöver alla 3 noder med följande kommando:**

```bash
docker service ls
```

![alt text](image-14.png)

- Som du kan se kör mitt Docker Swarm-kluster även Traefik för reverse proxy och HTTPS-hantering, och detta kommer jag att gå igenom detta mer detaljerat senare i guiden.
- Dessutom kör mitt Docker Swarm-kluster även Docker Visualizer för att enkelt kunna se noder, tjänster och containrar i realtid, och detta kommer jag att gå igenom mer detaljerat i nästa steg.

# Docker Vizualiser
Docker Swarm Visualizer är ett verktyg som ger en grafisk översikt över ditt Docker Swarm-kluster.
Det visar alla noder, både manager och worker, samt vilka containrar som körs på respektive nod i realtid.
Vizualizer är ett utmärkt sätt att snabbt förstå klustrets struktur, övervaka distributionen av tjänster och kontrollera att skalning och repliker fungerar som förväntat.

**Steg 1: Börja med att addera följande till docker-stack.yml som vi skapade tidigare för att lägga till Viazualiser som tjänst till vår stack:**

- Eftersom Docker Vizualiser är en tjänst listar vi även den under **services** som nedan.

```bash

services:
  viz:
    image: dockersamples/visualizer:stable
    deploy:
      placement:
        constraints:
          - node.role == manager
    ports:
      - "8081:8080"                   # Visualizer-webbgränssnitt
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock
    networks:
      - webnet

networks:
  webnet:
    driver: overlay
```

**Beskrivning**
- Kör Visualizer som en Swarm-tjänst på manager-noden.
- Mountar Docker-socket för att kunna läsa klustrets noder och containrar.
- Exponerar Visualizer på port 8081
- Använder overlay-nätverk så den kan kommunicera med andra tjänster om det behövs.

**Steg 2: Deploya återigen stacken genom att köra detta på manager-noden:**

```bash
docker stack deploy -c docker-stack.yml docker-swarm-app
```

**Steg 3: Kontrollera att tjänsten körs**

```bash
docker service ps docker-swarm-app_viz
```

![alt text](image-15.png)

**Steg 4: Öppna Visualizer**

- Surfa in till managers publika IP följt av port 8081, alltså i mitt fall: http://34.246.185.128:8081
- Du ser alla noder och containrar i ditt Swarm-kluster visuellt.

**Sammanfattningsvis:**
- Visualizer körs som en separat service på manager, exponerar ett webbläsargränssnitt och visar i realtid alla noder och containrar i Swarm-klustret.

![alt text](image-16.png)

**Beskrivning av de tre tjänsterna** i min stack:

* **docker-swarm-app_web** (Web-applikation)
  Min webbapplikation som körs i Swarm. Den hanterar själva innehållet, som HTML och PHP, och kan skalas över flera noder.

* **docker-swarm_viz (Docker Swarm Visualizer)**
  Ett grafiskt verktyg som visar **Swarm-klustret i realtid**, inklusive noder och containrar. Den hjälper dig att övervaka distribution och repliker.

* **docker-swarm_traefik (Traefik)**
  En modern reverse proxy och load balancer som hanterar inkommande trafik. Den styr HTTPS, certifikat via Let's Encrypt, och distribuerar trafiken till dina tjänster i Swarm (som min web-applikation)