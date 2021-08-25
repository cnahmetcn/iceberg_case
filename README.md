# Iceberg Real Estate Appointment System

## Technologies & Softwares Used

-   Laravel 8.x
-   PHP 7.4x
-   Tymon JWT
-   Google Maps Distance Matrix API
-   Postcode & Geolocation API for the UK (https://postcodes.io/)

## Installation & Run

-   git clone https://github.com/cnahmetcn/iceberg_case.git
-   composer install or (composer global update)
-   copy .env.example .env
-   php artisan key:generate
-   php artisan serve
-   php artisan migrate

# Request

-   **Postman Collection Link** https://documenter.getpostman.com/view/13154669/TzzEoZye

## Auth

### Register

-   POST -> http://127.0.0.1:8000/api/auth/register

| Key                   | Value           |
| --------------------- | --------------- |
| fullName              | Ahmet Can       |
| email                 | ahmet@gmail.com |
| password              | 1402657         |
| password_confirmation | 1402657         |
| phone                 | 05555555555     |

### Login

-   POST -> http://127.0.0.1:8000/api/auth/login

| Key      | Value           |
| -------- | --------------- |
| email    | ahmet@gmail.com |
| password | 1402657         |

**Response: token value**

### Me

-   POST -> http://127.0.0.1:8000/api/auth/me

**Auth: Bearer Token**

**Response**

```
{
    "id": 1,
    "fullName": "Ahmet Can",
    "email": "ahmet@gmail.com",
    "phone": null,
    "hold": 0,
    "created_at": "2021-08-23T14:06:47.000000Z",
    "updated_at": "2021-08-23T14:34:06.000000Z"
}
```

### Refresh (Token Refresh)

-   POST -> http://127.0.0.1:8000/api/auth/refresh

**Auth: Bearer Token**

**Response: token value**

### Logout

-   POST -> http://127.0.0.1:8000/api/auth/logout

**Auth: Bearer Token**

### Update

-   POST -> http://127.0.0.1:8000/api/auth/update/{id}

| Key                   | Value           |
| --------------------- | --------------- |
| fullName              | Ahmet Can       |
| email                 | ahmet@gmail.com |
| password              | 1402657         |
| password_confirmation | 1402657         |
| phone                 | 05555555556     |

**Auth: Bearer Token**

## Appointment

### All Appointment

-   GET -> http://127.0.0.1:8000/api/appointment/all

**Auth: Bearer Token**

### Show Appointment

-   GET -> http://127.0.0.1:8000/api/appointment/show/{id}

**Auth: Bearer Token**

### Add Appointment

-   POST -> http://127.0.0.1:8000/api/appointment/add

**Auth: Bearer Token**

| Key        | Value          |
| ---------- | -------------- |
| email      | adem@gmail.com |
| name       | Adem           |
| surname    | Can            |
| phone      | 333            |
| date       | 2021-08-25     |
| time       | 13:00          |
| address    | CM27PR         |
| created_by | 1              |

### Delete Appointment

-   DELETE -> http://127.0.0.1:8000/api/appointment/delete/{id}

**Auth: Bearer Token**

### Update Appointment

-   POST -> http://127.0.0.1:8000/api/appointment/update/{id}

**Auth: Bearer Token**

| Key        | Value          |
| ---------- | -------------- |
| email      | adem@gmail.com |
| name       | Adem           |
| surname    | Can            |
| phone      | 333            |
| date       | 2021-08-25     |
| time       | 13:00          |
| address    | CM27PR         |
| created_by | 1              |

### Done Appointment

-   GET -> http://127.0.0.1:8000/api/appointment/update/assignment/done/{id}

**Auth: Bearer Token**

| Key        | Value |
| ---------- | ----- |
| created_by | 1     |

### Change The Person Assigned To The Appointment

-   GET -> http://127.0.0.1:8000/api/appointment/update/assignment/{id}

**Auth: Bearer Token**

| Key        | Value |
| ---------- | ----- |
| created_by | 1     |
