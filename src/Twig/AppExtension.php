<?php
// src/Twig/AppExtension.php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('frequent', [$this, 'frequentFilter']),
        ];
    }

    public function frequentFilter(array $array)
    {
        $counts = array_count_values($array);
        arsort($counts);
        return array_key_first($counts);
    }
}