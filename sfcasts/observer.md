# The Observer Pattern

Time for pattern number three - the *observer pattern*. Here's the technical
definition:

## The Definition

> The observer pattern defines a one-to-many dependency between objects so
> that when one object changes state, *all* of its dependents are notified
> and updated automatically.

Okay, *not bad*, but let's try this:

> The observer pattern allows a bunch of different objects to be notified by a
> *central* object when something happens.

This is the classic situation where you write some code that needs to be called
whenever something *else* happens. And there are actually *two* strategies to solve
this: the observer pattern and the pub-sub pattern. We'll talk about both. But first
up - the *observer* pattern.

## Anatomy of Observer

There are two different types of classes that go into creating this pattern. The
first is called the "subject". That's the central thing that will do some work and
then notify *other* objects before or after it finishes. Those other objects are
the second type, and they're called "observers".

This is pretty simple. Each observer tells the subject that it wants to be notified.
Later, the subject loops over all of the observers and calls a method on them.

## The Real-Life Challenge

Back in our real app, we're going to make our game a bit more interesting by
introducing *levels* to the characters. Each time you win a fight, your character
will earn some XP or "experience points". After you've earned enough points, the
character will "level up", meaning it's base stats, like `$maxhealth` and
`$baseDamage`, will increase.

To write this new functionality, we *could* put the code right here inside of
`GameApplication` after the fight finishes. So... maybe down here after
`finishFightResult()`, we would do the XP calculation and see if the character can
level up.

*But*, to better organize our code, I want to put this new logic somewhere *else*
and use the observer pattern to connect things. `GameApplication` will be the
*subject*, which means *it* will be responsible for notifying any *observers* when
a battle finishes.

Another reason, beyond code organization, that someone might choose the observer
pattern is if `GameApplication` lived in a third-party vendor library and that vendor
library wanted to give *us* - the *user* of the library - some way to run code
*after* a battle finishes... since we don't have the luxury to just hack code into
`GameApplication`.

## Creating the Observer Interface

Step one to this pattern is to create an interface that all the observers will
implement. For organization's sake, I'll create an `Observer/` directory. Inside of
this, create a new PHP class, make sure "Interface" is selected, and call it,
how about, `GameObserverInterface`... since these classes will be "observing"
something related to each game. `FightObserver` would also have been a good name.

Inside we just need one `public` method. We can call it anything, so how about
`onFightFinished()`.

Why do we need this interface? Because, in a minute, we're going to write code that
loops over *all* of the observers inside of `GameApplication` and calls a method
on them. So... we need a way to *guarantee* that each observer *has* a method, like
`onFightFinished()`. And we can actually pass `onFightFinished()` whatever
arguments we want. In this case, let's pass it a `FightResult` argument because,
if you're listening to this, it'll probably be useful to know the *result* of the
fight. I'll also add a `void` return type.

## Adding the Subscribe Code

Okay, step two: We need a way for every observer to *subscribe* to be notified on
`GameApplication`. To do that, create a `public function` inside `GameApplication`
called, how about, `subscribe()`. You can name this anything. This is going to accept
any `GameObserverInterface`, I'll call it `$observer` and it will return `void`.
I'll fill in the logic in a moment.

The *second* part, which is *optional*, is to add a way to *unsubscribe* from the
changes. I'll copy everything we just did... paste... and change this to
`unsubscribe()`.

Perfect!

At the top of the class, create a new array property that's going to hold all of
the observers. Say `private array $observers = []` and then, to help my editor,
I'll add some documentation: `@var GameObserverInterface[]`.

Back down in `subscribe()`, let's populate this. I'll add a little check for
uniqueness by saying `if (!in_array($observer, $this->observers, true))`, and inside
of that, `$this->observers[] = $observer`.

Do something similar down in `unsubscribe()`. Say
`$key = array_search($observer, $this->observers)` and then `if ($key !== false)`,
meaning we *did* found that observer, `unset($this->observers[$key])`.

## Notifying the Observers

Finally, we're ready to *notify* these observers. Right after the fight finishes,
so every time a fight ends, `finishFightResult()` is called. So, right here, I'll
say `$this->notify($fightResult)`.

We don't *need* to do this... but I'm going to isolate this logic of notifying the
observers to a new `private function` down here called `notify()`. It will accept
the `FightResult $fightResult` argument and return `void`. Then
`foreach` over `$this->observers as $observer`. And because we know that those are
all `GameObserverInterface` instances, we can call `$observer->onFightFinished()`
and pass `$fightResult`.

And... the *subject* - `GameApplication` - is *done*! By the way, sometimes the
code that notifies the observers - so `notify()` in our case - is `public` and
meant to be called by someone *outside* of this class. That's just a variation
on the pattern. Like with many of the small details of these patterns, you can
do whatever you feel is best. I'm showing you the way *I* like to do things.

Next: let's implement an *observer* class, right the level up logic, then hook
it into our system.