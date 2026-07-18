# Vanilla PHP Ticketing System

Custom MVC ticketing sustav razvijen u čistom (vanilla) PHP-u, bez korištenja vanjskih frameworka. Projekt demonstrira razumijevanje naprednih backend koncepata, arhitekture softvera i relacijskih baza podataka.

## 🚀 Tehnički naglasci (Architecture & Patterns)

- **Custom Front Controller (Router):** Svi HTTP zahtjevi usmjeravaju se kroz `public/index.php`, odvajajući logiku usmjeravanja od poslovne logike.
- **MVC Arhitektura:** Strogo odvajanje Modela (SQL logika), Kontrolera (obrada zahtjeva) i Pogleda (Tailwind CSS UI).
- **Sigurnost na prvom mjestu:** Korištenje **PDO Prepared Statements** za prevenciju SQL Injection napada i `htmlspecialchars()` za sprječavanje XSS-a.
- **Transakcije baze podataka (ACID):** Promjene statusa ticketa i dodavanje novih odgovora omotani su u MySQL transakcije (`BEGIN`, `COMMIT`, `ROLLBACK`) kako bi se osigurao integritet podataka u slučaju greške.
- **Custom Autoloader:** Dinamičko učitavanje klasa bez potrebe za ručnim `require` izjavama.

## 🛠️ Tehnološki Stack

- **Backend:** PHP 8+ (Vanilla/Čisti PHP)
- **Baza podataka:** MySQL / MariaDB
- **Frontend:** HTML5, Tailwind CSS (preko CDN-a za brzi prototip)

## 📦 Upute za pokretanje lokalno (Local Setup)

1. **Klonirajte repozitorij:**
   ```bash
   git clone [https://github.com/vas-username/ticketing-app.git](https://github.com/vas-username/ticketing-app.git)