<?php

declare(strict_types=1);

namespace App\Tracker;

class Regexes
{
    public function __construct(
        /**
         * @var string[]
         */
        private readonly array $falsePositives,

        /**
         * @var string[]
         */
        private readonly array $offerStatuses,

        /**
         * @var string[][]
         */
        private readonly array $groupTranslations,
    ) {
    }

    /**
     * @return string[]
     */
    public function getFalsePositives(): array
    {
        return $this->falsePositives;
    }

    /**
     * @return string[]
     */
    public function getOfferStatuses(): array
    {
        return $this->offerStatuses;
    }

    /**
     * @return string[][]
     */
    public function getGroupTranslations(): array
    {
        return $this->groupTranslations;
    }
}
