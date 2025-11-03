# 🧪 Testing Guide for E-Commerce Platform

## 📋 Overview
This guide explains how to test all the features of your e-commerce platform, including the EXTRA CREDIT bulk upload feature.

---

## 🔐 **Part 1: Authentication & Session Management**

### Testing Login
1. **Navigate to:** `login/login.php`
2. **Enter credentials:**
   - Username: Your registered email
   - Password: Your password
3. **Expected Result:**
   - ✅ Redirects to `index.php` (dashboard)
   - ✅ Shows "Welcome Back, [Your Name]!" message
   - ✅ Navigation shows admin links (if admin) or logout (if regular user)

### Testing Registration
1. **Navigate to:** `login/register.php`
2. **Fill out the form:**
   - Name, Email, Password
   - Phone Number, Country, City
3. **Expected Result:**
   - ✅ Success message appears
   - ✅ Redirects to login page
   - ✅ Can log in with new credentials

### Testing Session Management
1. **After login:**
   - ✅ Should stay logged in when navigating between pages
   - ✅ Should redirect to login if accessing admin pages without being logged in
   - ✅ Should check admin privileges for admin-only pages

---

## 🏷️ **Part 2: Category Management (CRUD)**

### Testing Category Retrieval
1. **Navigate to:** `admin/category.php` (as admin)
2. **Expected Result:**
   - ✅ Categories list displays
   - ✅ Shows ID, Category Name, Created Date, Actions

### Testing Category Creation
1. **Click:** "Add New Category" button
2. **Enter:** Category name (e.g., "Test Category")
3. **Click:** "Add Category"
4. **Expected Result:**
   - ✅ Success message appears
   - ✅ Category appears in the list
   - ✅ All category names must be unique

### Testing Category Update
1. **Click:** "Edit" button on any category
2. **Modify:** Category name
3. **Click:** "Update Category"
4. **Expected Result:**
   - ✅ Success message appears
   - ✅ Updated name appears in the list

### Testing Category Delete
1. **Click:** "Delete" button on any category
2. **Confirm:** Delete action
3. **Expected Result:**
   - ✅ Success message appears
   - ✅ Category removed from list

---

## 🏭 **Part 3: Brand Management (CRUD)**

### Testing Brand Retrieval
1. **Navigate to:** `admin/brand.php` (as admin)
2. **Expected Result:**
   - ✅ Brands list displays
   - ✅ Shows ID, Category, Brand Name, Actions

### Testing Brand Creation
1. **Click:** "Add New Brand" button
2. **Select:** Category from dropdown
3. **Enter:** Brand name
4. **Click:** "Add Brand"
5. **Expected Result:**
   - ✅ Success message appears
   - ✅ Brand appears in the list
   - ✅ Brand + Category combination must be unique

### Testing Brand Update & Delete
- Same process as Categories

---

## 📦 **Part 4: Product Management (Add & Edit)**

### Testing Product Retrieval
1. **Navigate to:** `admin/product.php` (as admin)
2. **Expected Result:**
   - ✅ Products display in grid layout
   - ✅ Each product card shows: Image, Title, Category, Brand, Price, Description

### Testing Product Creation
1. **Click:** "+ Add Product" button
2. **Fill out form:**
   - Category (required)
   - Brand (required)
   - Product Title (required)
   - Product Description
   - Product Price (required)
   - Keywords
   - **Product Image** (optional - can add later)
3. **Click:** "Save Product"
4. **Expected Result:**
   - ✅ Success message appears
   - ✅ Product appears in grid
   - ✅ Product ID is auto-generated

### Testing Product Edit
1. **Click:** "Edit" button on any product
2. **Modify:** Any fields
3. **Click:** "Save Product"
4. **Expected Result:**
   - ✅ Success message appears
   - ✅ Updated product reflects changes

---

## 🖼️ **Part 5: Image Upload (Single & Bulk)**

### Testing Single Image Upload
1. **Click:** "Upload Image" button (blue button) on any product
2. **Select:** One image file (JPEG, PNG, or GIF)
3. **Click:** "Upload Image"
4. **Expected Result:**
   - ✅ Success message appears
   - ✅ Image appears on product card
   - ✅ Image stored in `uploads/u{user_id}/p{product_id}/image_timestamp.ext`

### Testing Bulk Image Upload (EXTRA CREDIT) ⭐
1. **Click:** "Bulk Upload" button (green button) on any product
2. **Select:** Multiple images (hold Ctrl/Cmd to select multiple, or use Shift)
   - ✅ Maximum 10 images at once
   - ✅ Each image max 5MB
   - ✅ Only JPEG, PNG, GIF formats
3. **Click:** "Upload All Images"
4. **Expected Result:**
   - ✅ Loading spinner appears
   - ✅ Success message with count: "X image(s) uploaded successfully"
   - ✅ All images stored in `uploads/u{user_id}/p{product_id}/`
   - ✅ Images named: `image_timestamp_0.ext`, `image_timestamp_1.ext`, etc.

### Verifying Upload Location
1. **SSH into server:**
   ```bash
   ssh -C griselda.owusu@169.239.251.102 -p 422
   ```
2. **Navigate to uploads:**
   ```bash
   cd ~/public_html/register_sample_one/uploads/
   ```
3. **Check structure:**
   ```bash
   ls -la u*/p*/
   ```
4. **Expected Result:**
   - ✅ Directory structure: `uploads/u{user_id}/p{product_id}/`
   - ✅ Images stored with unique filenames
   - ✅ All files are image formats

---

## 🔍 **Part 6: Product Display & Search**

