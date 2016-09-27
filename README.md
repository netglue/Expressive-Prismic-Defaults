# WIP: Expressive Prismic Website Defaults 'Module'

## Introduction

This is a module/library for a Zend Expressive app or website to help working with [prismic.io](https://prismic.io)'s content management api/platform. It depends on the [Expressive Prismic](https://github.com/netglue/Expressive-Prismic) module which provides the basics like an api instance in your di container etc.

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

## Content Slices View Helper Documentation

Prismic.io provides a powerful and flexible way of creating website content called Slices. Slices are units of content with a predefined structure contained in something called a Slice Zone. [Read up about slices](https://prismic.io/docs/fields/slices#?lang=javascript) themselves on the Prismic.io website.

You may have a slice for a header image, a call to action, a pricing table, etc.

A Slice Zone can be retrieved from a document just like any other fragment, with `$document->get('my-type.fragment-name');` but iterating over each slice and rendering the HTML for these can be time consuming and repetitive if you have to do it in multiple view templates. This is what the `contentSlices()` view helper is for.

To configure the view helper, all you need is a hash of slice types to template names under the key `['prismic']['slice_templates']` in your expressive configuration…

```php
[
    'prismic' => [
        'slice_templates' => [
            'my-slice-type' => 'my::template-name',
        ],
    ]
]
```

Within the view script/template for the document, assuming the document has been resolved during the current request, you can simply issue:
```php
echo $this->contentSlices('fragmentName');
```

The fragment name does not have to be fully qualified, i.e. you can use `my-type.body` or just `body`. This is helpful when you routinely use a slice zone as the main body of a document but use the same template to render multiple different types of document.

You can also provide a second argument to the helper to render the slices from a specific document, for example:
```php
echo $this->contentSlices('body', $someOtherDocument);
```

### Content Slices Templates

Templates are provided with two variables by the ContentSlices view helper: `$slice` and `$document`.
The `slice` variable refers to the slice you'll want to template and the document is the entire prismic document provided as context.


## LinkResolver View Helper Documentation

Prismic relies on the presence of a `LinkResolver` in order to construct URLs from internal links between documents. A Link Resolver implementation is provided in [the main module](https://github.com/netglue/Expressive-Prismic) but you can override the implementation by configuring a service with the name `Prismic\LinkResolver`. Moving on, the Link resolver is frequently used so it makes sense to have a view helper around that can provide a link resolver on demand rather than passing a reference to one in all of our templates or partials. The Helper `ExpressivePrismic\View\Helper\LinkResolver` has a single method, `__invoke` that when called will return the link resolver instance, not the helper itself. If called with a non-null argument _(Expected to be an instance of `\Prismic\Fragment\Link\LinkInterface`)_, the helper will call `resolve()` directly on the link resolver in order to return either a string URL or null if the link or argument cannot be resolved to a URL.

The helper is aliased as `linkResolver` so in view templates, you would call `$this->linkResolver()` to get the instance, or `$this->linkResolver($link)` to resolve the link to a URL.

