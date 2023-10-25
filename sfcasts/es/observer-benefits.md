# Observador dentro de Symfony + Beneficios

¡Hemos implementado el Patrón Observador! El `GameApplication` es nuestro sujeto, que notifica a todos los observadores... y de momento tenemos uno:`XpEarnedObserver`. Dentro de `GameCommand`, conectamos todo esto instanciando manualmente el observador y `XpCalculator`... y luego llamando a`$this->game->subscribe()`:

[[[ code('b81246b495') ]]]

Pero... eso no es muy propio de Symfony.

Tanto `XpEarnedObserver` como `XpCalculator` son servicios. Así que normalmente los autocablearíamos desde el contenedor, no los instanciaríamos manualmente. Estamos autocableando`GameApplication`... pero nuestra situación general no es del todo correcta. En un mundo perfecto, en el momento en que Symfony nos da este `GameApplication`, el contenedor de Symfony ya habría conectado todos sus observadores para que estuviera listo para ser utilizado inmediatamente. ¿Cómo podemos hacerlo? Hagámoslo primero de forma sencilla.

## Especificar manualmente los servicios

Elimina todo el código manual dentro de `GameCommand`:

[[[ code('b393cb46bc') ]]]

Vamos a recrear esta misma configuración... pero dentro de `services.yaml`. Ábrelo... y en la parte inferior, tenemos que modificar el servicio `App\GameApplication`. Pero no necesitamos configurar ningún argumento. En este caso, necesitamos configurar algunos `calls`. Aquí, básicamente le estoy diciendo a Symfony:

> ¡Oye! Después de instanciar `GameApplication`, llama al método `subscribe()` en
> él y pasa, como argumento, el servicio `@App\Observer\XpEarnedObserver`.

[[[ code('9680bb773e') ]]]

Así que cuando autocableemos `GameApplication`, Symfony irá a coger el servicio `XpEarnedObserver`y ese servicio, por supuesto, recibirá `XpCalculator` autocableado en él. Esto es un autocableado bastante normal: la única parte especial es que Symfony llamará ahora al método `subscribe()` en `GameApplication` antes de pasar ese objeto a `GameCommand`.

En otras palabras, esto debería funcionar. ¡Vamos a probarlo! Ejecuta:

```terminal
./bin/console app:game:play
```

De momento no hay errores y... oh. Hemos perdido. Mala suerte. ¡Volvamos a intentarlo! Hemos ganado y hemos recibido 30 XP. ¡Está funcionando!

## Configurar la autoconfiguración

El inconveniente de esta solución es que cada vez que añadamos un nuevo observador, tendremos que ir a `services.yaml` y cablearlo manualmente. Qué indigno...

¿Podríamos suscribir automáticamente todos los servicios que implementen`GameObserverInterface`? ¡Pues sí! ¡Y qué excelente idea! Podemos hacerlo en dos pasos.

Primero, abre `src/Kernel.php`. No es un archivo con el que trabajemos mucho, pero estamos a punto de hacer algunas cosas más profundas con el contenedor y, por tanto, es exactamente donde queremos estar. Ve a Generar Código o a `Command`+`O` y selecciona "Anular Métodos". Vamos a anular uno llamado `build()`:

[[[ code('9dbbdee6d4') ]]]

¡Perfecto! El método padre está vacío, así que no necesitamos llamarlo. En su lugar, di `$container->registerForAutoconfiguration()`, pásale`GameObserverInterface::class`, y luego di `->addTag()`. Voy a inventar una nueva etiqueta aquí llamada `game.observer`:

[[[ code('b21b3d58b0') ]]]

Esto probablemente no es algo que veas muy a menudo (o nunca) en tu código, pero es muy común en los bundles de terceros. Esto dice que cualquier servicio que implemente`GameObserverInterface` debe recibir automáticamente esta etiqueta `game.observer`... suponiendo que ese servicio tenga habilitado `autoconfigure`, cosa que hacen todos nuestros servicios.

Ese nombre de etiqueta podría ser cualquier cadena... y no hace nada por el momento: es sólo una cadena aleatoria que ahora está unida a nuestro servicio.

Pero, al menos, deberíamos poder verlo. Gira y ejecuta:

```terminal
./bin/console debug:container xpearnedobserver
```

¡Ha encontrado nuestro servicio! Y compruébalo: `Tags` - `game.observer`.

Bien, ahora que nuestro servicio tiene una etiqueta, vamos a escribir un poco más de código que llame automáticamente al método `subscribe` en `GameApplication` para cada servicio con esa etiqueta. Esto también va a ir en `Kernel`, pero en un método diferente. En este caso, vamos a implementar algo llamado "pase del compilador".

Añade una nueva interfaz llamada `CompilerPassInterface`. A continuación, vuelve a "Generar código", "Implementar métodos", y selecciona `process()`:

[[[ code('c2111a0bce') ]]]

Los pases de compilador son un poco más avanzados, ¡pero súper chulos! Es un trozo de código que se ejecuta al final del contenedor y de los servicios que se están construyendo... y puedes hacer lo que quieras dentro.

