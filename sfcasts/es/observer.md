# El patrón del observador

Ha llegado el momento del patrón número tres: el patrón del observador. Aquí está la definición técnica:

## La definición

> El patrón observador define una dependencia de uno a muchos entre los objetos, de modo que
> que cuando un objeto cambia de estado, todos sus dependientes son notificados
> y se actualizan automáticamente.

Vale, no está mal, pero probemos mi versión:

> El patrón observador permite que un grupo de objetos sea notificado por un
> objeto central cuando ocurre algo.

Esta es la clásica situación en la que escribes un código que necesita ser llamado cada vez que ocurre algo. Y en realidad hay dos estrategias para resolver esto: el patrón observador y el patrón pub-sub. Hablaremos de ambos. Pero primero: el patrón observador.

## Anatomía del observador

Hay dos tipos de clases diferentes para crear este patrón. La primera se llama "sujeto". Es el objeto central que hará algún trabajo y notificará a otros objetos antes o después de ese trabajo. Esos otros objetos son el segundo tipo, y se llaman "observadores".

Esto es bastante sencillo. Cada observador le dice al sujeto que quiere ser notificado. Después, el sujeto hace un bucle sobre todos los observadores y los "notifica"... lo que significa que llama a un método en ellos.

## El reto de la vida real

De vuelta a nuestra aplicación, vamos a hacer nuestro juego más interesante introduciendo niveles a los personajes. Cada vez que ganes un combate, tu personaje ganará algo de XP o "puntos de experiencia". Cuando hayas ganado suficientes puntos, el personaje "subirá de nivel", lo que significa que sus estadísticas básicas, como `$maxhealth` y `$baseDamage`, aumentarán.

Para escribir esta nueva funcionalidad, podríamos poner el código justo aquí dentro de`GameApplication` después de que termine el combate. Así que... quizás aquí abajo, en`finishFightResult()`, haríamos el cálculo de XP y veríamos si el personaje puede subir de nivel:

[[[ code('ca802bb003') ]]]

Pero, para organizar mejor nuestro código, quiero poner esta nueva lógica en otro lugar y utilizar el patrón de observador para conectar las cosas. `GameApplication` será el sujeto, lo que significa que será responsable de notificar a cualquier observador cuando termine un combate.

Otra razón, más allá de la organización del código, por la que alguien podría elegir el patrón del observador es si `GameApplication` viviera en una biblioteca de un proveedor externo y esa biblioteca del proveedor quisiera darnos a nosotros -el usuario de la biblioteca- alguna forma de ejecutar el código después de que termine una batalla... ya que no podríamos permitirnos el lujo de hackear el código en `GameApplication`.

## Crear la interfaz del observador

Bien, el primer paso de este patrón es crear una interfaz que implementen todos los observadores. Para organizarnos, crearé un directorio `Observer/`. Dentro, añade una nueva clase PHP, asegúrate de que se selecciona "Interfaz" y llámala, qué tal,`GameObserverInterface`... ya que estas clases estarán "observando" algo relacionado con cada juego. `FightObserverInterface` también habría sido un buen nombre:

[[[ code('d1df415b49') ]]]

Dentro sólo necesitamos un método `public`. Podemos llamarlo como queramos: ¿qué tal`onFightFinished()`:

[[[ code('7b81b7cff2') ]]]

¿Por qué necesitamos esta interfaz? Porque, dentro de un minuto, vamos a escribir un código que haga un bucle sobre todos los observadores dentro de `GameApplication` y llame a un método sobre ellos. Así que... necesitamos una forma de garantizar que cada observador tenga un método, como`onFightFinished()`. Y podemos pasar a `onFightFinished()` los argumentos que queramos. Vamos a pasarle un argumento `FightResult` porque, si quiero ejecutar algún código después de que termine un combate, probablemente será útil conocer el resultado de ese combate. También añadiré un tipo de retorno `void`:

[[[ code('5d135d0fb7') ]]]

## Añadir el código de suscripción

Bien, segundo paso: Necesitamos una forma de que cada observador se suscriba para ser notificado en`GameApplication`. Para ello, crea un `public function` llamado, qué tal,`subscribe()`. Puedes llamarlo como quieras. Esto va a aceptar cualquier`GameObserverInterface`, lo llamaré `$observer` y devolverá `void`. Completaré la lógica en un momento:

[[[ code('eaa486a415') ]]]

La segunda parte, que es opcional, es añadir una forma de darse de baja de los cambios. Copia todo lo que acabamos de hacer... pega... y cambia esto por`unsubscribe()`:

[[[ code('1b5bc1b59a') ]]]

¡Perfecto!

En la parte superior de la clase, crea una nueva propiedad array que va a contener todos los observadores. Digamos `private array $observers = []` y luego, para ayudar a mi editor, añadiré algo de documentación: `@var GameObserverInterface[]`:

[[[ code('5c6ef4af5d') ]]]

De vuelta a `subscribe()`, rellena esto. Añadiré una comprobación de unicidad diciendo `if (!in_array($observer, $this->observers, true))`, y luego`$this->observers[] = $observer`:

[[[ code('153f6cc498') ]]]

Haz algo similar en `unsubscribe()`. Di`$key = array_search($observer, $this->observers)` y luego `if ($key !== false)` - lo que significa que hemos encontrado ese observador - `unset($this->observers[$key])`:

[[[ code('b90ac73090') ]]]

## Notificar a los observadores

Por último, estamos preparados para notificar a los observadores. Justo después de que termine el combate, se llama a`finishFightResult()`. Entonces, aquí mismo, diré `$this->notify($fightResult)`:

[[[ code('4f1b1cd9b9') ]]]

No necesitamos hacer esto... pero voy a aislar la lógica de la notificación a los observadores a un nuevo `private function` aquí abajo llamado `notify()`. Aceptará el argumento `FightResult $fightResult` y devolverá `void`. Luego `foreach` sobre`$this->observers as $observer`. Y como sabemos que todas esas son instancias de`GameObserverInterface`, podemos llamar a `$observer->onFightFinished()`y pasarle `$fightResult`:

[[[ code('9c8d0896d2') ]]]

Y... ¡el tema - `GameApplication` - está hecho! Por cierto, a veces el código que notifica a los observadores -así que `notify()` en nuestro caso- vive en un método `public`y está destinado a ser llamado por alguien ajeno a esta clase. Eso es sólo una variación del patrón. Como con muchos de los pequeños detalles de estos patrones, puedes hacer lo que te parezca mejor. Yo te muestro la forma en que me gusta hacer las cosas.

A continuación: implementemos una clase observadora, escribamos la lógica de subida de nivel y enganchémosla a nuestro sistema.
