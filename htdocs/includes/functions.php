<?php
// includes/functions.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//for prevent attacks

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

//create csrf token

function csrf_token() {

    // if token not already created, generate one and save it in session
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}


  // validate a submitted csrf token
 
function validate_csrf($token) {

    // return true if tokens match

    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

//convert  markdown text to html
 
function markdown_to_html($text) {

    // escape all HTML tags first to prevent code injection
    $text = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    // convert code block like to <pre><code>...</code></pre>
    $text = preg_replace('/```(.*?)```/s', '<pre><code>$1</code></pre>', $text);

    // convert inline code like `code` into <code>code</code>
    $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);

    // convert headings using # symbols (# = h1, ## = h2, etc.)
    $text = preg_replace('/^######\s*(.+)$/m', '<h6>$1</h6>', $text);
    $text = preg_replace('/^#####\s*(.+)$/m', '<h5>$1</h5>', $text);
    $text = preg_replace('/^####\s*(.+)$/m', '<h4>$1</h4>', $text);
    $text = preg_replace('/^###\s*(.+)$/m', '<h3>$1</h3>', $text);
    $text = preg_replace('/^##\s*(.+)$/m', '<h2>$1</h2>', $text);
    $text = preg_replace('/^#\s*(.+)$/m', '<h1>$1</h1>', $text);

    // convert bold text **bold** or __bold__ into <strong>bold</strong>
    $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);
    $text = preg_replace('/__(.+?)__/s', '<strong>$1</strong>', $text);

    // convert italic text to <em>italic</em>
    $text = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $text);
    $text = preg_replace('/_(.+?)_/s', '<em>$1</em>', $text);

    // convert links to <a href="url">text</a>
    $text = preg_replace_callback('/\[(.*?)\]\((.*?)\)/', function ($m) {
        $text = $m[1]; // link text
        $url  = htmlspecialchars($m[2], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); // link URL
        return "<a href=\"$url\" target=\"_blank\" rel=\"noopener\">$text</a>";
    }, $text);

    // split paragraph
    $paragraphs = preg_split("/\r?\n\r?\n/", trim($text));

    $html = '';
    foreach ($paragraphs as $p) {
        // convert single new lines to <br> tags
        $p = nl2br($p);

        // put all paragraph into <p> tags
        $html .= "<p>$p</p>\n";
    }

   
    return $html;
}
