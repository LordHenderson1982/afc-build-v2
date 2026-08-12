-- IPTV Smarters MySQL Schema
-- Run this in phpMyAdmin to create all tables

CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username TEXT,
    password TEXT
);

CREATE TABLE IF NOT EXISTS dns (
    id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    title TEXT,
    url TEXT
);

CREATE TABLE IF NOT EXISTS maintenance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title TEXT,
    body TEXT,
    mode TEXT
);

CREATE TABLE IF NOT EXISTS devices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    deviceid TEXT,
    deviceusername TEXT,
    added_on TEXT
);

CREATE TABLE IF NOT EXISTS reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username TEXT,
    macaddress TEXT,
    section TEXT,
    section_category TEXT,
    report_title TEXT,
    report_sub_title TEXT,
    report_cases TEXT,
    report_custom_message TEXT,
    stream_name TEXT,
    stream_id INT
);

CREATE TABLE IF NOT EXISTS feedback (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username TEXT,
    macaddress TEXT,
    feedback_content TEXT
);

CREATE TABLE IF NOT EXISTS announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title TEXT NOT NULL,
    message TEXT NOT NULL,
    created_on TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS announcement_views (
    announcement_id INT NOT NULL,
    deviceid TEXT NOT NULL,
    FOREIGN KEY (announcement_id) REFERENCES announcements(id)
);

CREATE TABLE IF NOT EXISTS vpn (
    id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    vpn_country TEXT,
    vpn_file_name TEXT,
    username TEXT,
    password TEXT,
    embed TEXT
);

CREATE TABLE IF NOT EXISTS advertisement (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title TEXT,
    text TEXT
);

CREATE TABLE IF NOT EXISTS ads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title TEXT,
    text TEXT
);

CREATE TABLE IF NOT EXISTS ads2 (
    id INT PRIMARY KEY AUTO_INCREMENT,
    text TEXT
);

CREATE TABLE IF NOT EXISTS ads2_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ads2_id INT,
    url TEXT,
    FOREIGN KEY (ads2_id) REFERENCES ads2(id)
);

CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tmdb_api_enabled INT
);

CREATE TABLE IF NOT EXISTS update_apk (
    id INT PRIMARY KEY AUTO_INCREMENT,
    app_name TEXT,
    package TEXT,
    version TEXT,
    url TEXT,
    u_date TEXT
);

-- Insert default rows
INSERT IGNORE INTO advertisement (title, text) VALUES ('','');
INSERT IGNORE INTO maintenance (title, body, mode) VALUES ('','','no');
INSERT IGNORE INTO settings (tmdb_api_enabled) VALUES (0);