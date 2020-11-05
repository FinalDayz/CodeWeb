<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ByteConversionTwigExtension extends AbstractExtension
{

    /**
     * Gets filters
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('format_bytes', array($this, 'formatBytes')),
        );
    }


    function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'Kb', 'Mb', 'Gb', 'Tb');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

}