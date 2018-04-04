#added new fields into table venues
ALTER TABLE `venues`
ADD COLUMN `logo_url` VARCHAR(500) NULL DEFAULT NULL AFTER `pos_fee`,
ADD COLUMN `header_url` VARCHAR(500) NULL DEFAULT NULL AFTER `logo_url`;

#update logos
update venues v
inner join venue_images vi on v.id=vi.venue_id
inner join images i on i.id=vi.image_id
set v.logo_url = i.url
where i.image_type = 'Logo';

#update header
update venues v
inner join venue_images vi on v.id=vi.venue_id
inner join images i on i.id=vi.image_id
set v.header_url = i.url
where i.image_type = 'Header';

#remove Logos and headers
update venues v
inner join venue_images vi on v.id=vi.venue_id
inner join images i on i.id=vi.image_id
set i.url = ''
where i.image_type = 'Logo' OR i.image_type = 'Header';

delete venue_images, images from venue_images
inner join images on images.id = venue_images.image_id
where images.url = '' AND (images.image_type = 'Logo' OR images.image_type = 'Header');

#added new fields into table shows
ALTER TABLE `shows`
ADD COLUMN `logo_url` VARCHAR(500) NULL DEFAULT NULL AFTER `pos_fee`,
ADD COLUMN `header_url` VARCHAR(500) NULL DEFAULT NULL AFTER `logo_url`;

#update logos
update shows v
inner join show_images vi on v.id=vi.show_id
inner join images i on i.id=vi.image_id
set v.logo_url = i.url
where i.image_type = 'Logo';

#update header
update shows v
inner join show_images vi on v.id=vi.show_id
inner join images i on i.id=vi.image_id
set v.header_url = i.url
where i.image_type = 'Header';

#remove Logos and headers
update shows v
inner join show_images vi on v.id=vi.show_id
inner join images i on i.id=vi.image_id
set i.url = ''
where i.image_type = 'Logo' OR i.image_type = 'Header';

delete show_images, images from show_images
inner join images on images.id = show_images.image_id
where images.url = '' AND (images.image_type = 'Logo' OR images.image_type = 'Header');

#clean up MORE
delete from images
where images.url = '' AND (images.image_type = 'Logo' OR images.image_type = 'Header');

delete show_images, images from show_images
right join images on images.id = show_images.image_id
where images.url not like '/s3/%' AND (images.image_type = 'Logo' OR images.image_type = 'Header');

delete venue_images, images from venue_images
right join images on images.id = venue_images.image_id
where images.url not like '/s3/%' AND (images.image_type = 'Logo' OR images.image_type = 'Header');
