<?php

namespace Drupal\Tests\user\Functional\Views;

use Drupal\Tests\views\Functional\ViewTestBase;
use Drupal\views\Tests\ViewTestData;

/**
 * Tests the permission field handler ui.
 *
 * @group user
 * @see \Drupal\user\Plugin\views\filter\Permissions
 */
class FilterPermissionUiTest extends ViewTestBase {

  /**
   * Views used by this test.
   *
   * @var array
   */
  public static $testViews = ['test_filter_permission'];

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['user', 'user_test_views', 'views_ui'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  protected function setUp($import_test_views = TRUE): void {
    parent::setUp($import_test_views);

    ViewTestData::createTestViews(static::class, ['user_test_views']);
    $this->enableViewsTestModule();
  }

  /**
   * Tests basic filter handler settings in the UI.
   */
  public function testHandlerUI() {
    $this->drupalLogin($this->drupalCreateUser([
      'administer views',
      'administer users',
    ]));

    $this->drupalGet('admin/structure/views/view/test_filter_permission/edit/default');
    // Verify that the handler summary is correctly displaying the selected
    // permission.
    $this->assertSession()->linkExists('User: Permission (= View user information)');
    $this->submitForm([], 'Save');
    // Verify that we can save the view.
    $this->assertSession()->pageTextNotContains('No valid values found on filter: User: Permission.');
    $this->assertSession()->pageTextContains('The view test_filter_permission has been saved.');

    // Verify that the handler summary is also correct when multiple values are
    // selected in the filter.
    $edit = [
      'options[value][]' => [
        'access user profiles',
        'administer views',
      ],
    ];
    $this->drupalGet('admin/structure/views/nojs/handler/test_filter_permission/default/filter/permission');
    $this->submitForm($edit, 'Apply');
    $this->assertSession()->linkExists('User: Permission (or View us…)');
    $this->submitForm([], 'Save');
    // Verify that we can save the view.
    $this->assertSession()->pageTextNotContains('No valid values found on filter: User: Permission.');
    $this->assertSession()->pageTextContains('The view test_filter_permission has been saved.');
  }

}
