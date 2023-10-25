# Patrón de estrategia

El primer patrón del que hablaremos es el "patrón de estrategia". Se trata de un patrón de comportamiento que ayuda a organizar el código en clases separadas que pueden interactuar entre sí.

## Definición

Empecemos con la definición técnica:

> El patrón de estrategia define una familia de algoritmos, encapsula cada uno de ellos y
> los hace intercambiables. Permite que el algoritmo varíe independientemente de los clientes
> que lo utilizan.

Si eso tiene sentido para ti, ¡felicidades! ¡Te toca enseñar el resto del tutorial!

Vamos a intentarlo de nuevo. Ésta es mi definición:

> El patrón de estrategia es una forma de permitir que parte de una clase se reescriba desde
> el exterior.

## Ejemplo imaginario

Hablemos de un ejemplo imaginario antes de empezar a codificar. Supongamos que tenemos un`PaymentService` que hace un montón de cosas... incluyendo el cobro a la gente mediante tarjeta de crédito. Pero ahora, descubrimos que necesitamos utilizar esta misma clase para permitir que la gente pague a través de PayPal... o a través de un tesoro pirata - eso suena más divertido.

En cualquier caso, ¿cómo podemos hacerlo? ¡El patrón de estrategia! Permitiríamos pasar un nuevo objeto`PaymentStrategyInterface` a `PaymentService` y luego lo llamaríamos.

A continuación, crearíamos dos clases que implementen la nueva interfaz:`CreditCardPaymentStrategy` y `PiratesBootyPaymentStrategy`. ¡Y ya está! Ahora tenemos el control de la clase que pasamos. ¡Sí! Acabamos de hacer que parte del código dentro de`PaymentService` sea controlable desde fuera.

## El ejemplo real

Con esto en mente, vamos a codificar este patrón.

Ahora mismo, tenemos tres personajes que se crean dentro de `GameApplication`. Pero el `fighter` es el que domina. Para equilibrar el juego, quiero añadir habilidades de ataque especiales para cada personaje. Por ejemplo, el `mage` podrá lanzar hechizos.

[[[ code('ad64313e0a') ]]]

Actualmente, la función de ataque es bastante aburrida: tomamos el`baseDamage` del personaje y luego utilizamos esta genial función `Dice::roll()` para lanzar un dado de seis caras para conseguir algo de aleatoriedad.

[[[ code('fe778012c1') ]]]

Pero cuando un `mage` lanza un hechizo, el daño que causa será mucho más variable: a veces un hechizo funciona muy bien, pero... otras veces hace como pequeños fuegos artificiales que hacen casi cero daño.

Básicamente, para el mago, necesitamos un código completamente diferente para calcular el daño.

## ¿Pasar en una opción?

Entonces, ¿cómo podemos hacer esto? ¿Cómo podemos permitir que un personaje -el mago- tenga una lógica de daño diferente? La primera idea que se me ocurre es pasar una bandera al constructor del personaje, como `$canCastSpells`. Luego, en el método `attack()`, añadir una declaración `if` para que tengamos ambos tipos de ataques.

Genial... ¿pero qué pasa si un `archer` necesita otro tipo de ataque? Entonces tendríamos que pasar otra bandera y acabaríamos con tres variaciones dentro de`attack()`. Vaya.

## ¿Subclase?

Bien, entonces otra solución podría ser que subclasificáramos `Character`. Creamos un`MageCharacter` que extienda a `Character`, y luego anulamos el método `attack()` por completo. Pero, ¡maldita sea! No queremos anular todo `attack()`, sólo queremos sustituir una parte. Podríamos ponernos elegantes trasladando la parte que queremos reutilizar a una función protegida para poder llamarla desde nuestra subclase... pero esto se está poniendo un poco feo. Lo ideal es que resolvamos los problemas sin herencia siempre que sea posible.

## Creación de la interfaz "estrategia"

Así que vamos a retroceder. Lo que realmente queremos hacer es permitir que este código sea diferente carácter por carácter. Y eso es exactamente lo que permite el patrón de estrategia.

¡Hagamos esto! La lógica que necesitamos la flexibilidad de cambiar es esta parte de aquí, donde determinamos cuánto daño hizo un ataque.

Bien, el paso 1 del patrón es crear una interfaz que describa este trabajo. Voy a añadir un nuevo directorio `AttackType/` para organizar las cosas. Dentro, crea una nueva clase PHP, cambia el patrón a "Interfaz", y llámala`AttackType`.

¡Genial! Dentro, añade un `public function` llamado, qué tal, `performAttack()`. Esto aceptará el `$baseDamage` del personaje -porque eso puede ser útil- y luego devolverá el daño final que debe aplicarse.

[[[ code('c11c4a54ba') ]]]

¡Genial!

## Añadir la implementación de la interfaz

