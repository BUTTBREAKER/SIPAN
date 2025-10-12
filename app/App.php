<?php

namespace SIPAN;

use Flight;

final class App extends Flight
{
  static function renderPage(
    string $pageFileName,
    string $pageTitle,
    string $layoutFileName,
    array $pageData = []
  ): void {
    self::render(
      "pages/$pageFileName",
      ['title' => $pageTitle] + $pageData,
      'page'
    );

    self::render("layouts/$layoutFileName");
  }

  static function renderComponent(
    string $componentFileName,
    array $componentData = []
  ): void {
    self::render("components/$componentFileName", $componentData);
  }

  public static function formatMoney(float $amount): string
  {
    return 'S/ ' . number_format($amount, 2);
  }
}
