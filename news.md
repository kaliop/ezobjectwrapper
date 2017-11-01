Ver 4.0

* added a new method to the base Repository Class and Interface: `loadEntityFromContentAndLocation`
    This method is useful f.e. when you want to create an Entity that matches a given version and specific location;
    that happens notably when doing content previews, where eZ will inject into your controllers both of them.
    
    Since we changed an Interface, we consider this to be an API breackage, and had to increase the major version nr.


Ver 3.1

* added a new Trait to allow to easily get related Entities of an existing entity

* the RichTextConvertingEntity Trait has gained a new method getHtmlForField to make life easier to implementors


Ver 3.0.1

* fixed a bug in the RichTextConverterInjectingRepository trait


Ver 3.0

* changed: completely new API. No more Factory and Wrapper, we have EntityManager, Entity and Repository

* changed: the way to set the php classes to be used as Repositories is now by semantic configuration instead of parameters. See the example file in Resources/config

* changed: the twig helper has been renamed to 'render_location'

* new: the Factory service has gained 3 more methods to create Entities: buildFromContentId, buildFromContentRemoteId and buildFromLocationRemoteId

* new: it is now possible to tag services as Repositories instead of using plain php classes

* changed: the codebase now uses Traits, so minimum requirements have been bumped up to php 5.4


Ver 2.0

...
