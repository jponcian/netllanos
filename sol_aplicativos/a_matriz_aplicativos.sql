/*
 Navicat Premium Dump SQL

 Source Server         : Local
 Source Server Type    : MySQL
 Source Server Version : 50617 (5.6.17)
 Source Host           : localhost:3306
 Source Schema         : losllanos

 Target Server Type    : MySQL
 Target Server Version : 50617 (5.6.17)
 File Encoding         : 65001

 Date: 03/10/2025 13:02:11
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for a_matriz_aplicativos
-- ----------------------------
DROP TABLE IF EXISTS `a_matriz_aplicativos`;
CREATE TABLE `a_matriz_aplicativos`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_div` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `aplicativo` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `rol` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `division_area` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 66 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of a_matriz_aplicativos
-- ----------------------------
INSERT INTO `a_matriz_aplicativos` VALUES (1, '13, 24, 30, 45, 58', 'Anulación de Declaración', 'ANALÍSTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (2, '13, 24, 30, 45, 58', 'Beneficios Fiscales', 'TRANSCRIPTOR', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (3, '13, 24, 30, 45, 58', 'Consulta Actividad Económica', 'CONSULTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (4, '13, 24, 30, 45, 58', 'Consulta de excedentes de IVA', 'ANALÍSTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (5, '13, 24, 30, 45, 58', 'Consulta Estadística de Recaudación  por Forma- Periodo', 'NORMATIVO', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (6, '13, 24, 30, 45, 58', 'Consulta de Compromisos de pago', 'ANALÍSTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (7, '13, 24, 30, 45, 58', 'Consulta de la Cuenta Corriente', 'CONSULTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (8, '13, 24, 30, 45, 58', 'Consulta de Declaración Sucesiones', 'CONSULTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (9, '13, 24, 30, 45, 58', 'Consulta de pagos en ISENIAT  Pago Contribuyente', 'CONSULTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (10, '13, 24, 30, 45, 58', 'Consulta de Pagos UCE', 'SUPERVISOR', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (11, '13, 24, 30, 45, 58', 'Consulta de Relaciones RIF', 'SIN ROL', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (12, '13, 24, 30, 45, 58', 'Consulta de Retenciones', 'NORMATIVO', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (13, '13, 24, 30, 45, 58', 'Consulta de Retenciones ISLR', 'NORMATIVO', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (14, '13, 24, 30, 45, 58', 'Consulta de Transmisión Bancaria', 'NORMATIVO', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (15, '13, 24, 30, 45, 58', 'Consulta de RIF', 'CONSULTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (16, '13, 24, 30, 45, 58', 'Consulta del Estado de Cuenta', 'CONSULTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (17, '13, 24, 30, 45, 58', 'Consulta Indicadores de Fiscalización ', 'ANALÍSTA/ESPECIAL', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (18, '13, 24, 30, 45, 58', 'Consulta de Pagos electrónicos', 'NORMATIVO', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (19, '13, 24, 30, 45, 58', 'Consulta Saldo de Retenciones de IVA', 'ANALÍSTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (20, '13, 24, 30, 45, 58', 'Declaración Extemporánea y Pagos', 'ANALÍSTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (21, '13, 24, 30, 45, 58', 'Exportadores: Análisis de Proveedores', 'ANALÍSTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (22, '13, 24, 30, 45, 58', 'Exportadores: Carga de Relación de Compra/Venta', 'ANALÍSTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (23, '13, 24, 30, 45, 58', 'Exportadores: Consultar Relaciones de Compra/Venta', 'ANALÍSTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (24, '13, 24, 30, 45, 58', 'Módulo de Conciliación Manual', 'ANALÍSTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (25, '13, 24, 30, 45, 58', 'Módulo de Generación de Actas de Requerimientos', 'ANALÍSTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (26, '13, 24, 30, 45, 58', 'Registro de Devoluciones de Retenciones de IVA', 'ANALÍSTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (27, '13, 24, 30, 45, 58', 'Registro de Información Fiscal', 'TRANSCRIPTOR', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (28, '13, 24, 30, 45, 58', 'Registro de Información Fiscal', 'OPERATIVO GCIA. REGIONAL', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (29, '13, 24, 30, 45, 58', 'Registro de Información Fiscal', 'SUPERVISOR', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (30, '13, 24, 30, 45, 58', 'Registro de Tierras: Módulo de Anulación de Registro', 'SUPERVISOR', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (31, '13, 24, 30, 45, 58', 'Registro de Tierras: Consulta Registro Tierra Reg.', 'CONSULTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (32, '13, 24, 30, 45, 58', 'Registro de Tierras: Módulo de Registros de Tierras', 'TRANSCRIPTOR', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (33, '13, 24, 30, 45, 58', 'Registro de Vivienda Principal', 'CONSULTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (34, '13, 24, 30, 45, 58', 'Trámite de Devoluciones de Retenciones de IVA', 'ANALÍSTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (35, '13, 24, 30, 45, 58', 'Consulta de Ítems de Retenciones de IVA', 'ANALISTA', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (36, '13, 24, 30, 45, 58', 'Estadística de Recaudación', 'NORMATIVO  ', 'RECAUDACION');
INSERT INTO `a_matriz_aplicativos` VALUES (37, '17, 28, 32, 35, 44', 'Anulación de Declaración', 'ANALÍSTA', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (38, '17, 28, 32, 35, 44', 'Consulta Actividad Económica', 'CONSULTA', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (39, '17, 28, 32, 35, 44', 'Consulta de excedentes de IVA', 'ANALÍSTA', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (40, '17, 28, 32, 35, 44', 'Consulta Estadística de Recaudación      ', 'ANALÍSTA  /NORMATIVO', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (41, '17, 28, 32, 35, 44', 'Consulta de Compromisos de pago', 'ANALÍSTA', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (42, '17, 28, 32, 35, 44', 'Consulta de la Cuenta Corriente', 'CONSULTA', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (43, '17, 28, 32, 35, 44', 'Consulta de Declaración Sucesiones', 'CONSULTA', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (44, '17, 28, 32, 35, 44', 'Consulta de pagos en ISENIAT    ', 'CONSULTA/ESPECIAL', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (45, '17, 28, 32, 35, 44', 'Consulta de Pagos UCE', 'SUPERVISOR', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (46, '17, 28, 32, 35, 44', 'Consulta de Relaciones   RIF', 'SIN ROL', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (47, '17, 28, 32, 35, 44', 'Consulta de Retenciones', 'NORMATIVO', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (48, '17, 28, 32, 35, 44', 'Consulta de Retenciones ISLR', 'NORMATIVO', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (49, '17, 28, 32, 35, 44', 'Consulta de Transmisión Bancaria', 'NORMATIVO', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (50, '17, 28, 32, 35, 44', 'Consulta de RIF', 'CONSULTA', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (51, '17, 28, 32, 35, 44', 'Consulta del Estado de Cuenta', 'CONSULTA', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (52, '17, 28, 32, 35, 44', 'Consulta Indicadores de Fiscalización       ', 'ESPECIAL', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (53, '17, 28, 32, 35, 44', 'Consulta de Pagos electrónicos', 'NORMATIVO', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (54, '17, 28, 32, 35, 44', 'Declaración Extemporánea y Pagos', 'ANALÍSTA', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (55, '17, 28, 32, 35, 44', 'Módulo de Conciliación Manual', 'ANALÍSTA', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (56, '17, 28, 32, 35, 44', 'Módulo de Liquidaciones', 'ANALÍSTA', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (57, '17, 28, 32, 35, 44', 'Registro de Devoluciones de Retenciones de IVA', 'ANALÍSTA', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (58, '17, 28, 32, 35, 44', 'Registro de Información Fiscal', 'ESPECIAL', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (59, '17, 28, 32, 35, 44', 'Registro de Vivienda Principal', 'CONSULTA', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (60, '17, 28, 32, 35, 44', 'Consulta Saldo de Retenciones de IVA', 'CONSULTA', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (61, '17, 28, 32, 35, 44', 'Sistema Integrado de la Gestión Recaudadora', 'ANALÍSTA', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (62, '17, 28, 32, 35, 44', 'Trámite de Devoluciones de Retenciones de IVA', 'ANALÍSTA', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (63, '17, 28, 32, 35, 44', 'Consulta de Ítems de Retenciones de IVA', 'ANALÍSTA', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (64, '17, 28, 32, 35, 44', 'Estadística de Recaudación', 'NORMATIVO  ', 'ESPECIALES');
INSERT INTO `a_matriz_aplicativos` VALUES (65, '17, 28, 32, 35, 44', 'Autorizador de Pago/ Sincompago Proceso Bancario', 'ANALÍSTA', 'ESPECIALES');

SET FOREIGN_KEY_CHECKS = 1;
