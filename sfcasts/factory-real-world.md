# Factory Pattern in the Real World

If you are as excited as I am about the factory pattern, you might want to start
using it in your Symfony applications. And, fortunately Symfony supports it pretty well.

## Using Factories with Symfony

Let's take a look. Go to the Symfony docs site, search for "Using Factory", and
click on the [first link](https://symfony.com/doc/current/service_container/factories.html).
Here you can learn how to use a factory in many ways. For example, 
here's how to create your objects through a "Static Factory", which is
just a static method inside a service class. Below is the configuration to set this up.
In the client definition, you use the `factory` option to specify the
factory class, and the static method to call. 
Ok, the next variant is making the class you want to instantiate its own factory.
It also works with a static method. It's handy for minimizing the amount of classes
and keep the instantiation logic in one place.
After that one, we find the "Non-Static Factory", which is quite similar to what we've been doing.
The main difference is that Symfony will take care of instantiating the factory,
calling the `make()` method, and inject whatever it returns into the service that needs it.
Oh, and in case your `make()` method requires some arguments, you can use the `arguments` option
to define them. And, if none of these options works for you,
you can always inject your factory in your services and call the `make()` method yourself,
just like we did in this tutorial.

## Factory in Symfony Forms

Ok, those are the ways that Symfony give us for using the factory pattern. But, does
Symfony use the pattern internally? The answer is yes! Symfony uses the factory pattern
in the *Form component*.

Let's look at the code:

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

This is a simple controller method that receives a request, creates a form object by
calling `$this->createForm()`, and processes it. Now, let's see what does
the `createForm()` method do:

```php
class AbstractController 
{
    protected function createForm(string $type, mixed $data = null, array $options = []): FormInterface
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }
}
```

It is a shortcut for fetching the `form.factory` service from the container and
calling `create()` on it. Now take a look to the arguments, it receives a `$type` parameter
that determines what *form* object to create and also some data to fill it in.
Does it look familiar? Is exactly what we've been doing.

## Conclusion

Ok! That's the Factory pattern. Let's go over some of the pros and cons:

✅ Is a great way to decouple the code that creates objects from the code that uses them.
✅ It is very helpful when you need to create different families of objects.
✅ And, it leverages the SRP and Open/Close principles.

❌ But, it may make your code base more complex, especially if you have a lot of factories.

Okay, team. We're done! Although there are many more patterns out there. If there's
a pattern that you really want to hear about, let us know! Until then, put all these
into practice and master your patterns.

Thanks for coding with me, and I'll see you next time!
