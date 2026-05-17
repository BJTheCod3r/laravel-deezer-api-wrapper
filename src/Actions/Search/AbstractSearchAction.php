<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Actions\Search;

use BjTheCod3r\Deezer\Actions\BaseAction;
use BjTheCod3r\Deezer\Actions\Concerns\HasPagination;
use BjTheCod3r\Deezer\Config\DeezerConfig;
use BjTheCod3r\Deezer\Enums\SearchOrder;
use BjTheCod3r\Deezer\Enums\SearchType;
use BjTheCod3r\Deezer\Exceptions\ValidationException;
use BjTheCod3r\Deezer\Http\DeezerClient;
use BjTheCod3r\Deezer\Resources\Paginated;

/**
 * Shared request-building for any `GET /search[/<type>]` action.
 *
 * @method Paginated<mixed> get()
 * @method Paginated<mixed> execute()
 *
 * @see https://developers.deezer.com/api/search
 */
abstract class AbstractSearchAction extends BaseAction
{
    use HasPagination;

    protected string $query = '';

    protected ?bool $strict = null;

    protected ?SearchOrder $order = null;

    public function __construct(DeezerClient $client, protected DeezerConfig $config)
    {
        parent::__construct($client);

        $this->limit = $config->defaultLimit;
        $this->index = $config->defaultIndex;
    }

    public function q(string $query): static
    {
        $this->query = $query;

        return $this;
    }

    /**
     * When strict mode is on, Deezer disables the fuzzy matcher. Useful for
     * advanced queries like `artist:"daft punk" album:"discovery"`.
     */
    public function strict(bool $strict = true): static
    {
        $this->strict = $strict;

        return $this;
    }

    public function order(SearchOrder|string|null $order): static
    {
        $this->order = $order === null ? null : SearchOrder::coerce($order);

        return $this;
    }

    /**
     * Which `/search/<type>` segment to call. The default is `/search` (tracks).
     */
    protected function type(): ?SearchType
    {
        return null;
    }

    protected function path(): string
    {
        $type = $this->type();

        return $type === null ? '/search' : '/search/'.$type->value;
    }

    protected function validate(): void
    {
        if (trim($this->query) === '') {
            throw new ValidationException('A search query string is required.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function query(): array
    {
        return [
            'q' => $this->query,
            'strict' => $this->strictParam(),
            'order' => $this->order?->value,
            'limit' => $this->limit,
            'index' => $this->index,
        ];
    }

    /**
     * Deezer expects `on` / `off` literals (not booleans) for `strict`. Null
     * means "don't send the parameter at all" so the server uses its default.
     */
    protected function strictParam(): ?string
    {
        if ($this->strict === null) {
            return null;
        }

        return $this->strict ? 'on' : 'off';
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return Paginated<mixed>
     */
    protected function decode(array $payload): Paginated
    {
        $factory = ($this->type() ?? SearchType::Track)->resourceFactory();

        return Paginated::fromArray($payload, $factory);
    }
}
