<?php

namespace Drupal\ngeo_form\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 *
 */
class NgeoFormHelper {

  /**
   * The config factory.
   *
   * @var Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * Return TRUE if string contain any of the following element:
   * - russian char
   * - chinese char
   * - hebrew char
   * - http or https patern
   * - mailto pattern
   *
   * @param $string
   *
   * @return bool
   */
  public function isSpam($string) {

    // Russian.
    if (preg_match('/[А-Яа-яЁё]/u', $string)) {
      return TRUE;
    }

    // Chinese.
    if (preg_match("/\p{Han}+/u", $string)) {
      return TRUE;
    }

    // Hebrew.
    if (preg_match("/\p{Hebrew}/u", $string)) {
      return TRUE;
    }

    // Url test : http, https, ftp, ftps.
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\//";
    if (preg_match($reg_exUrl, $string)) {
      return TRUE;
    }

    // mailto.
    $reg_exUrl = "/(mailto)\:/";
    if (preg_match($reg_exUrl, $string)) {
      return TRUE;
    }

    // Custom words to filter (from config).
    $safeWords = $this->configFactory->get('ngeo_form.settings')->get('safe_words');
    if (!empty($safeWords)) {
      $words = explode(',', $safeWords);
      foreach ($words as $word) {
        if (preg_match("/\b" . trim($word) . "\b/i", $string)) {
          return TRUE;
        }
      }
    }
    return FALSE;
  }

  /**
   * Retourne la liste d'emails en une string séparés par une virgule.
   *
   * @param string $listNotification
   *
   * @return array
   */
  public function buildListEmailsFromListNotification(string $listNotification):string {
    $emails = explode(';', $listNotification);
    $stringListEmails = '';

    foreach ($emails as $emailWithSpaces) {
      $email = trim($emailWithSpaces);
      if (!empty($stringListEmails)) {
        $stringListEmails .= ', ';
      }
      $stringListEmails .= $email;
    }
    return $stringListEmails;
  }

}
