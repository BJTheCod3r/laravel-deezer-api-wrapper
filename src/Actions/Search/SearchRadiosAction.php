<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Actions\Search;

use BjTheCod3r\Deezer\Enums\SearchType;

class SearchRadiosAction extends AbstractSearchAction
{
    protected function type(): ?SearchType
    {
        return SearchType::Radio;
    }
}
