# Handling Difficulties with the State Pattern

How are *difficulty levels* handled in our application? Open up
`GameCommand` and, inside the `play()` method, find the part where we check
the outcome of the match. If the player *wins*, we call `victory()` on the game
object, *otherwise* we call `defeat()`. Let's check out the `victory()` method.
Hold "Command", click, and... oh! It's just a shortcut for calling `victory()`
on this `difficultyContext` property. That's an instance of the
`GameDifficultyContext` class, and it's in charge of managing the *difficulty
levels*.

Hold "Command" and click on the `victory()` method again and... *aha* - some
*real* code. Here's a `switch-case` statement for increasing the *difficulty
level* based on the *current* level, as well as some conditions. For example, to
move from *difficulty level* 1 to 2, the *player level* must be *at least* 2 or they have to have won
two fights. Then it makes the game harder by increasing some of the *enemy's*
stats. *But*, to keep it fair and fun, it also increases the player's XP bonus.
Difficulty level 2 is pretty similar, but the conditions are just harder to meet. And for
level 3, we have some randomness where we roll a 20-sided die and, depending on
the outcome, may apply some bonuses. *Sweet*! Below *that*, we have the
`defeat()` method, which is the *opposite* of `victory()`. If the player
*loses*, there's a chance the difficulty level will decrease, and *if so*, it
restores the bonus settings.

Okay! The *plan* is to refactor this code so it leverages the *State* pattern.
The *first* step is to move the logic of each level, or "state", into its own
class. Let's start by creating an interface for our states. Inside `src/`, add a
new folder called `DifficultyState`, and inside *that*, add a new PHP file -
`DifficultyStateInterface`. The state's interface *must* have a method for each
possible event. In our case, that would be `victory()` and `defeat()`, so write
`public function victory()`. For the arguments, write
`GameDifficultyContext $difficultyContext`, `Character $player`, and
`FightResult $fightResult`. The `defeat()` method has the same arguments, so we
can duplicate this line and rename it to "defeat". The `$player` and
`$fightResult` arguments *could have* been wrapped in the `DifficultyContext`,
but we'll leave it like this.

Alright, we're ready to add some *states*. Create a new PHP class inside the
same folder, and instead of using *numbers* to represent difficulty levels,
we're going to *name* them - "Easy", "Medium", etc. So let's name this
`EasyState`, make it implement the interface, and hold "option" + "enter" to add
the methods. Perfect! I'll close a few things, then, back in
`GameDifficultyContext`, find the first case in the `victory()` method and
copy it. Then, in `EasyState`, paste that and replace `$this` with
`$difficultyContext`. We'll also change this `level` property to reference the
current state object, so rename it to `difficultyState` and set it to the *next*
state - `new MediumState()`. We haven't created this class yet, but we will in a
moment. To add the property, hold "option" + "enter" and change its type hint
to `DifficultyStateInterface`. Awesome! And if we take a quick look at the
`defeat` function, scroll down and... okay. There's nothing to do when a player
is defeated in the `EasyState`, since that's the lowest level, so we can leave
this as it is.

*Now* let's refactor level 2. Add another PHP class and name it `MediumState`.
This next part should look familiar! We'll implement the interface... add the
methods by holding "option" + "enter"... copy the code from level 2 in
`GameDifficultyContext`... paste it into the `MediumState` class, and fix
the code. *This* state will move us into the "hard" difficulty, so set
`difficultyState` to `new HardState()`. That doesn't exist yet either, but we're
getting to that. Now we can copy the code for the `defeat()` method, fix it, and
*that* will move us back to the `EasyState`, so set `difficultyState` to
`new EasyState()`. *Finally*, we'll create our last difficulty state. Add a new
PHP class called `HardState`, and then we'll repeat the process one last time.
And don't forget to change the `difficultyState` to `new MediumState()`!

Phew... we're almost there! Now we just need to initialize the starting level.
Add a constructor to `GameDifficultyContext` and set the `difficultyState` to
`new EasyState()`. And, don't forget to update the `victory()` and `defeat()` methods
so they now call the `difficultyState` property. 

Ok, we're ready to give this a try, but before that, I'm going to cheat a little
bit to always trigger the `victory()` method. In `GameApplication`, let's set the
player's health to "100" at the start of each round so we never lose. After all,
I *am* the game master! Now let's see if that works.

Spin over to your terminal and run:

```terminal
php bin/console app:game:play
```

We'll play a few rounds until we win, and... *yes*! We did it! But we need to
win *two* battles before we can level up, so let's keep going. And... woohoo!
There's our message!

> Game difficulty level increased to Medium!

This is working nicely, *but* you may not be the biggest fan of how we
instantiated those state objects. What if they had some dependencies? Or what if
they were expensive to create? Great questions! If the states are simple to
create, do what we did, because it will keep your code simple. If they have
dependencies and are *expensive* to create, leverage the
[`AutowireLocator` attribute](https://bit.ly/sf-service-locator-attribute) to inject
them *lazily* and reuse the same instance. If you need a *fresh* state object every time,
use a *factory* to create them, and inject it into the `GameDifficultyContext`.
We'll talk more about factories soon.

Alright! Our new setup makes it super easy to add new levels to the game. If
we wanted to add a "hardest" level, we would just add a new state class and make
a tiny change to the `HardState` so it moves us up to the "hardest" level in the
`victory()` method. It's *that* simple!

Next: Let's take a look at the State pattern in the *real* world!
