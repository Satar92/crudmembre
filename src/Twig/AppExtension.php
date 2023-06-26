<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('ucfirst', [$this, 'capitalizeFirst']),
        ];
    }

    public function capitalizeFirst(string $phrase): string {
        return \ucfirst($phrase);
    }
}