<?php

if (!function_exists('displayTitleOrText')) {
    function displayTitleOrText($script, $length = 20)
    {
        $title = optional($script)->title;
        $text = optional($script)->text;
        if ($title) {
            return $title;
        }
        if ($text) {
            $plain = strip_tags($text);
            $truncated = mb_substr($plain, 0, $length);
            if (mb_strlen($plain) > $length) {
                $truncated .= '...';
            }
            return $truncated;
        }
        return '(無題)';
    }
}
