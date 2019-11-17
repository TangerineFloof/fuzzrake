<?php

declare(strict_types=1);

namespace App\Twig;

use App\Repository\ArtisanCommissionsStatusRepository;
use App\Service\HostsService;
use App\Utils\DateTimeException;
use App\Utils\DateTimeUtils;
use App\Utils\FilterItem;
use App\Utils\Regexp\Utils as Regexp;
use App\Utils\StrUtils;
use App\Utils\Tracking\Status;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtensions extends AbstractExtension
{
    /**
     * @var ArtisanCommissionsStatusRepository
     */
    private $acsRepository;

    /**
     * @var HostsService
     */
    private $hostsService;

    public function __construct(ArtisanCommissionsStatusRepository $acsRepository, HostsService $hostsService)
    {
        $this->acsRepository = $acsRepository;
        $this->hostsService = $hostsService;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('since', [$this, 'sinceFilter']),
            new TwigFilter('other', [$this, 'otherFilter']),
            new TwigFilter('event_url', [StrUtils::class, 'shortPrintUrl']),
            new TwigFilter('status_text', [Status::class, 'text']),
            new TwigFilter('filterItemsMatching', [$this, 'filterItemsMatchingFilter']),
            new TwigFilter('humanFriendlyRegexp', [$this, 'filterHumanFriendlyRegexp']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getLastSystemUpdateTimeUtcStr', [$this, 'getLastSystemUpdateTimeUtcStrFunction']),
            new TwigFunction('getLastDataUpdateTimeUtcStr', [$this, 'getLastDataUpdateTimeUtcStrFunction']),
            new TwigFunction('isDevMachine', [$this, 'isDevMachineFunction']),
            new TwigFunction('isProduction', [$this, 'isProductionFunction']),
        ];
    }

    public function isDevMachineFunction(): bool
    {
        return $this->hostsService->isDevMachine();
    }

    public function isProductionFunction(): bool
    {
        return $this->hostsService->isProduction();
    }

    public function getLastDataUpdateTimeUtcStrFunction(): string
    {
        return $this->acsRepository->getLastCstUpdateTimeAsString();
    }

    public function getLastSystemUpdateTimeUtcStrFunction(): string
    {
        try {
            return DateTimeUtils::getUtcAt(`TZ=UTC git log -n1 --format=%cd --date=local`)->format('Y-m-d H:i');
        } catch (DateTimeException $e) {
            return 'unknown/error';
        }
    }

    public function otherFilter($primaryList, $otherList)
    {
        $primaryList = str_replace("\n", ', ', $primaryList);

        if ('' !== $otherList) {
            if ('' !== $primaryList) {
                return "$primaryList, Other";
            } else {
                return 'Other';
            }
        } else {
            return $primaryList;
        }
    }

    public function filterItemsMatchingFilter(array $items, string $matchWord): array
    {
        return array_filter($items, function (FilterItem $item) use ($matchWord) {
            return Regexp::match("#$matchWord#i", $item->getLabel());
        });
    }

    public function filterHumanFriendlyRegexp(string $input): string
    {
        $input = Regexp::replace('#\(\?<!.+?\)#', '', $input);
        $input = Regexp::replace('#\(\?!.+?\)#', '', $input);
        $input = Regexp::replace('#\([^a-z]+?\)#i', '', $input);
        $input = Regexp::replace('#[()?]#', '', $input);
        $input = Regexp::replace('#\[.+?\]#', '', $input);

        return strtoupper($input);
    }
}
