-- ============================================================
-- Cms-shop — هيكل قاعدة البيانات (MySQL / MariaDB)
-- ============================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS settings (
  setting_key VARCHAR(64) NOT NULL PRIMARY KEY,
  setting_value TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'products',
  active TINYINT NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  last_login_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(30) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NULL,
  address TEXT NULL,
  wilaya_id INT NULL,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  parent_id INT NULL,
  image VARCHAR(255) NULL,
  position INT NOT NULL DEFAULT 0,
  active TINYINT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS category_translations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  lang CHAR(2) NOT NULL,
  name VARCHAR(150) NOT NULL,
  UNIQUE KEY uq_cat_lang (category_id, lang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(64) NULL,
  price DECIMAL(12,2) NOT NULL DEFAULT 0,
  sale_price DECIMAL(12,2) NULL,
  stock INT NOT NULL DEFAULT 0,
  category_id INT NULL,
  active TINYINT NOT NULL DEFAULT 1,
  featured TINYINT NOT NULL DEFAULT 0,
  image VARCHAR(255) NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_translations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  lang CHAR(2) NOT NULL,
  name VARCHAR(200) NOT NULL,
  description TEXT NULL,
  UNIQUE KEY uq_prod_lang (product_id, lang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  path VARCHAR(255) NOT NULL,
  position INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(24) NOT NULL UNIQUE,
  customer_id INT NULL,
  customer_name VARCHAR(120) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  phone2 VARCHAR(30) NULL,
  address TEXT NOT NULL,
  wilaya_id INT NOT NULL,
  wilaya_name VARCHAR(120) NOT NULL,
  delivery_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
  total DECIMAL(12,2) NOT NULL DEFAULT 0,
  status VARCHAR(20) NOT NULL DEFAULT 'new',
  payment_method VARCHAR(20) NOT NULL DEFAULT 'cod',
  notes TEXT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NULL,
  product_name VARCHAR(200) NOT NULL,
  price DECIMAL(12,2) NOT NULL DEFAULT 0,
  qty INT NOT NULL DEFAULT 1,
  total DECIMAL(12,2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  user_id INT NULL,
  action VARCHAR(60) NOT NULL,
  details TEXT NULL,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wilayas (
  id INT PRIMARY KEY,
  code VARCHAR(4) NOT NULL,
  name_ar VARCHAR(120) NOT NULL,
  name_fr VARCHAR(120) NOT NULL,
  name_en VARCHAR(120) NOT NULL,
  delivery_price DECIMAL(12,2) NOT NULL DEFAULT 500,
  active TINYINT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(120) NOT NULL UNIQUE,
  active TINYINT NOT NULL DEFAULT 1,
  position INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS page_translations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  page_id INT NOT NULL,
  lang CHAR(2) NOT NULL,
  title VARCHAR(190) NOT NULL,
  content MEDIUMTEXT NULL,
  UNIQUE KEY uq_page_lang (page_id, lang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS activity_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  entity VARCHAR(60) NOT NULL,
  entity_id INT NULL,
  action VARCHAR(60) NOT NULL,
  details TEXT NULL,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- الولايات الـ69 (الأسعار الافتراضية قابلة للتعديل من لوحة التحكم)
-- الولايات 59-69 (نوفمبر 2025): الترقيم افتراضي وقابل للتعديل
-- ------------------------------------------------------------
INSERT INTO wilayas (id, code, name_ar, name_fr, name_en, delivery_price, active) VALUES
(1,'01','أدرار','Adrar','Adrar',500,1),
(2,'02','الشلف','Chlef','Chlef',500,1),
(3,'03','الأغواط','Laghouat','Laghouat',500,1),
(4,'04','أم البواقي','Oum El Bouaghi','Oum El Bouaghi',500,1),
(5,'05','باتنة','Batna','Batna',500,1),
(6,'06','بجاية','Béjaïa','Bejaia',500,1),
(7,'07','بسكرة','Biskra','Biskra',500,1),
(8,'08','بشار','Béchar','Bechar',500,1),
(9,'09','البليدة','Blida','Blida',500,1),
(10,'10','البويرة','Bouira','Bouira',500,1),
(11,'11','تمنراست','Tamanrasset','Tamanrasset',500,1),
(12,'12','تبسة','Tébessa','Tebessa',500,1),
(13,'13','تلمسان','Tlemcen','Tlemcen',500,1),
(14,'14','تيارت','Tiaret','Tiaret',500,1),
(15,'15','تيزي وزو','Tizi Ouzou','Tizi Ouzou',500,1),
(16,'16','الجزائر','Alger','Algiers',500,1),
(17,'17','الجلفة','Djelfa','Djelfa',500,1),
(18,'18','جيجل','Jijel','Jijel',500,1),
(19,'19','سطيف','Sétif','Setif',500,1),
(20,'20','سعيدة','Saïda','Saida',500,1),
(21,'21','سكيكدة','Skikda','Skikda',500,1),
(22,'22','سيدي بلعباس','Sidi Bel Abbès','Sidi Bel Abbes',500,1),
(23,'23','عنابة','Annaba','Annaba',500,1),
(24,'24','قالمة','Guelma','Guelma',500,1),
(25,'25','قسنطينة','Constantine','Constantine',500,1),
(26,'26','المدية','Médéa','Medea',500,1),
(27,'27','مستغانم','Mostaganem','Mostaganem',500,1),
(28,'28','المسيلة','M''Sila','M''Sila',500,1),
(29,'29','معسكر','Mascara','Mascara',500,1),
(30,'30','ورقلة','Ouargla','Ouargla',500,1),
(31,'31','وهران','Oran','Oran',500,1),
(32,'32','البيض','El Bayadh','El Bayadh',500,1),
(33,'33','إليزي','Illizi','Illizi',500,1),
(34,'34','برج بوعريريج','Bordj Bou Arréridj','Bordj Bou Arreridj',500,1),
(35,'35','بومرداس','Boumerdès','Boumerdes',500,1),
(36,'36','الطارف','El Tarf','El Tarf',500,1),
(37,'37','تندوف','Tindouf','Tindouf',500,1),
(38,'38','تيسمسيلت','Tissemsilt','Tissemsilt',500,1),
(39,'39','الوادي','El Oued','El Oued',500,1),
(40,'40','خنشلة','Khenchela','Khenchela',500,1),
(41,'41','سوق أهراس','Souk Ahras','Souk Ahras',500,1),
(42,'42','تيبازة','Tipaza','Tipaza',500,1),
(43,'43','ميلة','Mila','Mila',500,1),
(44,'44','عين الدفلى','Aïn Defla','Ain Defla',500,1),
(45,'45','النعامة','Naâma','Naama',500,1),
(46,'46','عين تموشنت','Aïn Témouchent','Ain Temouchent',500,1),
(47,'47','غرداية','Ghardaïa','Ghardaia',500,1),
(48,'48','غليزان','Relizane','Relizane',500,1),
(49,'49','تيميمون','Timimoun','Timimoun',700,1),
(50,'50','برج باجي مختار','Bordj Badji Mokhtar','Bordj Badji Mokhtar',700,1),
(51,'51','أولاد جلال','Ouled Djellal','Ouled Djellal',700,1),
(52,'52','بني عباس','Béni Abbès','Beni Abbes',700,1),
(53,'53','عين صالح','In Salah','In Salah',700,1),
(54,'54','عين قزام','In Guezzam','In Guezzam',700,1),
(55,'55','تقرت','Touggourt','Touggourt',700,1),
(56,'56','جانت','Djanet','Djanet',700,1),
(57,'57','المغير','El M''Ghair','El M''Ghair',700,1),
(58,'58','المنيعة','El Meniaa','El Meniaa',700,1),
(59,'59','آفلو','Aflou','Aflou',500,1),
(60,'60','بريك','Barika','Barika',500,1),
(61,'61','قصر الشلالة','Ksar Chellala','Ksar Chellala',500,1),
(62,'62','مسعد','Messaad','Messaad',500,1),
(63,'63','عين وسارة','Ain Oussera','Ain Oussera',500,1),
(64,'64','بوسعادة','Bou Saada','Bou Saada',500,1),
(65,'65','الأبيض سيدي الشيخ','Sidi Cheikh','Sidi Cheikh',500,1),
(66,'66','القنطرة','El Khenitra','El Khenitra',500,1),
(67,'67','بئر العاتر','Bir El Ater','Bir El Ater',500,1),
(68,'68','قصر البخاري','Ksar El Boukhari','Ksar El Boukhari',500,1),
(69,'69','العريشة','El Aricha','El Aricha',500,1);

SET FOREIGN_KEY_CHECKS = 1;
