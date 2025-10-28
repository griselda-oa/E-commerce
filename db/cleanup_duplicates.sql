-- Clean up duplicate brands
DELETE b1 FROM brands b1
INNER JOIN brands b2 
WHERE b1.brand_id > b2.brand_id 
AND b1.brand_name = b2.brand_name;

-- Clean up duplicate categories  
DELETE c1 FROM categories c1
INNER JOIN categories c2 
WHERE c1.cat_id > c2.cat_id 
AND c1.cat_name = c2.cat_name;

-- Show remaining brands
SELECT brand_id, brand_name FROM brands ORDER BY brand_name;

-- Show remaining categories
SELECT cat_id, cat_name FROM categories ORDER BY cat_name;
