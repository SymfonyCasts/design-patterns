# Factory Pattern in the Real World

If you're as excited as I am about the Factory pattern, you *may* want to start
using it in your Symfony applications. *Fortunately*, Symfony provides solid
support.

## Using Factories with Symfony

Check it out! Go to the Symfony docs site, search for "Using Factory", and click
on the first link [here](https://symfony.com/doc/current/service_container/factories.html).

This shows us a *ton* of ways to use a factory, like creating objects through
a "static factory", which is just a static *method* inside of a service class.
And down here, we can see the configuration we need to set this up - by using
the `factory` option in the client definition to specify the factory class and
static method to call. *Super* helpful!

Another option is making the class you want to instantiate its *own* factory. It
also works with a static method, and it's super handy for minimizing the amount
of classes we have, all while keeping the instantiation logic in one place. Down
here, we also have the "Non-Static Factory", which is pretty similar to what
we've been doing. The *main* difference is that *Symfony* will take care of
instantiating the factory, calling the `make()` method, and injecting whatever
it returns into the service that needs it. Oh, and if your `make()` method
requires some arguments, you can use the `arguments` option to define them. And
if *none* of these options works for you, you can always inject your factory
into your services and call the `make()` method yourself, just like we did in
this tutorial.

## Factory in Symfony Forms

Okay, those are some of the options Symfony gives us for using the Factory
pattern. *But* does Symfony use the pattern *internally*? You bet! Symfony uses
the Factory pattern in the *Form component*.

Let's take a look at the code:

```php
class TaskController extends AbstractController
{
    public function new(Request $request): Response
    {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);
        
        $form->handleRequest($request);
        ...
    }
}
```

This is a simple controller method that receives a request, creates a form
object by calling `$this->createForm()`, and processes it. Now, let's look at
the `createForm()` method.

```php
class AbstractController 
{
    protected function createForm(string $type, mixed $data = null, array $options = []): FormInterface
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }
}
```

This is a shortcut for fetching the `form.factory` service from the container
and calling `create()` on it. If you look at the arguments, it receives a
`$type` parameter that determines what *form* object to create, as well as some
data to fill it in. Look familiar? We did this earlier too!

## Conclusion

All right! That's the Factory pattern! Let's go over some of the pros and cons:

✅ First, it's a great way to decouple the code that *creates* objects from the
code that *uses* them.
✅ It's also *very* helpful when you need to create different families of
objects.
✅ And *finally*, it leverages the SRP and Open-Closed principles.

❌ *But*, it *may* make your code base more complex, especially if you have a lot
of factories.

Okay, team! We're *done*! We covered *a lot* of patterns, but there are still
more out there. If there's a pattern that you'd really like to hear about, let
us know! Until then, have fun putting all these into practice, and if you need
help, we're here for you down in the comments.

Thanks for coding with me, and I'll see you next time!
