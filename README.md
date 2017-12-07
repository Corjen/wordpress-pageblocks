# Pageblocks plugin

This plugin allows you to easily add custom page blocks to wordpress pages and
posts.

## Installation

Download this repo and add it to your plugins folder & activate in wp-admin, or
add it as a submodule.

In your theme folder, create a new class that contains your registered blocks,
and looks like this:

```php
<?php
namespace MyProject\Pageblocks;

class Config {

  var $blocks;

  public function __construct() {

    $this->blocks = array(
      'test-block' => array(
        'class' => '\MyProject\Pageblocks\TestBlock', // 👈 Corresponding to your namespace and class name
        'title' => 'Test page block',
        'thumbnail' => 'http://placehold.it/150x75' // Optional
      ),
      'second-test-block' => array(
        'class' => '\MyProject\Pageblocks\SecondTestBlock',
        'title' => 'Second Test page block',
        'thumbnail' => 'http://placehold.it/150x75' // Optional
      )
    );

  }

  public function getConfig() {
    return $this->blocks;
  }

}
```

As you can see, the only thing this class does is registering the page blocks en
returning it when the class is constructed.

In `functions.php` initialize the pageblocks class by doing this:

```php
<?php
// functions.php

namespace MyProject;

if ( is_admin() ) {
  $config = new Pageblocks\Config(); // 👈 Your local config class
  $postTypes = array( 'page' ), // 👈  Post types to display the pageblocks form on
  $templates = array( 'templates/pageblocks.php' ); // 👈  Templates to display the pageblocks form on (optional!)
  new \Pageblocks\Pageblocks( $config->getConfig(), $templates, $postTypes );
}
?>
```

A pageblock class should contain two functions: `admin` for displaying in
wp-admin and `display` for displaying on the frontend. It looks like this:

```php
<?php

namespace MyProject\Pageblocks;

class TestBlock {
  // The function parameters get automically filled by the page blocks system
  public function admin ( $count, $type, $content = array(), $collapsed = false, $helpers ) {
    /**
     * $count is the current pageblock index, starting at 0
     * $type is the current pageblocks unique id
     * $content are the current pageblock values
     * $collapsed is a boolean which defines wether a pageblock should be closed or not
     * $helpers is an Object with some helper function (see a description below)
   */

    // Every admin class should at least open and close using the helper object, this ensures the right UI get's loaded
    echo $helpers->getOpeningPageBlockElement( $count, $type, $collapsed );
    echo $helpers->getClosingPageBlockElement();
  }

  // The display function has only one argument; $content. This is an array with the form values.
  public function display ( $content ) { }
}
```

### Helpers

The helpers paramater contains five functions:

```php
<?php

$helpers->getOpeningPageBlockElement( $count, 'test-block', $collapsed );
$helpers->getClosingPageBlockElement();

// For opening and closing an postbox element:
$helpers->getOpeningPostboxElement( 'Title' );
$helpers->getClosingPostBoxElement();

// For naming pageblock form inputs
$helpers->getFieldName( $count, $name ); // 👈 Important: You should alwasys wrap the name in brackets like [title], you can also use it as an associtive array like [button][url]
```

### Form fields in admin with Cuisine

[Cuisine](http://docs.get-cuisine.cooking/core/) is a very useful utility that
simplifies a lot of WordPress backend stuff. The Cuisine Field class makes it
really simple to build forms and works very nice with the pageblocks system. An
example of an admin function inside a pageblock class in combination with
Cuisine:

```php
<?php

namespace MyProject\PageBlocks;

use Cuisine\Wrappers\Field;

class TestBlock {

  public function admin( $count, $title, $content = array(), $collapsed = false, $helpers ) {
    // Default values
    $defaults = array(
      'title' => '',
      'content' => '',
    );

    // Merge with default
    $content = wp_parse_args( $content, $defaults );

    /**
     * Fields 👇
     */
    echo $helpers->getOpeningPageBlockElement( $count, 'test-block', $collapsed );
      echo $helpers->getOpeningPostboxElement('Content');
        Field::text( $helpers->getFieldName( $count, '[title]' ), 'Label', array(
          'defaultValue' => $content['title']
        ) )->render();
        Field::textarea( $helpers->getFieldName( $count, '[content]' ), 'Content', array(
          'defaultValue' => $content['content']
        ) )->render();
      echo $helpers->getClosingPostboxElement();
    echo $helpers->getClosingPageBlockElement();

  }

}
```

### Displaying on the frontend

To display on the frontend, you'll just need the `Display` class. It accepts two
arguments, `$blocks` which are the pageblocks from your `Config` class and
`$postId` which is the id of the current page. A pageblocks template would look
like this:

```php
<?php
/**
 * Template Name: Pageblocks
 * Template Post Type: page, project
 *
 * @package WordPress
 * @subpackage MyProject
 * @since 1.0.0
 */

namespace MyProject;
use \Pageblocks\Display;

$config = new Pageblocks\Config();

get_header();

$postId = get_the_id();
$Display = new Display( $config->getConfig(), $postId );
$Display->display();

get_footer();
```
