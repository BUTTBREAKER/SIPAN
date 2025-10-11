<?php

namespace SIPAN\Controllers;

use Gemini;
use GuzzleHttp\Client;
use SIPAN\App;

final readonly class GeminiController
{
  static function simplePrompt(): void
  {
    $prompt = (App::request()->data->prompt ?: App::request()->query->prompt) ?: 'Hola';
    $apiKey = $_ENV['GEMINI_API_KEY'];

    $geminiClient = Gemini::factory()
      ->withApiKey($apiKey)
      ->withHttpClient(new Client([
        'verify' => false,
      ]))
      ->make();

    $response = $geminiClient
      ->generativeModel('gemini-2.0-flash')
      ->generateContent($prompt);

    // echo $response->text(); // Hello! How can I assist you today?
    App::halt(200, $response->text());

    // Helper method usage
    // $response = $geminiClient->generativeModel(
    //     model: GeminiHelper::generateGeminiModel(
    //         variation: ModelVariation::FLASH,
    //         generation: 2.5,
    //         version: "preview-04-17"
    //     ), // models/gemini-2.5-flash-preview-04-17
    // );
    // $response->text(); // Hello! How can I assist you today?
  }
}
