<?php

namespace DominionEnterprisesTest\Psr\Log;

use DominionEnterprises\Psr\Log\SlackLogger;
use Psr\Log\LogLevel;

/**
 * @coversDefaultClass \DominionEnterprises\Psr\Log\SlackLogger
 * @covers ::__construct
 * @covers ::<private>
 */
final class SlackLoggerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    private $webHookUrl = 'http://localhost/';

    /**
     * Verify basic behavior of log().
     *
     * @test
     * @covers ::log
     *
     * @return void
     */
    public function logBasicUsage()
    {
        $exception = new \RuntimeException('error message');
        $text = $this->buildExpectedPayloadText($exception);
        $logger = $this->getLogger($this->getGuzzleClientMock($text));
        $logger->log(LogLevel::EMERGENCY, 'test message', ['exception' => $exception]);
    }

    /**
     * Verify behavior of log() when level is not included when constructed.
     *
     * @test
     * @covers ::log
     *
     * @return void
     */
    public function logIgnoredLevel()
    {
        $mock = $this->getMockBuilder('\\GuzzleHttp\\ClientInterface')->getMock();
        $mock->method('post')->will(
            $this->throwException(new \Exception('post() should not have been called.'))
        );
        $logger = $this->getLogger($mock);
        $this->assertNull($logger->log(LogLevel::INFO, 'test message'));
    }

    /**
     * Verify behavior of log() without an execption.
     *
     * @test
     * @covers ::log
     *
     * @return void
     */
    public function logWithoutException()
    {
        $text = '*[emergency]* test message';
        $logger = $this->getLogger($this->getGuzzleClientMock($text));
        $logger->log(LogLevel::EMERGENCY, 'test message');
    }

    /**
     * Verify behavior of log() with Throwable.
     *
     * @test
     * @covers ::log
     *
     * @return void
     */
    public function logThrowable()
    {
        $throwable = new \Error('error message'); //Error implements Throwable but does not extend Exception
        $text = $this->buildExpectedPayloadText($throwable);
        $logger = $this->getLogger($this->getGuzzleClientMock($text));
        $logger->log(LogLevel::EMERGENCY, 'test message', ['exception' => $throwable]);
    }

    private function buildExpectedPayloadText(\Throwable $throwable) : string
    {
        $class = get_class($throwable);
        return "*[emergency]* test message\n*Exception:* {$class}\n*Message:* {$throwable->getMessage()}\n*File:*"
            . " {$throwable->getFile()}\n*Line:* {$throwable->getLine()}";
    }

    private function getGuzzleClientMock(string $expectedPayloadText)
    {
        $body = ['payload' => json_encode(['text' => $expectedPayloadText, 'mrkdwn' => true])];
        $mock = $this->getMockBuilder('\\GuzzleHttp\\ClientInterface')->getMock();
        $mock->expects($this->once())->method('post')->with(
            $this->equalTo($this->webHookUrl),
            $this->equalTo(['body' => $body])
        );

        return $mock;
    }

    private function getLogger(\GuzzleHttp\ClientInterface $client) : SlackLogger
    {
        return new SlackLogger($client, $this->webHookUrl);
    }
}
