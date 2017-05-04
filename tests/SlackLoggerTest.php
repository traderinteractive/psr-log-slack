<?php

namespace DominionEnterprisesTest\Psr\Log;

use DominionEnterprises\Psr\Log\SlackLogger;
use Psr\Log\LogLevel;

/**
 * @coversDefaultClass \DominionEnterprises\Psr\Log\SlackLogger
 * @covers ::__construct
 */
final class SlackLoggerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Verify basic behavior of log().
     *
     * @test
     * @covers ::log
     *
     * @return void
     */
    public function log()
    {
        $clientMock = $this->getMockBuilder('\\GuzzleHttp\\ClientInterface')->getMock();
        $webHookUrl = 'http://localhost/';

        $exception = new \RuntimeException('error message');

        $body = [
            'payload' => json_encode(
                [
                    'text' => "*[emergency]* test message\n*Exception:* RuntimeException\n*Message:* error message\n"
                    . "*File:* {$exception->getFile()}\n*Line:* {$exception->getLine()}",
                    'mrkdwn' => true,
                ]
            ),
        ];

        $clientMock->expects($this->once())->method('post')->with(
            $this->equalTo($webHookUrl),
            $this->equalTo(['body' => $body])
        );

        $logger = new SlackLogger($clientMock, $webHookUrl);

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
        $clientMock = $this->getMockBuilder('\\GuzzleHttp\\ClientInterface')->getMock();
        $webHookUrl = 'http://localhost/';
        $clientMock->method('post')->will($this->throwException(new \Exception('post() should not have been called.')));
        $logger = new SlackLogger($clientMock, $webHookUrl);
        $this->assertNull($logger->log(LogLevel::INFO, 'test message'));
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

        $body = [
            'payload' => json_encode(
                [
                    'text' => "*[emergency]* test message\n*Exception:* Error\n*Message:* error message\n*File:*"
                    . " {$throwable->getFile()}\n*Line:* {$throwable->getLine()}",
                    'mrkdwn' => true,
                ]
            ),
        ];

        $webHookUrl = 'http://localhost/';
        $clientMock = $this->getMockBuilder('\\GuzzleHttp\\ClientInterface')->getMock();
        $clientMock->expects($this->once())->method('post')->with(
            $this->equalTo($webHookUrl),
            $this->equalTo(['body' => $body])
        );

        $logger = new SlackLogger($clientMock, $webHookUrl);
        $logger->log(LogLevel::EMERGENCY, 'test message', ['exception' => $throwable]);
    }
}
