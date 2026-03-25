# Field Grower 🌾

**Field Grower** is a comprehensive web-based platform designed to revolutionize modern agriculture. It serves as a bridge between technology and traditional farming, empowering farmers, labourers, and agribusiness administrators with tools to improve productivity, efficiency, and sustainability.

## 🚀 Vision & Mission

**Vision:** To lead the future of sustainable agriculture through advanced technology, empowering every farmer to cultivate smarter, greener, and more profitable fields.

**Mission:** Field Grower powers farmers and agribusinesses with innovative solutions and cutting-edge technologies that improve productivity, efficiency, and environmental sustainability.

## 🌍 Real-World Impact

Agriculture is the backbone of our economy, yet many farmers struggle with manual record-keeping, lack of market insights, and inefficient resource management. **Field Grower** addresses these challenges by:

-   **Digitizing Farm Management:** Replaces manual logs with digital records for crops, fertilizers, and pesticides.
-   **Bridging the Market Gap:** Provides farmers with real-time market trends and pricing, ensuring they get fair value for their produce.
-   **Optimizing Labor:** Streamlines work announcements and labor management, connecting farmers with available workforce.
-   **Data-Driven Decisions:** Enables administrators to track agricultural trends and provide better support to the farming community.
-   **Sustainability:** Promotes the efficient use of resources (fertilizers/pesticides) through better tracking and management.

## ✨ Key Features

### 👨‍🌾 Farmer Dashboard
Tools to manage the entire farming lifecycle.
-   **Crop Management:** Add, view, and remove crop details. Track fertilizers and pesticides usage.
-   **Market Access:** View current market prices and trends to make informed selling decisions.
-   **WorkFarm:** Manage farm work, add new tasks, view past works, and track updates.
-   **Queries:** Direct line of communication for support and inquiries.
-   **Profile Management:** Update personal contact details (Mobile, Email).

### 👷 Labour Dashboard
Streamlined interface for the workforce.
-   **Work Announcements:** View available farming jobs and announcements.
-   **Queries:** Raise operational queries or concerns.

### 🏢 Employee Dashboard
For field officers or intermediate staff.
-   **Crop & Market Operations:** Manage crop entries and market data (add/delete/update).
-   **Work Monitoring:** Track current and past farm works.
-   **Customer Insights:** View and manage customer queries.

### 👨‍💼 Admin Dashboard
Centralized control for system administrators.
-   **System Statistics:** Real-time animated counters for Total Users and Employees.
-   **Master Data Management:** View comprehensive lists of crops, fertilizers, and pesticides.
-   **Employee Management:** Add, remove, view employees, and update salaries.
-   **Market Trends:** Analyze broader market data.
-   **Query Resolution:** Oversee all user queries.
-   **Profile Management:** Update administrator contact details.

## 🛠️ Technology Stack

-   **Frontend:** HTML5, CSS3 (Responsive Design), JavaScript (Vanilla for animations and interactivity).
-   **Backend:** PHP (Core setup with session management and secure authentication).
-   **Database:** MySQL (Relational data storage for Users, Crops, Market, etc.).
-   **Design:** Custom CSS with embedded Google Fonts (Bona Nova SC, Italiana, Sixtyfour Convergence).

## ⚙️ Installation & Setup

1.  **Prerequisites:**
    -   Install a local server environment like **XAMPP**, **WAMP**, or **MAMP**.
    -   Ensure PHP and MySQL services are running.

2.  **Database Setup:**
    -   Open phpMyAdmin (e.g., http://localhost/phpmyadmin).
    -   Create a new database named ieldgrower.
    -   Import the project SQL dump or create the necessary tables (grarian, masterlogin, employee, crops, market, etc.) matching the PHP code schema.
    -   *Configuration:* The default database connection is configured for oot user with password Ravikumar123. If your setup differs, update the connection variables in the PHP files:
        `php
        $servername = "localhost";
        $db_username = "root";
        $db_password = "YOUR_PASSWORD";
        $dbname = "fieldgrower";
        `

3.  **Deploy Code:**
    -   Copy the FieldGrower folder to your server's root directory (e.g., htdocs in XAMPP or www in WAMP).

4.  **Run:**
    -   Open your browser and navigate to http://localhost/FieldGrower.
    -   Start at index.html for the landing page or 2signinpage.php to log in.

## 📞 Contact & Support

For queries, please visit the **Contact Us** page within the application or reach out via the official support channels.

---
*Empowering Farmers, Growing Futures.*