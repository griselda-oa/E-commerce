-- Insert sample data for categories and brands
-- Copy and paste this in phpMyAdmin SQL tab

-- First, insert categories
INSERT INTO categories (cat_name, user_id) VALUES
('Kente & Textiles', 2),
('Wood Carvings & Sculptures', 2),
('Handcrafted Jewelry', 2),
('Beaded Art & Accessories', 2),
('Ceramics & Pottery', 2);

-- Then insert brands
INSERT INTO brands (brand_name, cat_id, user_id) VALUES
('Bonwire Kente Weavers', 1, 2),
('Ashanti Traditional Textiles', 1, 2),
('Adinkra Artisans Guild', 2, 2),
('Ghana Wood Masters', 2, 2),
('Accra Bead Crafts', 3, 2),
('Krobo Glass Beads', 3, 2),
('Heritage Beadwork Studio', 4, 2),
('Contemporary Craft Designs', 4, 2),
('Volta Clay Works', 5, 2),
('Royal Pottery Accra', 5, 2);

