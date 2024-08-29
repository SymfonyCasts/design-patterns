# State in the Real World

Okay, it's time to see the State pattern in the *real* world - our IDE.
Open up `GameApplication`, and select the `play()` method. If we try to refactor it,
we can see that most of the options are *enabled*. Hm... If we try to refactor a
*property* instead... more of the options are disabled - we can't change the
signature *or* make it static. Why? The options available are based on what's
selected in the IDE. Sound familiar? In this case, the IDE is the *context*, and
*selecting* something is basically an "event" or "action" that makes the IDE
change its internal state.

Another place we can see the State pattern at work is in the Symfony Workflow
component. This component allows us to easily create *state machines*. If we
take a look at the documentation, we can see a few examples. The one I like is
the "pull request" feature. Here, we can see all of the possible states that a
pull request can be in, how to transition from one state into another, and what
events are executed along the way. If it's in the "test" state, for instance,
and there's an "update" event, it stays in the same state. But, if there's a
"wait_for_review" event, it *transitions* to the "review" state. Pretty cool,
right? And the *best* part is, we can configure our state machines using
YAML! The next time you need to implement a state machine, I *highly* recommend
you try Symfony Workflow. It's a pretty fun component to work with.

## State vs. Strategy

Did you know that the State and Strategy patterns share the same design? The
only *structural* difference is that state objects may have a reference to other
states. So... if both patterns have the same design, why does it matter which
one we use? Won't we get the same result either way? *Not exactly*. The most
important difference between each pattern is the *purpose* behind them. The
*State* pattern allows us to change behavior based on the internal state of an
object. The *Strategy* pattern allows us to choose from a family of algorithms,
regardless of the state of the system.

Here's an excellent [analogy](https://bit.ly/state-vs-strategy) from Eugene Kovko
and Michal Aibin:

> A car can be in different states. The engine can
be on, and the engine can be off. The battery can
be dead. The tank can be empty, and so on. In all
of these states, the car will behave differently.
However, a driver can access the car’s interface:
steering wheel, pedals, gears, etc. These are the
states, and the entire behavior can be considered
a combination of the conditions. All the states
would provide a distinct behavior [...]

This is the State pattern in action. But, if we change the engine to use gas or diesel,
it does not change the state of the car it only changes how an internal element
of the system works. *That's* the Strategy pattern.

## Conclusion

Okay, that's the State pattern in a nutshell. Let's go over some of the pros and
cons:

✅ First, the State pattern allows your objects to change behavior when their
internal state changes, and it can even hide what state the object is in.
✅ It's also a great way to avoid using big if-else blocks.
✅ And finally, it leverages SRP and the Open/Close principle.

❌ *However*, it may be overkill for simple cases
❌ and it *may* introduce a significant number of classes.

Up next: We'll take a look at the *last* pattern in this series - the *Factory pattern*!
