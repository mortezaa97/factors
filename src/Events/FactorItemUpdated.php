<?php

namespace Mortezaa97\Factors\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Mortezaa97\Factors\Models\FactorHasItem;

class FactorItemUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public FactorHasItem $item,
        public array $originalAttributes = []
    ) {
        //
    }
}


