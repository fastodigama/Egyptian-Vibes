# Egyptian-Vibes Admin Panel

The **Egyptian-Vibes Admin Panel** is a lightweight, PHP-based content management system designed for managing products, categories, users, and product media. It provides secure authentication, CRUD operations, and a responsive interface built with Bootstrap 5.

---

## Table of Contents

- [Project Overview](#project-overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [Usage](#usage)
- [Folder Structure](#folder-structure)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)

---

## Project Overview

This project enables administrators to manage products, categories, and users efficiently. Key functionalities include:

- Product management with multiple images and categories.
- Category management with CRUD operations.
- User management with secure login, password hashing, and role-based access.
- Bootstrap-powered responsive admin interface.
- Flash messages for user feedback.
- Image upload with automatic resizing and storage in the database.

---

## Features

- **User Authentication**
  - Secure login system with session management.
  - Passwords hashed using MD5 (consider upgrading to stronger hashing for production).

- **Product Management**
  - Add, edit, and delete products.
  - Assign multiple categories to each product.
  - Upload multiple product images.
  - Thumbnail preview for products.

- **Category Management**
  - Add, edit, and delete categories.
  - Link categories to products.
  - Consistent UI with confirmation modals for deletion.

- **User Management**
  - Add, edit, delete admin users.
  - Control user activation status.
  - Bootstrap-styled forms and tables.

- **Responsive UI**
  - Fully responsive layout using Bootstrap 5.
  - Accessible buttons and forms.
  - Consistent design for CRUD pages.

---

## Technology Stack

- **Backend:** PHP 7.x+
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3, Bootstrap 5, Bootstrap Icons
- **Security:** Session-based authentication, input sanitization
- **Optional Tools:** JSDOM, Fetch (for XML handling, if extended)

---

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/yourusername/Egyptian-Vibes.git

2. Import the database schema:
    CREATE DATABASE egyptian_vibes;
    USE egyptian_vibes;

    -- Import SQL tables for users, products, categories, product_photos, and product_category

3. Configure the database connection:

    // includes/config.php
    $connect = mysqli_connect('localhost', 'username', 'password', 'egyptian_vibes');
    if (!$connect) {
    die("Database connection failed: " . mysqli_connect_error());
    }

## Security

-Session-based authentication with the secure() function.
-Input sanitization using mysqli_real_escape_string and type casting.
-Passwords hashed using MD5.
-Deletion actions require explicit confirmation to prevent accidental data loss.