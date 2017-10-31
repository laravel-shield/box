<?php

namespace Shield\Skeleton;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Shield\Shield\Contracts\Service;

/**
 * Class Skeleton
 *
 * @package \Shield\Skeleton
 */
class Skeleton implements Service
{
    public function verify(Request $request, Collection $config): bool
    {
        // TODO: Implement verify() method.
    }

    public function headers(): array
    {
        // TODO: Implement headers() method.
    }
}
