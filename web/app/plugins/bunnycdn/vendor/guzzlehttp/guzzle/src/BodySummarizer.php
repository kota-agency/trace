<?php

namespace Bunny_WP_Plugin\GuzzleHttp;

use Bunny_WP_Plugin\Psr\Http\Message\MessageInterface;
final class BodySummarizer implements BodySummarizerInterface
{
    /**
     * @var int|null
     */
    private $truncateAt;
    public function __construct(int $truncateAt = null)
    {
        $this->truncateAt = $truncateAt;
    }
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message) : ?string
    {
        return $this->truncateAt === null ? \Bunny_WP_Plugin\GuzzleHttp\Psr7\Message::bodySummary($message) : \Bunny_WP_Plugin\GuzzleHttp\Psr7\Message::bodySummary($message, $this->truncateAt);
    }
}
