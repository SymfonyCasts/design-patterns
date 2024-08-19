# Configurar el CdR con Symfony

Esta es una aplicación Symfony, así que vamos a aprovecharnos de ello y utilizar la función de autoconfiguración para configurar nuestra cadena. Incluso hay un atributo `Autoconfigure` superútil que podemos utilizar en nuestras clases manejadoras.

Empezaremos abriendo `CasinoHandler` y, encima del nombre de la clase, añadiremos el atributo `#[Autoconfigure()]`. Cuando se instancie esta clase, queremos llamar a `setNext()` y pasarle otro objeto manejador. Para ello, utilizaremos la opción `calls`, así que dentro escribe `calls: [['setNext' => ['@'.LevelHandler::class]]]`. Ten cuidado con esta sintaxis de matriz anidada.

[[[ code('d878e0e315') ]]]

Ahora, cuando Symfony instancie esta clase, llamará a `setNext()` y le pasará un objeto `LevelHandler`.

Por cierto, si te estás preguntando a qué viene este prefijo de símbolo `@`, ¡buen ojo! Esto le dice a Symfony que pase el servicio `LevelHandler`. Si no añadiéramos esto, pasaría la cadena de clase `LevelHandler`, que definitivamente no es lo que queremos.

Haremos lo mismo en la clase `LevelHandler`. Ábrela, escribe `#[Autoconfigure()]`, y luego `calls: [['setNext' => ['@'.OnFireHandler::class]]]`. Podemos dejar el `OnFireHandler` como está porque es el último manejador de la cadena ¡Perfecto!

[[[ code('94270e65bc') ]]]

Ahora que la cadena está configurada, podemos eliminar el código que la inicializa manualmente en `GameApplication`. Ábrelo... y borra todo lo que haya dentro de su constructor excepto esta línea. El último paso es configurar la propiedad `$xpBonusHandler`. Podemos añadirla al constructor escribiendo `private readonly XpBonusHandlerInterface $xpBonusHandler`. Encima, utiliza el atributo `#[Autowire]` y, dentro, escribe `service: CasinoHandler::class` porque es el primer manejador de nuestra cadena.

[[[ code('21aae2b95f') ]]]

Necesitamos el atributo `#[Autowire]` porque Symfony no sabrá cómo inyectar`XpBonusHandlerInterface` ya que hay múltiples clases que lo implementan.

¡Muy bien! ¡Vamos a intentarlo! Ve a tu terminal y ejecuta:

```terminal
php bin/console app:game:play
```

Ejecuta: El juego se está ejecutando. ¡Buena señal! Y si luchamos... ¡hemos perdido! Por el lado bueno, podemos ver nuestro mensaje informativo que nos dice que el `CasinoHandler` ha funcionado, y no hemos conseguido XP extra, así que todo funciona bien.

## Bonificación: Patrón de Objeto Nulo

¿Listo para un tema extra? Es hora de hablar del patrón Objeto Nulo. ¿Qué es el patrón Objeto Nulo? En pocas palabras, es una forma inteligente de evitar las comprobaciones de `null`. En lugar de comprobar si una propiedad es `null`, como hemos hecho en el pasado, crearemos un "objeto nulo" que implemente la misma interfaz y no haga nada en sus métodos. ¿Qué significa esto? En pocas palabras, si un método devuelve un valor, devolverá lo más parecido posible a null. Por ejemplo, si devuelve un `array`, devolverá una matriz vacía. ¿Una cadena? Devuelve una cadena vacía. ¿Un `int`? Devuelve 0. Puede complicarse aún más, pero ya te haces una idea.

Así que ¡manos a la obra! De vuelta a nuestro código, busca ese `if` del que hemos estado hablando, que está dentro de cualquier manejador. Lo que tenemos que hacer es eliminar el `if` y llamar directamente al siguiente manejador. Muy fácil. Crea una nueva clase de manejador y llámala `NullHandler`. Haz que implemente el `XpBonusHandlerInterface` y mantén pulsadas las teclas "Opción" + "Intro" para implementar los métodos. Y ahora... ¡Que el manejador no haga nada! Bueno, lo más parecido a nada posible. El método `setNext()` no devuelve nada, así que podemos dejarlo vacío, pero el método `handle()` devuelve un `int`. Cuando encuentres un método que devuelva algo, siempre debes preguntarte cómo se está utilizando el valor. La respuesta te ayudará a identificar el valor más próximo a nulo que debes devolver. En nuestro caso, podemos devolver `0` y estará bien porque representa la XP extra que ganará el jugador. Sin embargo, si este valor se utilizara para multiplicar, como un valor que multiplica la XP ganada por cada partido, devolver `0` causaría problemas.

[[[ code('5cbf84b53b') ]]]

Bien, ¡continuemos! Abre `CasinoHandler` y añade un constructor en el que inicializaremos `$this->next` en un objeto `new NullHandler()`. Copia este constructor porque lo necesitaremos para los otros manejadores. Dentro del método `handle()`, busca ese molesto `if` y elimínalo. También eliminaremos este `return 0` de la parte inferior. Ahora siempre devolveremos la salida del siguiente manejador. Ésa es la belleza del patrón Objeto Nulo.

[[[ code('81da8e6c5e') ]]]

Haremos lo mismo con los demás manejadores y... ¡perfecto! ¡Vamos a probarlo!

[[[ code('ba630e7a95') ]]]

[[[ code('65226eba9c') ]]]

Dirígete a tu terminal y ejecuta:

```terminal
php bin/console app:game:play
```

¡Eh, eh! ¡Hemos ganado! Y hemos ganado XP extra gracias al `LevelHandler`! Todo funciona y el código tiene un aspecto estupendo, pero conozco un pequeño truco que podría hacer que esto fuera aún mejor. Podríamos hacer que la propiedad del manejador `$next` fuera un argumento del constructor e inyectar `NullHandler` en el último de la cadena utilizando el atributo `Autowire` que hemos visto antes, así:

```php
class OnFireHandler implements XpBonusHanderInterface
{
    public function __construct(
        #[Autowire(service: NullHandler::class)]
        private readonly XpBonusHanderInterface $next
    ) {
    }
}
```

Esto nos permitiría incluso eliminar el método `setNext()` de la interfaz, lo cual es bastante práctico. No quiero desviarme tanto del diseño del patrón original, así que lo desharé, pero es bueno tenerlo en cuenta.

A continuación: Conoceremos al primo de la Cadena de Responsabilidad: el patrón Middleware.
