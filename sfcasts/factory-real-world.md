# Factory Pattern in the Real World

## Using Factories with Symfony
https://symfony.com/doc/current/service_container/factories.html

We can quickly cover how to use factories in Symfony. Perhaps just talk about a couple of the use cases
and mention the others pointing to the documentation.

## Where do we see this in the real world?

- Form Component: uses factories to create form types, form fields, and form data transformers.
  the `FormFactory` class provides methods to create forms, which can be customized with different types and options.
  It uses factories and builders in conjunction to create and configure form objects.

## Conclusion
Factory pattern is a great way to decouple the code that creates objects from the code that uses them.
It centralizes the logic for creating objects in a single place, and it allows you to swap the implementation
of the objects at runtime. It is also a great pattern to use when you need to create different families of objects§