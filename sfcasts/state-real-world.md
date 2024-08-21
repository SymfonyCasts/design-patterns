# State in the Real World

Ok, it's time to see the state pattern in the real world. A good example are IDE's
like PhpStorm or Visual Studio. Let's take a look, open up `GameApplication`,
and select the `play()` method, if I try to refactor it I can see that most of the
options are enabled. But what happens if instead I try to refactor a property? 
There are now a few options disabled, I cannot change the signature, or make it static.
So, based on what's selected in the IDE the menu changes its behavior. Does it sound
familiar? In this case, the IDE is the context, and selecting something can be
seen as an event or action that makes the IDE to change its internal state.

Ok! Another place where the state pattern is used is in the Symfony Workflow component.
This component allows you to create *state machines* quite easily. Let's take a look
to the documentation. I have it already opened. It has a few examples but the one
I like is the "pull request" feature. Here we can see all the possible states that a pull
request can be in, how to transition from one state into another, and what events are executed.
For example, if it's in the "test" state and there's an "update" event, it stays in the same state.
But, if there's a "wait_for_review" event, it transitions to the "review" state.
It's pretty cool, isn't it? And the best part is that you can configure your state machines
using yaml! Below is the configuration for the pull request example. The next time
you need to implement a state machine I recommend you to use Symfony Workflow, it's a
pretty fun component to work with.

## State vs. Strategy

When I started researching about the State pattern, I realised that both patterns
share the same design.
(show diagram at this point)
The only structural difference is that state objects may have a reference to other states.
So, what's the deal here? Does not matter if I use strategy or state, will I get
the same result? Well, not exactly. The main difference is the purpose behind each pattern.
The State pattern allows you to change behavior based on the internal state of an object,
and Strategy allows you to choose from a family of algorithms regardless of the state
of the system. 

Here's an excellent analogy I found in the internet from "Michal Aibin".
(We can show an image of the link below (item 4) - or let me know if I should record a video)
A car can be in different states. If the engine is on or off, or if the tank is full or empty.
In all these states, the car behaves differently. This is the State pattern in action.
But, if we change the engine to use gas or diesel, it does not change the state of the car
it only changes how an internal element of the system works. That's the Strategy pattern.
(link to the article: https://www.baeldung.com/cs/design-state-pattern-vs-strategy-pattern#strategy-vs-state)

## Conclusion

Okay, that's the state pattern! Let's go over some of the pros and cons:

✅ Allows your objects to change behavior when their internal state changes.
And it can even hide on what state the object is.
✅ It's a great way to avoid big if-else blocks.
✅ Leverages SRP and Open/Close principles.

❌ But, it may be an overkill for simple cases.
❌ And, it may introduce a significant amount of classes. 

Coming next: Our last pattern of this series. The *Factory pattern*!
