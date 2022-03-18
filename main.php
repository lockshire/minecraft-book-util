<?php

$mode = php_sapi_name();

switch ($mode) {
    case 'cli':
        $text = file_get_contents('book1.txt');
        $title = 'Book Title';
        $author = 'Author';
        $generation = 0;
        break;
    default:
        //output(var_export($_POST, true));
        $text = $_POST['book_text'];
        $title = $_POST['book_title'];
        $author = $_POST['book_author'];
        $generation = $_POST['book_generation'];
        break;
}

// Convert book to array
$text = str_split($text);

// Define book structure
$book = [
    'page' => [],
    'title' => $title,
    'author' => $author,
    'generation' => $generation,
    'display' => 'N/A',
];

// Build book

$lineLength = 0;
$lines = 0;
$currentPage = '';

foreach ($text as $char) {

    // Intercept newlines
    if ($char === "\n") {
        $lines++;
        $lineLength = 0;
        $currentPage .= '\\n';
        continue;
    }

    // Add current character to current page
    $currentPage .= $char;

    // Figure out the line length
    $lineLength += calcLen($char);

    // If the line length is too long, start counting a new line
    if ($lineLength > 25) {
        $lines++;
        //output("\$lines = $lines");
        $lineLength = 0;
    }

    // If the number of lines is too long, start a new page
    if ($lines > 13) {
        //output("Just started a new page");
        $book['page'][] = $currentPage;
        $currentPage = '';
        $lineLength = 0;
        $lines = 0;
    }
}

$cmd = buildCommand($book);
output($cmd);
output();

function calcLen($char) {
    switch ($char) {
        case 'abcdefghijklmnopqrstuvwxyz':
            return 1;
            break;
        case 'ABCDEFGHIJKLMNOPQRSTUVWXZY':
            return 2;
            break;
        default:
            return 1;
    }
}

function buildCommand(array $book)
{
    $pages = [];
    foreach ($book['page'] as $page) {
        $page = str_replace("'", "\'", $page);
        $page = str_replace('"', '\\"', $page);
        $pages[] = sprintf('\'{"text":"%s"}\'', $page);
    }

    $contents = sprintf('pages:[%s],title:"%s",author:"%s"',
        join(',', $pages), 
        $book['title'],
        $book['author']
    );

    $cmd = sprintf('/give @p written_book{%s}', $contents);

    return $cmd;
}

function output(string $msg = '')
{
    global $mode;

    if ($mode === 'cli') {
        echo $msg . "\n";
    } else {
        //echo '<form><textarea width="120" height="10">' . str_replace('\\n', '&#92;&#92;n', $msg) . '</textarea></form>';
        $msg = str_replace('\\n', '&#92;&#92;n', $msg);
        echo '<p><code>' . $msg . '</code></p>';
    }
}