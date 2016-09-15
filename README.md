# Expressive Prismic Website Defaults 'Module'

Note: As this is in the same namespace as the main `ExpressivePrismic` module, care must be taken to avoid namespace clashes.

## Goals/Todo

* Search Service
* Opinionated User-Land configuration using a bookmarked Prismic Document
* Opinionated generation of Link Lists (Think Navigation) and associated view helpers
* Easy way of mapping 'Slices' to view partials and rendering all of them with a single call to a view helper
* Middleware that sets various head meta defaults from the CMS configuration when none have been set on a per document basis
* Describe and implement a way to create and configure forms from the CMS


### Link lists:

expect a group to iterate over.
When iterating over the group, link fragments are considered the url, structured text is considered as the anchor and everything else, providing it's text is considered as element attributes.
if the link fragment points to document of a specific type, matching the 'link-list' type, then iterate over that too as a nested list.


