# eZObjectWrapperBundle

A Symfony Bundle for eZ Publish 5 development (>=5.3).

Developed by the [Kaliop](http://www.kaliop.com/) team.


## Description

This bundle offers a simple model to encapsulate and lazy-load Location and Content objects in a single `eZObjectWrapper`
object.

The default encapsulating class is ; it .

Extended wrapper classes can for example add methods to fetch secondary contents or return computed values
which depend on the fields making the content. These will be computed without overloading the main controller
and creating a new kernel request.

The bundle provides a factory class to build `eZObjectWrapper` and extended classes, exposed as a service.
The extended classes to be built are set up via the class_map configuration in the  `ezobject_wrapper` namespace.
They can also be declared as services and built using the service_map configuration.

This bundle also provides a Twig function, `renderLocation`, which uses the ViewController as a service, and doesn't
re-execute the Symfony kernel, for more efficiency.


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
// get the service
$factory = $this->container->get('ezobject_wrapper.factory');
// build accepts a Content, Location or locationID as parameter
$factory->build($location);
```

You can also easily build wrapper instances out of arrays of Content/Location/LocationId, or from Remote Ids. 

### Calling methods or accessing attributes of an `eZObjectWrapper` in Twig

```twig
{{ ez_field_value(wrapper.content, 'title') }}
```

### renderLocation

```twig
{{ renderLocation(locationId, 'view_type', { ezObjectWrapper : myObject }) |raw }}
```


## Contact
E-mail : asavoie@kaliop.com / sbressey@kaliop.com
