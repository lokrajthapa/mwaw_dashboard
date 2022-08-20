<?php

namespace App\Google;

class GmailParser
{
    static public function parse(string $message): string
    {
        $messageParts = explode("\r\n", $message);
        $output = [];
        if (str_contains($message, "wrote:")) {

            foreach ($messageParts as $part) {
                if ($part == "wrote:") {
                    break;
                } else {
                    $output[] = $part;
                }
            }
            array_pop($output);
            array_pop($output);
            return implode("\r\n", $output);
        }
        return $message;

    }

}
