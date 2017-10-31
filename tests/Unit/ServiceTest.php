<?php

namespace Shield\Skeleton\Test\Unit;

use PHPUnit\Framework\Assert;
use Shield\Shield\Contracts\Service;
use Shield\Testing\TestCase;
use Shield\Skeleton\Skeleton;

/**
 * Class ServiceTest
 *
 * @package \Shield\Skeleton\Test\Unit
 */
class ServiceTest extends TestCase
{
    /**
     * @var \Shield\Skeleton\Skeleton
     */
    protected $service;

    protected function setUp()
    {
        parent::setUp();

        $this->service = new Skeleton;
    }

    /** @test */
    public function it_is_a_service()
    {
        Assert::assertInstanceOf(Service::class, new Skeleton);
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
        Assert::assertArraySubset([], $this->service->headers());
    }
}
