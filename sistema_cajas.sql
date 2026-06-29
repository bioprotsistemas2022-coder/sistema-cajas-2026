/*
 Navicat Premium Dump SQL

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 100427 (10.4.27-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : sistema_cajas

 Target Server Type    : MySQL
 Target Server Version : 100427 (10.4.27-MariaDB)
 File Encoding         : 65001

 Date: 29/06/2026 12:15:49
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  INDEX `cache_expiration_index`(`expiration` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache
-- ----------------------------

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  INDEX `cache_locks_expiration_index`(`expiration` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache_locks
-- ----------------------------

-- ----------------------------
-- Table structure for caja_cirugia
-- ----------------------------
DROP TABLE IF EXISTS `caja_cirugia`;
CREATE TABLE `caja_cirugia`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `caja_id` bigint UNSIGNED NOT NULL,
  `cirugia_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `caja_cirugia_caja_id_foreign`(`caja_id` ASC) USING BTREE,
  INDEX `caja_cirugia_cirugia_id_foreign`(`cirugia_id` ASC) USING BTREE,
  CONSTRAINT `caja_cirugia_caja_id_foreign` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `caja_cirugia_cirugia_id_foreign` FOREIGN KEY (`cirugia_id`) REFERENCES `cirugias` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of caja_cirugia
-- ----------------------------
INSERT INTO `caja_cirugia` VALUES (1, 1, 1, NULL, NULL);
INSERT INTO `caja_cirugia` VALUES (2, 1, 2, NULL, NULL);
INSERT INTO `caja_cirugia` VALUES (3, 2, 3, NULL, NULL);

-- ----------------------------
-- Table structure for caja_imagenes
-- ----------------------------
DROP TABLE IF EXISTS `caja_imagenes`;
CREATE TABLE `caja_imagenes`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `caja_id` bigint UNSIGNED NOT NULL,
  `ruta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `caja_imagenes_caja_id_foreign`(`caja_id` ASC) USING BTREE,
  CONSTRAINT `caja_imagenes_caja_id_foreign` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of caja_imagenes
-- ----------------------------

-- ----------------------------
-- Table structure for cajas
-- ----------------------------
DROP TABLE IF EXISTS `cajas`;
CREATE TABLE `cajas`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_interno` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('DISPONIBLE','EN ESTERILIZADORA','EN CX','EN TRANSITO','PENDIENTE','ACONDICIONAMIENTO','EN REPARACION','BAJA') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DISPONIBLE',
  `pdf_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `imagen_salida_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `cajas_codigo_interno_unique`(`codigo_interno` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cajas
-- ----------------------------
INSERT INTO `cajas` VALUES (1, 'Caja Instrumental 1', 'INST-001', 'DISPONIBLE', NULL, NULL, '2026-05-11 14:14:33', '2026-06-29 15:05:39');
INSERT INTO `cajas` VALUES (2, 'Caja Instrumental 2', 'INST-002', 'EN ESTERILIZADORA', NULL, NULL, '2026-05-11 14:14:33', '2026-06-29 15:03:49');
INSERT INTO `cajas` VALUES (3, 'Motor Quirúrgico A', 'MOT-001', 'DISPONIBLE', NULL, NULL, '2026-05-11 14:14:33', '2026-05-11 14:14:33');
INSERT INTO `cajas` VALUES (4, 'Set Traumatología', 'TRAU-001', 'DISPONIBLE', NULL, NULL, '2026-05-11 14:14:33', '2026-05-11 14:14:33');

-- ----------------------------
-- Table structure for cirugias
-- ----------------------------
DROP TABLE IF EXISTS `cirugias`;
CREATE TABLE `cirugias`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `bioimplant_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `paciente` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `medico` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_cx` date NOT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `tecnico_id` bigint UNSIGNED NULL DEFAULT NULL,
  `access_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('PENDIENTE','EN_CURSO','COMPLETADA','CANCELADA') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `cirugias_access_token_unique`(`access_token` ASC) USING BTREE,
  INDEX `cirugias_tecnico_id_foreign`(`tecnico_id` ASC) USING BTREE,
  CONSTRAINT `cirugias_tecnico_id_foreign` FOREIGN KEY (`tecnico_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cirugias
-- ----------------------------
INSERT INTO `cirugias` VALUES (1, NULL, 'PEPITO', 'JUAN', '2026-05-11', '2026-05-11 15:36:48', '2026-05-11 15:37:06', 3, 'nMP0n5w1CfMH6w5qao0lucQNl0TbyqKO', 'COMPLETADA', '2026-05-11 15:34:08', '2026-05-11 15:37:06');
INSERT INTO `cirugias` VALUES (2, NULL, 'JUAN ROMERO', 'JUAN', '2026-06-29', '2026-06-29 15:04:18', '2026-06-29 15:04:25', 3, 'HenmtZsuUt5gmSjiFqvijK3EW1DhyRWV', 'COMPLETADA', '2026-06-29 14:36:46', '2026-06-29 15:04:25');
INSERT INTO `cirugias` VALUES (3, NULL, 'JUAN ROMERO', 'JUAN', '2026-06-29', NULL, NULL, 3, 'NZN9MtmXgk0PcHPhzBD6814FDyR05dtI', 'PENDIENTE', '2026-06-29 15:03:49', '2026-06-29 15:03:49');

-- ----------------------------
-- Table structure for consumos
-- ----------------------------
DROP TABLE IF EXISTS `consumos`;
CREATE TABLE `consumos`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `cirugia_id` bigint UNSIGNED NOT NULL,
  `caja_id` bigint UNSIGNED NOT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `consumos_cirugia_id_foreign`(`cirugia_id` ASC) USING BTREE,
  INDEX `consumos_caja_id_foreign`(`caja_id` ASC) USING BTREE,
  CONSTRAINT `consumos_caja_id_foreign` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `consumos_cirugia_id_foreign` FOREIGN KEY (`cirugia_id`) REFERENCES `cirugias` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of consumos
-- ----------------------------
INSERT INTO `consumos` VALUES (1, 1, 1, '\"1 ESPACIADOR\"', 'TODO OK', '2026-05-11 15:37:06', '2026-05-11 15:37:06');
INSERT INTO `consumos` VALUES (2, 2, 1, '\"OK\"', 'OK', '2026-06-29 15:04:25', '2026-06-29 15:04:25');

-- ----------------------------
-- Table structure for evento_cajas
-- ----------------------------
DROP TABLE IF EXISTS `evento_cajas`;
CREATE TABLE `evento_cajas`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `caja_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `estado_anterior` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `estado_nuevo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `fotos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `evento_cajas_caja_id_foreign`(`caja_id` ASC) USING BTREE,
  INDEX `evento_cajas_user_id_foreign`(`user_id` ASC) USING BTREE,
  CONSTRAINT `evento_cajas_caja_id_foreign` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `evento_cajas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of evento_cajas
-- ----------------------------
INSERT INTO `evento_cajas` VALUES (1, 1, 1, 'DISPONIBLE', 'EN ESTERILIZADORA', 'Egreso hacia esterilizadora para CX de PEPITO', NULL, '2026-05-11 15:34:08', '2026-05-11 15:34:08');
INSERT INTO `evento_cajas` VALUES (2, 1, 3, 'EN ESTERILIZADORA', 'EN CX', 'Llegado en condiciones para CX', NULL, '2026-05-11 15:36:48', '2026-05-11 15:36:48');
INSERT INTO `evento_cajas` VALUES (3, 1, 3, 'EN CX', 'EN TRANSITO', 'Cirugía finalizada. Pendiente de control de consumos.', NULL, '2026-05-11 15:37:06', '2026-05-11 15:37:06');
INSERT INTO `evento_cajas` VALUES (4, 1, 4, 'EN TRANSITO', 'PENDIENTE', 'Iniciando control de caja recibida.', NULL, '2026-05-11 15:37:56', '2026-05-11 15:37:56');
INSERT INTO `evento_cajas` VALUES (5, 1, 4, 'PENDIENTE', 'ACONDICIONAMIENTO', 'Control finalizado correctamente. Pasa a lavado.', NULL, '2026-05-11 15:38:23', '2026-05-11 15:38:23');
INSERT INTO `evento_cajas` VALUES (6, 1, 5, 'ACONDICIONAMIENTO', 'DISPONIBLE', 'Lavado finalizado. Caja disponible para nuevo uso.', NULL, '2026-05-11 15:39:07', '2026-05-11 15:39:07');
INSERT INTO `evento_cajas` VALUES (7, 1, 1, 'DISPONIBLE', 'EN ESTERILIZADORA', 'Egreso hacia esterilizadora para CX de JUAN ROMERO', NULL, '2026-06-29 14:36:46', '2026-06-29 14:36:46');
INSERT INTO `evento_cajas` VALUES (8, 2, 2, 'DISPONIBLE', 'EN ESTERILIZADORA', 'Egreso hacia esterilizadora para CX de JUAN ROMERO', NULL, '2026-06-29 15:03:49', '2026-06-29 15:03:49');
INSERT INTO `evento_cajas` VALUES (9, 1, 3, 'EN ESTERILIZADORA', 'EN CX', 'Llegado en condiciones para CX', NULL, '2026-06-29 15:04:18', '2026-06-29 15:04:18');
INSERT INTO `evento_cajas` VALUES (10, 1, 3, 'EN CX', 'EN TRANSITO', 'Cirugía finalizada. Pendiente de control de consumos.', NULL, '2026-06-29 15:04:25', '2026-06-29 15:04:25');
INSERT INTO `evento_cajas` VALUES (11, 1, 4, 'EN TRANSITO', 'PENDIENTE', 'Iniciando control de caja recibida.', NULL, '2026-06-29 15:05:13', '2026-06-29 15:05:13');
INSERT INTO `evento_cajas` VALUES (12, 1, 4, 'PENDIENTE', 'ACONDICIONAMIENTO', 'Control finalizado correctamente. Pasa a lavado.', NULL, '2026-06-29 15:05:23', '2026-06-29 15:05:23');
INSERT INTO `evento_cajas` VALUES (13, 1, 5, 'ACONDICIONAMIENTO', 'DISPONIBLE', 'Lavado finalizado. Caja disponible para nuevo uso.', NULL, '2026-06-29 15:05:39', '2026-06-29 15:05:39');

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `cancelled_at` int NULL DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of job_batches
-- ----------------------------

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED NULL DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jobs_queue_index`(`queue` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jobs
-- ----------------------------

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` VALUES (4, '2026_05_11_135955_create_cajas_table', 1);
INSERT INTO `migrations` VALUES (5, '2026_05_11_135955_create_cirugias_table', 1);
INSERT INTO `migrations` VALUES (6, '2026_05_11_135955_create_evento_cajas_table', 1);
INSERT INTO `migrations` VALUES (7, '2026_05_11_135956_create_consumos_table', 1);
INSERT INTO `migrations` VALUES (8, '2026_05_11_140003_add_role_to_users_table', 1);
INSERT INTO `migrations` VALUES (9, '2026_05_11_140001_create_caja_imagenes_table', 2);

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sessions_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `sessions_last_activity_index`(`last_activity` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sessions
-- ----------------------------
INSERT INTO `sessions` VALUES ('1fKmP8PD2bzeqa38zlSXMCYrIrxWe40QwCM0pjEC', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; es-AR) WindowsPowerShell/5.1.19041.6456', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaDl5UmJEN2FQQ0JISG52ZkVXUFZTWDBzNTdRU1dFNG5PUHNObkkzeiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782743970);
INSERT INTO `sessions` VALUES ('zaXVT9W82FSigbMoBBIQJwsboLwcedGip0CiekeV', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTWV3UVkzeDRUNFVUTkFBZlY2SnJGZUVpQzR4VXFwVUd4YlFKQlhWdCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9fQ==', 1782745951);

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','deposito','tecnico','consumo','acondicionador') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'deposito',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Admin User', 'admin@test.com', NULL, '$2y$12$ahNPWBt98BOAOxqRumL5Be3HzGcczS0ZR2..9KQzIbEXxr.M6OU3e', NULL, '2026-05-11 14:14:33', '2026-05-11 14:14:33', 'admin');
INSERT INTO `users` VALUES (2, 'Deposito User', 'deposito@test.com', NULL, '$2y$12$sE/hLMZTCZ.Yn/Upx4/2I.GcHSODEtwG0Xp.qKwQoAiVAjYdQIREi', NULL, '2026-05-11 14:14:33', '2026-05-11 14:14:33', 'deposito');
INSERT INTO `users` VALUES (3, 'Tecnico User', 'tecnico@test.com', NULL, '$2y$12$/AijsqpBEDER.4XGEd6rtuEE3K2oNDrn9uWROwtUhljshhZjrZcKe', NULL, '2026-05-11 14:14:33', '2026-05-11 14:14:33', 'tecnico');
INSERT INTO `users` VALUES (4, 'Consumo User', 'consumo@test.com', NULL, '$2y$12$ymiim8umrUvfT9kFXzHmDuDexs6gDUKnFuDgPU37SFFnyqB0rkQdu', NULL, '2026-05-11 14:14:33', '2026-05-11 14:14:33', 'consumo');
INSERT INTO `users` VALUES (5, 'Acondicionador User', 'acondicionador@test.com', NULL, '$2y$12$U9QpQ1R5YNqkfk8wNHp.Hu35Joen9t/H/bqn0zCiVWE9NfE3pxUHa', NULL, '2026-05-11 14:14:33', '2026-05-11 14:14:33', 'acondicionador');

SET FOREIGN_KEY_CHECKS = 1;
