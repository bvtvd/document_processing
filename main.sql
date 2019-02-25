/*
Navicat SQLite Data Transfer

Source Server         : documen_processing
Source Server Version : 31300
Source Host           : :0

Target Server Type    : SQLite
Target Server Version : 31300
File Encoding         : 65001

Date: 2019-02-25 13:46:24
*/

PRAGMA foreign_keys = OFF;

-- ----------------------------
-- Table structure for collectors
-- ----------------------------
DROP TABLE IF EXISTS "main"."collectors";
CREATE TABLE "collectors" (
"id"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"name"  TEXT,
"commission"  INTEGER,
"created_at"  TEXT,
"updated_at"  TEXT
);

-- ----------------------------
-- Table structure for merchants
-- ----------------------------
DROP TABLE IF EXISTS "main"."merchants";
CREATE TABLE "merchants" (
"id"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"name"  TEXT,
"collector_id"  INTEGER,
"created_at"  TEXT,
"updated_at"  TEXT
);

-- ----------------------------
-- Table structure for sqlite_sequence
-- ----------------------------
DROP TABLE IF EXISTS "main"."sqlite_sequence";
CREATE TABLE sqlite_sequence(name,seq);
PRAGMA foreign_keys = ON;
