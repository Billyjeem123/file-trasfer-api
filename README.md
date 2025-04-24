# 📁 Laravel File Upload API (WeTransfer-style)

This is a Laravel 10+ backend API for uploading, storing, and sharing files securely via generated download links. 

## 🚀 Features
- Upload up to 5 files (max 100MB each)
- Generate download link
- Optional: Email notification
- Auto-expiry after set days
- Artisan command to clean expired uploads

## 🛠 Setup

```bash
git clone https://github.com/Billyjeem123/file-trasfer-api.git
cd file-trasfer-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

````

# Guide on usage: 
📤 Upload API
POST /api/upload
Form Data:

files[] (required)

expires_in (optional, in default 1 means it expires 1 day after upload

2 means it expires 2 days after upload)

email_to_notify (optional)

Returns:

````

{
  "success": true,
  "download_link": "http://file-transfer-api.test/api/download/{token}"
}
````


`````



🔽 Download API
GET /api/download/{token}
Returns the file if valid, expired otherwise.

🗑 Artisan Cleanup

php artisan clean:expired-uploads




``````

````


📘 Code Architeture


✅ Used service classes to handle business logic, keeping controllers clean and testable.

☁️ Used Laravel storage system for the Upload

📨 Email notifications handled via queues. Laravel data base queue(Used in this case).

# file-trasfer-api
