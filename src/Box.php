<?php

namespace Shield\Box;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Shield\Shield\Contracts\Service;

/**
 * Class Box
 *
 * @package \Shield\Box
 */
class Box implements Service
{
    public function verify(Request $request, Collection $config): bool
    {
        $rawTimestamp = (string) $request->header('BOX-DELIVERY-TIMESTAMP');

        $timestamp = Carbon::parse($rawTimestamp);

        // 10 Minute Tolerance
        if (Carbon::now(config('app.timezone', 'UTC'))->diffInSeconds($timestamp) > $config->get('tolerance', 600)) {
            return false;
        }

        $generated = $request->getContent() . $rawTimestamp;

        // Primary or Secondary can pass to be valid.
        return $this->check($generated, $config->get('primary'), $request->header('BOX-SIGNATURE-PRIMARY')) || $this->check($generated, $config->get('secondary'), $request->header('BOX-SIGNATURE-SECONDARY'));
    }

    public function check($generated, $key, $supplied)
    {
        $encoded = base64_encode(hash_hmac('sha256', $generated, $key, true));

        return hash_equals($encoded, $supplied);
    }

    public function headers(): array
    {
        return ['BOX-DELIVERY-TIMESTAMP', 'BOX-SIGNATURE-PRIMARY', 'BOX-SIGNATURE-SECONDARY'];
    }
}
