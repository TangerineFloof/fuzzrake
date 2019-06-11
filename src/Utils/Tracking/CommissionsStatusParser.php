<?php

declare(strict_types=1);

namespace App\Utils\Tracking;

use App\Utils\Regexp\Regexp;
use App\Utils\Regexp\Factory;
use App\Utils\Regexp\Variant;
use App\Utils\Web\WebpageSnapshot;
use App\Utils\Web\WebsiteInfo;
use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;

class CommissionsStatusParser
{
    /**
     * @var Regexp[]
     */
    private $falsePositivesRegexps;

    /**
     * @var Regexp[]
     */
    private $statusRegexps;

    /**
     * @var Variant
     */
    private $open;
    /**
     * @var Variant
     */
    private $closed;

    public function __construct()
    {
        $this->open = new Variant(['STATUS' => 'OPEN']);
        $this->closed = new Variant(['STATUS' => 'CLOSED']);
        $this->any = new Variant(['STATUS' => '(OPEN|CLOSED)']);

        $rf = new Factory(CommissionsStatusRegexps::COMMON_REPLACEMENTS);
        $this->falsePositivesRegexps = $rf->createSet(CommissionsStatusRegexps::FALSE_POSITIVES_REGEXES, [$this->any]);
        $this->statusRegexps = $rf->createSet(CommissionsStatusRegexps::GENERIC_REGEXES, [$this->open, $this->closed]);

//        $this->debugDumpRegexpes();
    }

    /**
     * @param WebpageSnapshot $snapshot
     *
     * @return bool
     *
     * @throws CommissionsStatusParserException
     */
    public function areCommissionsOpen(WebpageSnapshot $snapshot): bool
    {
        $additionalFilter = $this->guessFilterFromUrl($snapshot->getUrl());
        $artisanName = $snapshot->getOwnerName();

        $inputTexts = array_map(function (string $input) use ($artisanName, $additionalFilter) {
            return $this->processInputText($artisanName, $additionalFilter, $input);
        }, $snapshot->getAllContents());

        $open = $this->matchesGivenRegexpSet($inputTexts, $this->statusRegexps, $this->open);
        $closed = $this->matchesGivenRegexpSet($inputTexts, $this->statusRegexps, $this->closed);

        return $this->analyseResult($open, $closed);
    }

    /**
     * @param string $artisanName
     * @param string $additionalFilter
     * @param string $inputText
     *
     * @return string
     *
     * @throws CommissionsStatusParserException
     */
    private function processInputText(string $artisanName, string $additionalFilter, string $inputText): string
    {
        $inputText = $this->cleanHtml($inputText);
        $inputText = $this->processArtisansName($artisanName, $inputText);
        $inputText = $this->removeFalsePositives($inputText);

        try {
            $inputText = $this->applyFilters($inputText, $additionalFilter);
        } catch (InvalidArgumentException $ex) {
            throw new CommissionsStatusParserException("Filtering failed ({$ex->getMessage()})");
        }

        return $inputText;
    }

    private function processArtisansName(string $artisanName, string $inputText)
    {
        $inputText = str_ireplace($artisanName, 'STUDIO_NAME', $inputText);
        if (strlen($artisanName) > 2 && 's' === strtolower(substr($artisanName, -1))) {
            /* Thank you, English language, I am enjoying this */
            $inputText = str_ireplace(substr($artisanName, 0, -1)."'s", 'STUDIO_NAME', $inputText);
        }

        return $inputText;
    }

    private function removeFalsePositives(string $inputText): string
    {
        foreach ($this->falsePositivesRegexps as $regexp) {
            $inputText = $regexp->removeFrom($inputText);
        }

        return $inputText;
    }

    private function matchesGivenRegexpSet(array $testedStrings, array $regexpSet, Variant $variant): bool
    {
        foreach ($testedStrings as $testedString) {
            foreach ($regexpSet as $regexp) {
                if ($regexp->matches($testedString, $variant)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function cleanHtml(string $inputText): string
    {
        $inputText = strtolower($inputText);
        $inputText = $this->extractFromJson($inputText);

        foreach (CommissionsStatusRegexps::HTML_CLEANER_REGEXPS as $regexp => $replacement) {
            $inputText = preg_replace($regexp, $replacement, $inputText);
        }

        return $inputText;
    }

    /**
     * @param string $inputText
     * @param string $additionalFilter
     *
     * @return string
     *
     * @throws CommissionsStatusParserException
     */
    private function applyFilters(string $inputText, string $additionalFilter): string
    {
        if (WebsiteInfo::isFurAffinity(null, $inputText)) {
            if (false !== stripos($inputText, '<p class="link-override">The owner of this page has elected to make it available to registered users only.')) {
                throw new CommissionsStatusParserException('FurAffinity login required');
            }

            if (WebsiteInfo::isFurAffinityUserProfile(null, $inputText)) {
                $additionalFilter = 'profile' === $additionalFilter ? 'td[width="80%"][align="left"]' : '';

                $crawler = new Crawler($inputText);

                return $crawler->filter("#page-userpage tr:first-child table.maintable $additionalFilter")->html();
            }

            return $inputText;
        }

        if (WebsiteInfo::isTwitter($inputText)) {
            $crawler = new Crawler($inputText);

            return $crawler->filter('div.profileheadercard')->html();
        }

        if (WebsiteInfo::isInstagram($inputText)) {
            $crawler = new Crawler($inputText);

            return $crawler->filter('script[type="application/ld+json"]')->html();
        }

        return $inputText;
    }

    private function guessFilterFromUrl(string $url): string
    {
        if (preg_match('/#(?<profile>.+)$/', $url, $matches)) {
            return $matches['profile'];
        } else {
            return '';
        }
    }

    /**
     * @param bool $open
     * @param bool $closed
     *
     * @return bool
     *
     * @throws CommissionsStatusParserException
     */
    private function analyseResult(bool $open, bool $closed): bool
    {
        if ($open && !$closed) {
            return true;
        }

        if ($closed && !$open) {
            return false;
        }

        if ($open) { // && $closed
            throw new CommissionsStatusParserException('BOTH matches');
        } else {
            throw new CommissionsStatusParserException('NONE matches');
        }
    }

    private function extractFromJson(string $webpage)
    {
        if (empty($webpage) || '{' !== $webpage[0]) {
            return $webpage;
        }

        $result = json_decode($webpage, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            return $webpage;
        }

        return $this->flattenArray($result);
    }

    /**
     * https://stackoverflow.com/questions/1319903/how-to-flatten-a-multidimensional-array#comment7768057_1320156.
     *
     * @param array $array
     *
     * @return string
     */
    private function flattenArray(array $array)
    {
        $result = '';

        array_walk_recursive($array, function ($a, $b) use (&$result) {
            $result .= "$b: $a\n";
        });

        return $result;
    }

    private function debugDumpRegexpes(): void
    {
        echo "FALSE-POSITIVES =========================================\n";
        foreach ($this->falsePositivesRegexps as $regexp) {
            echo "{$regexp->getCompiled()}\n";
        }
        echo "OPEN ====================================================\n";
        foreach ($this->statusRegexps as $regexp) {
            echo "{$regexp->getCompiled($this->open)}\n";
        }
        echo "CLOSED ==================================================\n";
        foreach ($this->statusRegexps as $regexp) {
            echo "{$regexp->getCompiled($this->closed)}\n";
        }
    }
}
