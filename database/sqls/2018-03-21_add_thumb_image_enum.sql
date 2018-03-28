#ALTER TABLE `images`
CHANGE COLUMN `image_type` `image_type` ENUM('Logo', 'Image', 'Header', 'Header Medium', 'Mobile Header', 'Thumbnail') NOT NULL DEFAULT 'Image' ;