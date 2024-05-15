<?php

namespace Bunny_WP_Plugin\GuzzleHttp;

use Bunny_WP_Plugin\Psr\Http\Message\MessageInterface;
interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message) : ?string;
}
