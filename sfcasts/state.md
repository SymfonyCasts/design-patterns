# The State Pattern

It's time to talk about the *State pattern*. Here's how I like to define it:

*State* is a way to organize your code so that an object can change its
*behavior* when its *internal state* changes. It helps you represent different
states as separate classes and allows the *object* to switch between these
states *seamlessly*.

How does it do that? Let's check it out!

## Pattern Anatomy

The State pattern consists of three elements:

-A *Context* class that represents the object whose behavior *changes* based on
its internal state, and has a reference to the *current* state object.

-A common interface for all concrete state classes. This declares methods that
represent actions that can be taken in each state.

-And lastly, *concrete* states, where each class represents a state of the
context.

## Objects Collaboration

These elements collaborate together in a couple of different ways. The context
can delegate state-specific actions to the current state object, and it may pass
*itself* as an argument. Context or concrete states can also decide which state
*succeeds* another and they can be responsible for *instantiating* state
objects. What does that look like? Let's see an example.

## Imaginary Example

Suppose we have a `publishPost()` function that will do different things based
on the *status* of an article. If the article is a *draft*, it will change the
status to "moderation" and notify the moderator. If it's *already* in moderation
and the user is an *admin*, it will change the status to "published" and send a
tweet.

```php
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

If we apply the State pattern to this function, we need to create a *class* for
each state: `DraftState`, `ModerationState`, and `PublishedState`. Each class
will have a `publish()` method that encapsulates the specific logic for that
state, so the `DraftState` class would look something like this:

```php
class DraftState implements StateInterface {
    public function publish(Article $article) {
        $article->setStatus('moderation');
        $this->notifyModerator();
    }
}
```

Okay, it's time to have some fun and put this into practice! Let's refactor how
our game application handles *difficulty levels* using the State pattern.
That's *next*!
