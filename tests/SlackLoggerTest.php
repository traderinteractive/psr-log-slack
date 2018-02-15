<?php

namespace TraderInteractiveTest\Psr\Log;

use TraderInteractive\Psr\Log\SlackLogger;
use Psr\Log\LogLevel;

/**
 * @coversDefaultClass \TraderInteractive\Psr\Log\SlackLogger
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
        $mock->method('request')->will(
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
     * @param \Throwable $throwable The exception or error to be logged in the test.
     *
     * @test
     * @covers ::log
     * @dataProvider provideThrowables
     *
     * @return void
     */
    public function logThrowable(\Throwable $throwable)
    {
        $text = $this->buildExpectedPayloadText($throwable);
        $logger = $this->getLogger($this->getGuzzleClientMock($text));
        $logger->log(LogLevel::EMERGENCY, 'test message', ['exception' => $throwable]);
    }

    /**
     * Data provider for ensure all types of exceptions can be logged.
     *
     * @return array
     */
    public function provideThrowables() : array
    {
        return [
            'runtimeException' => [new \RuntimeException('a runtime exception')],
            'typeError' => [new \TypeError('a type error')],
        ];
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
        $mock->expects($this->once())->method('request')->with(
            $this->equalTo('POST'),
            $this->equalTo($this->webHookUrl),
            $this->equalTo(['json' => $body])
        );

        return $mock;
    }

    private function getLogger(\GuzzleHttp\ClientInterface $client) : SlackLogger
    {
        return new SlackLogger($client, $this->webHookUrl);
    }
}