El paso 2 es crear al menos una implementación de esta interfaz. Imaginemos que nuestro `mage` tiene un ataque de fuego genial. Dentro del mismo directorio, crea una clase llamada `FireBoltType`... y haz que implemente `AttackType`. A continuación, ve a "Código -> Generar" -o "comando" + "N" en un Mac- y selecciona "Implementar métodos" como atajo para añadir el método que necesitamos.

[[[ code('f9079aa907') ]]]

Para el ataque mágico, devuelve `Dice::roll(10)` 3 veces. Así, el daño causado es el resultado de lanzar 3 dados de 10 caras.

[[[ code('c1b3e47c75') ]]]

Y... ¡nuestro primer tipo de ataque está hecho! Ya que estamos aquí, vamos a crear otros dos. Añadiré un `BowType`... y pegaré algo de código. Puedes copiarlo del bloque de código de esta página. Este ataque tiene una posibilidad de hacer daño crítico. Por último, añade un `TwoHandedSwordType`... y pegaré también ese código. Este es bastante sencillo: es el `$baseDamage` más algunas tiradas al azar.

[[[ code('9a356c3919') ]]]

[[[ code('556fc5ee89') ]]]

## Pasar y utilizar la estrategia

Estamos listos para el tercer y último paso de este patrón: permitir que una interfaz `AttackType`se pase a `Character` para que podamos utilizarla a continuación. Así que, literalmente, vamos a añadir un nuevo argumento: `private` -por lo que también es una propiedad-, con el tipo de interfaz `AttackType` (para que podamos permitir que se pase cualquier `AttackType`) y llamarlo `$attackType`.

[[[ code('13120ad101') ]]]

A continuación, elimina este comentario... porque ahora, en lugar de hacer la lógica manualmente, diremos `return $this->attackType->performAttack($this->baseDamage)`.

[[[ code('85ffef89de') ]]]

¡Y ya hemos terminado! Nuestra clase `Character` está ahora aprovechando el patrón de estrategia. Permite que alguien ajeno a esta clase pase un objeto `AttackType`, permitiéndole efectivamente controlar sólo una parte de su código.

## Aprovechar nuestra flexibilidad

Para aprovechar la nueva flexibilidad, abre `GameApplication`, y dentro de`createCharacter()`, pasa un `AttackType` a cada uno de ellos, como`new TwoHandedSwordType()` para el `fighter`, `new BowType()` para el `archer`, y`new FireBoltType()` para el `mage`.

[[[ code('f6351d3c55') ]]]

¡Qué bien! Para asegurarte de que no hemos roto nada, dirígete y prueba el juego.

```terminal-silent
php bin/console app:game:play
```

Y... ¡woohoo! ¡Sigue funcionando!

## Añadir un personaje de ataque mixto

Lo bueno del "patrón de estrategia" es que, en lugar de intentar pasar opciones a `Character` como `$canCastSpells = true` para configurar el ataque, tenemos el control total.

Para demostrarlo, añadamos un nuevo personaje, un mago arquero: un personaje legendario que tiene un arco y lanza hechizos. ¡Doble amenaza!

Para apoyar esta idea de tener dos ataques, crea un nuevo `AttackType` llamado`MultiAttackType`. Haz que implemente la interfaz `AttackType` y ve a "Implementar métodos" para añadir el método.

[[[ code('6cafce6c02') ]]]

En este caso, voy a crear un constructor en el que podemos pasar un `array`de `$attackTypes`. Para ayudar a mi editor, añadiré algo de PHPDoc por encima para señalar que se trata de un array específicamente de objetos `AttackType`.

[[[ code('fceb356011') ]]]

Esta clase funcionará eligiendo aleatoriamente entre uno de sus `$attackTypes` disponibles. Así que, aquí abajo, diré `$type = $this->attackTypes[]` - ¡Ups! Quería llamar a esto`attackTypes` con una "s" - entonces `array_rand($this->attackTypes)`. Devuelve`$type->performAttack($baseDamage)`.

[[[ code('1618495cf0') ]]]

Ya está Este es un ataque muy personalizado, pero con el "patrón de estrategia", no hay problema. En `GameApplication`, añade el nuevo carácter `mage_archer`... y copiaré el código anterior. Que sea... `75, 9, 0.15`. Entonces, para el `AttackType`, digamos `new MultiAttackType([])` pasando por `new BowType()` y `new FireBoltType()`.

[[[ code('2d0d25091c') ]]]

¡Qué bien! A continuación, también tenemos que actualizar `getCharacterList()` para que aparezca en nuestra lista de selección de personajes.

[[[ code('6d267e2cda') ]]]

Bien, vamos a comprobar el nuevo personaje legendario:

```terminal-silent
php bin/console app:game:play
```

Selecciona `mage_archer` y... ¡oh! una impresionante victoria contra un `archer` normal. ¿Cómo de genial es eso?

A continuación, vamos a utilizar el "patrón de estrategia" una vez más para que nuestra clase `Character`sea aún más flexible. A continuación, hablaremos de dónde puedes ver el "patrón de estrategia" en la naturaleza y qué ventajas concretas nos aporta.
