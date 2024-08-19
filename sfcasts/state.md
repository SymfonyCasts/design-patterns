# The State Pattern

It's now turn to talk about the *State pattern*, this is how I like to define it:

State is a way to organize your code so that an object can change its *behavior*
when its internal state changes. It helps you represent different states as separate classes
and allows the object to switch between these states seamlessly.

Long story short, state helps you replace *chained if-elseif* statements into a set of classes.

## Pattern Anatomy

The State pattern consists of the following elements:

A *Context* class that represents the object whose behavior changes based on its internal state.
The context class has a reference to the current state object.

A common interface for all concrete state classes. It declares methods that represent 
the actions that can be taken in each state.

And lastly, concrete states. Each class represent a state of the Context.

## Objects Collaboration

This is how those elements collaborate together:

The context delegates state-specific actions to the current state object, and it may
pass itself as an argument.

Either context or the concrete state can decide which state succeeds another and can
be responsible for instantiating state objects.

## Imaginary Example

Suppose we have this `publishPost()`function that depending on the *status* of the article
it will do different things. If the article is a *draft* it will change the status to *moderation*,
if it's already in *moderation* and the user is an admin, it will change the status
to *published* and send a tweet.

```php
// the "publishPost()" method represents the event "publish"
public function publishPost(Article $article) {
    if ($article->getStatus() === 'draft') {
        $article->setStatus('moderation');
        $this->notifyModerator();
    } elseif ($article->getStatus() === 'moderation') {
        if ($this->getCurrentUser()->isAdmin()) {
            $article->setStatus('published');
        } 
        $this->sendTweet($article);
    } elseif ($article->getStatus() === 'published') {
        throw new AlreadyPublishedException();
    }
}
```

If apply the state pattern to this function, we'd need to create a class for each state,
in other words, we would end up with a `DraftState`, `ModerationState`, and `PublishedState` classes.
(here's where I'd like to show an animation of the if-elseif-else statements becoming classes)
Each of them would have a `publish()` method that encapsulates the logic *specific* for that state.

This is how the `DraftState` class would look like:

```php
class DraftState implements StateInterface {
    public function publish(Article $article) {
        $article->setStatus('moderation');
        $this->notifyModerator();
    }
}
```

Ok! It's time to get some fun and put this into practice. We'll refactor how our
game application handles the difficulty levels so that it uses the State pattern.
That's next!
