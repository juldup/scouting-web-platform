<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Helpers;
use Elasticsearch;
use Elasticsearch\ClientBuilder;
use App\Models\Parameter;
use App\Models\News;

/**
 * This class provides a tool to index and search the website
 */
class ElasticsearchHelper {
  
  /**
   * Creates and returns the elasticsearch client
   */
  private static $client = null;
  private static function getClient() {
    if (self::$client) return self::$client;
    self::$client = ClientBuilder::create()->build();
    return self::$client;
  }
  
  /**
   * Returns the name of the elasticsearch database to be used, based on the website url
   */
  private static $indexName = null;
  private static function getIndexName() {
    if (self::$indexName) return self::$indexName;
    $baseURL = file_get_contents(__DIR__ . "/../../storage/app/site_data/website-base-url.txt");
    self::$indexName = Helper::slugify($baseURL);
    return self::$indexName;
  }
  
  /**
   * Generates the content of elasticsearch
   */
  public static function fillElasticsearchDatabase() {
    
    // Set duration limit to 20 minutes instead of 30 seconds
    set_time_limit(1200);
    
    $client = self::getClient();
    
    // Clear index
    /*try {
      $params = ['index' => self::getIndexName()];
      if ($client->indices()->exists($params)) {
        $response = $client->indices()->delete($params);
      }
    } catch (Exception $e) {
      
    }*/
    


    $indexCounter = 1;
    $params = ['body' => []];
    // Add news
    if (Parameter::get(Parameter::$SHOW_NEWS)) {
      foreach (News::all() as $news) {
        try {
          $client->index([
              'id' => $indexCounter++,
              'index' => self::getIndexName(),
              'type' => '_doc',
              'body' => [
                  'testField' => 'abc',
//                  'search_content' => Helper::removeSpecialCharacters($news->title . " " . html_entity_decode(strip_tags($news->body))),
//                  'content' => $news->body,
//                  'title' => $news->title,
//                  'text_type' => 'news',
//                  'text_type_name' => 'Nouvelle',
//                  'original_id' => $news->id,
//                  'section_id' => $news->section_id,
//                  'visibility' => 'public',
//                  'url' => url()->route('single_news', ['news_id' => $news->id]),
               ]
          ]);
        } catch (Exception $e) {
          dd($e);
        }
      }
    }
/*    // Add documents
    if (Parameter::get(Parameter::$SHOW_DOCUMENTS)) {
      foreach (Document::where('archived', '=', 0)->get() as $document) {
        $documentPath = $document->getPath();
        if (file_exists($documentPath)) {
          try {
            if (strtolower(substr($document->filename, strlen($document->filename) - 4)) == ".pdf") {
              // Read pdf content
              $parser = new \Smalot\PdfParser\Parser();
              $pdfText = $parser->parseFile($document->getPath())->getText();
            } else {
              $pdfText = "";
            }
            $params['body'][] = [
                'index' => [
                    '_index' => self::getIndexName(),
                    '_type' => 'text',
                    '_id' => $indexCounter++,
                ]
            ];
            $params['body'][] = [
                'search_content' => Helper::removeSpecialCharacters($document->title . " " . $document->description . " " . $pdfText),
                'content' => $document->description,
                'title' => $document->title . " (" . Helper::dateToHuman($document->doc_date) . ")",
                'text_type' => 'document',
                'text_type_name' => 'Document',
                'original_id' => $document->id,
                'section_id' => $document->section_id,
                'visibility' => $document->public ? "public" : "private",
                'url' => URL::route('download_document', array('document_id' => $document->id)),
            ];
          } catch (Exception $e) {}
        }
      }
    }
    // Add e-mails
    if (Parameter::get(Parameter::$SHOW_EMAILS)) {
      foreach (Email::where('target', '=', 'parents')->where('deleted', '=', 0)->where('archived', '=', 0)->where('date', '>=', Helper::oneYearAgo())->get() as $email) {
        $params['body'][] = [
            'index' => [
                '_index' => self::getIndexName(),
                '_type' => 'text',
                '_id' => $indexCounter++,
            ]
        ];
        $params['body'][] = [
            'search_content' => Helper::removeSpecialCharacters($email->subject. " " . html_entity_decode(strip_tags($email->body_html))),
            'content' => $email->body_html,
            'title' => $email->subject . " (" . Helper::dateToHuman($email->date) . ")",
            'text_type' => 'email',
            'text_type_name' => 'E-mail',
            'original_id' => $email->id,
            'section_id' => $email->section_id,
            'visibility' => 'private',
            'url' => URL::route('emails', ['section_slug' => $email->getSection()->slug]) . "#email_" . $email->id,
        ];
      }
    }
    // Add pages
    foreach (Page::all() as $page) {
      $showPage = false;
      $linkURL = "";
      $title = "";
      switch ($page->type) {
        case "registration":
          if (Parameter::get(Parameter::$SHOW_REGISTRATION)) {
            $showPage = true;
            $linkURL = URL::route('registration');
            $title = "Inscription dans l'unité";
          }
          break;
        case "help":
          if (Parameter::get(Parameter::$SHOW_HELP)) {
            $showPage = true;
            $linkURL = URL::route('help');
            $title = "Aide";
          }
          break;
        case "home":
          $showPage = true;
          $linkURL = URL::route('home');
          $title = "Page d'accueil";
          break;
        case "addresses":
          if (Parameter::get(Parameter::$SHOW_ADDRESSES)) {
            $showPage = true;
            $linkURL = URL::route('contacts');
            $title = "Contacts et liens";
          }
          break;
        case "section_home":
          if (Parameter::get(Parameter::$SHOW_SECTIONS)) {
            $showPage = true;
            $linkURL = URL::route('section', ['section_slug' => Section::find($page->section_id)->slug]);
            $title = $page->section_id == 1 ? "Présentation de l'unité" : Section::find($page->section_id)->name;
          }
          break;
        case "unit_policy":
          if (Parameter::get(Parameter::$SHOW_UNIT_POLICY)) {
            $showPage = true;
            $linkURL = URL::route('unit_policy');
            $title = "Charte d'unité";
          }
          break;
        case "gdpr":
          if (Parameter::get(Parameter::$SHOW_GDPR)) {
            $showPage = true;
            $linkURL = URL::route('gdpr');
            $title = "RGPD";
          }
          break;
        case "section_uniform":
          if (Parameter::get(Parameter::$SHOW_UNIFORMS)) {
            $showPage = true;
            $linkURL = URL::route('uniform', ['section_slug' => Section::find($page->section_id)->slug]);
            $title = "Uniforme " . Section::find($page->section_id)->de_la_section;
          }
          break;
        case "custom":
          $showPage = true;
          $linkURL = URL::route('custom_page', ['page_slug' => $page->slug, 'section_slug' => Section::find($page->section_id)->slug]);
          $title = $page->title;
          break;
        case "annual_feast":
          if (Parameter::get(Parameter::$SHOW_ANNUAL_FEAST)) {
            $showPage = true;
            $linkURL = URL::route('annual_feast');
            $title = "Fête d'unité";
          }
          break;
      }
      if ($showPage) {
        $params['body'][] = [
            'index' => [
                '_index' => self::getIndexName(),
                '_type' => 'text',
                '_id' => $indexCounter++,
            ]
        ];
        $params['body'][] = [
            'search_content' => Helper::removeSpecialCharacters($title . " " . html_entity_decode(strip_tags($page->body_html))),
            'content' => $page->body_html,
            'title' => $title,
            'text_type' => 'page',
            'text_type_name' => 'Page',
            'original_id' => $page->id,
            'section_id' => $page->section_id,
            'visibility' => 'public',
            'url' => $linkURL,
        ];
      }
    }
    $client->bulk($params);
*/
    echo('indexes done');
  }
  
  /**
   * Searches through all elasticsearch data for the given string
   */
  public static function find($string, $showPrivateDocuments = false) {
    $string = Helper::removeSpecialCharacters($string);
    $client = self::getClient();
    // Search parameters
    $must = [
        [
            'match' => [
                'search_content' => [
                    'query' => $string,
                    'fuzziness' => 1,
                    'operator' => 'and'
                ],
            ],
        ]
    ];
    // Force only public results for non-registered users
    if (!$showPrivateDocuments) {
      $must[] = [
          'match' => [
              'visibility' => [
                  'query' => 'public',
              ]
          ]
      ];
    }
    // Create and apply search
    $params = [
        'index' => self::getIndexName(),
        'type' => '_doc',
//        'size' => 20,
        'body' => [
            'query' => [
                'bool' => [
                    'must' => $must
                ]
            ]
        ]
    ];
    
    $results = $client->search($params);
    dd($results);
    return $results;
  }
  
}
