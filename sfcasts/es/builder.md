# Patrón constructor

Es hora del "patrón de diseño" número dos: el "patrón constructor". Este es uno de esos patrones de creación que te ayudan a instanciar y configurar objetos. Y es un poco más fácil de entender que el "patrón de estrategia".

## Definición oficial

La definición oficial del "patrón constructor" es la siguiente

> Un patrón de diseño de creación que te permite construir y configurar objetos complejos
> paso a paso.

Eso... realmente tiene sentido. La segunda parte de la definición oficial dice

> el patrón te permite producir diferentes tipos y representaciones de un objeto
> utilizando el mismo código de construcción.

En otras palabras, creas una clase constructora que ayuda a construir otros objetos... y esos objetos pueden ser de diferentes clases o de la misma clase con diferentes datos.

## Ejemplo sencillo

Un ejemplo sencillo podría ser el de una pizzería que necesita crear un montón de pizzas, cada una con una corteza diferente, ingredientes, etc. Para facilitar la vida, el propietario de la pizzería, que es un desarrollador de Symfony de noche, decide crear una clase `PizzaBuilder` con métodos sencillos como `addIngredient()`, `setDough()` y`addCheese()`. Luego, crea un método `buildPizza()`, que toma toda esa información y hace el trabajo pesado de crear ese objeto `Pizza` y devolverlo. Ese método `buildPizza()` puede ser tan complicado como sea necesario. Cualquier persona que utilice esta clase no sabe ni le importa nada de eso. El método también podría crear diferentes clases para diferentes situaciones, si eso es lo que nuestro valiente propietario de una pizzería-barra-Symfony-dev necesita para su aplicación.

## Creación de la clase constructora

Bien, vamos a crear un constructor en nuestro proyecto. Dirígete a `GameApplication` y baja a `createCharacter()`. El problema es que estamos construyendo cuatro objetos diferentes de`Character` y pasando bastantes datos para configurar cada uno. Y, ¿qué pasa si necesitamos crear estos objetos de `Character` en otros lugares de nuestro código? Ahora mismo no son súper fáciles de construir. Podríamos hacer alguna subclase de `Character` que pudiera configurar estos datos automáticamente, como llamando al constructor padre. Pero, como hemos hablado con el patrón de estrategia, eso podría ponerse muy feo cuando empecemos a tener combinaciones extrañas de cosas como un `mage-archer`con una clase de escudo `IceBlockType`.

¿Y si crear un objeto `Character` fuera aún más difícil? ¿Por ejemplo, si requiriera hacer consultas a la base de datos u otras operaciones? Nuestro objetivo es hacer que la instanciación de los objetos `Character` sea más fácil y clara. Y podemos conseguirlo creando una clase constructora.

Añade un directorio `src/Builder/` para organizarlo y, dentro de él, una nueva clase PHP llamada `CharacterBuilder`. Estoy creando esta clase, pero no estoy creando la interfaz correspondiente. Las clases constructoras suelen implementar una interfaz como`CharacterBuilderInterface`, pero no es necesario. Más adelante hablaremos de por qué puedes decidir añadir una interfaz en algunas situaciones.

[[[ code('43eafc54a6') ]]]

## Métodos y encadenamiento de métodos

Bien, en el interior, podemos crear los métodos que queramos para permitir que el mundo exterior elabore personajes. Por ejemplo, `public function setMaxHealth()`, que aceptará un argumento `int $maxHealth`. Voy a dejar este método vacío por el momento... pero al final se devolverá a sí mismo: devolverá `CharacterBuilder`. Esto es muy común en el patrón constructor porque permite el encadenamiento de métodos, también conocido como "interfaz fluida". Pero no es un requisito del patrón.

Muy bien, vamos a rellenar rápidamente algunos métodos más, como `setBaseDamage()`... y los dos últimos son los tipos de armadura y de ataque. Digamos que `setAttackType()`. Recuerda que los tipos de ataque son objetos. Pero en lugar de permitir un argumento de interfaz `AttackType`, voy a aceptar un argumento `string` llamado `$attackType`. ¿Por qué? No tengo por qué hacerlo, pero intento facilitar al máximo la creación de personajes. Así que, en lugar de hacer que otra persona instancie los tipos de ataque, voy a permitirles que pasen una simple cadena -como la palabra `bow` - y, en unos minutos, nos encargaremos de la complejidad de instanciar el objeto por ellos.

Bien, copia eso, y haz lo mismo para `setArmorType()`.

[[[ code('023ae931c6') ]]]

Y... ¡ya está! Esas son las únicas cuatro cosas que puedes controlar en un personaje.

## El método de creación

El último método que necesita nuestro constructor es el que realmente construirá el `Character`. Puedes llamarlo como quieras, por ejemplo `buildCharacter()`. Y, por supuesto, va a devolver un objeto `Character`.

[[[ code('a4a7a472ca') ]]]

Para almacenar las estadísticas de los personajes, vamos a crear cuatro propiedades, que voy a pegar: `private int $maxHealth`, `private int $baseDamage`, y luego`private string $attackType` y `private string $armorType`. Luego, en cada método, asigna esa propiedad y `return $this`. Lo haremos para `$baseDamage`...`$attackType`... y `$armorType`.

[[[ code('127be7b83e') ]]]

¡Qué bonito! El método `buildCharacter()` es bastante sencillo: hacemos el trabajo feo necesario para crear el `Character`. Así que diré `return new Character()`pasando por `$this->maxHealth` y `$this->baseDamage`. Los dos últimos argumentos requieren objetos... así que son un poco más complejos. Pero ¡no pasa nada! No me importa que mi constructor se complique un poco.

## Haciendo un poco de trabajo pesado

Iré al final de esta clase y pegaré dos nuevos métodos `private`. Estos se encargan de crear los objetos `AttackType` y `ArmorType`. Pero... Necesito un montón de declaraciones `use` para esto, que he olvidado. Ups. Así que voy a volver a escribir el final de estas clases y a pulsar "tab" para añadir esas sentencias `use`. Ya está

Bien, ahora podemos utilizar los dos nuevos métodos `private` para asignar las cadenas a los objetos. Éste es el trabajo pesado -y el verdadero valor- de `CharacterBuilder`. Digamos`$this->createAttackType()` y `$this->createArmorType()`.

[[[ code('59e68ff2c8') ]]]

Y... ¡nuestro constructor está hecho! A continuación: vamos a utilizarlo en `GameApplication`. Luego, haremos que nuestro constructor sea aún más flexible (pero no más difícil de usar) teniendo en cuenta los caracteres que utilizan varios tipos de ataque.
