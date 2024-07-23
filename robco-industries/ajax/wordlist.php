<?php
header('Content-Type: application/json');

// Read words from the file
$words = @file_get_contents('wordlist.txt');
if ($words === FALSE) {
    echo json_encode(["error" => "Unable to read word list"]);
    exit();
}

// Check and sanitize input parameters
if (!isset($_GET['length']) || !isset($_GET['count']) || 
    !is_numeric($_GET['length']) || !is_numeric($_GET['count'])) {
    echo json_encode(["error" => "Invalid parameters"]);
    exit();
}

$length = (int)$_GET['length'];
$count = (int)$_GET['count'];

// Split words and filter by length
$wordsArray = explode(" ", $words);
$filteredWords = array_filter($wordsArray, function($word) use ($length) {
    return strlen($word) == $length;
});

// If not enough words match the criteria, return an error
if (count($filteredWords) < $count) {
    echo json_encode(["error" => "Not enough words of the specified length"]);
    exit();
}

// Select random words
$selectedWords = [];
$failsafe = 0;
while (count($selectedWords) < $count && $failsafe < 1000) {
    $randomWord = strtolower($filteredWords[array_rand($filteredWords)]);
    if (!in_array($randomWord, $selectedWords)) {
        $selectedWords[] = $randomWord;
    }
    $failsafe++;
}

// If failsafe triggered, return error
if ($failsafe >= 1000) {
    echo json_encode(["error" => "Failsafe triggered"]);
    exit();
}

// Output selected words as JSON
echo json_encode(["words" => $selectedWords]);
?>
