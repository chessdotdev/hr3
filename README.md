# HR3 Setup Guide

This guide will help you set up the HR3 project, setting up MySQL as the database, and importing the initial data.

---

## 1. Prerequisites

- **MySQL** (8.0+)
- Access to the `hr3.sql` file (provided in the repository or by your admin)

---
## 2. Clone the Repository

```sh
git clone https://github.com/chessdotdev/hr3.git

```
## 3. Database Setup

### a. Install MySQL

If you don't have MySQL installed, [download it here](https://dev.mysql.com/downloads/mysql/) and follow the installation instructions for your operating system.


```

```

### b. Import the `hr3.sql` Schema

Before importing, make sure that the **`hr3.sql`** file is available in your project’s **backup** folder.

Follow these steps to import the database into MySQL using phpMyAdmin:

1. Make sure MySQL and phpMyAdmin are installed and running on your system.  
2. Open **phpMyAdmin** (usually available at `http://localhost/phpmyadmin`).  
3. Click on the **Databases** tab at the top.  
4. Create a new database named **`hr3`**.  
5. After creating it, select the **`hr3`** database from the sidebar.  
6. Open the **Import** tab in the top menu.  s
7. Click the **Choose File** button and select the **`hr3.sql`** file from your project folder.  
8. Scroll down and click **Go** to start the import process.  
9. Once the import is complete, you’ll see all the tables and data under the **`hr3`** database.


