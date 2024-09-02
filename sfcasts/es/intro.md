# Introducción

¡Hola amigos! ¡Bienvenidos de nuevo al segundo episodio de nuestra serie Patrones de diseño! En este episodio, continuaremos nuestro viaje para construir el mejor juego de rol de línea de comandos de la historia. Para ello, aplicaremos no uno, ni dos, sino cinco nuevos patrones de diseño.

Aprenderemos tres nuevos patrones de comportamiento: Comando, Cadena de Responsabilidad y Estado. Estos patrones ayudan a organizar el código en clases separadas que luego pueden interactuar entre sí.

También conoceremos el patrón Fábrica, que es un patrón de creación. Este tipo de patrón sirve para ayudar a instanciar objetos, igual que el patrón Constructor del episodio uno. Y, como extra, trataremos uno de mis favoritos: el patrón NullObject.

Para más información sobre los tipos de patrones de diseño, consulta el primer capítulo del episodio uno.

## Recordatorio Sobre los Patrones de Diseño

Antes de entrar en materia, recapitulemos qué son los patrones de diseño y lo que hemos tratado hasta ahora.

En pocas palabras, los patrones de diseño son soluciones probadas en batalla a problemas de diseño de software. Cuando te encuentres con un problema, puedes consultar el [catálogo](https://java-design-patterns.com/patterns/) de patrones de diseño y encontrar el patrón ideal para tu caso de uso. Y recuerda que siempre puedes modificar el patrón de la forma que mejor se adapte a tu aplicación.

En el episodio uno, cubrimos cinco patrones de diseño: Estrategia, Constructor, Observador, PubSub y Decorador. Seguimos utilizando esos patrones en nuestro juego, pero no es necesario que los entiendas para seguir este tutorial.

## Configuración del Proyecto

Bien, ¡vamos a hacerlo! Te recomiendo encarecidamente que descargues el código del curso desde esta página y codifiques conmigo. El código base ha cambiado bastante desde el episodio uno, así que si estás utilizando el código de ese tutorial, asegúrate de descargar esta nueva versión. Después de descomprimirlo, encontrarás un directorio `start/` con el mismo código que ves aquí. El archivo `README.md` contiene todos los detalles de configuración que necesitarás.

Ésta no puede ser más fácil. Ejecuta

```terminal
composer install
```

y, para jugar, ejecuta:

```terminal
php bin/console app:game:play
```

Tenemos unos cuantos personajes para elegir. Yo seré un luchador. Y... ¡qué bien! ¡Ganamos! Hubo cuatro rondas de lucha, hicimos 79 puntos de daño, recibimos 0, ¡y ganamos 30 puntos de XP! Estupendo. Y aquí arriba, puedes ver cómo se desarrolló el combate. ¡Esto sí que es emocionante!

Entonces... ¿cómo funciona esto? Abre `GameCommand.php`. Este es un comando de Symfony que prepara las cosas, inicializa este objeto global `$printer`, que es muy práctico para imprimir información donde la necesitemos, y luego nos pregunta qué personaje queremos ser. 

[[[ code('7d8341bfb2') ]]]

A continuación, inicia la batalla llamando a `play()`en la propiedad `GameApplication`, imprime los resultados y nos permite seguir jugando.

[[[ code('fc81858298') ]]]

Esto no es nada del otro mundo. Todo el trabajo pesado ocurre en el método `play()`de `GameApplication`. Si mantienes pulsada la tecla "CMD" + "B" para ir a la definición, podemos ver que este método toma dos objetos personaje -el jugador, que somos nosotros, y la IA- y hace que se ataquen mutuamente hasta que uno de ellos gane.

[[[ code('98e8471a98') ]]]

Si exploramos un poco más esta clase, encontraremos algunos lugares en los que ya hemos aplicado algunos patrones de diseño. Si buscas el método `createCharacter()`, podrás ver cómo hemos utilizado el patrón Constructor para crear y configurar objetos personaje. 

[[[ code('db964ebbb5') ]]]

Y, aquí abajo, estamos utilizando el patrón Observador, añadiendo o eliminando observadores, y notificándoles una vez finalizado el combate.

[[[ code('01fb44ac6b') ]]]

¡Muy bien! Es hora de conocer el patrón Comando y hacer que nuestro juego sea más interactivo. Eso a continuación.