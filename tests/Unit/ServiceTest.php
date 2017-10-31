<?php

namespace Shield\Box\Test\Unit;

use Carbon\Carbon;
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
    public function it_can_verify_a_valid_request_with_primary()
    {
        $this->app['config']['shield.services.box.options.primary'] = 'primary';
        $this->app['config']['shield.services.box.options.secondary'] = 'secondary';

        $timestamp = Carbon::now();

        $request = $this->request('anything');
        $request->headers->add([
            'BOX-DELIVERY-TIMESTAMP' => $timestamp->toAtomString(),
            'BOX-SIGNATURE-PRIMARY' => base64_encode(hash_hmac('sha256', 'anything' . $timestamp->toAtomString(), 'primary', true)),
            'BOX-SIGNATURE-SECONDARY' => base64_encode(hash_hmac('sha256', 'anythingx' . $timestamp->toAtomString(), 'secondary', true)),
        ]);

        Assert::assertTrue($this->service->verify($request, collect($this->app['config']['shield.services.box.options'])));
    }

    /** @test */
    public function it_can_verify_a_valid_request_with_secondary()
    {
        $this->app['config']['shield.services.box.options.primary'] = 'primary';
        $this->app['config']['shield.services.box.options.secondary'] = 'secondary';

        $timestamp = Carbon::now();

        $request = $this->request('anything');
        $request->headers->add([
            'BOX-DELIVERY-TIMESTAMP' => $timestamp->toAtomString(),
            'BOX-SIGNATURE-PRIMARY' => base64_encode(hash_hmac('sha256', 'anythingx' . $timestamp->toAtomString(), 'primary', true)),
            'BOX-SIGNATURE-SECONDARY' => base64_encode(hash_hmac('sha256', 'anything' . $timestamp->toAtomString(), 'secondary', true)),
        ]);

        Assert::assertTrue($this->service->verify($request, collect($this->app['config']['shield.services.box.options'])));
    }

    /**
     * @test
     */
    public function it_will_not_verify_an_old_request()
    {
        $this->app['config']['shield.services.box.options.primary'] = 'primary';
        $this->app['config']['shield.services.box.options.secondary'] = 'secondary';

        $timestamp = Carbon::now()->subMinutes(11);

        $request = $this->request('anything');
        $request->headers->add([
            'BOX-DELIVERY-TIMESTAMP' => $timestamp->toAtomString(),
            'BOX-SIGNATURE-PRIMARY' => base64_encode(hash_hmac('sha256', 'anything' . $timestamp->toAtomString(), 'primary', true)),
            'BOX-SIGNATURE-SECONDARY' => base64_encode(hash_hmac('sha256', 'anything' . $timestamp->toAtomString(), 'secondary', true)),
        ]);

        Assert::assertFalse($this->service->verify($request, collect($this->app['config']['shield.services.box.options'])));
    }

    /** @test */
    public function it_will_not_verify_a_bad_request()
    {
        $this->app['config']['shield.services.box.options.primary'] = 'primary';
        $this->app['config']['shield.services.box.options.secondary'] = 'secondary';

        $timestamp = Carbon::now();

        $request = $this->request('anything');
        $request->headers->add([
            'BOX-DELIVERY-TIMESTAMP' => $timestamp->toAtomString(),
            'BOX-SIGNATURE-PRIMARY' => base64_encode(hash_hmac('sha256', 'anythingx' . $timestamp->toAtomString(), 'primary', true)),
            'BOX-SIGNATURE-SECONDARY' => base64_encode(hash_hmac('sha256', 'anythingx' . $timestamp->toAtomString(), 'secondary', true)),
        ]);

        Assert::assertFalse($this->service->verify($request, collect($this->app['config']['shield.services.box.options'])));
    }

    /** @test */
    public function it_has_correct_headers_required()
    {
        Assert::assertArraySubset(['BOX-DELIVERY-TIMESTAMP', 'BOX-SIGNATURE-PRIMARY', 'BOX-SIGNATURE-SECONDARY'], $this->service->headers());
    }
}
