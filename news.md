# Ver 3.0

* changed: the way to set the php classes to be used as Object Wrappers is now by semantic configuration instead of
  parameters. See the example file in Resources/config

* changed: the twig helper has been renamed to 'render_location'

* changed: the Factory method to create Object Wrappers is called 'build'

* changed: the Factory method to create Object Wrappers from arrays is called 'buildFromArray'

* new: the Factory service has gained 3 more methods to create Object Wrappers: buildFromContentId, buildFromContentRemoteId
  and buildFromLocationRemoteId

* new: it is now possible to tag services as Object Wrappers instead of using plain php classes.
  This allows for great flexibility, but please read the comments in eZObjectWrapperService.php to grasp the subtleties.


# Ver 2.0

...
