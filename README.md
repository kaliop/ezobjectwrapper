# eZObjectWrapperBundle

A Symfony Bundle for eZ Publish 5 development (>=5.3).

Developed by the [Kaliop](http://www.kaliop.com/) team.


## Description

This bundle offers a simple model to encapsulate and lazy-load Location and Content objects in a single `wrapper`
object.

The default encapsulating class is `eZObjectWrapper`, and can be extended (subclassed) by the user.

Extended wrapper classes can for example add methods to fetch secondary contents or return derived values
which depend on the fields which make up the content. These will be computed without overloading the main controller
and creating a new kernel request.

The bundle provides a factory class, exposed as a service, to build `eZObjectWrapper` and extended classes.
The extended classes to be built are set up via the class_map configuration in the  `ezobject_wrapper` namespace.
They can also be declared as services and tagged to be used as wrappers by using the `ezobject_wrapper.wrapper` tag.

This bundle also provides a Twig function, `render_location`, which uses the ViewController as a service, and doesn't
re-execute the Symfony kernel, which can be more efficient in many cases.


## Installation

The recommended way to install this bundle is through [Composer](http://getcomposer.org/). 

* Add the `kaliop/ezobjectwrapperbundle` package into your composer.json file :

```json
{
    "require": {
        "kaliop/ezobjectwrapperbundle": "~3.0"
    }
}
```

* Add eZObjectWrapperBundle into EzPublishKernel.php: 

```php
new \Kaliop\eZObjectWrapperBundle\eZObjectWrapperBundle(),
```

## Usage

### Configuration

```yml
ezobject_wrapper:
    class_map:
        article: \Acme\AcmeBundle\eZObjectWrapper\Article
```

### Building `eZObjectWrapper` instances

```php
// get the factory service
$factory = $this->container->get('ezobject_wrapper.factory');
// 'build' accepts a Content, Location or LocationId as parameter
$factory->build($location);
```

You can also easily build wrapper instances out of arrays of Content/Location/LocationId, or from Remote Ids. 

### Calling methods or accessing attributes of an `eZObjectWrapper` in Twig

```twig
{{ ez_field_value(wrapper.content, 'title') }}
```

### render_location twig helper

```twig
{# the 3rd argument is an array of parameters, we can pass the current wrapper as a variable named 'wrapper' to the view template #}
{{ render_location(wrapper.location.id, 'view_type', { wrapper : wrapper }) }}
```

## Impact with the caches (a.k.a. don't shoot yourself in the foot)

When using the `render_location` twig helper, or using subclasses of `eZObjectWrapper` which fetch other Content objects
in helper methods, be aware that you are introducing caching dependencies.

eZPublish by default goes to great lengths to make sure that the caches which keep the 'html version' of Content objects
are automatically expired whenever the objects are edited. One might say that view-cache-expiration is the prime reason
for using Symfony sub-requests when displaying a list of Content objects.

Whenever you end up displaying a Location which is not the current one, remember to add its Id to the X-Location-Id header
in your main controller response, so that eZPublish will know when to clear its cache. 

For more details see: https://doc.ez.no/display/EZP/HttpCache


## Contact
E-mail : asavoie@kaliop.com / sbressey@kaliop.com
