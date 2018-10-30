# WordPress Handlebars Engine

[![licence](https://img.shields.io/badge/licence-MIT-blue.svg?style=flat-square)]() [![tag](https://img.shields.io/badge/tag-v0.0.1-lightgrey.svg?style=flat-square)]()

Renders Handlebars/Moustache templates within your WordPress Theme/Plugin. Handlebars rendering is powered by [LightnCandy](https://github.com/zordius/lightncandy)

## Installation
You can install this must-use plugin two ways

### Via Composer

If you load your dependenies via Composer you can load this plugin with

```sh
$ php composer require iantsch/wp-hbs-engine
```

### Via Download

Download/fork this repository and copy the plugin-folder into `wp-content/plugins/`.
If you visit your Plugin section in the `wp-admin` area, activate it and you are good to go.

## Usage

Setup and modify the render engine to your needs in your ``functions.php`` or your plugin

```php
add_filter('MBT/Engine/Handlebars/Helpers', function($helpers) {
  $helpers['__'] = function($string) {
    return __($string, 'mbt');
  };
  $helpers['permalink'] = 'get_permalink';
  $helpers['content'] = function() {
    return apply_filters('the_content', get_the_content());
  };
  return $helpers;
});
```

In your theme call it like a WordPress function.

```php
global $post;
while (have_posts()){
  the_post();
  $data = (array) $post;
  the_hbs_template('article', $data);
}
```

In your ``article.hbs`` it is [handlebarsjs.com](https://handlebarsjs.com) syntax

```hbs
<h2>{{this.post_title}}</h2>
<div>{{{content}}}</div>
<a href="{{permalink}}">{{__ 'Click'}}</a>
```

## API

#### get_hbs_template
| Parameter | Type | Description |
|---|---|---|
| ``$template`` | string | template name |
| ``$data`` | string | associative array with all the needed data for the template |
| ``$templateDir`` | string \| boolean | absolutee path to template entry directory, defaults to ``/src/templates/`` in current theme |
| ``$cacheDir`` | string \| boolean | absolute path to caching directory, defaults to ``/cache/`` in plugins folder |
*Returns*: ``$html`` - the rendered HTML output

#### the_hbs_template
| Parameter | Type | Description |
|---|---|---|
| ``$template`` | string | template name |
| ``$data`` | string | associative array with all the needed data for the template |
| ``$templateDir`` | string \| boolean | absolutee path to template entry directory, defaults to ``/src/templates/`` in current theme |
| ``$cacheDir`` | string \| boolean | absolute path to caching directory, defaults to ``/cache/`` in plugins folder |
echoes the rendered HTML output
## Hooks

There are several hooks to modify and adapt the render engine

### Filters

#### MBT/Engine/Handlebars/Extension
| Parameter | Type | Description |
|---|---|---|
| ``$extension`` | string | file extension, default: ``hbs`` |
*Returns*: ``$extension`` - file extension of your handlebar templates
#### MBT/Engine/Handlebars/Helpers
| Parameter | Type | Description |
|---|---|---|
| ``$helpers`` | array | associative array with callback functions for the implemented helpers |
| ``$this`` | object | Current instance of Handlebars engine |
*Returns*: ``$helpers`` - array of handlebars helper
#### MBT/Engine/Handlebars/Flags
| Parameter | Type | Description |
|---|---|---|
| ``$flags`` | int | bitwise flags used by LightnCandy |
| ``$this`` | object | Current instance of Handlebars engine |
*Returns*: ``$flags`` - LightnCandy Flags
#### MBT/Engine/Handlebars/Partials
| Parameter | Type | Description |
|---|---|---|
| ``$partials`` | array | associative array with the relative path to the partials |
| ``$this`` | object | Current instance of Handlebars engine |
*Returns*: ``$partials`` - array of available partials
#### MBT/Engine/Handlebars/DefaultData
| Parameter | Type | Description |
|---|---|---|
| ``$defaultData`` | array | fallback data for the template |
| ``$template`` | string | template name |
*Returns*: ``$defaultData`` - fallback data for the template
#### MBT/Engine/Handlebars/Data
| Parameter | Type | Description |
|---|---|---|
| ``$data`` | array | associative array with all the needed data for the template |
| ``$template`` | string | template name |
*Returns*: ``$data`` - the data for the template
#### MBT/Engine/Handlebars/Html
| Parameter | Type | Description |
|---|---|---|
| ``$html`` | string | rendered HTML output (with data) |
| ``$template`` | string | template name |
| ``$data`` | array | data used to render output |
*Returns*: ``$html`` - the rendered HTML output

### Actions

#### MBT/Engine/Handlebars/Init
| Parameter | Type | Description |
|---|---|---|
| ``$this`` | object | Current instance of Handlebars engine |

## Credits
[@iantsch](https://twitter.com/iantsch) - [web developer](https://mbt.wien) behind this and other projects.
