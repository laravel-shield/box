<?php

namespace Shield\Box\Test\Unit;

use PHPUnit\Framework\Assert;
use Shield\Shield\Contracts\Service;
use Shield\Testing\TestCase;
use Shield\Box\Box;

/**
 * Class ServiceTest
 *
 * @package \Shield\Box\Test\Unit
 */
class ServiceTest extends TestCase
{
    /**
     * @var \Shield\Box\Box
     */
    protected $service;

    protected function setUp()
    {
        parent::setUp();

        $this->service = new Box;
    }

    /** @test */
    public function it_is_a_service()
    {
        Assert::assertInstanceOf(Service::class, new Box);
    }

    /** @test */
    public function it_can_verify_a_valid_request()
    {

    }

    /** @test */
    public function it_will_not_verify_a_bad_request()
    {

    }

    /** @test */
    public function it_has_correct_headers_required()
    {
        Assert::assertArraySubset(['BOX-DELIVERY-TIMESTAMP', 'BOX-SIGNATURE-PRIMARY', 'BOX-SIGNATURE-SECONDARY'], $this->service->headers());
    }
}
