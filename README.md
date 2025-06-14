# Chapter Apps – Backend Assessment (Scenario 2: Search & Filter Optimization)

This project implements an optimized and scalable product search system for an e-commerce backend. It solves performance and filtering issues over a large dataset using SQL optimization, indexing, and faceted search logic.

---

## 🚀 Technologies Used

- PHP 8.2 (CLI)
- MySQL 5.7
- Docker & Docker Compose
- PDO (with prepared statements)
- Composer & Dotenv

---

## 📁 Folder Structure

    backend-assessment/
        ├── Dockerfile
        ├── docker-compose.yml
        ├── .env
        ├── setup_search2.php
        ├── seed_search2.php
        ├── README.md
        ├── vendor/
        ├── src/
        │ └── Scenario2/
        │ └── ProductSearchService.php
        └── tests/
            ├── SearchScenarioTest.php
            └── FacetedSearchTest.php

---

## ⚙️ Setup Instructions

### 1. Clone the Project

```bash
git clone https://github.com/rambir-bhatiwal/flash-sale-queue.git
cd backend-assessment

```

### 2. Create .env File 

    DB_HOST=chapter_db
    DB_PORT=3306
    DB_NAME=test
    DB_USER=root
    DB_PASS=root


### 3. Build and Start Docker
    docker-compose up --build -d
### 4. Install Composer Dependencies
    docker exec -it chapter_app composer install

## 🗄️ Database Setup
    -- Run setup to create tables:
        docker exec -it chapter_app php /var/www/html/setup.php
    -- Insert test product: 
        docker exec -it chapter_app php /var/www/html/seed.php

##  🔍 Scenario 2.1 – Optimized Product Search

        Avoids per-row subqueries with JOIN + GROUP BY

        -> Filters by:

            --> keyword (name/description)

            --> min/max price
            
            --> brand list
            
            --> minimum rating

        -> Uses prepared statements

        -> Limits to 50 results

## ✅ Testing
   # 🔹 Run Search Test command: 
    --> docker exec -it chapter_app php /var/www/html/tests/SearchScenarioTest.php
        ✅ Example Output

            🔍 Found 17 results:
            - Product 32 | ₹720 | Rating: 4.1
            - Product 71 | ₹480 | Rating: 3.8

   # 📊 Scenario 2.2 – Faceted Search System command:
    --> docker exec -it chapter_app php /var/www/html/tests/FacetedSearchTest.php
         ✅ Example Output
    
            ```
                🔍 Products Found: 24

                📊 Brand Facets:
                - Sony (5)
                - Dell (4)

                📚 Category Facets:
                - Electronics (12)
                - Books (3)

                💸 Price Histogram:
                - ₹200 to ₹399: 6
                - ₹400 to ₹599: 10

                📦 Stock:
                - in_stock: 20
                - out_of_stock: 4
            ```


## 👨‍💻 Author
    -> Backend system implemented for the Chapter Apps Fullstack Engineer Assessment

### 🧑‍💻 Developer: Rambir


