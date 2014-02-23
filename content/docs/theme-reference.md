/*
Title: Theme reference
*/

## Functions

### get_post( path )

- **path**: a string of the path of desired file (eg. `posts/hello-world`)

If the specified path doesn't exist, it gets the 404 page.

### get_posts( options )

- **options**: an object of options

Default settings:

	dir: '.',               // What directory to index
	exclude: [],            // List of filenames to exclude
	limit: OPT_POST_LIMIT,  // You can modify the default post limit in the config
	nobody: false,          // Should I read headers only?
	offset: 0,              // The offset, used for pagination

	order: 'desc',          // The sort order
	orderby: 'date',        // What to order. For a meta tag, use ['meta', 'yourtag']
	ordertype: 'date'       // date, number, string or random

---

## Variables

	// Global site variables
	// Note that the $_VARS in your config gets added here
	site: {
		dir: DIR_ROOT,      // Defined in the config
		url: URI_ROOT       // Defined in the config
	},

	// The current theme variables
	theme: {
		dir: DIR_THEME,     // Defined in the config in a complex way
		name: OPT_THEME,    // Defined in the config
		url: URI_THEME      // Defined in the config in a complex way
	},

	// Current post variables
	post: {
		title: 'Hello World!',
		date: '2014-02-23 20:58',
		template: 'foo',

		// Your 'custom' headers
		meta: {
			'color':
		}

		content: 'Lorum ipsum...'
	}
