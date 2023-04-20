# Constructor en Symfony y con una Fábrica

¿Y si, para instanciar los objetos `Character`, `CharacterBuilder` necesitara, por ejemplo, hacer una consulta a la base de datos? Bien, cuando necesitamos hacer una consulta, normalmente damos a nuestra clase un constructor y luego autocontratamos el servicio del gestor de entidades. Pero `CharacterBuilder` no es un servicio. Técnicamente podrías utilizarlo como un servicio, pero un servicio es una clase de la que normalmente sólo necesitas una única instancia en tu aplicación. Sin embargo, en `GameApplication` estamos creando un`CharacterBuilder` por personaje. Si intentáramos autoconducir `CharacterBuilder`a `GameApplication`, eso funcionaría. Symfony autocablearía el EntityManager en `CharacterBuilder` y luego autocablearía ese objeto `CharacterBuilder` aquí. El problema es que entonces sólo tendríamos un `CharacterBuilder`... cuando en realidad necesitamos cuatro para crear nuestros cuatro objetos `Character`.

Por eso los objetos constructores suelen ir asociados a una fábrica de constructores. Déjame deshacer todos los cambios que acabo de hacer en `GameApplication`... y en `CharacterBuilder`.

## Crear una fábrica

En el directorio `Builder/`, crea una nueva clase llamada `CharacterBuilderFactory`:

[[[ code('3e60167997') ]]]

Por cierto, existe un patrón llamado patrón de fábrica, que no trataremos específicamente en este tutorial. Pero una "fábrica" no es más que una clase cuyo trabajo es crear otra clase. Al igual que el patrón constructor, es un patrón de creación. Dentro de la clase fábrica, crea un nuevo método llamado, qué tal...`createBuilder()`, que devolverá un `CharacterBuilder`. Y dentro de éste, simplemente `return new CharacterBuilder()`:

[[[ code('b5f993c014') ]]]

Este `CharacterBuilderFactory` es un servicio. Aunque necesitemos cinco objetos`CharacterBuilder` en nuestra aplicación, sólo necesitaremos un `CharacterBuilderFactory`. Simplemente llamaremos a este método cinco veces.

Eso significa que, en `GameApplication`, podemos crear un `public function __construct()`y autoconectar `CharacterBuilderFactory $characterBuilderFactory`. También añadiré`private` delante para que sea una propiedad:

[[[ code('143d0cd6f8') ]]]

Luego, dentro de `createCharacterBuilder()`, en lugar de crear esto a mano, confía en la fábrica: `return $this->characterBuilderFactory->createBuilder()`:

[[[ code('030b3e5ed4') ]]]

Lo bueno de esta fábrica (y éste es realmente el propósito del patrón de fábrica en general) es que hemos centralizado la instanciación de este objeto.

## Introducir los servicios en el constructor

¿Cómo ayuda esto a nuestra situación? Recuerda que el problema que imaginé era el siguiente: ¿Qué pasaría si nuestro constructor de personajes necesitara un servicio como el de `EntityManager`?

Con nuestra nueva configuración, podemos conseguirlo. En realidad no tengo Doctrine instalado en este proyecto, así que en lugar de `EntityManager`, vamos a requerir`LoggerInterface $logger`... y volveré a añadir `private` delante para convertirlo en una propiedad:

[[[ code('20943113c3') ]]]

Luego, abajo, en `buildCharacter()`, sólo para probar que esto funciona, lo utilizaré:`$this->logger->info('Creating a character')`. También pasaré un segundo argumento con alguna información extra como `'maxHealth' => $this->maxHealth` y`'baseDamage' => $this->baseDamage`:

[[[ code('96b0798cd8') ]]]

`CharacterBuilder` ahora requiere un `$logger`... pero `CharacterBuilder` no es un servicio que vayamos a obtener directamente del contenedor. Lo obtendremos a través de`CharacterBuilderFactory`, que es un servicio. Así que el autocableado de `LoggerInterface`funcionará aquí:

[[[ code('ca2d80546d') ]]]

Entonces, pásalo manualmente al constructor como `$this->logger`:

[[[ code('bea49ea816') ]]]

