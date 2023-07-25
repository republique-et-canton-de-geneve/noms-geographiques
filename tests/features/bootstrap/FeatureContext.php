<?php

use Behat\Behat\Context\Context;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawDrupalContext implements Context {

  /**
   * FeatureContext constructor.
   *
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   *
   * @param $root_path
   */
  public function __construct($root_path) {
    $this->root_path = $root_path;
  }

  /**
   * @BeforeSuite
   *
   * @param \Behat\Testwork\Hook\Scope\BeforeSuiteScope $scope
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function prepare(BeforeSuiteScope $scope) {
    Drupal::service('module_installer')->uninstall(['simplesamlphp_auth']);
    Drupal::service('module_installer')->uninstall(['edg_saml_behavior']);
  }

  /**
   * @AfterSuite
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function teardown() {
    Drupal::service('module_installer')->install(['simplesamlphp_auth']);
    Drupal::service('module_installer')->install(['edg_saml_behavior']);
  }

  /**
   * Go on the page of a given node of specific bundle.
   *
   * @Given I am on a :content_type node with the title :node_title
   *
   * @param $content_type
   * @param $node_title
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Exception
   */
  public function iAmOnANodeWithTheTitle($content_type, $node_title) {
    $nodes = Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties([
        'type' => $content_type,
        'title' => $node_title,
      ]);

    $node = current($nodes);

    $message = sprintf('Impossible to find the page of the node "%s".', $node_title);

    if (!empty($node)) {
      $this->getSession()
        ->visit($this->locatePath('/node/' . $node->nid->value));
    }
    else {
      throw new Exception($message);
    }
  }

  /**
   * Go on the editing page of a given node of specific bundle.
   *
   * @Given I am editing a :content_type node with the title :node_title
   *
   * @param $content_type
   * @param $node_title
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Exception
   */
  public function iAmEditingANodeWithTheTitle($content_type, $node_title) {
    $nodes = Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties([
        'type' => $content_type,
        'title' => $node_title,
      ]);

    $node = current($nodes);

    $message = sprintf('Impossible to find the editing page of the node "%s".', $node_title);

    if (!empty($node)) {
      $this->getSession()
        ->visit($this->locatePath('/node/' . $node->nid->value . '/edit'));
    }
    else {
      throw new Exception($message);
    }
  }

  /**
   * @When I click element :arg1
   * @throws \Exception
   */
  public function iClickElement($selector) {
    $page = $this->getSession()->getPage();
    $element = $page->find('css', $selector);
    if (empty($element)) {
      throw new Exception("No html element found for the selector ('$selector')");
    }

    $element->click();
  }

}
