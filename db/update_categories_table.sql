-- Update categories table to include user_id field for user-specific categories
-- This allows each user to manage their own categories

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

