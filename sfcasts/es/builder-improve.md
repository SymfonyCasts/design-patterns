# Mejoras en el constructor

¡La primera versión de nuestra clase constructora está terminada! Aunque, en `GameApplication`, el `mage_archer` tiene dos tipos de ataque diferentes. Nuestro `CharacterBuilder`no admite eso ahora mismo... pero lo añadiremos en un momento.

## ¿Limpiar el estado después de construir?

Ah, ¡una cosa más sobre la clase constructora! En el método "construir", después de crear el objeto, puedes elegir "reiniciar" el objeto constructor. Por ejemplo, podríamos establecer el `Character` en una variable, y luego, antes de devolverlo, restablecer el`$maxHealth` y todas las demás propiedades a su estado original. ¿Por qué haríamos esto? Porque permitiría utilizar un único constructor una y otra vez para crear muchos objetos, o personajes en este caso.

[[[ code('b8abf2868c') ]]]

Sin embargo, no voy a hacer eso... lo que significa que un solo `CharacterBuilder`estará destinado a ser utilizado una sola vez para construir un solo personaje. Puedes elegir cualquiera de las dos opciones en tu aplicación: no hay una forma correcta o incorrecta para el patrón constructor.

## Utilizar el Constructor

Muy bien, ¡vamos a utilizarlo! Dentro de `GameApplication`, primero, sólo para facilitar la vida, voy a crear un `private function` en la parte inferior llamado`createCharacterBuilder()` que devolverá `CharacterBuilder`. Dentro,`return new CharacterBuilder()`.

[[[ code('f692613059') ]]]

Eso va a estar bien porque... aquí arriba, en `createCharacter()`, podemos usar eso. Voy a borrar lo viejo... y ahora, usar la forma fluida de hacer caracteres: `$this->createCharacterBuilder()`, `->setMaxHealth(90)`,`->setBaseDamage(12)`, `->setAttackType('sword')` y `->setArmorType('shield')`. Ah, y, aunque no lo he hecho, estaría bien añadir constantes en el constructor para estas cadenas, como `sword` y `shield`.

Por último, llama a `->buildCharacter()` para... ¡construir ese carácter!

[[[ code('5df4a1e0a6') ]]]

¡Eso está muy bien! Y sería aún más bonito si la creación de un personaje fuera aún más compleja, como si implicara llamadas a la base de datos.

Para ahorrar algo de tiempo, voy a pegar los otros tres personajes, que tienen un aspecto similar. Aquí abajo, para nuestro `mage_archer`, estoy utilizando actualmente el tipo de ataque`fire_bolt`. Tenemos que volver a añadir una forma de tener tanto `fire_bolt` como`bow`, pero esto debería funcionar por ahora.

[[[ code('4d91597390') ]]]

¡Vamos a probarlo! En tu terminal, ejecuta:

```terminal
php bin/console app:game:play
```

¡Eh! ¡No ha explotado! Eso siempre es una buena señal. Y si lucho como `archer`... ¡gané! ¡Nuestra aplicación sigue funcionando!

## Permitir varios tipos de ataque

¿Y qué hay de permitir los dos tipos de ataque de nuestro mago_arquero? Bueno, esa es la belleza del patrón constructor. Parte de nuestro trabajo, cuando creamos la clase constructora, es hacer la vida lo más fácil posible a quien utiliza esta clase. Por eso elegí utilizar `string` `$armorType` y `$attackType` en lugar de objetos.

Podemos resolver el manejo de dos `AttackTypes` diferentes como queramos. Personalmente, creo que sería genial poder pasar múltiples argumentos. Así que ¡hagámoslo realidad!

En `CharacterBuilder`, cambia el argumento a `...$attackTypes` con una "s", utilizando el elegante `...` para aceptar cualquier número de argumentos. Luego, como esto va a contener una matriz, cambia la propiedad a `private array $attackTypes`... y aquí abajo, `$this->attackTypes = $attackTypes`.

[[[ code('aea3d4c901') ]]]

Es fácil. A continuación, tenemos que hacer algunos cambios abajo, en `buildCharacter()`, como cambiar las cadenas de `$attackTypes` por objetos. Para ello, voy a decir `$attackTypes =` y... a ponerme un poco elegante. No hace falta que lo hagas, pero voy a utilizar `array_map()` y la nueva sintaxis corta de `fn` - `(string
$attackType) => $this->createAttackType($attackType)`. Para el segundo argumento de `array_map()` -el array que realmente queremos mapear- utiliza`$this->attackTypes`.

[[[ code('1c906efee4') ]]]

Ahora, en el método privado, en lugar de leer la propiedad, lee un argumento de `$attackType`.

[[[ code('891e62b0e6') ]]]

Vale, podríamos haber hecho esto con un bucle `foreach`... y si te gustan más los bucles `foreach`, hazlo. Sinceramente, creo que he estado escribiendo demasiado JavaScript últimamente. De todos modos, esto dice básicamente:

> Quiero hacer un bucle sobre todas las cadenas de "tipo de ataque" y, para cada una, llamar a esta
> función en la que cambiamos esa cadena `$attackType` por un objeto `AttackType`.
> A continuación, pon todos esos objetos `AttackType` en una nueva variable `$attackTypes`.

En otras palabras, ahora se trata de una matriz de objetos `AttackType`.

Para terminar, di `if (count($attackTypes) === 1)`, y luego`$attackType = $attackTypes[0]` para coger el primer y único tipo de ataque. Si no, di `$attackType = new MultiAttackType()` pasando por `$attackTypes`. Por último, al final, utiliza la variable `$attackType`.

[[[ code('6e376e40ab') ]]]

¡Uf! Puedes ver que es un poco feo, ¡pero no pasa nada! Estamos ocultando la complejidad de la creación dentro de esta clase. Y podemos probarla fácilmente de forma unitaria.

Vamos a probarlo. Ejecuta nuestro comando...

```terminal-silent
./bin/console app:game:play
```

... seamos un `mage_archer` y... ¡impresionante! ¡No hay error! Así que... voy a suponer que todo funciona.

Vale, en `GameApplication`, estamos instanciando el `CharacterBuilder` manualmente. Pero, ¿qué pasa si el `CharacterBuilder` necesita acceder a algunos servicios para hacer su trabajo, como el EntityManager para poder hacer consultas a la base de datos?

A continuación, vamos a hacer más útil este ejemplo viendo cómo manejamos la creación de este objeto `CharacterBuilder` en una aplicación Symfony real aprovechando el contenedor de servicios. También hablaremos de las ventajas del patrón constructor.
