<?php
/**
 * Подключение к SQLite и первоначальное создание базы данных.
 */
$dbDirectory = __DIR__ . '/../database';
if (!is_dir($dbDirectory)) {
    mkdir($dbDirectory, 0775, true);
}
$dbPath = $dbDirectory . '/movie_catalog.sqlite';

$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$pdo->exec('PRAGMA foreign_keys = ON');

$pdo->exec("
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'user' CHECK(role IN ('user', 'admin'))
)");

$pdo->exec("
CREATE TABLE IF NOT EXISTS movies (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    year INTEGER NOT NULL,
    genre TEXT NOT NULL,
    rating REAL NOT NULL,
    description TEXT NOT NULL,
    created_by INTEGER,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
)");

$count = (int)$pdo->query('SELECT COUNT(*) FROM movies')->fetchColumn();
if ($count === 0) {
    $seed = [
        ['Interstellar', 2014, 'Sci-Fi', 8.7, 'A team travels beyond our galaxy to find a new home for humanity.'],
        ['The Matrix', 1999, 'Sci-Fi', 8.7, 'A computer hacker discovers the reality he knows is a simulation.'],
        ['Titanic', 1997, 'Romance', 7.9, 'A romance unfolds aboard the famous passenger ship.'],
        ['Inception', 2010, 'Sci-Fi', 8.8, 'A specialist enters dreams to carry out a difficult mission.'],
        ['The Shawshank Redemption', 1994, 'Drama', 9.3, 'A banker forms a friendship while serving a prison sentence.']
    ];
    $insert = $pdo->prepare('INSERT INTO movies(title, year, genre, rating, description) VALUES (?, ?, ?, ?, ?)');
    foreach ($seed as $movie) {
        $insert->execute($movie);
    }
}
