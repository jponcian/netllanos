/*
 Navicat Premium Data Transfer

 Source Server         : Local
 Source Server Type    : MySQL
 Source Server Version : 80300
 Source Host           : localhost:3306
 Source Schema         : losllanos

 Target Server Type    : MySQL
 Target Server Version : 80300
 File Encoding         : 65001

 Date: 03/10/2025 23:36:04
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for sol_aplicativos_solicitados
-- ----------------------------
DROP TABLE IF EXISTS `sol_aplicativos_solicitados`;
CREATE TABLE `sol_aplicativos_solicitados`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_funcionario` int NOT NULL,
  `id_aplicativo` int NOT NULL,
  `id_solicitud` int NOT NULL,
  `accion` enum('INCLUSION','EXCLUSION') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT 'INCLUSION',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_funcionario`(`id_funcionario` ASC) USING BTREE,
  INDEX `id_aplicativo`(`id_aplicativo` ASC) USING BTREE,
  INDEX `id_solicitud`(`id_solicitud` ASC) USING BTREE,
  CONSTRAINT `sol_aplicativos_solicitados_ibfk_1` FOREIGN KEY (`id_funcionario`) REFERENCES `sol_funcionarios` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `sol_aplicativos_solicitados_ibfk_2` FOREIGN KEY (`id_aplicativo`) REFERENCES `a_matriz_aplicativos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `sol_aplicativos_solicitados_ibfk_3` FOREIGN KEY (`id_solicitud`) REFERENCES `sol_solicitudes` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 38 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sol_aplicativos_solicitados
-- ----------------------------
INSERT INTO `sol_aplicativos_solicitados` VALUES (31, 2, 1, 18, 'INCLUSION');
INSERT INTO `sol_aplicativos_solicitados` VALUES (32, 2, 2, 18, 'INCLUSION');
INSERT INTO `sol_aplicativos_solicitados` VALUES (33, 2, 3, 18, 'INCLUSION');
INSERT INTO `sol_aplicativos_solicitados` VALUES (34, 2, 6, 18, 'INCLUSION');
INSERT INTO `sol_aplicativos_solicitados` VALUES (35, 3, 1, 19, 'INCLUSION');
INSERT INTO `sol_aplicativos_solicitados` VALUES (36, 3, 2, 19, 'INCLUSION');
INSERT INTO `sol_aplicativos_solicitados` VALUES (37, 3, 3, 19, 'INCLUSION');

-- ----------------------------
-- Table structure for sol_funcionarios
-- ----------------------------
DROP TABLE IF EXISTS `sol_funcionarios`;
CREATE TABLE `sol_funcionarios`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_solicitud` int NOT NULL,
  `cedula` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `cargo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `division` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usuario` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_solicitud`(`id_solicitud` ASC) USING BTREE,
  CONSTRAINT `sol_funcionarios_ibfk_1` FOREIGN KEY (`id_solicitud`) REFERENCES `sol_solicitudes` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sol_funcionarios
-- ----------------------------
INSERT INTO `sol_funcionarios` VALUES (2, 18, 'V-12512260', 'AIDA JOSEFINA ANDRADE MATOS', '', 'AREA RECAUDACION', '');
INSERT INTO `sol_funcionarios` VALUES (3, 19, 'V-13820854', 'Jose Miguel Pérez Linares', '', 'RECAUDACION', '');

-- ----------------------------
-- Table structure for sol_solicitudes
-- ----------------------------
DROP TABLE IF EXISTS `sol_solicitudes`;
CREATE TABLE `sol_solicitudes`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `fecha` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `cedula_solicitante` int NOT NULL,
  `status` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT 'PENDIENTE',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sol_solicitudes
-- ----------------------------
INSERT INTO `sol_solicitudes` VALUES (18, '2025-10-03 22:50:44', 16912337, 'PENDIENTE');
INSERT INTO `sol_solicitudes` VALUES (19, '2025-10-03 23:03:31', 16912337, 'PENDIENTE');

SET FOREIGN_KEY_CHECKS = 1;
