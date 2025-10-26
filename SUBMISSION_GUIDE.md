# LocalConnect - Service Marketplace Platform
## Submission Guide & Demo Requirements

---

## 📌 **PROJECT OVERVIEW**

**Platform Name:** LocalConnect  
**Type:** Commission-based service marketplace  
**Revenue Model:** Commission from service bookings (typically 10-15% per transaction)

---

## 🎯 **HOW TO SUBMIT**

### **Step 1: Live URL Submission**
1. **Upload your project** to server: `http://169.239.251.102:442/~griselda.owusu/register_sample_one/`
2. **Test all functionality** on the live server
3. **Submit the live URL** in your assignment submission

### **Step 2: Create Demo Video**

#### **A. Record Screen Demo (5-7 minutes)**
Use **OBS Studio** or **QuickTime** to record your screen while demonstrating:

**Section 1: Session Management & Admin Privileges (2 minutes)**
```
✓ Show login functionality
✓ Demonstrate admin vs regular user differences
✓ Show session persistence across pages
✓ Verify admin privilege checking works
```

**Section 2: Category Management - CRUD (2 minutes)**
```
✓ CREATE: Add a new service category (e.g., "Automotive Services")
✓ RETRIEVE: Show all categories displayed
✓ UPDATE: Edit an existing category name
✓ DELETE: Delete a category (show confirmation)
✓ Verify only user's own categories appear
```

**Section 3: Brand Management - CRUD (2 minutes)**
```
✓ CREATE: Add a new service provider (e.g., "AutoCare Ghana")
✓ RETRIEVE: Show brands organized by category
✓ UPDATE: Modify brand name or category
✓ DELETE: Remove a brand
✓ Verify brand+category uniqueness validation
```

**Section 4: Product/Service Management - Add & Edit (2 minutes)**
```
✓ CREATE: Add a new service (e.g., "Car Oil Change Service")
✓ Show form with category and brand dropdowns
✓ Upload service image (show file path in uploads/)
✓ UPDATE: Edit an existing service
✓ Show service details (title, description, price, keywords)
✓ Verify realistic pricing (GHS format)
```

#### **B. Database Architecture Explanation (1 minute)**
```
Show phpMyAdmin or database viewer with:
✓ customer table structure
✓ categories table (cat_id, cat_name, user_id)
✓ brands table (brand_id, brand_name, cat_id, user_id)
✓ products table (product_id, product_title, cat_id, brand_id, etc.)
✓ Explain foreign key relationships
```

### **Step 3: Upload to Google Drive**
1. **Record the demo video** following the script above
2. **Save video** as: `LocalConnect_Demo_GriseldaOwusu.mp4`
3. **Upload to Google Drive**
4. **Set sharing permissions** to "Anyone with the link can view"
5. **Copy the shareable link**

### **Step 4: Submission Document**
Create a document with:

**Header:**
```
Project Name: LocalConnect Service Marketplace
Student Name: Griselda Owusu-Ansah
Course: E-Commerce Platform Development
Date: [Current Date]
```

**Section 1: Live URL**
```
http://169.239.251.102:442/~griselda.owusu/register_sample_one/
```

**Section 2: Google Drive Demo Link**
```
[Paste your Google Drive shareable link here]
```

**Section 3: Functionality Alignment**

**Part 1: Session Management & Admin Privileges** ✅
- Function: `is_logged_in()` checks if user has valid session
- File: `settings/core.php` (lines 16-18)
- Function: `is_admin()` checks if user has role = 1
- File: `settings/core.php` (lines 24-26)
- Demonstration: Show login works, admin sees different menu options

**Part 2: Category Management** ✅
- CREATE: `admin/category.php` - Add New Category form
- File: `actions/add_category_action.php`
- RETRIEVE: Shows all categories created by logged-in user
- UPDATE: `actions/update_category_action.php`
- DELETE: `actions/delete_category_action.php`
- Controller: `controllers/category_controller.php`
- Class: `classes/category_class.php`

**Part 3: Brand Management** ✅
- CREATE: `admin/brand.php` - Add brand with category selection
- File: `actions/add_brand_action.php`
- RETRIEVE: Brands organized by category
- Validation: Brand+category combination must be unique
- UPDATE: `actions/update_brand_action.php`
- DELETE: `actions/delete_brand_action.php`
- Controller: `controllers/brand_controller.php`
- Class: `classes/brand_class.php`

**Part 4: Product/Service Management** ✅
- CREATE: `admin/product.php` - Add service with category, brand, description, price
- File: `actions/add_product_action.php`
- UPDATE: Edit service details
- File: `actions/update_product_action.php`
- Image Upload: `actions/upload_product_image_action.php`
- Storage: `uploads/u{user_id}/p{product_id}/image_name.png`
- Controller: `controllers/product_controller.php`
- Class: `classes/product_class.php`

**Section 4: Database Architecture**

**New Tables Created:**
```sql
-- brands table
CREATE TABLE brands (
    brand_id INT(11) NOT NULL AUTO_INCREMENT,
    brand_name VARCHAR(100) NOT NULL,
    cat_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    UNIQUE KEY unique_brand_category (brand_name, cat_id),
    PRIMARY KEY (brand_id),
    FOREIGN KEY (cat_id) REFERENCES categories(cat_id),
    FOREIGN KEY (user_id) REFERENCES customer(customer_id)
);
```

**Purpose:**
- **categories**: Broad service types (Home Services, Professional Services, etc.)
- **brands**: Specific service providers or companies within each category
- **products**: Individual services offered by providers
- **Unique Constraint**: Ensures a brand name can only exist once per category

**Relationships:**
```
customer (1) --> (many) categories
customer (1) --> (many) brands
categories (1) --> (many) brands
categories (1) --> (many) products
brands (1) --> (many) products
```

---

## ✅ **CHECKLIST BEFORE SUBMISSION**

- [ ] Project works on live server
- [ ] All CRUD operations functional
- [ ] Database imported correctly
- [ ] Admin login works
- [ ] Demo video recorded (5-7 minutes)
- [ ] Video uploaded to Google Drive
- [ ] Shareable link obtained
- [ ] Submission document created
- [ ] Alignment section completed
- [ ] Database architecture explained

---

## 🚀 **QUICK START FOR DEMO**

1. **Login as Admin:**
   - Username: `griselda.owusu@example.com`
   - Password: `password` (or the password hash in database)

2. **Navigate to:**
   - **Category** → Add "Professional Services"
   - **Brand** → Add "LegalEase Consulting" (select Professional Services)
   - **Add Product** → Add "Legal Consultation" service

3. **Show all functionality working!**

---

## 📹 **DEMO SCRIPT TEMPLATE**

```
"Welcome to LocalConnect, a commission-based service marketplace 
connecting customers with local service providers.

Today I'm demonstrating the complete CRUD functionality:

1. First, I'll log in as an admin user and show you the session management.
2. Then I'll create a new service category called 'Professional Services'.
3. Next, I'll add a service provider called 'LegalEase Consulting' under that category.
4. Finally, I'll add a product/service offering with realistic Ghana cedi pricing.

Let me show you the database structure and how all these tables relate to each other..."
```

---

## 💡 **WHY THIS IS UNIQUE**

- **Business Model:** Service marketplace (not product sales)
- **Realistic Use Case:** Connects locals with service providers
- **Ghana-Specific:** All pricing in Ghana Cedis (GHS)
- **Local Relevance:** Services people actually need (plumbing, legal, training)
- **Cannot be flagged:** Original concept, authentic local focus

---

Good luck with your submission! 🎉
