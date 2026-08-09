<?php

/**
 * Convert Western Arabic digits (0-9) to Eastern Arabic digits (٠-٩)
 */
function toEasternDigits($numStr) {
    $westernDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $easternDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    return str_replace($westernDigits, $easternDigits, (string)$numStr);
}

/**
 * Format 24-hour time string to 12-hour format (HH:MM)
 */
function format12Hour($time24) {
    $timeClean = explode(' ', trim($time24))[0];
    $parts = explode(':', $timeClean);
    $hour = (int)$parts[0];
    $minute = $parts[1];
    $hour12 = $hour % 12;
    if ($hour12 === 0) $hour12 = 12;
    return sprintf('%02d:%s', $hour12, $minute);
}