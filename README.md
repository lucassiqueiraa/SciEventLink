# SciEventLink: Scientific Event Management System

![Status](https://img.shields.io/badge/status-in_development-yellow)
![Framework](https://img.shields.io/badge/Framework-Yii2-blue)
![Database](https://img.shields.io/badge/Database-MySQL-orange)

SciEventLink is a full-stack academic project (Yii2, REST API, Mobile App) designed to manage the complete lifecycle of a scientific event, from author submissions and peer review to the in-loco mobile experience.

## 🎓 Academic Context

This project integrates three core disciplines:

* **PLSI (Web Platforms):** The Yii2 Web Application (Front-office & Back-office).
* **SIS (Services):** The RESTful API serving data to the mobile client.
* **AMSI (Mobile Access):** The mobile application for participants' in-loco experience.

## ✨ Key Features

### Web (PLSI)
* **Admin Dashboard:** Manages Organizers (Create/Suspend).
* **Organizer Dashboard:** Creates and manages events (deadlines, rooms, fees).
* **Scientific Workflow:** Manages the blind peer-review process (assigning reviewers, accepting/rejecting papers).
* **Q&A Moderation:** Live dashboard for approving questions sent from the mobile app.
* **Front-Office:** Event catalog, user registration, and author paper submission.

### Mobile (AMSI & SIS)
* **Optimized Agenda:** A high-performance Master-Detail view of the event schedule.
* **My Agenda:** Ability to "Favorite" sessions to create a personal schedule.
* **Live Interaction:**
    * **Live Q&A:** Submit questions to the moderator during a session.
    * **Session Feedback:** Rate sessions (1-5 stars) via the app.
* **Notification Inbox:** Receives push notifications for room changes or alerts.

## 🛠️ Tech Stack

* **Backend (Web & API):** PHP 8+ with **Yii2 Framework**
* **Database (Primary):** **MySQL 8+ (InnoDB)**
* **Database (Local Cache):** **SQLite** (For Offline functionality)
* **Frontend (Web):** HTML5, CSS3, JavaScript, **Bootstrap**
* **Mobile Client (AMSI):** **Java/Kotlin** and **Android Studio**
* **Mobile Communication:** **Volley Library** (Asynchronous and efficient REST API consumption)
* **Testing:** **Codeception** (Framework for unit, functional, and acceptance testing)
* **Design & Management:** Figma / Jira

## 🚀 Getting Started

To run this project locally, ensure you have a WAMP (or equivalent) stack.

1.  **Clone the repository:**
    ```bash
    git clone [URL_DO_SEU_REPOSITORIO]
    cd scieventlink
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

3.  **Database Setup:**
    * Create a new MySQL database (e.g., `scieventlink_db`).
    * Import the `/database/scieventlink_db.sql` file (which contains the 12 tables) to create the schema.

4.  **Configure Yii2:**
    * Set your database connection in `common/config/main-local.php`.

5.  **Run:**
    * Point your web server (Apache/Nginx) to the `/frontend/web` and `/backend/web` directories.
