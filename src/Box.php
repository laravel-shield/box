<?php

namespace Shield\Box;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Log;
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
        $timestamp = Carbon::parse($request->header('BOX-DELIVERY-TIMESTAMP'));

        // 10 Minute Tolerance
        if (Carbon::now(config('app.timezone', 'UTC'))->diffInSeconds($timestamp) > $config->get('tolerance', 600)) {
            return false;
        }

        $generated = $request->getContent() . $request->header('BOX-DELIVERY-TIMESTAMP');

        // Primary or Secondary can pass to be valid.
        $encoded = base64_encode(hash_hmac('sha256', $generated, $config->get('primary'), true));
        if (hash_equals($encoded, $request->header('BOX-SIGNATURE-PRIMARY'))) {
            return true;
        }

        $encoded = base64_encode(hash_hmac('sha256', $generated, $config->get('secondary'), true));
        if (hash_equals($encoded, $request->header('BOX-SIGNATURE-SECONDARY'))) {
            return true;
        }

        return false;
    }

    public function headers(): array
    {
        return ['BOX-DELIVERY-TIMESTAMP', 'BOX-SIGNATURE-PRIMARY', 'BOX-SIGNATURE-SECONDARY'];
    }
}
