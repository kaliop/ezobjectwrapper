ezobject
Symfony Bundle eZObjectWrapperBundle for eZ Publish 5 development eZ 5.3).
version 1.0 24/10/2014

============================================================================================================
          DESCRIPTION
============================================================================================================

This bundle offer a simple model to encapsulate location and content for eZPublish 5 development.
It provide a factory to build eZObjectWrapper and extended classes. eZObjectWrapper provide a lazy-loading 
method to fetch content. Extended classes can offer method to fetch secondary contents without overloading 
main controller and creating new kernel request. Extended classes are built via a class_mapping in parameters.yml

It also provide a Twig function, nammed renderLocation, wich use the ViewController as a service, and did not
relaunch the Symfony kernel, for more efficiency.

============================================================================================================
          USAGE
============================================================================================================

- To build eZObjectWrapper :
get the service : $factory = $this->container->get('ezobject_wrapper.services.factory');
build : $factory->buildeZObjectWrapper($location);
buildeZObjectWrapper can accept Location or locationID as parameter.

- Class mapping :
parameters:
    class_mapping:
        content_identifier: \myGreat\BundleBundle\eZObjectWrapper\ClassesExtendingeZObjectWrapper
        
- renderLocation :
{{ renderLocation(locationId, 'view_type', { ezObjectWrapper : myObject }) |raw }}

- call method or attribute from eZObjectWrapper in Twig :
{{ ez_field_value(ezObjectWrapper.content, 'title') }}

============================================================================================================
          INSTALLATION
============================================================================================================

Clone this repository under src/ezobject.
In EzPublishKernel.php, ad the following line in the method "registerBundles()" : 
new ezobject\WrapperBundle\eZObjectWrapperBundle(),

============================================================================================================
          CONTACT
============================================================================================================
E-mail : savoie.antonin@gmail.com / asavoie@kaliop.com


