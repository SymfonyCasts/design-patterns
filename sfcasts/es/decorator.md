# El patrón decorador

¡Queda un patrón de diseño más! Y sinceramente, creo que hemos dejado lo mejor para el final. Se trata del patrón decorador. Este patrón es un patrón estructural, por lo que se trata de cómo organizar y conectar clases relacionadas. Esto tendrá más sentido cuando lo descubramos.

## Definición

Esta es la definición técnica:

> El patrón decorador te permite adjuntar nuevos comportamientos a los objetos colocando
> estos objetos dentro de objetos envolventes especiales que contienen los comportamientos.

Sí... Probemos en cambio esta definición:

> El patrón decorador es como un ataque intencionado de hombre en el medio. Sustituyes
> una clase con tu implementación personalizada, ejecutas algo de código y luego llamas al método verdadero.

Antes de profundizar más y más, veámoslo en acción.

## El objetivo

Éste es el objetivo: quiero imprimir algo en la pantalla cada vez que un jugador suba de nivel. La lógica para subir de nivel está dentro de `XpCalculator`:

[[[ code('32b5c07f39') ]]]

Pero en lugar de cambiar el código de esta clase, vamos a aplicar el patrón decorador, que nos permitirá ejecutar código antes o después de esta lógica... sin cambiar realmente el código que hay dentro.

Este es un patrón particularmente común para aprovechar si la clase que quieres modificar es un servicio de proveedor que... no puedes cambiar realmente. Y sobre todo si esa clase no nos da ninguna otra forma de engancharnos a ella, como por ejemplo implementando los patrones de observador o estrategia.

## Añadir la interfaz para soportar el decorado

Para que el patrón decorador funcione, sólo hay una regla: la clase que queremos decorar (es decir, la clase que queremos extender o modificar - `XpCalculator` en nuestro caso) tiene que implementar una interfaz. Verás por qué en unos minutos. Si`XpCalculator` fuera un paquete de un proveedor, tendríamos que esperar que hicieran un buen trabajo y lo hicieran implementar una interfaz.

Pero como este es nuestro código, podemos añadir una. En el directorio `Service/`, crea una nueva clase... pero cámbiala por una interfaz. Llamémosla `XpCalculatorInterface`. Luego, robaré la firma del método de `addXp()`, la pegaré aquí, añadiré una declaración `use`y un punto y coma:

[[[ code('9660b97bcc') ]]]

¡suficientemente fácil!

En `XpCalculator`, implementa `XpCalculatorInterface`:

[[[ code('cfa716c650') ]]]

Y por último, abre `XpEarnedObserver`. Este es el único lugar de nuestro código que utiliza `XpCalculator`. Cambia esto para permitir cualquier `XpCalculatorInterface`:

[[[ code('e82416f233') ]]]

Esto nos muestra por qué una clase debe implementar una interfaz para soportar la decoración. Dado que las clases que utilizan nuestro `XpCalculator` pueden ahora indicar una interfaz en lugar de la clase concreta, vamos a poder cambiar el verdadero `XpCalculator`por nuestra propia clase, conocida como decorador. ¡Vamos a crear esa clase ahora!

## Crear el decorador

En el directorio `src/Service/`, añade una nueva clase PHP y llámala, qué tal,`OutputtingXpCalculator`, ya que se trata de un `XpCalculator` que mostrará cosas en la pantalla:

[[[ code('0a2bfcb396') ]]]

Lo más importante de la clase decoradora es que debe llamar a todos los métodos reales del servicio real. Sí, vamos a pasar literalmente el `XpCalculator` real a éste para poder llamar a los métodos de éste.

Crea un `public function __construct()` y acepta un`private readonly XpCalculatorInterface` llamado, qué tal, `$innerCalculator`. Nuestro `OutputtingXpCalculator` también necesita implementar `XpCalculatorInterface` para que se pueda pasar a cosas como nuestro observador:

[[[ code('cbf7c94c01') ]]]

Ve a "Código"->"Generar" y selecciona "Implementar métodos" para generar `addXp()`. Añadiré la declaración `use` que falta y:

[[[ code('fa2e44182e') ]]]

¡Perfecto!

