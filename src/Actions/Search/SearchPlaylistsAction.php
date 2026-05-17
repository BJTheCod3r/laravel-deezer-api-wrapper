<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Actions\Search;

use BjTheCod3r\Deezer\Enums\SearchType;

class SearchPlaylistsAction extends AbstractSearchAction
{
    protected function type(): ?SearchType
    {
        return SearchType::Playlist;
    }
}
