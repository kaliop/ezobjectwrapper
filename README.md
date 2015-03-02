# ezobject
Symfony Bundle eZObjectWrapperBundle for eZ Publish 5 development eZ 5.3).

version 1.0 24/10/2014

Developed by the [Kaliop](http://www.kaliop.com/) team.

## Description

This bundle offers a simple model to encapsulate location and content for eZPublish 5 development.
It provides a factory to build `eZObjectWrapper` and extended classes. `eZObjectWrapper` provides a lazy-loading 
method to fetch content. Extended classes can offer method to fetch secondary contents without overloading 
main controller and creating new kernel request. Extended classes are built via a class_mapping in `parameters.yml`.

It also provide a Twig function, `renderLocation, wich uses the `ViewController` as a service, and doesn't
relaunch the Symfony kernel, for more efficiency.

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

## Installation
Clone this repository under `src/ezobject`: `git clone https://github.com/Erilan/ezobject.git src/ezobject`

Register the bundle in `EzPublishKernel.php`, adding this line to `registerBundles()`: 
```php
new ezobject\WrapperBundle\eZObjectWrapperBundle(),
```

## Contact
E-mail : savoie.antonin@gmail.com / asavoie@kaliop.com


