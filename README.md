# eZObjectWrapperBundle

A Symfony Bundle for eZ Publish 5 development (>=5.3).

Developed by the [Kaliop](http://www.kaliop.com/) team.


## Description

This bundle offers a simple model to organize all the "business logic" code which deals with eZPublish Location and
Content objects, keeping it away from Controllers, Commands and other application layers which relate to the Framework.

This way, if you change the definition of one of your ContentTypes, you will not have to hunt across the whole codebase,
to apply fixes but, ideally, change the code only in one place.

It also tries to adopt patterns which are familiar to Symfony developers who have not used eZPublish before.
NB: if you are a Symfony developer please read the *This is not your grandpa's ORM* chapter further down to help avoid
common pitfalls.

## How it works

The bundle provides an *Entity Manager* service, which is used to retrieve Repository services. For each specific type of
Content, a different Repository service is used.

*Repository* services are used to hold the logic to fetch Entities. A typical Repository method would be f.e.
`getLastTenModified()`.

*Entity* instances provide a lazy-loading wrapper for Contents and Locations. They are supposed to hold the logic to
decode data from the Content Fields, and to fetch related Entities (without overloading the view Controller and creating
a new kernel request). They are generally not configured as Symfony services.

Developers are supposed to create a new Repository and Entity class for each of the Content Types in use in the website;
the easiest way to do so is to subclass the existing ones and just add in the custom business logic.


## The twig helper

This bundle also provides a Twig function, `renderLocation`, which uses the ViewController as a service, and doesn't
relaunch the Symfony kernel, for faster execution.


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

* Add eZObjectWrapperBundle to EzPublishKernel.php:

```php
new \Kaliop\eZObjectWrapperBundle\KaliopeZObjectWrapperBundle(),
```

## Usage

### Retrieving Entities

```php
$entityManager = $this->container->get('ezobject_wrapper.entity_manager');
$repository = $entityManager->getRepository('article');
$articleEntity = $repository->loadEntityFromLocationId(2);
echo 'Article entity 2 is named: ' . $articleEntity->content()->contentInfo->name;
echo 'Article entity 2 has parent Id: ' . $articleEntity->location()->parentLocationId;
```

There are many more methods available in the Repository, you can retrieve an Entity by id, Content, Location, LocationId
and even Remote Ids.

### Calling methods or attributes of an Entity in Twig

```twig
{{ ez_field_value(articleEntity.content, 'title') }}
```

### Twig function render_location

This does render a Location without using a separate Controller and reloading the kenel

```twig
{{ render_location(locationId, 'view_type', {}) }}
```

### Creating your own Entities and Repositories

Let's imagine you want to handle the 'newsletter' content type.

1. Create an Entity class:

    ```php
    namespace Acme\AcmeBundle\Entity;
    use Kaliop\eZObjectWrapperBundle\Core\Entity as BaseEntity;

    class Newsletter extends BaseEntity
    {
    }
    ```

2. Create a Repository class, which relates to that:

    ```php
    namespace Acme\AcmeBundle\Repository;
    use Kaliop\eZObjectWrapperBundle\Core\Repository as BaseRepository;

    class Newsletter extends BaseRepository
    {
        protected $entityClass = '\Acme\AcmeBundle\Entity\Newsletter';
    }
    ```

3. Register the Repository with the Entity Manager

    ```yml
    ezobject_wrapper:
        class_map:
            newsletter: \Acme\AcmeBundle\Repository\Newsletter
    ```

    (Note that this is "plain" configuration, it does not have to be in `parameters`)

4. Test that it works

    ```php
    $entityManager = $this->container->get('ezobject_wrapper.entity_manager');
    $repository = $entityManager->getRepository('newsletter');
    ```

5. Add new methods to the Entity and Repository classes

    ```php
    namespace Acme\AcmeBundle\Repository;
    use ...;

    class Newsletter extends BaseRepository
    {
        protected $entityClass = '\Acme\AcmeBundle\Entity\Newsletter';

        /**
         * @return \Acme\AcmeBundle\Entity\Newsletter[]
         */
        public function getAllNewsletters()
        {
            $query = new Query();
            $query->filter = new Criterion\LogicalAnd(array(
                new Criterion\ContentTypeIdentifier($this->contentTypeIdentifier),
                new Criterion\Subtree('/1/2/212') // root node where all newsletters are located
            ));
            $query->performCount = false;
            $query->limit = PHP_INT_MAX-1;
            $query->offset = 0;
            // A helper method made available from the base repository class
            return $this->loaddEntitiesFromSearchResults(
                $this->getSearchService()->findContent($query)
            );
        }
    }

    namespace Acme\AcmeBundle\Entity;
    use ...;

    class Newsletter extends BaseEntity
    {
        protected $issueTypeIdentifier = 'newsletter_issue';

        /**
         * @return \DateTime
         */
        public function getLatestIssueDate()
        {
            $query = new Query();
            $query->filter = new Criterion\LogicalAnd(array(
                new Criterion\ContentTypeIdentifier($this->issueTypeIdentifier),
                new Criterion\Subtree($this->location()->pathString)
            ));
            $query->performCount = false;
            $query->limit = 1;
            $query->offset = 0;
            $query->sortClauses = array(new DatePublished(Query::SORT_DESC));
            $result = $this->repository->getSearchService()->findContent($query);
            if (count($result->searchHits) > 0) {
                $latest = $result->searchHits[0];
                return $latest->valueObject->contentInfo->publishedDate;
            }
            return new \DateTime("@0");
        }
    }
    ```

## Advanced Usage

### Registering new Repositories as services

We have seen above how to register a php class as Repository.
Another way to register classes as repositories is to use tagged Symfony services. The main advantage that you get in
exchange for a little bit more configuration is that you can now inject configuration settings into the repository.

Example:

```yml
services:
    ezobject_wrapper.repository.newsletter:
        class: \Acme\AcmeBundle\Repository\Newsletter
        parent: ezobject_wrapper.repository.abstract
        arguments:
            - @ezpublish.api.repository
            - @ezobject_wrapper.entity_manager
        calls:
            # Injecting some settings to our custom repository class. E.g. the root path of newsletter contents
            - [ setSettings, [ { newsletter_location_path: %newsletter_location_path% } ] ]
        tags:
            # Tagging the service will make it register with the Entity Manager for the given contentType
            -  { name: ezobject_wrapper.repository, content_type: newsletter }

parameters:
    # Using a parameter allows to easily set different values for different environments for things like location Ids
    newsletter_location_path: /1/2/212/
```
Then, inside the Acme\AcmeBundle\Repository class, you can use the *settings* member:

```php
    ...
    $query->filter = new Criterion\LogicalAnd(array(
        new Criterion\ContentTypeIdentifier($this->contentTypeIdentifier),
        new Criterion\Subtree($this->settings['newsletter_location_path']) // root node where all newsletters are located
    ));
    ...
```

NB: if you want to make sure that the settings injected into your custom Repository are always valid, you simply have to
implement the `validateSettings` method

### Passing configuration settings into the Entities

Since Entity classes are not registered as Symfony services, injecting settings into Entity instances might seem problematic
at first instance. The *enrichEntityAtLoad* method is available in Repository classes for that purpose.

```php
namespace Acme\AcmeBundle\Repository;
use ...;

class Newsletter extends BaseRepository
{
    protected $entityClass = '\Acme\AcmeBundle\Entity\Newsletter';

    /**
     * @return \Acme\AcmeBundle\Entity\Newsletter[]
     */
    protected function enrichEntityAtLoad($entity)
    {
        $entity = parent::enrichEntityAtLoad($entity);
        return $entity->setIssueTypeIdentifier('newsletter_issue');
    }
}

namespace Acme\AcmeBundle\Entity;
use ...;

class Newsletter extends BaseEntity
{
    protected $issueTypeIdentifier;

    /**
     * @return $this
     */
    public function setIssueTypeIdentifier($issueTypeIdentifier)
    {
        $this->issueTypeIdentifier = $issueTypeIdentifier
        return $this;
    }
}
```

### Allowing an Entity to generate URLs to its Location view

For this common scenario, Traits are made available, to be added to bot Repository an Entity classes

```php
namespace Acme\AcmeBundle\Repository;
use ...;

class Newsletter extends BaseRepository
{
    use Kaliop\eZObjectWrapperBundle\Core\Traits\RouterInjectingRepository;
}

namespace Acme\AcmeBundle\Entity;
use ...;

class Newsletter extends BaseEntity
{
    use Kaliop\eZObjectWrapperBundle\Core\Traits\UrlGeneratingEntity;

    /**
     * To be used when absolute urls to this Location have to be generated, and there is no twig template or routing service available
     * @return string
     */
    public function absoluteUrl()
    {
        return $this->getUrlAlias(true);
    }
}
```

```yml
services:
    ezobject_wrapper.repository.newsletter:
        class: \Acme\AcmeBundle\Repository\Newsletter
        parent: ezobject_wrapper.repository.abstract
        arguments:
            - @ezpublish.api.repository
            - @ezobject_wrapper.entity_manager
        calls:
            - [ setRouter, [ '@router' ] ]
        tags:
            -  { name: ezobject_wrapper.repository, content_type: newsletter }
```

### Allowing an Entity to render RichText fields as html

RichText fields need the help of a Symfony service to convert their xml content to html.
Again, Traits are made available for that, to be added to bot Repository an Entity classes.

```php
namespace Acme\AcmeBundle\Repository;
use ...;

class Newsletter extends BaseRepository
{
    use \Kaliop\eZObjectWrapperBundle\Core\Traits\RichTextConverterInjectingRepository;
}

namespace Acme\AcmeBundle\Entity;
use ...;

class Newsletter extends BaseEntity
{
    use \Kaliop\eZObjectWrapperBundle\Core\Traits\RichTextConvertingEntity;

    /**
     * @return string
     */
    public function bodyAsHtml()
    {
        return $this->getHtml($this->content()->getField('body')->xml);
    }
}
```
```yml
services:
    ezobject_wrapper.repository.newsletter:
        class: \Acme\AcmeBundle\Repository\Newsletter
        parent: ezobject_wrapper.repository.abstract
        arguments:
            - @ezpublish.api.repository
            - @ezobject_wrapper.entity_manager
        calls:
            - [ setRichTextConverter, [ '@ezpublish.fieldType.ezxmltext.converter.html5' ] ]
        tags:
            -  { name: ezobject_wrapper.repository, content_type: newsletter }
```

### Allowing an Entity to fetch related Entities

A common usecase is when, given an instance of an Entity, you want to fetch its related object(s) as Entities too.
A Trait is available for this case as well:

    Kaliop\eZObjectWrapperBundle\Core\Traits\RelationTraversingEntity

Just add it to your Entity class and you will be able to use 2 new methods to retrieve the contents of its object relation(s)
fields:

    $relatedEntity = $this->getRelation('fieldName');
    $relatedEntitiesArray = $this->getRelations('anotherFieldName');


## Impact with the caches (a.k.a. don't shoot yourself in the foot)

When using the render_location twig helper, or using subclasses of Entity which fetch other Content objects in helper
methods, be aware that you are introducing caching dependencies.

eZPublish by default goes to great lengths to make sure that the caches which keep the 'html version' of Content objects
are automatically expired whenever the objects are edited. One might say that view-cache-expiration is the prime reason
for using Symfony sub-requests when displaying a list of Content objects.

Whenever you end up displaying a Location which is not the current one, remember to add its Id to the X-Location-Id header
in your main controller response, so that eZPublish will know when to clear its cache.

For more details see: https://doc.ez.no/display/EZP/HttpCache

## This is not your grandpa's ORM

The EntityManager, Repository and Entity classes in eZObjectWrapper have only a vague resemblance with their counterparts
in the Doctrine ORM. Please do not assume that you can use them the same way.

F.e., at the moment:
- Entities can not be persisted at all, except by using the eZPublish repository API
- There is no concept of creating Entities without attaching them to the EntityManager
- There is no DQL or other equivalent language
- the only Query builder available is the one from the eZPublish repository API


## Contact

E-mail : asavoie@kaliop.com / sbressey@kaliop.com

[![License](https://poser.pugx.org/kaliop/ezobjectwrapperbundle/license)](https://packagist.org/packages/kaliop/ezobjectwrapperbundle)
[![Latest Stable Version](https://poser.pugx.org/kaliop/ezobjectwrapperbundle/v/stable)](https://packagist.org/packages/kaliop/ezobjectwrapperbundle)
[![Total Downloads](https://poser.pugx.org/kaliop/ezobjectwrapperbundle/downloads)](https://packagist.org/packages/kaliop/ezobjectwrapperbundle)

[![Build Status](https://travis-ci.org/kaliop/ezobjectwrapper.svg?branch=master)](https://travis-ci.org/kaliop/ezobjectwrapper)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kaliop/ezobjectwrapper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kaliop/ezobjectwrapper/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/kaliop/ezobjectwrapper/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/kaliop/ezobjectwrapper/?branch=master)
