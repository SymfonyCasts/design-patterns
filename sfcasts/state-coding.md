# Handling Difficulties with the State Pattern

Alright! Let's start by reviewing how *difficulty levels* are handled in our application.
Open up `GameCommand`, inside the `play()` method find where we check the outcome of the
match. If the player wins we call `victory()` on the game object, otherwise we call `defeat()`.
Let's see what does the `victory()` method do. Hold "Command" and click on it.
Oh! It's just a shortcut for calling `victory()`on this `difficultyContext` property,
which is an instance of the `GameDifficultyContext` class, and it's on charge
of managing the difficulty levels. Good, let's keep digging. Hold "Command" and click
on this `victory()` method. Aha! We finally see some real code. Here's a `switch-case`
statement for increasing the difficulty level based on the current level and some conditions.
For example, to move from level 1 to level 2, the player must be at least level 2
or have won 2 fights, then we make the game harder by increasing some enemy stats,
but to keep it fair and fun! We also increase the player's XP bonus.
Level 2 is quite similar but the conditions are just harder to meet. And, for level 3
we have some randomness where we roll a 20 sided dice and depending on the outcome
some bonuses may apply. Cool! Ok, below that it's the `defeat()` method, which is the opposite
of `victory()`. If the player loses there's a chance to decrease the difficulty level,
and if so it restores the bonus settings.

Okay! The plan is to refactor this code so it leverages the *State pattern*. The first step
is to move the logic of each level or "state" into its own class. Let's start by
creating an interface for our states. Inside `src/` add a new folder named `DifficultyState`,
and inside add a PHP file, call it `DifficultyStateInterface`. The state's interface
must have a method for each possible event, in our case that would be, `victory()` and `defeat()`.
So, write `public function victory()` and for the arguments write, `GameDifficultyContext $difficultyContext`,
then `Character $player`, and finally `FightResult $fightResult`. The `defeat()` method
has the same arguments so I'll duplicate this line and rename it to `defeat`.
By the way the `$player` and `$fightResult` arguments could've been wrapped
in the `DifficultyContext`.

Ok! We're ready to add some *states*. Create a new PHP class inside the same folder,
and instead of using numbers to represent levels, we'll make it better by naming
levels as "easy", "medium", etc. So, name it `EasyState`, then make it implement the interface
and hold "option" + "enter" to add the methods. Perfect! Let's start with the `victory()`
method. Go back to `GameDifficultyContext` and copy the first case of the `victory()` method.
Then paste it into `EasyState` and change the `$this` references by `$difficultyContext`.
The `level` property is not going to be an `int` anymore, it'll hold a reference to
the current state object, so rename it to `difficultyState` and set it to the
next state `new MediumState()`. We'll create this class in a moment. Now add the
property, hold "Options" + "Enter" and change its type-hint to `DifficultyStateInterface`.
Good! Let's handle the `defeat` function now, scroll down and... there's nothing to do
when there's a defeat in the `EasyState`, it is the lowest level. Let's keep going
and refactor level 2. Add another PHP class and name it `MediumState`. You know the drill,
implement the interface, add the methods by holding "Option" + "Enter", and copy the
code from level 2 in `GameDifficultyContext`. Then, paste it into the `MediumState` class
and fix the code. This state will move us into the `HardState`, so
set `difficultyState` to `new HardState()`. Good! Now copy the code for the `defeat()` method,
fix the code, and it will move us back to the `EasyState`, so set `difficultyState`
to `new EasyState()`. Lastly, the `HardState`, add another PHP class, name it `HardState`
and repeat the process... just don't forget to change the `difficultyState` to `new MediumState()`.
(we can speed up the video here)

Phew! We're almost there, we only need to initialize the starting level. Add a
constructor to the `GameDifficultyContext` and set the `difficultyState` to `new EasyState()`.
Before we give this a try I'm gonna cheat a little bit to trigger the `victory()` method.
In `GameApplication`, I'll set the player's health to 100 at the start of each round
so I never lose - after all I'm the game master! Ok, let's see if it works.

Spin over to your terminal and run:

```php
php bin/console app:game:play
```

Let's play two battles and see what happens... Yes! There's our message, the difficulty
level increased to medium! So, this is working nicely, but I'm guessing you did not like how
I instantiated those state objects. What if they had some dependencies? Or if they were
expensive to create? Here are a few recommendations: if the states are simple to create,
do what we did, it will keep your code simple. 
If they have dependencies and are expensive to create, leverage the `AutowireLocator`
attribute to inject them lazily and reuse the same instance.
(how does Ryan manage links? https://symfony.com/doc/current/service_container/service_subscribers_locators.html#service-locator_autowire-locator)
If you need a fresh state object every time, use a *factory* to create them and
inject into the `GameDifficultyContext`. We'll talk about factories soon.

Ok! With this new and fancy design, adding new levels to the game would be super easy.
Suppose that we want to add a "hardest" level, we would just need to add a new state class,
and make a tiny change to the `HardState`, it would need to move us to the "hardest" level
in the `victory()` method, and we would be done!

Ok! Coming next: the *state pattern* in the real world!
