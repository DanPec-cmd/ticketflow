# 🎫 PHP Ticketing System (Custom Architecture)

Sustav za upravljanje korisničkim upitima (ticketing) izgrađen od nule pomoću modernog PHP-a (OOP). 

Ovaj projekt nije rađen pomoću gotovih rješenja (poput Laravela ili Symfonyja). Umjesto toga, razvijen je kao **custom MVC sustav** kako bi demonstrirao razumijevanje objektnog programiranja, dizajn uzoraka i modernih PHP standarda ispod haube.

## 🚀 Ključne funkcionalnosti
- **Sustav uloga (RBAC):** Različite razine pristupa za Klijente i Project Managere (PM).
- **Upravljanje ticketima:** Kreiranje, čitanje i odgovaranje na tickete.
- **Dodjeljivanje agenata:** PM može preuzimati ili dodjeljivati tickete specifičnim agentima.
- **Upravljanje statusima:** Otvoreno, U tijeku, Riješeno.

## 🛠️ Arhitektura i Tehnologije

Projekt prati **Solid** principe i industrijske standarde za pisanje održivog PHP koda:

- **PSR-4 Autoloading:** Upravljanje klasama putem Composera. Nema ručnog `require`anja datoteka.
- **Dependency Injection (Container):** Centralizirano upravljanje instancama (Baza podataka, Modeli) čime su klase oslobođene tvrdog kodiranja ovisnosti i spremne za testiranje.
- **Custom Router:** Dinamičko mapiranje URL-ova na metode kontrolera (`GET`, `POST`).
- **Middleware (AuthGuard):** Centralizirana provjera autentifikacije i rola prije pristupa samim kontrolerima.
- **Single Responsibility (Validator):** Izdvojena klasa za validaciju podataka koja osigurava "mršave" kontrolere.
- **MVC Obrazac:** Striktno odvajanje logike rute/kontrolera, manipulacije podacima (Model) i prezentacije (View).

## 🔒 Sigurnost
Sigurnosne mjere su ugrađene u samu bazu arhitekture:
- **SQL Injection:** Isključivo korištenje PDO *Prepared Statements* za sve upite prema bazi.
- **CSRF Zaštita:** Automatsko generiranje i provjera CSRF tokena unutar Routera za sve `POST` zahtjeve.
- **XSS Prevencija:** Sigurno ispisivanje korisničkih unosa putem `htmlspecialchars`.
- **Sigurnost lozinki:** Korištenje ugrađenog `password_hash()` (Bcrypt) algoritma.

## 📂 Struktura projekta


/
├── src/
│   ├── Controllers/   # HTTP upravljanje (AuthController, TicketController)
│   ├── Core/          # Temelj sustava (Router, Container, Validator, AuthGuard)
│   ├── Models/        # Poslovna logika i interakcija s bazom (User, Ticket)
│   └── Views/         # HTML predlošci
├── composer.json      # Konfiguracija i PSR-4 mapiranje
├── index.php          # Entry point (Bootstrap aplikacije)
└── baza.sql           # Dump baze podataka

⚙️ Instalacija i pokretanje

Za pokretanje ovog projekta lokalno, potrebno je imati instaliran PHP 8.0+, MySQL i Composer.

1. Kloniraj repozitorij

git clone [https://github.com/TVOJE-KORISNICKO-IME/tvoj-repo.git](https://github.com/TVOJE-KORISNICKO-IME/tvoj-repo.git)
cd tvoj-repo

2. Instaliraj ovisnosti (Autoloader):

composer install

3. Pripremi bazu podataka:

Kreiraj novu MySQL bazu (npr. elatus_tickets.sql).

Importaj SQL datoteku  u svoju bazu kako bi kreirao tablice.

Podesi konekciju prema bazi u .env.example

4. Pokreni lokalni server:

php -S localhost:8000 -t public
