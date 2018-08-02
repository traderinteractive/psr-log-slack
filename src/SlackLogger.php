<?php

namespace TraderInteractive\Psr\Log;

use Chadicus\Psr\Log\LevelValidatorTrait;
use Chadicus\Psr\Log\MessageValidatorTrait;
use Chadicus\Psr\Log\MessageInterpolationTrait;
use GuzzleHttp\ClientInterface;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * PSR-3 Logger implementation writing to a slack web hook.
 */
final class SlackLogger extends AbstractLogger implements LoggerInterface
{
    use LevelValidatorTrait;
    use MessageValidatorTrait;
    use MessageInterpolationTrait;

    /**
     * Guzzle HTTP client.
     *
     * @var ClientInterface
     */
    private $client;

    /**
     * Slack web hook url
     *
     * @var string
     */
    private $webHookUrl;

    /**
     * Array of log levels which should be reported to Slack.
     *
     * @var string[]
     */
    private $observedLevels;

    /**
     * Create a new instance of SlackLogger.
     *
     * @param ClientInterface $client         Guzzle HTTP client.
     * @param string          $webHookUrl     Slack web hook url.
     * @param array           $observedLevels Array of log levels which should be reported to Slack.
     */
    public function __construct(
        ClientInterface $client,
        string $webHookUrl,
        array $observedLevels = [LogLevel::EMERGENCY]
    ) {
        $this->client = $client;
        $this->webHookUrl = $webHookUrl;
        array_walk($observedLevels, [$this, 'validateLevel']);
        $this->observedLevels = $observedLevels;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param string $level   A valid RFC 5424 log level.
     * @param string $message The base log message.
     * @param array  $context Any extraneous information that does not fit well in a string.
     *
     * @return void
     */
    public function log($level, $message, array $context = [])//@codingStandardsIgnoreLine Interface does not define type-hints
    {
        if (!in_array($level, $this->observedLevels)) {
            return;
        }

        $this->validateLevel($level);
        $this->validateMessage($message);

        $exception = $this->getExceptionStringFromContext($context);
        unset($context['exception']);

        $payload = [
            'text' => "*[{$level}]* {$this->interpolateMessage($message, $context)}{$exception}",
            'mrkdwn' => true,
        ];

        $this->client->request('POST', $this->webHookUrl, ['json' => $payload, 'http_errors' => false]);
    }

    private function getExceptionStringFromContext(array $context) : string
    {
        $exception = $context['exception'] ?? null;
        if (!is_a($exception, '\\Throwable')) {
            return '';
        }

        return sprintf(
            "\n*Exception:* %s\n*Message:* %s\n*File:* %s\n*Line:* %d",
            get_class($context['exception']),
            $context['exception']->getMessage(),
            $context['exception']->getFile(),
            $context['exception']->getLine()
        );
    }
}