¡Compruébalo! Di `$definition = $container->findDefinition(GameApplication::class)`:

[[[ code('edee7fe331') ]]]

No, esto no devuelve el objeto `GameApplication`. Devuelve un objeto `Definition`que sabe todo sobre cómo instanciar un `GameApplication`, como su clase, los argumentos del constructor y las llamadas que pueda tener.

A continuación, di `$taggedObservers = $container->findTaggedServiceIds('game.observer')`:

[[[ code('d7559f373c') ]]]

Esto devolverá un array de todos los servicios que tengan la etiqueta `game.observer`. Entonces podemos hacer un bucle sobre ellos con `foreach ($taggedObservers as $id => $tags)`. El`$id` es el identificador del servicio... y el `$tags` es un array porque técnicamente puedes poner la misma etiqueta en un servicio varias veces... pero eso no nos importa:

[[[ code('e78648b174') ]]]

Ahora digamos que `$definition->addMethodCall()`, que es la versión PHP de `calls` en YAML. Pásale el método `subscribe` y, como argumentos, un `new Reference()` (el de `DependencyInjection`), con `id`:

[[[ code('5129f22d15') ]]]

Esta es una forma elegante de decir que queremos que se llame al método `subscribe()` en `GameApplication`... y que se le pase el servicio que contiene la etiqueta`game.observer`.

El resultado final es el mismo que teníamos antes en `services.yaml`... sólo que más dinámico y mejor para impresionar a tus amigos programadores. Así pues, elimina todo el código YAML que hemos añadido:

[[[ code('48a9a1bae1') ]]]

Si volvemos a probar nuestro juego...

```terminal
./bin/console app:game:play
```

¡No hay errores! Y... ¡sí! ¡Sigue funcionando! Si más adelante necesitamos añadir otro observador, sólo tenemos que crear una clase, hacer que implemente `GameObserverInterface` y... ¡listo! Se suscribirá automáticamente a `GameApplication`.

## Patrón observador en la naturaleza

Este es el patrón del observador. Su aspecto puede variar, con diferentes nombres de métodos para la suscripción. A veces, los observadores se pasan a través del constructor Pero la idea es siempre la misma: un objeto central hace un bucle y llama a un método en una colección de otros objetos cuando ocurre algo.

¿Dónde vemos esto en la naturaleza? Aparece en muchos sitios, pero aquí tienes un ejemplo. En la página GitHub de Symfony, voy a pulsar "T" y buscar una clase llamada `LocaleSwitcher`. Si necesitas hacer algo en tu aplicación cada vez que cambie la configuración regional, puedes registrar tu código en `LocaleSwitcher` y te llamará. En este caso, los observadores se pasan a través del constructor. Y luego puedes ver aquí abajo, después de que se establezca la configuración regional, hace un bucle sobre todos ellos y llama a `setLocale()`. Así que `LocaleSwitcher` es el sujeto, y estos son los observadores.

¿Cómo se registra un observador? Como es lógico, creando una clase que implemente `LocaleAwareInterface`. Gracias a la autoconfiguración, Symfony etiquetará automáticamente tu servicio con `kernel.locale_aware`. ¡Sí, utiliza el mismo mecanismo para enganchar todo esto que acabamos de utilizar!

## Ventajas del patrón observador

En realidad, las ventajas del patrón observador se describen mejor si se observan los principios SOLID. Este patrón ayuda al patrón de Responsabilidad Única porque puedes encapsular (o aislar) el código en clases más pequeñas. En lugar de poner todo en `GameApplication`, como toda nuestra lógica XP aquí, pudimos aislar las cosas en `XpEarnedObserver` y mantener ambas clases más centradas. Este patrón también ayuda con el principio de abierto-cerrado, porque ahora podemos ampliar el comportamiento de `GameApplication` sin modificar su código.

El patrón observador también sigue el Principio de Inversión de la Dependencia o DIP, que es uno de los principios más complicados, en mi opinión. En cualquier caso, el DIP es feliz porque la clase de alto nivel - `GameApplication` - acepta una interfaz -`GameObserverInterface` - y esa interfaz fue diseñada con el propósito de que`GameApplication` la utilice. Desde la perspectiva de GameApplication, esta interfaz representa algo que quiere "observar" lo que ocurre cuando algo ocurre dentro del juego. A saber, el final del combate. Por tanto, `GameObserverInterface`es un buen nombre.

Pero, si le hubiéramos puesto un nombre basado en la forma en que los observadores utilizarán la interfaz, eso habría entristecido al DIP. Por ejemplo, si lo hubiéramos llamado`XpChangerInterface` y el método `timeToChangeTheXp`, eso sería una violación del Principio de Inversión de la Dependencia. Si esto te resulta confuso y quieres saber más, consulta nuestro tutorial sobre SOLID.

A continuación, pasemos rápidamente al patrón hermano del observador: Pub/sub.
