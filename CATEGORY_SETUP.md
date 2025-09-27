# Category Management System Setup

This document provides instructions for setting up the Category Management CRUD operations for your e-commerce platform.

## Database Setup

Before using the category management system, you need to update your database to include the `user_id` field in the categories table.

### Step 1: Update Database Schema

Run the SQL script located at `db/update_categories_table.sql` in your MySQL database:

```sql
-- Add user_id column to categories table
ALTER TABLE `categories` 
ADD COLUMN `user_id` int(11) NOT NULL AFTER `cat_id`;

-- Add foreign key constraint to link categories to users
ALTER TABLE `categories` 
ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Add index on user_id for better performance
ALTER TABLE `categories` 
ADD KEY `user_id` (`user_id`);

-- Add unique constraint on cat_name per user (category names must be unique per user)
ALTER TABLE `categories` 
ADD UNIQUE KEY `unique_cat_name_per_user` (`cat_name`, `user_id`);
```

### Step 2: Verify Database Changes

After running the SQL script, your `categories` table should have the following structure:

- `cat_id` (int, PRIMARY KEY, AUTO_INCREMENT)
- `user_id` (int, NOT NULL, FOREIGN KEY to customer.customer_id)
- `cat_name` (varchar(100), NOT NULL)

## Features Implemented

### 1. Category Class (`classes/category_class.php`)
- Extends database connection
- Methods: `add_category()`, `get_categories_by_user()`, `update_category()`, `delete_category()`
- User-specific category management
- Input validation and error handling

### 2. Category Controller (`controllers/category_controller.php`)
- `add_category_ctr()` - Add new category
- `fetch_categories_ctr()` - Retrieve user's categories
- `update_category_ctr()` - Update category name
- `delete_category_ctr()` - Delete category
- Parameter validation and error handling

### 3. Admin Interface (`admin/category.php`)
- Authentication checks (logged in + admin role)
- Bootstrap-based responsive UI
- Category listing with edit/delete actions
- Add/Edit/Delete modals
- Real-time category count display

### 4. Action Scripts
- `actions/fetch_category_action.php` - GET categories for user
- `actions/add_category_action.php` - POST new category
- `actions/update_category_action.php` - POST category update
- `actions/delete_category_action.php` - POST category deletion

### 5. JavaScript (`js/category.js`)
- AJAX form submissions
- Form validation
- Real-time UI updates
- Error handling and user feedback
- XSS protection

### 6. Navigation Updates (`index.php`)
- Category link in main navigation (admin only)
- Category link in user dropdown (admin only)
- Quick action button for category management

## Usage Instructions

### For Admin Users:

1. **Access Category Management**: 
   - Click "Categories" in the main navigation
   - Or click "Manage Categories" in Quick Actions
   - Or use the dropdown menu → "Manage Categories"

2. **Add Category**:
   - Click "Add New Category" button
   - Enter category name (must be unique)
   - Click "Add Category"

3. **Edit Category**:
   - Click the edit icon (pencil) next to any category
   - Modify the category name
   - Click "Update Category"

4. **Delete Category**:
   - Click the delete icon (trash) next to any category
   - Confirm deletion in the modal
   - Click "Delete Category"

### Security Features:

- **Authentication**: Only logged-in admin users can access category management
- **Authorization**: Users can only manage their own categories
- **Input Validation**: Server-side and client-side validation
- **XSS Protection**: HTML escaping in JavaScript
- **SQL Injection Protection**: Prepared statements
- **CSRF Protection**: Session-based validation

### Error Handling:

- Duplicate category names are prevented
- Empty category names are rejected
- Category names longer than 100 characters are rejected
- Database errors are logged and user-friendly messages shown
- Network errors are handled gracefully

## File Structure

```
register_sample/
├── admin/
│   └── category.php                 # Main category management page
├── actions/
│   ├── add_category_action.php      # Add category endpoint
│   ├── delete_category_action.php   # Delete category endpoint
│   ├── fetch_category_action.php    # Fetch categories endpoint
│   └── update_category_action.php   # Update category endpoint
├── classes/
│   └── category_class.php           # Category model class
├── controllers/
│   └── category_controller.php      # Category controller
├── db/
│   └── update_categories_table.sql  # Database update script
├── js/
│   └── category.js                  # Category management JavaScript
└── index.php                        # Updated with category links
```

## Testing

1. **Login as Admin**: Use an admin account (user_role = 1)
2. **Access Categories**: Navigate to the category management page
3. **Test CRUD Operations**:
   - Add a new category
   - Edit the category name
   - Delete the category
   - Verify categories are user-specific

## Notes

- Categories are user-specific (each user manages their own categories)
- Category names must be unique per user
- Category IDs are auto-generated and cannot be edited
- The system follows the MVC pattern as specified in the requirements
- All operations use AJAX for better user experience
- The interface is fully responsive and mobile-friendly

