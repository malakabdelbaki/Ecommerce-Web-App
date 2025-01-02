# E-commerce Web App



## How to run?

### Clone this repo

```git
git clone https://github.com/malakabdelbaki/Ecommerce-Web-App.git
```

### run migrations
```bash
php artisan migrate 
php artisan db:seed
```

### Start Queue
```bash
php artisan queue:work --daemon
```

### To test user story 12: Admin Daily Email with Orders Spreadsheet
Please configure a tool like mailtrap or debugmail
run this command in terminal
```bash
php artisan email:daily-orders-report
```
