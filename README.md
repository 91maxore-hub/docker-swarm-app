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

docker-swarm-app/

├── Dockerfile

├── index.html

├── contact_form.html

├── process_contact_form.php

├── style.css

└── .github/

    └── workflows/
        
        └── deploy.yml


![alt text](image.png)
![alt text](image-1.png)
![alt text](image-2.png)
![alt text](image-3.png)
![alt text](image-4.png)
![alt text](image-5.png)
![alt text](image-6.png)
![alt text](image-7.png)

Test