# The Chain of Responsibility's *Cousin* - the *Middleware* Pattern

Did you know that the Chain of Responsibility pattern has a *cousin*? It's
called the *Middleware* pattern. The two patterns are *very* similar, but the
Middleware pattern has a subtle difference; This pattern *always* makes it to
the end of the chain. In other words, *every single handler* (or middleware) is
*always* executed. It's a cool way to introduce more actions or logic when
processing a request. For example, if you use middleware to handle user
registrations, you could have a middleware for sending analytics to an API,
*another* one for tracking a query parameter in the request for marketing
purposes, and so on.

In our game application, we could use it to finish building our character
objects. We could have a middleware that gives a random weapon to the character,
or increases their level, or to even boost their health. Long story short, *all*
middlewares will be executed, and using them is a great way to make your
application more flexible.

## Chain of Responsibility in the Real World - Symfony Voters

Okay, before we move on to the next pattern, let's take a look at a real-world
example. Symfony leverages Chain of Responsibility in its security component. It
uses a concept called "Voters" to determine if a user has access to a specific
resource or not. But Symfony *implements* this pattern in a slightly different
way. Instead of having explicit references to other voters, Symfony handles the
iteration and execution of voters in the chain *internally* - Voters
*themselves* don't have direct references to *other* voters in the chain.

Let's take a closer look at how Voters are used in Symfony. Over at your
browser, go to GitHub, and inside, find the *Symfony/security-core* repository.
I already have it open. Now, search for the `AccessDecisionManager` class. If we
check out its constructor, we can see that it receives a list of *voters*, and
at some point, it iterates *over* them and calls `vote()`. This class does a lot
more than that, of course, but we can see the essence of Chain of Responsibility
at work here.

## Conclusion

Okay, that's the pattern! Simple, right? Let's go over some of the pros and
cons:

✅ Most importantly, it allows you to add or remove handlers *dynamically* by
changing the members or order of the chain.
✅ It also leverages the *Single Responsibility Principle*, which allows us to
decouple classes that do work from classes that *use* them.
✅ And finally, it leverages the *Open/Closed Principle*. That allows us to
introduce new handlers to the app without touching existing code.

❌ *But*, as we've already seen, it can be hard to debug.
❌ Requests may also end up *unhandled* if no object handles it.
❌ And unfortunately, if the chain is too long, it can affect performance.

So there you have it! Next: Let's move on to the *State* pattern and handle
difficulty levels in a *stylish* way.