### Testing All Products Page
1. **Navigate to:** `all_product.php`
2. **Expected Result:**
   - ✅ All products display in grid
   - ✅ Products show: Image, Title, Category, Brand, Price, Description

### Testing Keyword Search
1. **Enter keyword** in search box (e.g., "kente", "wood", "handcrafted")
2. **Click:** "Search Products" button
3. **Expected Result:**
   - ✅ Only products matching keyword in title, description, or keywords appear
   - ✅ Results display count: "Found X product(s)"
   - ✅ Search is efficient (fast response)

### Testing Category Filter
1. **Select:** Category from dropdown
2. **Click:** "Search Products"
3. **Expected Result:**
   - ✅ Only products in selected category appear

### Testing Brand Filter
1. **Select:** Brand from dropdown
2. **Click:** "Search Products"
3. **Expected Result:**
   - ✅ Only products from selected brand appear

### Testing Price Range Filter
1. **Enter:** Min Price (e.g., 50)
2. **Enter:** Max Price (e.g., 500)
3. **Click:** "Search Products"
4. **Expected Result:**
   - ✅ Only products within price range appear

### Testing Composite Search (Multiple Filters)
1. **Enter:** Keyword (e.g., "kente")
2. **Select:** Category (e.g., "Kente & Textiles")
3. **Select:** Brand (e.g., "Bonwire Kente Weavers")
4. **Enter:** Max Price (e.g., 300)
5. **Click:** "Search Products"
6. **Expected Result:**
   - ✅ Results match ALL filters combined
   - ✅ Example: "all Kente textiles from Bonwire under 300 GHS"
   - ✅ Fast, efficient search algorithm

### Testing Single Product View
1. **Click:** "View Details" on any product
2. **Expected Result:**
   - ✅ Product details page shows
   - ✅ URL uses token: `single_product.php?token=xyz`
   - ✅ Shows full product information
   - ✅ Related products appear at bottom

---

## 🔗 **Part 7: Navigation & Links**

### Testing All Navigation Links
1. **Check each link** in navigation bar:
   - ✅ Home → `index.php`
   - ✅ Products → `all_product.php`
   - ✅ Category (admin) → `admin/category.php`
   - ✅ Brand (admin) → `admin/brand.php`
   - ✅ Add Product (admin) → `admin/product.php`
   - ✅ Register → `login/register.php`
   - ✅ Login → `login/login.php`
   - ✅ Logout → `actions/logout_action.php`

### Testing Admin Page Links
1. **On admin pages:**
   - ✅ "Back to Dashboard" → `../index.php` (works, no 404)
   - ✅ "Back to Home" → `../index.php` (works, no 404)

### Expected Result:
- ✅ All links work correctly
- ✅ No 404 errors
- ✅ All buttons lead somewhere

---

## 🧪 **Complete Testing Checklist**

### ✅ **Must Have Features:**
- [ ] Login/Logout works
- [ ] Registration works
- [ ] Session management works (stays logged in)
- [ ] Admin privileges checked correctly
- [ ] Category CRUD works (Create, Read, Update, Delete)
- [ ] Brand CRUD works
- [ ] Product Add & Edit works
- [ ] Single image upload works
- [ ] Images stored in `uploads/u{user_id}/p{product_id}/`
- [ ] All Products page displays products
- [ ] Keyword search works
- [ ] Category filter works
- [ ] Brand filter works
- [ ] Price range filter works
- [ ] Composite search works (all filters combined)
- [ ] Single product view works with token
- [ ] All navigation links work (no 404 errors)

### ⭐ **EXTRA CREDIT Features:**
- [ ] Bulk image upload works
- [ ] Can upload multiple images at once (up to 10)
- [ ] All images stored correctly in `uploads/u{user_id}/p{product_id}/`
- [ ] Bulk upload validates file types and sizes
- [ ] Progress feedback during bulk upload
- [ ] Error handling for partial uploads

---

## 🐛 **Common Issues & Solutions**

### Issue: Images not showing
**Solution:**
- Check file permissions: `chmod -R 755 uploads/`
- Verify upload directory exists
- Check image paths in database

### Issue: 404 errors on links
**Solution:**
- Verify file paths are correct
- Check server URL structure
- Ensure all files are uploaded to server

### Issue: Search not working
**Solution:**
- Check browser console for JavaScript errors
- Verify `composite_search_action.php` exists
- Check database connection

### Issue: Bulk upload fails
**Solution:**
- Check file size (max 5MB per file)
- Check file count (max 10 images)
- Verify file types (JPEG, PNG, GIF only)
- Check `uploads/` directory permissions

---

## 📞 **Quick Test Commands**

### Test Database Connection:
```bash
curl http://169.239.251.102:442/~griselda.owusu/register_sample_one/actions/test_db_connection.php
```

### Test Categories Loading:
```bash
curl -X POST http://169.239.251.102:442/~griselda.owusu/register_sample_one/actions/fetch_category_action.php
```

### Test Composite Search:
```bash
curl -X POST http://169.239.251.102:442/~griselda.owusu/register_sample_one/actions/composite_search_action.php \
  -d "keyword=kente&cat_id=1&max_price=300"
```

---

## ✅ **Final Verification**

After testing everything:

1. **✅ All CRUD operations work**
2. **✅ Image uploads work (single & bulk)**
3. **✅ Search functionality works (keyword & composite)**
4. **✅ All navigation links work**
5. **✅ No 404 errors**
6. **✅ All buttons functional**
7. **✅ Security features working (session, admin checks)**
8. **✅ Images stored in correct location: `uploads/u{user_id}/p{product_id}/`**

---

**🎉 Congratulations! Your e-commerce platform is fully functional!**

