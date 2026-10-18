# Flight Booking Website

A full-stack flight booking system with three kinds of users: **passengers** who search and book flights, **airline companies** who publish and manage their flights, and a **messaging** feature between them. Built as a university team project (6 contributors) with **PHP, MySQL, HTML, CSS and JavaScript**.

## 🧩 My Contribution (Amira): Airline Company Module
I built the airline-company side of the platform, frontend and backend.

**Frontend** (`Frontend/views/company/`, `Frontend/css/company/`)
- Company dashboard (`index.php`) and company profile (`profile.php`, `edit-profile.php`) with profile editing.
- Flights list (`flight.php`) and the **Add New Flight** form (`add-flight.php`): flight code, name, itinerary (cities), departure and arrival date and time, max passengers and ticket price.
- Styling for the whole company area (`company-styles.css`).

**Backend** (`Backend/`)
- `models/Company.php`, `controllers/ProfileController.php` and `services/profile.php` for company profile data.
- Flight management for companies: `models/flight_data_class.php`, `services/flight_service_class.php` and `controllers/flight_controller_class.php`.

My commits: `9f58dec`, `3c19638`, `a3659c4`

## ✨ Features
- Passenger and company registration and login
- Search flights, view flight details and book (passenger side)
- Company dashboard: manage the company profile and its flights
- Messages between users

## 👥 Team
| Member | Main work |
|---|---|
| **Amira** | Airline-company module (frontend and backend), company flight management |
| **Malak** ([@MalakkSherif](https://github.com/MalakkSherif)) | Project structure, database schema, flight search, flight details, booking |
| **Basmala** ([@basmala352004](https://github.com/basmala352004)) | Messaging, home page, navigation bar, UI styling |
| **Emy** ([@Emy-04](https://github.com/Emy-04)) | Authentication (registration and login) |
| **Raghad** ([@Raghad-r4](https://github.com/Raghad-r4)) | Passenger module |
| **Basant** | Contributed files to the project |

## 🗄️ Database
MySQL database `Flight-Booking-System` with the tables `users`, `companies`, `passengers`, `flights`, `bookings` and `messages` (schema in `Backend/database/flight-booking-system.sql`).

## 🚀 How to Run
Requires Apache with PHP and MySQL (for example XAMPP).
1. Place the project in your web root as `Flight-Booking-Website` (the code uses paths starting with `/Flight-Booking-Website/`).
2. Import `Backend/database/flight-booking-system.sql` into MySQL.
3. Set your MySQL credentials in `Backend/config/database.php`.
4. Open `http://localhost/Flight-Booking-Website/Frontend/pages/auth/login.php` (or the home page at `Frontend/pages/Home/home.php`).

## 📁 Structure
```
Backend/    config, controllers, models, services, utils, database
Frontend/   pages, views/company, css, js, navbar, images
```

Original team repository: <https://github.com/basmala352004/Flight-Booking-Website>
