Ver 3.1

* added a new Trait to allow to easily get related Entities of an existing entity


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
