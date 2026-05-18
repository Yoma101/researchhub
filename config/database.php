<?php
// config/database.php

// ── Session ──────────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Autoloader ───────────────────────────────────────────────────────────────
$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

// ── Application constants ─────────────────────────────────────────────────────
define('SITE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/public/');

// Login-attempt throttling
define('MAX_LOGIN_ATTEMPTS',   5);
define('LOCKOUT_TIME_MINUTES', 15);

// OTP expiry
define('OTP_EXPIRY_MINUTES', 10);

// SMTP (set via environment variables or edit directly)
define('SMTP_HOST',       getenv('SMTP_HOST')       ?: 'smtp.gmail.com');
define('SMTP_USERNAME',   getenv('SMTP_USERNAME')   ?: '');
define('SMTP_PASSWORD',   getenv('SMTP_PASSWORD')   ?: '');
define('SMTP_ENCRYPTION', getenv('SMTP_ENCRYPTION') ?: 'tls');
define('SMTP_PORT',       getenv('SMTP_PORT')       ?: 587);

// Google OAuth
define('GOOGLE_CLIENT_ID',     getenv('GOOGLE_CLIENT_ID')     ?: '');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '');
define('GOOGLE_REDIRECT_URI',  getenv('GOOGLE_REDIRECT_URI')  ?: SITE_URL . 'google-callback.php');

// GitHub Models API (for AI chatbot)
define('GITHUB_TOKEN', getenv('GITHUB_TOKEN') ?: '');

// ── Database credentials (Railway / generic PostgreSQL env vars) ──────────────
$db_host = getenv('PGHOST')     ?: 'localhost';
$db_port = getenv('PGPORT')     ?: '5432';
$db_name = getenv('PGDATABASE') ?: 'researchhub';
$db_user = getenv('PGUSER')     ?: 'postgres';
$db_pass = getenv('PGPASSWORD') ?: '';

// ── PDO connection ────────────────────────────────────────────────────────────
try {
    $dsn = "pgsql:host={$db_host};port={$db_port};dbname={$db_name}";
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(503);
    exit('Database connection failed. Please try again later.');
}

// ── Schema initialisation (creates tables if they do not exist) ───────────────
$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id            SERIAL PRIMARY KEY,
        full_name     VARCHAR(255)        NOT NULL,
        email         VARCHAR(255) UNIQUE NOT NULL,
        password_hash VARCHAR(255),
        google_id     VARCHAR(255),
        role          VARCHAR(50)         NOT NULL DEFAULT 'user',
        profile_pic   VARCHAR(255)                 DEFAULT 'default.png',
        affiliation   VARCHAR(255),
        created_at    TIMESTAMP           NOT NULL DEFAULT NOW()
    );

    CREATE TABLE IF NOT EXISTS papers (
        id          SERIAL PRIMARY KEY,
        user_id     INTEGER      NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        title       VARCHAR(500) NOT NULL,
        abstract    TEXT,
        file_path   VARCHAR(500),
        downloads   INTEGER      NOT NULL DEFAULT 0,
        upload_date TIMESTAMP    NOT NULL DEFAULT NOW()
    );

    CREATE TABLE IF NOT EXISTS comments (
        id         SERIAL PRIMARY KEY,
        paper_id   INTEGER NOT NULL REFERENCES papers(id)  ON DELETE CASCADE,
        user_id    INTEGER NOT NULL REFERENCES users(id)   ON DELETE CASCADE,
        comment    TEXT    NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT NOW()
    );

    CREATE TABLE IF NOT EXISTS questions (
        id         SERIAL PRIMARY KEY,
        user_id    INTEGER      NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        title      VARCHAR(500) NOT NULL,
        body       TEXT         NOT NULL,
        created_at TIMESTAMP    NOT NULL DEFAULT NOW()
    );

    CREATE TABLE IF NOT EXISTS answers (
        id          SERIAL PRIMARY KEY,
        question_id INTEGER NOT NULL REFERENCES questions(id) ON DELETE CASCADE,
        user_id     INTEGER NOT NULL REFERENCES users(id)     ON DELETE CASCADE,
        answer      TEXT    NOT NULL,
        created_at  TIMESTAMP NOT NULL DEFAULT NOW()
    );

    CREATE TABLE IF NOT EXISTS follows (
        id          SERIAL PRIMARY KEY,
        follower_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        followed_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE (follower_id, followed_id)
    );

    CREATE TABLE IF NOT EXISTS login_attempts (
        id           SERIAL PRIMARY KEY,
        email        VARCHAR(255) NOT NULL,
        attempt_time TIMESTAMP    NOT NULL DEFAULT NOW()
    );

    CREATE TABLE IF NOT EXISTS password_resets (
        id         SERIAL PRIMARY KEY,
        email      VARCHAR(255) NOT NULL,
        otp        VARCHAR(10)  NOT NULL,
        expires_at TIMESTAMP    NOT NULL,
        used       SMALLINT     NOT NULL DEFAULT 0,
        created_at TIMESTAMP    NOT NULL DEFAULT NOW()
    );
");
