# The Observer Class

Now that we've finished our *subject* class - `GameApplication` - where we can
call `subscribe()` if we want to be notified after a fight finishes - let's turn
to creating an *observer* that will calculate how much XP the winner should earn
and whether or not the character should level up.

But first, we need to add a few things to the `Character` class to help. On top,
add `private int $level` that will default to `1` and a `private int $xp` that
will default to `0`. Down here a bit, add `public function getLevel(): int`
which will `return $this->level`... and another convenience method called `addXp()`
that will accept the new `$xpEarned` and return the *new* XP number. Inside say
`$this->xp += $xpEarned`... and `return $this->xp`.

Finally, right after, I'm going to paste in one more method called `levelUp()`.
We'll call *this* when a character levels up: it increases the `$level`, `$maxHealth`,
and `$baseDamage`. We *could* also level-up the attack and armor types if we wanted.

## Creating the Observer Class

Ok, *now* let's create that observer. Inside the `src/Observer/` directory, add
a new PHP class. Let's call it `XpEarnedObserver`. And all of our observers need to
`implement` the `GameObserverInterface`. Go to Code Generate, or "command" + "N"
on a Mac to implement the `onFightFinished()` method.

For the *guts* of `onFightFinished()`, I'm going to delegate the *real* work
to a service called `XpCalculator`.

If you downloaded the course code, you should have a `tutorial/` directory with
`XpCalculator.php` inside. Copy that, in `src/`, create a new
`Service/` directory and paste that inside. You can check this out if you want to,
but it's nothing fancy. It takes the `Character` that won, the enemy's level,
and it figures out how much XP it should award to the winner. Then, if they're
eligible to level up, it levels-up that character.

Over in `XpEarnedObserver`, we can use that. Create a constructor so that we can
autowire in a `private readonly` (`readonly` just to be super trendy) `XpCalculator
$xpCalculator`. Below, let's set the `$winner` to a variable -
`$fightResult->getWinner()` - and `$loser` to `$fightResult->getLoser()`.
Finally, say `$this->xpCalculator->addXp()` and pass `$winner` and `$loser->getLevel()`.

## Connecting the Subject & Observer

Beautiful! The subject and observer are now *done*. The final step is to
instantiate the observer and make it *subscribe* to the subject: `GameApplication`.
We're going to do this manually inside of `GameCommand`.

Open up `src/Command/GameCommand.php`, and find `execute()`, which is where we're
currently initializing all of the code inside our app. In a few minutes, we'll
see a more *Symfony* way of connecting all of this. For right now, say
`$xpObserver = new XpEarnedObserver()`... and pass that a `new XpCalculator()` service
so it's happy. Then, we can say `$this->game` (which is the `GameApplication`)
`->subscribe($xpObserver)`.

So we're *subscribing* the observer before we actually run our app down here.

This means... we're ready! But, just to make it a bit more obvious if this is working,
head back to `Character` and add *one more* function here called `getXp()`, which
will return `int` via `return $this->xp`.

This will allow us, inside of `GameCommand`... if you scroll down a bit to
`printResults()`... here we go... to add a few things like
`$io->writeIn('XP: ' . $player->getXp())`... and the same thing for `Final Level`,
with `$player->getLevel()`.

Ok team - testing time! Spin over, run

```terminal
./bin/console app:game:play
```

and let's play as the `fighter`, because that's still one of the toughest characters.
And... awesome! Because we won, we received 30 XP. We're *still* Level 1, so let's
fight a few more times. Aw... we lost, so no XP. Now we have 60 XP... 90 XP...
woo! We leveled up! It says `Final Level: 2`. It's working!

What's great about this is that `GameApplication` doesn't need to know or care
about the XP and the leveling up logic. It just notifies its subscribers and they
can do whatever they want.

Next, let's see how we could wire all of this up using Symfony's *container*. We'll
also talk about the *benefits* of this pattern and what parts of SOLID it helps with.
