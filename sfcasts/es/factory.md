# Patrón Fábrica

Ha llegado el momento de nuestro último patrón de diseño: el patrón Fábrica. Fábrica es un patrón de diseño de creación que proporciona una forma de ocultar los detalles de cómo se crean tus objetos al código que los utiliza. En lugar de instanciar directamente los objetos con `new`, utilizamos una fábrica que decide qué objeto crear en función de la entrada.

## Anatomía del patrón

El patrón Fábrica se compone de cinco partes:

La primera parte es una interfaz de los productos que queremos crear. Si quisiéramos crear armas para nuestros personajes, por ejemplo, tendríamos un`WeaponInterface`, y los productos serían armas.

La segunda son los productos concretos que implementan la interfaz. En este ejemplo, tendríamos clases como `Sword`, `Axe`, `Bow`, etc.

En tercer lugar está la interfaz de fábrica. Es opcional, pero resulta muy útil cuando necesitas crear familias de productos.

La cuarta es la fábrica concreta que implementa la interfaz de fábrica, si tenemos una. Esta clase lo sabe todo sobre la creación de productos.

Y por último, tenemos el cliente, que utiliza una fábrica para crear objetos producto. Esta clase sólo sabe cómo utilizar los productos, pero no cómo se crean, ni qué producto concreto está utilizando.

## Fábrica con varios métodos de creación

Puede que te sorprenda saber que existe más de una variante del patrón Fábrica. La variante más sencilla es una fábrica con múltiples métodos `make`, uno para cada producto posible. Esa fábrica tendría el siguiente aspecto:

```php
class WeaponFactory
{
    public function makeSword(): WeaponInterface
    {
        return new Sword(Dice::rollRange(4, 8), 12);
    }

    public function makeAxe(int $bonusDamage = 0): WeaponInterface
    {
        return new Axe($bonusDamage + Dice::rollRange(6, 12), 8);
    }
}
```

Esta variante es útil cuando quien llama ya sabe qué objeto necesita. También es fácil tener argumentos de constructor diferentes para cada tipo.

## Fábrica con un único método constructor

Otro enfoque es utilizar un único método `make`. Que recibirá un argumento y determinará qué objeto necesita crear. Esto es útil cuando la aplicación es más dinámica. El valor de `$type` puede proceder de la entrada del usuario, de una petición o de cualquier otra cosa.

Sin embargo, este enfoque tiene un par de inconvenientes, como la pérdida de seguridad de tipo, ya que se puede enviar cualquier cadena como tipo. Por suerte, eso puede solucionarse con un buen conjunto de pruebas, o transformando la cadena en un `enum`. También es difícil tener diferentes argumentos de constructor en cada tipo.

## Fábrica abstracta

La última variante de la que hablaremos es la "Fábrica Abstracta". En este enfoque, tenemos varias fábricas que implementan la misma interfaz, y cada fábrica concreta crea una familia de objetos. En nuestro ejemplo de las armas de los personajes, podríamos agrupar las armas en función del material del que están hechas, como hierro o acero, y cada fábrica sólo crearía armas con ese material.

Dependiendo de la aplicación, podemos elegir qué fábrica se utilizará en función de alguna configuración, o cambiar la fábrica en tiempo de ejecución en función de algún evento. En nuestro juego, podríamos cambiar la fábrica de armas cada vez que cambie el nivel del juego, lo que sin duda haría las cosas más emocionantes.

## Crear una fábrica de tipo de ataque

¡Muy bien! ¡Es hora de ver el patrón Fábrica en acción! Empezaremos creando la fábrica más simple posible, y luego la convertiremos en una fábrica abstracta. Ya hemos creado objetos `AttackType` en algunos lugares de nuestra aplicación. Uno de ellos es en el `CharacterBuilder`. Ábrelo y busca el método `createAttackType()`. Si miramos la sentencia `match`, veremos que estamos creando objetos `AttackType` a partir de una cadena de entrada. Así que estamos utilizando la variante de método único.

[[[ code('9a5fe010a2') ]]]

Si abrimos `GameInfoCommand`, en la parte inferior... tenemos la misma declaración `match`. Este código duplicado no es súper ideal porque si alguna vez queremos añadir un nuevo `AttackType` o cambiar los argumentos del constructor, tendríamos que encontrar y actualizar todos los lugares donde los instanciamos. En aplicaciones más grandes, este proceso sería propenso a errores y llevaría un montón de tiempo.

Seguro que hay una forma mejor, ¿verdad? ¡La hay! Vamos a refactorizar este código con una fábrica. Copia este código de la declaración `match`, y dentro del directorio `src/`, crea una carpeta llamada `Factory/`. Dentro de ella, añade una nueva clase PHP, y la llamaremos `AttackTypeFactory`. ¡Estupendo! Ahora necesitamos un método que cree objetos `AttackType`. Escribe `public function create()`, dale un argumento`string $type`, y haz que devuelva objetos `AttackType`. En `create()`, pega el código y cambia el nombre de la variable a `$type`.

[[[ code('2686068f45') ]]]

Vale, lo que hemos hecho hasta ahora puede parecer insignificante, pero hemos conseguido mucho. Hemos encapsulado cómo se crean los objetos `AttackType` en toda nuestra aplicación y, como extra, también hemos sentado las bases para manejar familias de `AttackType`'s. Hablaremos de ello más adelante.

El siguiente paso es inyectar el `AttackTypeFactory` en el`CharacterBuilder`. Ábrelo y, en la parte superior, añade un constructor con un argumento: `private readonly AttackTypeFactory $attackTypeFactory`.

[[[ code('534e8df2bf') ]]]

Después, busca el método `buildCharacter()`. Ahí es donde llamamos a `createAttackType()`. Dividiré esto en varias líneas para que sea más fácil de leer. Y ahora, sustituye`createAttackType()` por `$this->attackTypeFactory->create()`. 

[[[ code('d568393d25') ]]]

Perfecto Hagamos lo mismo en `GameInfoCommand`. Abre eso... y añade un constructor. Podemos dejar que PhpStorm lo autogenere por nosotros para que añada automáticamente la llamada a`parent`. Luego inyectaremos la fábrica - `private readonly AttackTypeFactory $attackTypeFactory`.

[[[ code('947cf7974b') ]]]

Por último, desplázate hacia abajo, busca el método `computeAverageDamage()`... y, una vez más, sustituye `createAttackType()` por `$this->attackTypeFactory->create()`. ¡Alucinante!

[[[ code('f37840d7d5') ]]]

¡Creo que estamos listos para probarlo! Dirígete a tu terminal y, esta vez, ejecuta `GameInfoCommand`:

```terminal
php bin/console app:game:info
```

Y... ¡sí! ¡Esto es genial! Podemos ver información sobre las clases de nuestros personajes y sus armas. Vamos a celebrarlo eliminando todo el código duplicado de`CharacterBuilder` y `GameInfoCommand`.

¡Muy bien! ¡Hemos implementado con éxito el patrón Fábrica! Ésta ha sido la variante más sencilla del patrón, así que ahora vamos a dar un paso más y manejar familias de `AttackType` con una fábrica abstracta.
