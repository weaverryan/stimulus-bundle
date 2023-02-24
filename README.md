# StimulusBundle: Symfony integration with Stimulus!

This bundle adds some Twig `stimulus_*` functions & filters to add Stimulus controllers, actions & targets in your
templates.

It also includes a helper to build the Stimulus data attributes and use them in your services.

Install the bundle with:

```
composer require symfony/stimulus-bundle
```

## Twig functions & filters usage

### stimulus_controller

This bundle also ships with a special `stimulus_controller()` Twig function
that can be used to render [Stimulus Controllers & Values](https://stimulus.hotwired.dev/reference/values)
and [CSS Classes](https://stimulus.hotwired.dev/reference/css-classes).
See [stimulus-bridge](https://github.com/symfony/stimulus-bridge) for more details.

For example:

```twig
<div {{ stimulus_controller('chart', { 'name': 'Likes', 'data': [1, 2, 3, 4] }) }}>
    Hello
</div>

<!-- would render -->
<div
   data-controller="chart"
   data-chart-name-value="Likes"
   data-chart-data-value="&#x5B;1,2,3,4&#x5D;"
>
   Hello
</div>
```

If you want to set CSS classes:

```twig
<div {{ stimulus_controller('chart', { 'name': 'Likes', 'data': [1, 2, 3, 4] }, { 'loading': 'spinner' }) }}>
    Hello
</div>

<!-- would render -->
<div
   data-controller="chart"
   data-chart-name-value="Likes"
   data-chart-data-value="&#x5B;1,2,3,4&#x5D;"
   data-chart-loading-class="spinner"
>
   Hello
</div>

<!-- or without values -->
<div {{ stimulus_controller('chart', controllerClasses = { 'loading': 'spinner' }) }}>
    Hello
</div>
```

Any non-scalar values (like `data: [1, 2, 3, 4]`) are JSON-encoded. And all
values are properly escaped (the string `&#x5B;` is an escaped
`[` character, so the attribute is really `[1,2,3,4]`).

If you have multiple controllers on the same element, you can chain them as there's also a `stimulus_controller` filter:

```twig
<div {{ stimulus_controller('chart', { 'name': 'Likes' })|stimulus_controller('other-controller') }}>
    Hello
</div>
```

You can also retrieve the generated attributes as an array, which can be helpful e.g. for forms:

```twig
{{ form_start(form, { attr: stimulus_controller('chart', { 'name': 'Likes' }).toArray() }) }}
```

### stimulus_action

The `stimulus_action()` Twig function can be used to render [Stimulus Actions](https://stimulus.hotwired.dev/reference/actions).

For example:

```twig
<div {{ stimulus_action('controller', 'method') }}>Hello</div>
<div {{ stimulus_action('controller', 'method', 'click') }}>Hello</div>

<!-- would render -->
<div data-action="controller#method">Hello</div>
<div data-action="click->controller#method">Hello</div>
```

If you have multiple actions and/or methods on the same element, you can chain them as there's also a
`stimulus_action` filter:

```twig
<div {{ stimulus_action('controller', 'method')|stimulus_action('other-controller', 'test') }}>
    Hello
</div>

<!-- would render -->
<div data-action="controller#method other-controller#test">
    Hello
</div>
```

You can also retrieve the generated attributes as an array, which can be helpful e.g. for forms:

```twig
{{ form_row(form.password, { attr: stimulus_action('hello-controller', 'checkPasswordStrength').toArray() }) }}
```

You can also pass [parameters](https://stimulus.hotwired.dev/reference/actions#action-parameters) to actions:

```twig
<div {{ stimulus_action('hello-controller', 'method', 'click', { 'count': 3 }) }}>Hello</div>

<!-- would render -->
<div data-action="click->hello-controller#method" data-hello-controller-count-param="3">Hello</div>
```

### stimulus_target

The `stimulus_target()` Twig function can be used to render [Stimulus Targets](https://stimulus.hotwired.dev/reference/targets).

For example:

```twig
<div {{ stimulus_target('controller', 'a-target') }}>Hello</div>
<div {{ stimulus_target('controller', 'a-target second-target') }}>Hello</div>

<!-- would render -->
<div data-controller-target="a-target">Hello</div>
<div data-controller-target="a-target second-target">Hello</div>
```

If you have multiple targets on the same element, you can chain them as there's also a `stimulus_target` filter:

```twig
<div {{ stimulus_target('controller', 'a-target')|stimulus_target('other-controller', 'another-target') }}>
    Hello
</div>

<!-- would render -->
<div data-controller-target="a-target" data-other-controller-target="another-target">
    Hello
</div>
```

You can also retrieve the generated attributes as an array, which can be helpful e.g. for forms:

```twig
{{ form_row(form.password, { attr: stimulus_target('hello-controller', 'a-target').toArray() }) }}
```

## Helper

You can retrieve the helper using dependency injection, and use the built-in methods to help you build your Stimulus
data attributes:

```php
<?php

use Symfony\StimulusBundle\Helper\StimulusHelper;

final class YourService
{
    public function __construct(private readonly StimulusHelper $stimulusHelper)
    {
    }

    public function doSomething()
    {
        // And now you can call the helper to build Stimulus DTOs and retrieve strings or arrays of attributes.
        $dto = $this->stimulusHelper->buildStimulusControllerDto('my-controller');

        // Each Stimulus helper return a DTO, which implement the \Stringable interface, and have toArray() methods.
        $dataAttr = (string) $this->stimulusHelper->buildStimulusActionDto('my-controller', 'myAction');
        $dataAttrAsArray = $this->stimulusHelper->buildStimulusTargetDto('my-controller', 'myTarget')->toArray();
        
        // Also, each method have a previousDto parameter, allowing you to extend a previously built DTO.
        $extendedDto = $this->stimulusHelper->buildStimulusControllerDto('my-controller', previousDto: $dto);
    }
}
```

Ok, have fun!
