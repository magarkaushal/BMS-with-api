# 📰 Blog Management System with api - Laravel 12

A role-based content management system built with **Laravel 12**, featuring:
- User authentication via **Sanctum**
- Role and permission system via **Spatie Laravel Permission**
- Post and category management with validation and access control
- **Excel import/export** support for Posts & Categories using **Maatwebsite Excel**
- RESTful API with **API Resources** and **Form Requests**

---

## 📦 Features

### ✅ Entities

- **User**
- **Post**: `title`, `body`, `category_id`, `author_id`
- **Category**: `name`, `slug`
- **Permissions** via Spatie

---

### 🔐 1. Authentication & Authorization

- Sanctum-based token authentication
- Roles: `admin`, `editor`
- Permissions:
  - `post-create`, `post-edit`, `post-delete`
  - `category-manage`, `user-manage`
- Access control using middleware (`auth:sanctum`, `can:`)

---

### 📝 2. Post Management

- Create, update, delete via API (only if permitted)
- Posts are linked to categories and authors
- Admin or post author can update/delete the post

---

### 🗂 3. Category Management

- Only `admin` can manage categories
- Prevent deletion if category has related posts
- Excel import/export support

- ### 🗂 4. User Management
- only `admin` can manage users
- Admin can create/update/delete and also change the roles for the users via API

 ###📤 5. Excel Import/Export
- Use Maatwebsite Laravel Excel
- Export:/api/categories/export → categories.xlsx
- /api/posts/export → posts.xlsx (handles large exports 1000+)
- Import:/api/categories/import
- accepts: name, slug

###⚙️ Setup Instructions
### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/bms.git
cd bms

2. Install Dependencies

composer install
npm install && npm run build

3. Environment Setup
cp .env.example .env
php artisan key:generate
Edit .env with your database configuration:
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_password

4. Migrate & Seed

php artisan migrate --seed
✅ Includes admin user and default permissions via seeders.

🔐 Login Credentials

Email: admin@admin.com
Password: password

📤 Import/Export Instructions
➕ Import Posts / Categories (Excel)
Go to /posts or /categories
Use the Import form to upload .xlsx, .xls, or .csv
The file must contain headers matching:
Title, Body, Category, Author for posts
Name, Slug for categories

📥 Export Posts / Categories (Excel)
Click the Export button from the Posts or Categories page
A file named posts.xlsx or categories.xlsx will be downloaded
Exports support large datasets (1000+ rows)


### 🛡 6. Roles & Permissions
Managed using Spatie Laravel Permission.

Admin: full access
Editor: limited to their own posts

⚙️ API Endpoints Overview
All endpoints are protected via Sanctum Auth.
Method	Endpoint	Description These are the some of the api endpoints list::::
GET	/api/posts	List posts
POST	/api/posts	Create post
PUT	/api/posts/{id}	Update post
DELETE	/api/posts/{id}	Delete post
GET	/api/categories	List categories
GET	/api/posts/export	Export to Excel
POST	/api/posts/import	Import from Excel


🎯 Bonus Features
Single-page frontend (Blade + Axios)
FormRequest validation
API Resource formatting
Efficient Excel export for 1000+ posts

📚 Tech Stack
Laravel 12
Sanctum
Spatie Laravel Permission
Maatwebsite Excel
Axios for API requests
Blade templates


