# Decoración con el contenedor de Symfony

Acabamos de implementar el patrón decorador, en el que básicamente envolvimos el`XpCalculator` original en un cálido abrazo con nuestro `OutputtingXpCalculator`. Luego... lo introducimos silenciosamente en el sistema en lugar del original... sin que nadie más -como `XpEarnedObserver` - sepa o se preocupe de que lo hemos hecho:

[[[ code('2ba9ee7ec1') ]]]

Pero para configurar la decoración, estoy instanciando los objetos manualmente, lo que no es muy realista en una aplicación Symfony. Lo que realmente queremos es que `XpEarnedObserver`se autoconecte a `XpCalculatorInterface` de forma normal, sin que tengamos que hacer nada de esta instanciación manual. Pero necesitamos que el contenedor le pase nuestro servicio decorador`OutputtingXpCalculator`, no el original `XpCalculator`. ¿Cómo podemos conseguirlo? ¿Cómo podemos decirle al contenedor que cada vez que alguien haga una sugerencia de tipo `XpCalculatorInterface`, debe pasarle nuestro servicio de decorador?

Para responder a esto, empecemos por deshacer nuestro código manual: Tanto en `GameCommand`... como en `Kernel`... vuelve a poner el código de fantasía que adjunta el observador a`GameApplication`:

[[[ code('e13dcf2306') ]]]

[[[ code('8e486aaea9') ]]]

Si ahora probamos el comando

```terminal-silent
php ./bin/console app:game:play
```

Falla:

> No se puede autoconectar el servicio `XpEarnedObserver`: el argumento `$xpCalculator` hace referencia
> interfaz `XpCalculatorInterface` pero no existe tal servicio. Tal vez debas
> alias de esta interfaz a uno de estos servicios existentes `OutputtingXpCalculator`
> o `XpCalculator`.

## Cablear manualmente la decoración del servicio: Alias

Ese es un gran error... y tiene sentido. Dentro de nuestro observador, estamos haciendo un guiño a la interfaz en lugar de a una clase concreta. Y, a menos que hagamos un poco más de trabajo, Symfony no sabe qué servicio de `XpCalculatorInterface` debe pasarnos. ¿Cómo se lo decimos? Creando un alias de servicio.

En `config/services.yaml`, digamos que `App\Service\XpCalculatorInterface` se convierte en`@App\Service\OutputtingXpCalculator`:

[[[ code('a718edce5f') ]]]

Esto crea un servicio cuyo id es `App\Service\XpCalculatorInterface`... pero en realidad es sólo un "puntero", o "alias" al servicio `OutputtingXpCalculator`. Y recuerda que, durante el autocableado, cuando Symfony ve un argumento indicado con`XpCalculatorInterface`, para saber qué servicio pasar, simplemente busca en el contenedor un servicio cuyo id coincida con ese, por lo que`App\Service\XpCalculatorInterface`. Y ahora, ¡encuentra uno!

Así que vamos a intentarlo de nuevo.

```terminal-silent
php ./bin/console app:game:play
```

Y... sigue sin funcionar. ¡Estamos de enhorabuena!

> Referencia circular detectada para el servicio `OutputtingXpCalculator`,
> ruta: `OutputtingXpCalculator` -&gt `OutputtingXpCalculator`

Oh! Symfony está autocableando `OutputtingXpCalculator` en `XpEarnedObserver`... pero también está autocableando `OutputtingXpCalculator` en sí mismo:

[[[ code('93037abab4') ]]]

¡Ups! Queremos que `OutputtingXpCalculator` se utilice en todas las partes del sistema que autocablean `XpCalculatorInterface`... excepto en sí mismo.

Para conseguirlo, de nuevo en `services.yaml`, podemos configurar manualmente el servicio. Aquí abajo, añade `App\Service\OutputtingXpCalculator` con `arguments`,`$innerCalculator` (ese es el nombre de nuestro argumento) establecido en`@App\Service\XpCalculator`:

[[[ code('e62fd3dad8') ]]]

Esto anulará el argumento sólo para este caso. Y ahora...

```terminal-silent
php ./bin/console app:game:play
```

¿Funciona? Quiero decir, ¡claro que funciona! Si jugamos unas cuantas rondas y avanzamos rápidamente... ¡sí! ¡Ahí está el mensaje de "has subido de nivel"! ¡Sí que pasó por nuestro decorador!

Esta forma de cablear el decorador no es nuestra solución definitiva. Pero antes de llegar ahí, tengo un reto aún mayor: vamos a sustituir completamente un servicio principal de Symfony por el nuestro a través del decorador. ¡Eso a continuación!
