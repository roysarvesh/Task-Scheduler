

```markdown
# 🗓 Task Scheduler – PHP Project

A simple, pure PHP-based Task Management system that lets users:

- Add, view, complete, and delete tasks
- Subscribe for email reminders
- Get hourly task reminders via CRON
- Verify email subscriptions
- Unsubscribe in one click

---

##  Project Structure

```

src/
├── functions.php               # Core logic
├── index.php                  # Main user interface
├── verify.php                 # Handles email verification
├── unsubscribe.php            # Handles one-click unsubscription
├── cron.php                   # Sends hourly email reminders
├── setup\_cron.sh              # Script to schedule CRON job
├── tasks.txt                  # JSON file storing tasks
├── subscribers.txt            # Verified subscribers
├── pending\_subscriptions.txt  # Emails pending verification
└── test.php                   # Optional CLI test runner

````

---

## ✅ Features

### 📝 Task Management
- Add new tasks with duplicate prevention
- Mark tasks as complete/incomplete
- Delete tasks
- All tasks saved to `tasks.txt` (JSON)

### 📧 Email Subscription System
- Users enter their email to subscribe
- 6-digit verification code sent by email
- Emails stored in `subscribers.txt` after verification
- Pending verifications stored in `pending_subscriptions.txt`

### 🔄 CRON Reminder System
- `cron.php` fetches pending tasks
- Sends HTML email every hour to verified users
- Each email includes an unsubscribe link

---

## ⚙️ Technologies Used
- 🐘 PHP 8.3 (no frameworks)
- 📂 File-based JSON storage (no database)
- 📨 PHP `mail()` function for email
- 🖥️ CRON for automation (via `setup_cron.sh`)

---

## 🔧 Setup Instructions

### 1. Run Locally
```bash
php -S localhost:8000
````

Then open [http://localhost:8000/index.php](http://localhost:8000/index.php)

> Make sure all files are inside the `src/` folder and you're running PHP from there.

### 2. Email Configuration

To use `mail()` on local machine (Windows), configure SMTP settings in `php.ini` or use tools like [msmtp](https://msmtp.sourceforge.io/).

### 3. CRON Setup (Linux/Mac)

```bash
chmod +x setup_cron.sh
./setup_cron.sh
```

This will register the CRON job to execute `cron.php` hourly.

---

## 📩 Email Templates

### ✅ Verification Email

* Subject: `Verify subscription to Task Planner`
* HTML Body:

```html
<p>Click the link below to verify your subscription to Task Planner:</p>
<p><a id="verification-link" href="{verification_link}">Verify Subscription</a></p>
```

### 📬 Reminder Email

* Subject: `Task Planner - Pending Tasks Reminder`
* HTML Body:

```html
<h2>Pending Tasks Reminder</h2>
<p>Here are the current pending tasks:</p>
<ul>
  <li>Task 1</li>
  <li>Task 2</li>
</ul>
<p><a id="unsubscribe-link" href="{unsubscribe_link}">Unsubscribe from notifications</a></p>
```

---

## 👨‍💻 Author

**Sarvesh Kumar Roy**
B.Tech CSE – 2025
Lingaya’s Vidyapeeth
GitHub: [@roysarvesh](https://github.com/roysarvesh)

---

