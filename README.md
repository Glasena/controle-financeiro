# Finances Controll

**Personal Finance Data Importer** | Laravel 12 + PostgreSQL

---

## About the Project

Finances Controll is a personal project developed to automate my financial control routines.  
It imports credit card and bank account statement data from multiple institutions into a PostgreSQL database, enabling further analysis with third-party Business Intelligence (BI) tools.

The primary focus was **functionality and automation**, not UI or public distribution.

---

## Main Features

- Import credit card transactions (PDF) from:
  - Nubank
  - Banco do Brasil
  - XP Investimentos
- Import bank account statements (PDF) from:
  - Nubank
  - Banco do Brasil
  - XP Investimentos
- Parse and store extracted data into structured PostgreSQL tables
- Prepare data for analysis with external BI tools (e.g., Metabase, Power BI)

---

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2
- **Database:** PostgreSQL
- **PDF Parsing:** [smalot/pdfparser](https://github.com/smalot/pdfparser)
- **Local Development:** Laravel built-in server

---

## Installation and Setup

1. Clone the repository
```bash
git clone https://github.com/teu-usuario/finances-controll.git
cd finances-controll
```

2. Install dependencies
```bash
composer install
```

3. Configure your `.env` file
```bash
cp .env.example .env
php artisan key:generate
```
Set your database credentials for PostgreSQL in `.env`.

4. Run migrations
```bash
php artisan migrate
```

5. Start local server
```bash
php artisan serve
```

6. Access the app at `http://localhost:8000`

---

## Notes

- This project was developed for personal use.
- The main goal was to automate data extraction for financial analysis, not to create a full web application or public product.

---

# 🚀

**If you find this project helpful or inspiring, feel free to fork it!**
