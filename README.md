# Prize Winner Registration System

A bilingual (Arabic/English) prize winner registration page built with PHP and MySQL.

## Features

- Bilingual form (Arabic & English)
- Collects: Name (Arabic), Name (English), IBAN Number, Employee ID
- Saves data to MySQL database
- Shows congratulations modal after successful submission
- Responsive design with modern UI

## Installation

1. **Start XAMPP**
   - Start Apache and MySQL services

2. **Setup Database**
   - Open your browser and navigate to: `http://localhost/prize/setup_database.php`
   - This will create the database `prize_db` and the `winners` table

3. **Access the Application**
   - Navigate to: `http://localhost/prize/`
   - Fill in the form and submit

## Database Configuration

Default settings (can be modified in `index.php`):
- Host: `localhost`
- Database: `prize_db`
- Username: `root`
- Password: `` (empty)

## Database Structure

**Table: winners**
- `id` - Auto increment primary key
- `name_arabic` - Winner's name in Arabic
- `name_english` - Winner's name in English
- `iban_number` - IBAN number
- `employee_id` - Employee ID
- `created_at` - Timestamp of entry

## Files

- `index.php` - Main application file (form and processing)
- `setup_database.php` - Database setup script (run once)
- `README.md` - This file

## Usage

1. Open the application in your browser
2. Fill in all required fields
3. Click Submit
4. A congratulations modal will appear with the winner's name
5. Click OK to return to the form

## Technologies Used

- PHP 7.4+
- MySQL/MariaDB
- PDO for database operations
- HTML5/CSS3
- Responsive design
