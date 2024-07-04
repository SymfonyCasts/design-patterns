# Implementing More Actions

All right! We're ready to add more actions to our game and allow players to *choose* their actions.

*First*, we need to create an *interface* for our commands. To do that, inside the `ActionCommand` directory, let's create a new PHP file and call it `ActionCommandInterface`. Inside, we'll add a single method called `execute()` with no arguments. Interface *done*!

Next, let's open `AttackCommand` and make it implement `ActionCommandInterface`. The `execute()` method is *already* implemented here, so this is ready to go. *Nice*!

If you downloaded the course code, we can save some time by grabbing the rest of the actions we need in our `tutorial` directory at the root of our project. Copy the `HealCommand` and `SurrenderCommand` files into the `ActionCommand` directory.

Let's check those out. Inside `HealCommand`, we can see that it has a constructor that *only* cares about the player object. And in the `execute()` method, we have some code that calculates how much damage the player will heal, and then sets the player's health to the new amount (not exceeding their max health). Finally, it prints a message on the screen.

If we take a look at the `SurrenderCommand`, the constructor here is the same as the one in `HealCommand` - it only cares about the player object. And in the `execute()` method, I cheated a little bit because there's no proper way to end a battle, so I just set the player's health to `0`. Cool, right?

## Asking the Player to Choose an Action

All right! It's time to ask the player to choose an action! I'll close a few files first. Okay, head back to the `GameApplication`... and right before we define `$playerAction`, write `$actionChoice` and set it to `GameApplication::$printer->choice()`, where the question is `Your Turn`, and the choices are `Attack`, `Heal`, and `Surrender`. *Then*, we'll replace the `AttackCommand` instantiation with a `match` expression, but copy this first, because we'll need it in a moment. Now write `match ($actionChoice)`. Inside, the first case we want to add is `Attack`, and now... *paste*. For the second case, write `Heal` and set it to `new HealCommand($player)`. The third and final case is `Surrender`, and we'll set that to `new SurrenderCommand($player)`. *Perfect*!

Let's give this a try. Spin over to your terminal and run:

```terminal
php bin/console app:game:play
```

This time, I'll be a "mage archer", and... look at that! It's asking us what to do! Let's *attack* first, and... cool! We did `12` points of damage and *received* `10`, so our current health is `40` out of `50`. Let's try *healing* next. And... check it out! We healed `8` points, and the AI did `0` points of damage because we blocked its attack, so our current health is `48`. The "heal" action is working as expected! Lastly, let's try to *surrender*. Choose option `2` and... awesome! We surrendered and lost the match. Giving up isn't something we'd *normally* celebrate, but in this case, it means our "surrender" action is working like it's supposed to. 

High five your rubber duck because we've successfully made our game more interactive! *But*... wouldn't it be awesome if we could *undo* our last action if, say... the AI got a little *too* lucky? That's *next*!
