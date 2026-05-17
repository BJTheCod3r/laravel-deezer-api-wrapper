<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Actions\Search;

use BjTheCod3r\Deezer\Enums\SearchType;

/**
 * Calls `GET /search` (Deezer's default search, which returns tracks). Use
 * the typed siblings (`SearchAlbumsAction`, etc.) for other resource types.
 */
class SearchTracksAction extends AbstractSearchAction
{
    protected function type(): ?SearchType
    {
        return null;
    }
}