Como he mencionado, lo más importante que debe hacer siempre el decorador es llamar a ese servicio interno en todos los métodos de la interfaz pública. En otras palabras, digamos`$this->addXp($winner, $enemyLevel)`... oh, quiero decir `$this->innerCalculator->addXp()`:

[[[ code('bd34e0b9b1') ]]]

## Una cadena de decoradores

¡Mucho mejor! Con los decoradores, creas una cadena de objetos. En este caso, tenemos dos: el `OutputtingXpCalculator` llamará al verdadero`XpCalculator`. Una de las ventajas de los decoradores es que puedes tener tantos como quieras: ¡podríamos decorar nuestro decorador para crear tres clases! ¡Lo veremos más adelante!

## Añadir lógica personalizada

De todos modos, aquí abajo, ahora tenemos la posibilidad de ejecutar código antes o después de llamar al servicio interno. Así que antes, digamos `$beforeLevel = $winner->getLevel()` para almacenar el nivel inicial. Luego, abajo, `$afterLevel = $winner->getLevel()`. Por último,`if ($afterLevel > $beforeLevel)`, ¡sabemos que acabamos de subir de nivel!

[[[ code('ebc5587e85') ]]]

Y eso merece una celebración... ¡como imprimir algunas cosas! Diré`$output = new ConsoleOutput()`... que no es más que una forma barata de escribir en la consola, y luego pegaré unas cuantas líneas para dar salida a un bonito mensaje:

[[[ code('9b959c184d') ]]]

## Cómo introducir la clase decoradora en tu aplicación

Bien, ¡nuestra clase decoradora está hecha! Pero... ¿cómo lo conectamos? Lo que tenemos que hacer es sustituir todas las instancias de `XpCalculator` en nuestro sistema por nuestro nuevo`OutputtingXpCalculator`.

Hagamos esto manualmente primero, sin las cosas de contenedor de Symfony. Sólo hay un lugar en nuestro código que utiliza `XpCalculator`: `XpEarnedObserver`. Abre`src/Kernel.php` y comenta temporalmente la magia "subscribe" que hemos añadido antes:

[[[ code('2e328452d3') ]]]

Lo hago porque, de momento, quiero instanciar manualmente`XpEarnedObserver` y suscribirlo manualmente en `GameApplication`... sólo para que podamos ver cómo funciona la decoración.

En `src/Command/GameCommand.php`, volvamos a poner nuestra lógica de configuración del patrón observador manual de antes: `$xpCalculator = new XpCalculator()` y luego`$this->game->subscribe(new XpEarnedObserver()` pasando a `$xpCalculator`:

[[[ code('9505904d91') ]]]

Todavía no estamos utilizando el decorador... pero esto debería ser suficiente para que nuestra aplicación siga funcionando como antes. Cuando probamos el comando

```terminal-silent
php ./bin/console app:game:play
```

¡Ganamos! Y conseguimos algo de XP, lo que significa que `XpEarnedObserver` está haciendo su trabajo.

Entonces, ¿cómo utilizamos el decorador? Sustituyendo sigilosamente el `XpCalculator` real por el falso. Decimos `$xpCalculator = new OutputtingXpCalculator()`, y le pasamos el original `$xpCalculator`:

[[[ code('1159422900') ]]]

¡Ya está! De repente, aunque no tenga ni idea, ¡se está pasando a `XpEarnedObserver` nuestro servicio de decorador! ¡Te dije que era furtivo!

Así que vamos a empezar de nuevo. Ejecuta el juego de nuevo y lucha unas cuantas veces. El nuevo decorador debería imprimir un mensaje especial en el momento en que subamos de nivel. Lucha una vez más y... ¡ya está! Ahora somos de nivel 2. ¡Funciona!

Si te preguntas por qué el mensaje se imprimió antes de que la batalla empezara realmente... eso "podría" ser porque estos iconos de batalla valiente son... en realidad sólo una decoración elegante: técnicamente la batalla termina antes de que aparezcan.

Bien, hemos creado con éxito una clase decoradora. ¡Es increíble! Pero, ¿cómo podríamos sustituir el servicio `XpCalculator` por el decorador a través del contenedor de Symfony? Vamos a descubrir una forma a continuación. Después haremos algo aún más genial con el decorador.
