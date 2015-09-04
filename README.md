# eZObjectWrapperBundle

A Symfony Bundle for eZ Publish 5 development (>=5.3).

Developed by the [Kaliop](http://www.kaliop.com/) team.


## Description

This bundle offers a simple model to encapsulate location and content for eZPublish 5 development.
It provides a factory to build `eZObjectWrapper` and extended classes.

`eZObjectWrapper` provides a lazy-loading method to fetch content.
Extended wrapper classes can for example expose methods to fetch secondary contents without overloading main controller
and creating new kernel request.

These extended classes are built via a class_mapping in `eZObjectWrapper.yml`.

This bundle also provides a Twig function, `renderLocation`, which uses the ViewController as a service, and doesn't
relaunch the Symfony kernel, for more efficiency.


## Installation

The recommended way to install this bundle is through [Composer](http://getcomposer.org/). 

* Add the `kaliop/ezobjectwrapperbundle` package into your composer.json file :

```json
{
    "require": {
        "kaliop/ezobjectwrapperbundle": "~1.0"
    }
}
```

* Add eZObjectWrapperBundle into EzPublishKernel.php: 

```php
new \Kaliop\eZObjectWrapperBundle\eZObjectWrapperBundle()
```

## Usage

### Building `eZObjectWrapper`

```php
// get the service
$factory = $this->container->get('ezobject_wrapper.services.factory');
// build accepts Location or locationID as parameter
$factory->buildeZObjectWrapper($location);
```

### Class mapping

```yml
parameters:
    class_mapping:
        content_identifier: \myGreat\BundleBundle\eZObjectWrapper\ClassesExtendingeZObjectWrapper
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
