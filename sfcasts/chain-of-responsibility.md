# Chain of Responsibility

Time for design pattern number two - the *Chain of Responsibility* pattern.
Sometimes, an *official* definition for a pattern doesn't exist. This is no
exception, so here's *my* definition. Put simply, Chain of Responsibility is a
way to set up a sequence of methods to be executed, where each method can decide
to execute the *next* one in the chain or *stop* the sequence entirely.

When we have to run a sequence of checks to determine what to do next, this
pattern can help us do that. Suppose we want to check to see if a comment is
spam or not, and we have *five* different algorithms to help us make that
determination. If any of them return `true`, it means the comment *is* spam and
we should stop the process because running algorithms is expensive. In a
situation like this, we need to encapsulate each algorithm into a "handler"
class, set up the chain, and run it.

Now, if you're wondering what a "handler" class is, or what I mean by "chain",
great questions! Let's take a closer look at the anatomy of the pattern.

## Pattern Anatomy

Chain of Responsibility is composed of three parts:

First, it has a `HandlerInterface`, which usually contains two
methods: `setNext()` and `handle()`. The `setNext()` method receives a
new `HandlerInterface` object. This allows us to set up a sequence of handlers,
choosing which handlers we want in the sequence and in what order they appear.
This sequence is called a *chain*. The `handle()` method is where we put our business logic.

*Second* is the *concrete handlers*, which implement the `HandlerInterface`.
They can hold the *next* `HandlerInterface` object (added with `setNext()`) and
decide if it should be called or not. If they don't contain the next handler,
this handler is the final link in the chain.

Finally, we have a *client* that sets up the chain, ensuring that the sequence
is in the right order and triggers the first handler.

If you have more questions now than you did when we started, don't worry! It'll
make more sense when we see it in action.

## The Real-Life Challenge

For our next challenge, we're going to boost the player's level. To do that,
we'll reward players with extra XP after a battle. We
can reward them in a few different ways, but only *one* should apply at a time.
The conditions for XP rewards are as follows:

One: If the player is level 1.
Two: If the player has won 3 times or more in a row.
And three, to add some randomness, the player will throw two six-sided dice.
They win if a *pair* is rolled, but if the result is 7, they do not.

Each condition will reward the player with 25 XP.

Okay, let's do this! The first step we need to take is creating an interface
for our handlers. Inside the `src/` directory, create a new folder
called `ChainHandler/`. And inside *that*, we'll add a new PHP class called...
what about `XpBonusHandlerInterface`. I recommend including the name of the
pattern as part of the interface name so it's more obvious what pattern we're
using.

Now we can add the first method - `public function handle()` - and the
arguments - `Character $player` and `FightResult $fightResult`. These
are the two objects required for calculating all the above conditions.

Oh! And don't forget to add the return type `int`. This will be the XP.

For the next method
write `public function setNext(XpBonusHandlerInterface $next): void`. And I
almost forgot to add the `Character` import statement. Press "Option" + "Enter"
and select "Import class".

Okay, the interface is ready! It's time to add some *handlers*. Inside
the `ChainHandler/` directory, add a PHP class. The first condition to reward
players is if they're level 1, so we'll call this `LevelHandler`. Now we need to
implement the `XpBonusHandlerInterface`. We've seen this before! Hold "Option" + "Enter"
to add both methods.

We'll work on `setNext()` first. Inside, write `$this->next = $next;`. Then, to
add the property, click on `$this->next`, press "Option" + "Enter", and
select "Add property". Perfect! Now let's work on the `handle()` method. We want
to reward the player if their level is 1, so
write `if ($player->getLevel() === 1)` and, inside, we'll `return 25;`. If the
player's level is *not* 1, we'll call the next handler, but only if it's set, so
write `if (isset($this->next))`. Inside,
we'll `return $this->next->handle($player, $fightResult)`. At the bottom, we'll
just `return 0;`. That means we made it to the end of the chain and none of the
handlers applied.

Let's keep it going! Add another PHP class for our second winning condition -
when the player has won 3 or more times in a row - and we'll call
it `OnFireHandler`. Implement the interface... and use the same trick with
"Option" + "Enter" to add the methods. We'll do the same thing with
the `setNext()`. Write `$this->next = $next`, and hold "Option" + "Enter" to add
the property. In the `handle()` method, we need to check for the player's win
streak. That value is inside `$fightResult`, so
write `if ($fightResult->getWinStreak() >= 3)`. Inside *that*, reward the player
by returning `25`. Below, add the same check as before, calling the next
handler: `if (isset($this->next))`...
and `return $this->next->handle($player, $fightResult)`. So... I'm not in love
with this repetition. Surely there's a better way to do this, right? There *is*,
and we'll talk about that later, but for now, let's finish up this method and
return `0` at the bottom.

On to the last handler condition, where we roll two dice and check to see if we rolled
a pair or a 7. Let's call it `CasinoHandler`, since we're doing a little
gambling. We'll start this in the same way, implementing the interface and
adding the methods by holding "Option" + "Enter". Then, just like before,
implement `setNext()`. Inside, write `$this->next = $next` and add the property
on top.

Now let's work on the `handle()` method. Roll a couple of six-sided dice by
writing `$dice1 = Dice::roll(6)` and `$dice2 = Dice::roll(6)`. The first thing
we need to do here is check to see if we rolled 7, because if we did, we need to
exit immediately. Write `if ($dice1 + $dice2 === 7)` and, inside, return `0`.
Below that, we'll check to see if we rolled a pair so we can reward the player.
Write `if ($dice1 === $dice2)`, and inside that, return `25`. If we didn't roll
well, we'll call the next handler, so, once again, check to see if it's set by
writing `if (isset($this->next))`. Inside,
write `return $this->next->handle($player, $fightResult)`... and return `0` at
the bottom.

Phew! We finished implementing our handlers! But before we can give this a try,
we'll need to *initialize* the chain. When we do that, we'll get to see a
*downside* of this pattern. Let's do that *next*.
