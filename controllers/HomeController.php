<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../services/PrayerService.php';

// Formatter for Egyptian Arabic numbers formatting
$fmt = new NumberFormatter('ar-EG', NumberFormatter::DECIMAL);

// Get Location and Prayer Data
$location = PrayerService::getUserLocation();
$city = $location['city'];
$country = $location['country'];
$timings = PrayerService::getPrayerTimings($city, $country);

// Fetch DB Statistics
$stmt = $pdo->query("SELECT COUNT(*) FROM books");
$stmt2 = $pdo->query("SELECT books.*, scholars.name AS author_name FROM books JOIN scholars ON books.author_id = scholars.id ORDER BY books.id DESC LIMIT 4");

$booksCount = $fmt->format($stmt->fetchColumn());
$books = $stmt2->fetchAll();

$stmt = $pdo->query("SELECT COUNT(*) FROM inventions");
$inventionsCount = $fmt->format($stmt->fetchColumn());