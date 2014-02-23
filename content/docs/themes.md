/*
Title: Theming 101
*/

Are you now to creating themes? Well, I've tried everything I could to make this as easy as possible.

1. [About content files](#content-files)
2. [About theme files](#theme-files)
3. [Using variables](#using-variables)
4. [Fetching an additional post](#fetching-post)
5. [Fetching a list of posts](#fetching-lists)
6. [Including another template](#including)

---

## 1. About content files {#content-files}
The content can be found in the `content` directory and is split per page in Markdown (**.md**) files. Each file contains a **header**, and **content**.

The header is the top part of the file that contains important information in a `Parameter: value` format. An header should always be at the top of the file, start with a `/*` and end with an `*/`. Everything after the header definition is considered content.

There are no required parameters, but the only fixed parameter is `Template` which I need to pick a template file. Also, the `Date` field is used for sorting on default. All these fields can be used in the template files.

    /*
    Title: Hello World!
    Date: 2014-02-23 20:58
    Template: foo

    Color: #ff0000
    */

    Lorum ipsum I speak no Latin.

---

## 2. About theme files {#theme-files}

The theme files is where *your* magic happens. A theme is a bunch of HTML files and all of your assets like styles and scripts in the `felix/themes` directory. For theming I've used the Twig templating engine. You can use all of Twig's syntax as defined at [Twig for Template Designers](http://twig.sensiolabs.org/doc/templates.html).

Just create a directory that resembles your theme name, and start working your magic. Remember, I use `index.html` as default template.

---

## 3. Using variables {#using-variables}

In order for our theme to display anything, we need to get a page's content. As mentioned, I use Twig as template engine, so you may want to browse through their docs for syntax.

The content of a page or post is saved in the `post` object and is accessible everywhere. To access data from it's header, you have to address `post.meta.[field name]` with the exception of `Title`, `Date` and `Template`. The content is accessible via `post.content`.

To make it a little more sense, here's an example object in JSON(-ish) format:

	post: {
		title: "Hello World!",
		date: "2014-02-23 20:58"
		template: "foo",

		meta: {
		    color: "#ff0000"
		},

		content: "Lorum ipsum I speak no Latin."
	}

A (digital) real-world example would be:

	<div class="post" style="background-color: {{ post.meta.color }}">
		<h1>{{ post.title }}</h1>
		{{ post.content }}
	</div>

---

## 4. Fetching an additional post {#fetching-post}

To avoid indexing all posts without needing them, you have to call the function `get_page( path )`. You will probably like to store it's response in a variable.

In your template, you would use:

    {% set copyright = get_page('path/to/file') %}

	<footer>
		<small>{{ copyright.content }}</small>
	</footer>

---

## 5. Fetching a list of posts {#fetching-lists}

When you're working on a blog or portfolio, you may want to list a bunch of files. In that case, you will want to use the `get_pages( options )` object.

The `options` argument is an object with as you would have guessed... a bunch of options:

	dir: '.',               // What directory to index
	exclude: [],            // List of filenames to exclude
	limit: OPT_POST_LIMIT,  // You can modify the default post limit in the config
	nobody: false,          // Should I read headers only?
	offset: 0,              // The offset, used for pagination

	order: 'desc',          // The sort order
	orderby: 'date',        // What to order. For a meta tag, use ['meta', 'yourtag']
	ordertype: 'date'       // date, number, string or random

In a theme it would probably look a lot like this:

	{% set posts = get_posts({
		dir: 'posts',
		nobody: true,
		order: 'asc',
		orderby: 'title',
		ordertype: 'string'
	}) %}

	{% for post in posts %}
		<h1><a href="{{ post.url }}">{{ post.title }}</a></h1>
	{% endfor %}

---

## 6. Including another template {#including}

I like to keep my header, footer and optionally a sidebar seperate. That's actually really easy!

	{% include 'header.html' %}

	...

	{% include 'footer.html' %}

The `post` variable **will** be accessible in those templates.
