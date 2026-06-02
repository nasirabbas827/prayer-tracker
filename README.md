# prayer‑tracker‑final  

A lightweight PHP web application that lets users record, view, and manage their daily prayers. Administrators can moderate user accounts, respond to feedback, and maintain guidance content—all through a simple, responsive interface.

---

## Overview  

The **prayer‑tracker‑final** project provides:

* A personal prayer log for individual users.  
* A public guidance page with verses and tips.  
* An admin dashboard for managing users, guidance, and support messages.  
* Basic authentication (login / register) and profile editing.  

All functionality is built with plain PHP, MySQL, and a minimal CSS stylesheet.

---

## Features  

| User Side | Admin Side |
|-----------|------------|
| Register / login | Secure admin login |
| Log daily prayers (including *qaza* prayers) | View & reply to user feedback |
| Edit personal profile | Add / edit / delete guidance entries |
| Browse guidance content | Manage user accounts (activate, deactivate, delete) |
| Contact support form | Export or delete support messages |
| Responsive navigation bar | Logout / session handling |
| Simple, clean UI (CSS) | Centralized configuration (`config.php`) |

---

## Tech Stack  

| Layer | Technology |
|-------|------------|
| Backend | PHP 7.4+ |
| Database | MySQL (schema in `Database/prayer_db.sql`) |
| Front‑end | HTML5, CSS3 (see `css/style.css`) |
| Server | Apache / Nginx (any LAMP/LEMP stack) |
| Version control | Git (GitHub) |

---

## Installation  

1. **Clone the repository**  

   ```bash
   git clone https://github.com/yourusername/prayer-tracker-final.git
   cd prayer-tracker-final
   ```

2. **Create the database**  

   ```sql
   -- In MySQL client or phpMyAdmin
   SOURCE Database/prayer_db.sql;
   ```

3. **Configure the application**  

   *Copy the sample config and edit the credentials.*  

   ```bash
   cp config.sample.php config.php
   ```

   Edit `config.php` (and `admin/config.php` if you keep a separate admin DB) to match your environment:

   ```php
   <?php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'prayer_tracker');
   define('DB_USER', 'YOUR_DB_USERNAME');
   define('DB_PASS', 'YOUR_DB_PASSWORD');
   ?>
   ```

4. **Set up the web server**  

   - Place the project folder inside your web root (e.g., `/var/www/html/prayer-tracker-final`).  
   - Ensure the server has permission to read the files and write session data.  
   - Enable PHP processing for `.php` files.

5. **Optional: Secure the admin area**  

   - Move `admin/` outside the public web root and adjust the `include` paths in admin scripts, or protect the directory with `.htaccess` rules.

6. **Run the application**  

   Open a browser and navigate to `http://localhost/prayer-tracker-final/`.  
   Register a new user or log in with the default admin credentials (set in `admin/config.php`).

---

## Usage  

### User workflow  

1. **Register / Login** – `register.php` & `login.php`.  
2. **Log a prayer** – `home.php` (or `qaza_prayers.php` for missed prayers).  
3. **View guidance** – `guidance.php`.  
4. **Update profile** – `update_profile.php`.  
5. **Contact support** – `contact_support.php`.  

### Admin workflow  

1. **Login** – `admin/admin_login.php`.  
2. **Dashboard** – `admin/admin_home.php`.  
3. **Manage users** – `admin/manage_users.php`.