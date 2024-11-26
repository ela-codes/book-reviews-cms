<?php
function display_content_preview($content, $limit = 150, $ellipsis = "...")
{
    $max_characters = 0;
    $result = "";
    $contentLength = strlen($content);

    if ($contentLength < $limit) {
        $max_characters = $contentLength;
    } else {
        $max_characters = $limit;
    }

    for ($i = 0; $i < $max_characters; $i++) {
        $result .= $content[$i];
    }
    return $result . $ellipsis;
}


?>