# Command Pattern in the Real World

Now that we're pros at *utilizing* the Command Pattern, can you guess where *Symfony* uses it? If you said "Symfony Console", you're right! And, we've been using it all along. Open up `GameCommand.php`. We can see that it extends from this `Command` base class. This belongs to Symfony and it comes with a bunch of goodies. Check it out! Hold "command" and click on the class name to open it. This has *a lot* of stuff, and that makes sense. It needs to handle a lot of things like options, arguments, and more. It can't be as simple as our commands because it needs to be extendable and handle a lot of different use cases. Lucky for us, we're only interested in the `execute()` method. Let's find that and see what it does. Aha! It does *nothing*. Well, it does force us to implement it, and that's great because this is our *entry point* to the command system. Symfony doesn't care what code we put here, so we can do whatever we want. *Cool*!

Another place the Command Pattern is used in Symfony is in the *Messenger* component. It's not *super* obvious because Messenger handles events, queues, workers, and more, and it leverages other patterns like *middleware*, *event dispatcher*, etc, but if we pay attention to "Message Handlers", we can tell that they're pretty similar to our action commands. For example, the constructor can have *any* dependency, and the `__invoke()` method is our `execute()` method. Our business logic goes there. The only thing it's missing is an *interface*. It *used to* have that in a previous version, but it was replaced by PHP attributes because it's only purpose was to tag the class as a message handler.

## Conclusion

Okay, *that's* the Command Pattern! Let's recap the pros and cons:

Pros:
- The Command Pattern is a great way to encapsulate methods into objects.
- Allow us to decouple the *what* from the *how* and *when*.
- It's handy when you need to support *undoable* operations.
- It's well-accepted by libraries and frameworks.
- And it leverages the *Single Responsibility Principle* and the *Open/Closed Principle*.

Cons:
- It increases code complexity because we're introducing a new layer. When each command requires a separate class, it can introduce a *significant* number of additional classes.

All right! It's time to *reward* players after a hard-fought battle! *But* there are different kinds of rewards and only *one* should apply. To do that, we'll leverage the *Chain of Responsibility Pattern*. That's *next*.
