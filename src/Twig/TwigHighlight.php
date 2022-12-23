<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use function Symfony\Component\String\u;

class TwigHighlight extends AbstractExtension
{

  public function getFilters(): array
  {
    return [
      new TwigFilter('highlight', [$this, 'formatHighlight'],
        ['is_safe' => ['html']]
      )];
  }

  public function formatHighlight(string $text, ?string $searchQuery): string
  {
    if ($searchQuery === null) {
      return $text;
    }

    $searchQuery = u($searchQuery)->replaceMatches('/[[:space:]]+/', ' ')->trim();
    $terms = array_unique($searchQuery->split(' '));

    $highlight = [];
    foreach ($terms as $term) {
      $highlight[] = '<span class="highlight">' . $term . '</span>';
    }

    return str_ireplace($terms, $highlight, $text);
  }
}
