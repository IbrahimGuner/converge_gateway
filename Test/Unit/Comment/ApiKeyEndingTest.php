<?php

namespace Adyen\Payment\Test\Comment;

use Adyen\Payment\Model\Comment\ApiKeyEnding;
use Magento\Framework\Encryption\Encryptor;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class ApiKeyEndingTest extends TestCase
{
    /**
     * @var ApiKeyEnding
     */
    private $apiKeyEndingComment;

    public function setUp(): void
    {
        /** @var MockObject|Encryptor $encryptor */
        $encryptor = $this->createMock(Encryptor::class);
        $map = [
            ['4321', '1234'],
            ['asdfasdfasdf', 'qwerqwerqwer']
        ];
        $encryptor->method('decrypt')
            ->will($this->returnValueMap($map));

        $this->apiKeyEndingComment = new ApiKeyEnding($encryptor);
    }

    public function testCommentReturnsJustTheEnding()
    {
        $this->assertEquals(
            'Your stored key ends with <strong>1234</strong>',
            $this->apiKeyEndingComment->getCommentText('4321')
        );
        $this->assertEquals(
            'Your stored key ends with <strong>qwer</strong>',
            $this->apiKeyEndingComment->getCommentText('asdfasdfasdf')
        );
    }
}
