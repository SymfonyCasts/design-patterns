# El patrón Estado

Ha llegado el momento de hablar del patrón Estado. Así es como me gusta definirlo:

El estado es una forma de organizar tu código para que un objeto pueda cambiar su comportamiento cuando cambie su estado interno. Te ayuda a representar distintos estados como clases separadas y permite que el objeto cambie de un estado a otro sin problemas.

¿Cómo lo hace? ¡Veámoslo!

## Anatomía del patrón

El patrón Estado consta de tres elementos

-Una clase Contexto que representa al objeto cuyo comportamiento cambia en función de su estado interno, y tiene una referencia al objeto de estado actual.

-Una interfaz común para todas las clases de estado concretas. En ella se declaran los métodos que representan las acciones que se pueden realizar en cada estado.

-Y por último, estados concretos, donde cada clase representa un estado del contexto.

## Colaboración de los objetos

Estos elementos colaboran entre sí de un par de formas distintas. El contexto puede delegar acciones específicas del estado en el objeto estado actual, y puede pasarse a sí mismo como argumento. El contexto o los estados concretos también pueden decidir qué estado sucede a otro y pueden encargarse de instanciar objetos estado. ¿Qué aspecto tiene esto? Veamos un ejemplo.

## Ejemplo imaginario

Supongamos que tenemos una función `publishPost()` que hará cosas diferentes en función del estado de un artículo. Si el artículo es un borrador, cambiará el estado a "moderación" y notificará al moderador. Si ya está en moderación y el usuario es un administrador, cambiará el estado a "publicado" y enviará un tuit.

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

Si aplicamos el patrón Estado a esta función, tendremos que crear una clase para cada estado: `DraftState`, `ModerationState`, y `PublishedState`. Cada clase tendrá un método `publish()` que encapsulará la lógica específica para ese estado, por lo que la clase `DraftState` tendría un aspecto similar al siguiente:

```php
class DraftState implements StateInterface {
    public function publish(Article $article) {
        $article->setStatus('moderation');
        $this->notifyModerator();
    }
}
```

Bien, ¡es hora de divertirse y poner esto en práctica! Vamos a refactorizar la forma en que nuestra aplicación de juego gestiona los niveles de dificultad utilizando el patrón Estado. ¡Eso a continuación!
