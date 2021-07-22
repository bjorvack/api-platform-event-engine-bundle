<?php

declare(strict_types=1);

namespace ADS\Bundle\ApiPlatformEventEngineBundle\Resolver;

use ADS\Bundle\ApiPlatformEventEngineBundle\Filter\InMemoryFilterConverter;
use ApiPlatform\Core\DataProvider\ArrayPaginator;
use Closure;
use EventEngine\JsonSchema\JsonSchemaAwareRecord;

use function count;

final class InMemoryResolver extends FilterResolver
{
    /** @var array<JsonSchemaAwareRecord> */
    private array $states;

    public function __construct(InMemoryFilterConverter $filterConverter)
    {
        $this->filterConverter = $filterConverter;
    }

    /**
     * @param array<JsonSchemaAwareRecord> $states
     *
     * @return static
     */
    public function setStates(array $states)
    {
        $this->states = $states;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function states(): array
    {
        $states = $this->states;
        /** @var Closure|null $filter */
        $filter = $this->filter();
        /** @var Closure|null $order */
        $order = $this->orderBy();

        if ($filter) {
            $states = ($filter)($states);
        }

        if ($order) {
            $states = ($order)($states);
        }

        return $states;
    }

    /**
     * @inheritDoc
     */
    protected function totalItems(array $states): int
    {
        return count($this->states);
    }

    /**
     * @inheritDoc
     */
    protected function result(array $states, int $page, int $itemsPerPage, int $totalItems)
    {
        return new ArrayPaginator(
            $this->states,
            ($page - 1) * $itemsPerPage,
            $itemsPerPage,
        );
    }
}