Aquí vemos algunas de las ventajas del patrón de fábrica. Como ya hemos centralizado la instanciación de `CharacterBuilder`, cualquier cosa que necesite un`CharacterBuilder`, como `GameApplication`, no necesita cambiar en absoluto... ¡aunque acabemos de añadir un argumento al constructor! `GameApplication` ya estaba descargando el trabajo de instanciación a `CharacterBuilderFactory`.

Para ver si esto funciona, ejecuta

```terminal
./bin/console app:game:play -vv
```

El `-vv` nos permitirá ver los mensajes de registro mientras jugamos. Y... ¡lo conseguimos! ¡Mira! Aparece nuestro mensaje`[info] Creating a character`. No podemos ver las demás estadísticas en esta pantalla, pero están en el archivo de registro. Impresionante.

## ¿Qué resuelve el patrón constructor?

¡Así que ése es el patrón constructor! ¿Qué problemas puede resolver? Muy sencillo Tienes un objeto que es difícil de instanciar, así que añades una clase constructora para facilitarte la vida. También ayuda con el principio de responsabilidad única. Es una de las estrategias que ayuda a abstraer la lógica de creación de una clase de la clase que utilizará ese objeto. Anteriormente, en `GameApplication`, teníamos la complejidad tanto de crear los objetos `Character` como de utilizarlos. Aquí seguimos teniendo código para utilizar el constructor, pero la mayor parte de la complejidad vive ahora en la clase constructora.

## ¿Necesita mi constructor una interfaz?

A menudo, cuando estudias este patrón, te dirá que el constructor (`CharacterBuilder`, por ejemplo) debería implementar una nueva interfaz, como`CharacterBuilderInterface`, que tendría métodos como `setMaxHealth()`,`setBaseDamage()`, etc. Esto es opcional. ¿Cuándo lo necesitarías? Bueno, como todas las interfaces, es útil si necesitas la flexibilidad de cambiar la forma de crear tus personajes por alguna otra implementación.

Por ejemplo, imagina que creamos un segundo constructor que implementa`CharacterBuilderInterface` llamado `DoubleMaxHealthCharacterBuilder`. Éste crea objetos`Character`, pero de una forma ligeramente diferente... como si duplicara el `$maxHealth`. Si ambos constructores implementaran`CharacterBuilderInterface`, entonces dentro de nuestro `CharacterBuilderFactory`, que ahora devolvería `CharacterBuilderInterface`, podríamos leer alguna configuración para averiguar qué clase de `CharacterBuilder` queremos utilizar.

Así que la creación de esa interfaz realmente tiene menos que ver con el patrón constructor en sí mismo... y más con hacer tu código más flexible. Déjame deshacer ese código falso dentro de `CharacterBuilderFactory`. Y... dentro de `CharacterBuilder`, eliminaré esa interfaz falsa.

## ¿Dónde vemos el patrón constructor?

¿Y dónde vemos el patrón constructor en la naturaleza? Es bastante fácil de detectar porque el encadenamiento de métodos es una característica muy común de los constructores. El primer ejemplo que me viene a la mente es `QueryBuilder` de Doctrine:

```php
class CharacterRepository extends ServiceEntityRepository
{
    public function findHealthyCharacters(int $healthMin): array
    {
        return $this->createQueryBuilder('character')
            ->orderBy('character.name', 'DESC')
            ->andWhere('character.maxHealth > :healthMin')
            ->setParameter('healthMin', $healthMin)
            ->getQuery()
            ->getResult();
    }
}
```

Nos permite configurar una consulta con un montón de buenos métodos antes de llamar finalmente a `getQuery()` para crear realmente el objeto `Query`. También aprovecha el patrón de fábrica: para crear el constructor, llamas a `createQueryBuilder()`. Ese método, que vive en la base `EntityRepository` es la "fábrica" responsable de instanciar el `QueryBuilder`.

Otro ejemplo es el de Symfony `FormBuilder`:

```php
public function buildForm(FormBuilderInterface $builder, $options)
{
    $animals = ['🐑', '🦖', '🦄', '🐖'];
    $builder
        ->add('name', TextType::class)
        ->add('animal', ChoiceType::class, [
            'placeholder' => 'Choose an animal',
            'choices' => array_combine($animals, $animals),
        ]);
}
```

En ese ejemplo, no llamamos al método `buildForm()`, pero Symfony finalmente sí lo llama una vez que hemos terminado de configurarlo.

Bien equipo, hablemos ahora del patrón observador.
