# Undoing Action Commands

There we were... in the middle of a *fierce* battle and... uh, what's that? We *lost*? *No way*! Our opponent got *super* lucky. Surely there's a way to undo that operation and try again, right? There *is* - with the *command pattern*. Our commands only need to remember some *state* about the things that need to be reverted. In our case, we want to undo the last turn's actions if the player lost, so they get a second chance to play better and win the battle. Let's do this!

The first thing we need to do is add a new method to `ActionCommandInterface`. Open that up and, below `execute()`, write `public function undo();` with *no* arguments.

Next, we need to *implement* it in all of our commands. Let's start with `AttackCommand.php`. Open that and, up here at the top, we can see that PHP Storm is already mad at us because it's missing the `undo()` method. To implement that, click on the interface and press "Alt" + "Enter". Select "Add method stubs", and since the `undo()` method is already selected, we can just press "Enter". At the bottom, we can see that our method was added. We can leave our "TODO" comment here for now because we still need to figure out what data the command needs to remember.

Hold "command" and click on the `attack()` method to go to the definition. Here, we can see that it consumes some stamina and calculates the attack's damage. That means we need to remember the player's *stamina* prior to an attack as well as the damage dealt. *But*, down here, you can see that we're sending the opposing player damage with `receiveAttack()`. So the value we're *really* after is the `$damageDealt` variable. Let's go to the top of this class and add those properties: `private int $damageDealt` and `private int $stamina`. Inside `execute()`, before the player attacks, write `$this->stamina = $this->player->getStamina()`, and down here, write `$this->damageDealt = $damageDealt`. Perfect!

When we undo an atack, we also need to restore the *opponent's* health. To do that, say `$this->opponent->setHealth($this->opponent->getCurrentHealth() + $this->damageDealt)`. And to restore the player's *stamina*, write `$this->player->setStamina($this->stamina)`. Oh, and we almost forgot to revert the `fightResultSet`! We don't want to report incorrect data. To revert the damage *dealt*, write `$this->fightResultSet->of($this->player)->removeDamageDealt($this->damageDealt)`. And to revert damage received by the *opponent*, write `$this->fightResultSet->of($this->opponent)->removeDamageReceived($this->damageDealt);`. Great! This class is *ready*. Let's keep going!

Open `HealCommand.php`, and we'll do the same thing here - add the `undo()` method, click on the name of the interface, and press "Alt" + "Enter". Now we can decide what data we need to remember. This command is simpler - it just changes the player's health and stamina - so let's store their starting values. At the top of the class, add both properties: `private int $currentHealth` and `private int $stamina`. Next, inside `execute()`, before healing the player, let's save their current health with `$this->currentHealth = $this->player->getCurrentHealth()`. We'll do the same for stamina - `$this->stamina = $this->player->getStamina()`.

Now we can implement the `undo()` method. We only need to revert those two player properties, so write `$this->player->setHealth($this->currentHealth)` and `$this->player->setStamina($this->stamina)`. Another command *done*! Nice!

Finally, open `SurrenderCommand.php` and do this one more time - add the `undo()` method, and hit "Alt" + "Enter". We can leave this method empty because it would be silly to revert a surrender action.

All right! It's time to ask the player if they want to revert the last action in case of defeat. I'll close a few files and go back to `GameApplication.php`. Find the AI's turn, and inside this `if()` where we check if the player died, write `$undoChoice = GameApplication::$printer->confirm()`. The question will read:

`You've lost! Do you want to undo your last turn?`.

If the answer is "no", we need to end the battle and exit, so I'll move these two lines inside the `if`. If the answer is "yes", we *undo* actions from the last turn, which means that we need to call `undo()` on the command objects. But we can't just undo these commands in any order. We need to undo them in the reverse order that they were executed... or weird things could happen. This is basically a "FILO" stack - "First In, Last Out".

Anyway, let's undo the AI's attack first with `$aiAttackCommand->undo()`. *Then* we'll undo the player's action - `$playerAction->undo()`. Awesome! Now we can give this a try. Spin over to your terminal and run:

```terminal
php bin/console app:game:play
```

We'll be an archer this time, and we'll try to lose the battle. Maybe we start off by healing, and then we'll attack until we *hopefully* lose. And... yes! We lost! Now it's asking if we would like to undo our last turn. Say "yes" or press "Enter" and... the game continued! That's a good sign! But to be absolutely certain that this is working the way it should, let's compare the amount of health we have now to the amount we had one turn ago.

Okay, right now we have "9/50" and the AI has "50/60". One turn ago, which was round two, we had "9/50" and the AI had "50/60". The AI *and* our character have the same amount health in both rounds, so this is working as expected! Nice! And if we say "no" this time just to see if the battle concludes... yes! The battle ended and we lost!

## Queuing Command Actions

Thanks to the Command Pattern, we were able to revert actions with *ease*. But that's not the only thing the Command Pattern can do for us. We can also use it to put our actions in a *queue* and execute them whenever we want.

Suppose we want to *replay* our battles and watch everything play out again. We *could* store *all* of the commands that happened in a battle somewhere, like a list, database, or any other storage mechanism. *Then*, we grab the list and execute them one by one. While that would be super fun to work on, we have more patterns to cover! But before we move onto the next one, let's figure out where and *how* Symfony leverages the Command Pattern. That's *next*.
