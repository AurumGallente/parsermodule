CREATE TABLE `par` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
 `category` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
 `region` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
 PRIMARY KEY (`id`)
); 
CREATE TABLE `par_items` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `par_id` int(10) unsigned NOT NULL,
 `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `desc` text COLLATE utf8_unicode_ci NOT NULL,
 `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `text` text COLLATE utf8_unicode_ci,
 `author` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
 `date` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
 PRIMARY KEY (`id`)
);