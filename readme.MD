<h2>Component Controllers</h2>
<p>A Component Controller is a simple wrapper that allows you to define it as a service with a number of components and a template. The components provide the data for the template, and the controller combines the data from each of the components and passes it into the template.</p>
<p>The component controller is for use with the twig render() function</p>
<p>For example, if you wished to render a footer in your layout file with a number of elements, you would defined a new Footer service in your service definition file. This service would call the addComponent() method to pass through a new componenent, say one that produces a footer specifc navigation. As well as addComponent(), you will also need to call the setTemplate() method to set the template the controller should render.</p>
...
acme.controller.footer:
    class: Acme\ExampleBundle\Controller\ComponentController
    arguments: [ @templating.engine.twig ]
    calls:
        - [ setTemplate, [ ::footer.html.twig ] ]
        - [ addComponent, [ @component_footer ] ]
...
<p>You would need to define the Footer component as a service, and its class must implement the TemplateComponent interface</p>
<p>Once you have done this you could render this footer in your layout file by adding:</p>
...
{{ render(controller('acme.controller.footer:render')) }}
...

<h3>Passing through arguments</h3>
<p>Because the ComponentController does not handle arguments, its not possible to pass through arguments directly by using the following syntax:</p>
...
{{ render(controller('acme.controller.footer:render', {data: "data to be passed through"})) }}
...
<p>Instead you will need to pass through the data as query arguments like:</p>
...
{{ render(controller('acme.controller.footer:render', {}, {data: 1})) }}
...
<p>To use this data in your components, you will need to inject the RequestStack, which will allow you to produce the Request object and access the data passed through as a query string.</p>

