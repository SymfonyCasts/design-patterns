# The Observer Class

Coming soon...

Now, let's implement a concrete *observer* that will calculate how much XP the winner
should earn and detect if the character should level up. But first, we need to add a
couple of things to the `Character` class to help with this. On top, add a `private
int $level` that will default to `1` and a `private int $xp` that will default to
`0`. Down here a bit, I'll add `public function getLevel(): int` which will `return
$this->level`, and another convenient function called `addXp()` that will accept the
new `$xpEarned`. And we actually need to return the new XP number, so inside I'll say
`$this->xp += $xpEarned`, and `return $this->xp`. Finally, right after this, I'm
going to paste in one more method in called `levelUp()`. This is the method we'll
call when the user's character levels up, and you can see that it increases the
`level`, `maxHealth`, and `baseDamage` by a little bit. We can level up the attack
and armor type if we wanted to as well. Perfect!

We're now ready to *implement* that observer. Inside the `/src/Observer` directory,
create a new PHP class. Let's call it `XpEarnedObserver`. All of our observers need
to `implement` the `GameObserverInterface`, and I'll go to Code Generate, or
"command" + "N" on a Mac, to implement the `onFightFinished()` method. In the actual
*guts* of `onFightFinished()`, we're going to delegate the real work to a service
called `XpEarnedService`.

If you downloaded the course code, then you *should* have a `/tutorial` directory
with `XpCalculator.php` inside. I'm going to copy that, and in `/src`, create a new
`/Service` directory and paste that inside. You can check this out if you want to,
but basically what it's doing is taking the `Character` that won, the *enemy's*
level, and it's figuring out how much XP it should award to the character. Then, if
they're eligible to level up, it will level up that character.

Over in `XpEarnedObserver`, we can use that. Create a constructor so that we can
autowire in a `private readonly` (so we can be super trendy) `XpCalculator
$xpCalculator`. Then, down here, let's set the `$winner` to a variable -
`$fightResult->getWinner()`, and set the `$loser` to `$fightResult->getLoser()`.
Below, say `$this->xpCalculator->addXp()` and pass `$winner, $loser->getLevel()`.
Beautiful!

The subject *and* observer for `GameApplication.php` are *done*. The final step is to
instantiate the observer and make it *subscribe* to `GameApplication.php`. We're
going to do this manually inside of `GameCommand.php`. Go to
`/src/Command/GameCommand.php`, and find `execute()`, which is where we're currently
initializing all of the code inside of our application. In a few minutes, we'll see a
more *Symfony* way of connecting all of this. For right now, we'll say `$xpObserver =
new XpEarnedObserver()`. We'll pass that a `new XpCalculator()` service so it's
happy. Then, we can say `$this->game` (to use the game property)
`->subscribe($xpObserver)`. So we're *subscribing* the observer before we actually
run our application down here.

And we're ready! But, just to make it a bit more obvious if this is working, head
back to `Character` and add *one more* function here called `getXp()`, which will
return `int`, and that will `return $this->xp`. This will allow us inside of
`GameCommand.php`. If you scroll down a little bit to `printResults()`... here we
go... we'll add a couple of new things in here like `$io->writeIn('XP: ' .
$player->getXp())`, and we'll do the same thing for `Final Level`, or the level they
are after they finish their battle, `$player->getLevel()`.

All right, let's try this out! Spin over, run

```terminal
./bin/console app:game:play
```

and let's play as the `fighter`, because that's still one of the toughest characters.
And... awesome! Because we won, we received 30 XP. We're *still* Level 1, so let's
fight a few more times. Aw... we lost, so no XP there. Now we have 60 XP... 90 XP...
woo! We leveled up! It says `Final Level: 2`. It's working! What's great about this
is that `GameApplication.php` doesn't need to know or care about the XP and the
leveling up logic. It just notifies its subscribers and they can do whatever they
want.

Next, let's see how we could wire all of this up using Symfony's *container*. We'll
also talk about the *benefits* of this pattern and what parts of SOLID it helps with.
