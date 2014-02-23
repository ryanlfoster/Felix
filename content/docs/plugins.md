/*
Title: Plugins 101
*/

If you're missing something, you can always extend me with a plugin.

1. [Structure](#structure)
2. [Constants](#constants)
	1. [Variables](#constants-variables)
	2. [Constants](#constants-constants)
3. [Hooks](#hooks)

---

## 1. Structure {#structure}

To avoid conflicts, plugins are classes. Simple plugins that can be written in one PHP file. Complex plugins can be directories, but must contain a PHP file with the same name.

    /plugins/my-simple-plugin.php
	/plugins/my-complex-plugin/my-complex-plugin.php

The name of the class must be the filename in camelcase, like so:

	class MySimplePlugin {}
	class MyComplexPlugin {}

To start actually doing things, go to the [hooks](#hooks) chapter.

---

## 2. Constants {#constants}
I use constants and a couple of global variables to have some info everywhere. You can find all of that in **config.php**, but just to summarize the important stuff for you:

### 2.1 Variables {#constants-variables}
Don't forget to use the `global` keyword if you're using these in a function.

- **$_TWIG** - Twig options
- **$_VARS** - Variables that are loaded into the templates

### 2.2 Constants {#constants-constants}
These are accessible anytime, anywhere.

- **DIR_CONTENT** - Content directory
- **DIR_CORE** - Core directory
- **DIR_PLUGINS** - Plugins directory
- **DIR_ROOT** - Root directory
- **DIR_THEME** - Current theme directory
- **DIR_THEMES** - Themes directory
- **URI_THEME** - URI of current theme directory
- **URI_THEMES** - URI of themes directory
- **URI_ROOT** - URI of the homepage
- **OPT_THEME** - The current theme name
- **OPT_DEBUG** - A boolean to toggle debugging (use this well)

---

## 3. Hooks {#hooks}
To do things before and after happenings, I've made hooks. Creating any of these public methods will automagically run these when appropiate. It doesn't get much easier.

### init()
Run when Felix is initialized

### post_query( &$query )
Executed after the query string is parsed. Useful for custom URL handling with PHP.

### twig_init( &$twig )
Run when Twig is being initialized. Useful for adding functions to Twig.

### pre_load()
Is run before plugins, libraries and content is loaded.

### post_load( &$content )
Is run after content is loaded.

### plugin_init()
After all plugins are loaded.

### load_lib()
Run after libraries are loaded. Useful for adding custom libraries.

### pre_parse_content( &$post )
Before Markdown is being processed.

### post_parse_content( &$post )
After Markdown is being processed.

### pre_render()
Run before rendering. Can be used when you need the output buffer for caching or saving.

### post_render()
Run after rendering. Like **pre_render()**, can be useful for output buffering.

### error( $msg )
Custom (global) error handling.
