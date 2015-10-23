# eZObjectWrapperBundle

A Symfony Bundle for eZ Publish 5 development (>=5.3).

Developed by the [Kaliop](http://www.kaliop.com/) team.


## Description

This bundle offers a simple model to encapsulate location and content.
It provides a factory to build `eZObjectWrapper` and extended classes.

`eZObjectWrapper` provides a lazy-loading method to fetch content.
Extended wrapper classes can for example add methods to fetch secondary contents without overloading the main controller
and creating a new kernel request.

The extended classes are built via the class_map configuration in the  `ezobject_wrapper` namespace.
They can also be declared as services and built using the service_map configuration.

This bundle also provides a Twig function, `renderLocation`, which uses the ViewController as a service, and doesn't
relaunch the Symfony kernel, for more efficiency.


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

### Building `eZObjectWrapper`

```php
// get the service
$factory = $this->container->get('ezobject_wrapper.factory');
// build accepts a Location or locationID as parameter
$factory->buildWrapper($location);
```

### renderLocation

```twig
{{ renderLocation(locationId, 'view_type', { ezObjectWrapper : myObject }) |raw }}
```

### Call method or attribute from eZObjectWrapper in Twig

```twig
{{ ez_field_value(ezObjectWrapper.content, 'title') }}
```


## Contact
E-mail : asavoie@kaliop.com / sbressey@kaliop.com
