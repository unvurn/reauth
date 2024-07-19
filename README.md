# Reauth

This package provides token-based authentication ability to your laravel-based api server. 

## Install

```shell
$ composer require unvurn/reauth
```

And migration files to be generated.

```shell
$ php artisan vendor:publish --tag=reauth-migrations
```

## Configuration

### Guards

```php
# config/auth.php
return [
    'guard' => 'bearer',

    'guards' => [
        # this 'bearer' entry is implied within reauth package itself.
        # you don't have to duplicate it.
        'bearer' => [
            'driver' => 'bearer',
            'pipelines' => [
                [
                    'resolver' => OpaqueAccessTokenResolver::class,
                    'users' => AccessTokenUserProvider::class
                ]
            ]
        ],
        'json' => [
            'driver' => 'json',
            'pipelines' => [
                'custom_id' => [
                    'resolver' => function ($value, &$decoded) {
                        $decoded = $value;
                        return [ 'custom_id' => $value ];
                    },
                    // 'users': can be omitted
                ]
            ],        
        ]
    ]
];
```

Reauth package provides new form of `Guard`. Guard has two attributes - 'driver' and 'pipelines'.

'Driver' attribute assigns token source per each http requests. 'Bearer' driver requires that each http request has a bearer token carried by "Authorization" header. it means that each request should contain header like this:

```http request
Authorization: Bearer (token string)
```

'Json' driver requires that json request body contains specified attribute, such as "custom_id" in the example above.

### Pipelines

Guard has one or more pipelines to pass the result of authentication from token to trailing processes such as Controller.
Each pipeline is processed in order, finishes when authenticated user picked up (and later ones are abandoned).

#### Resolver

Resolver has role for decoding of raw token string (or pass through if it is "opaque" token) and arrange it into "credentials" for `UserProvider::retrieveByCredentials()` usage.

There are two ways to configure resolver how it works.
The first one is to use class instance that implements `TokenResolverInterface`, like this.

```php
            'pipelines' => [
                [
                    'resolver' => OpaqueAccessTokenResolver::class,
                    // ...
                ]
            ]
```

This class `OpaqueAccessTokenResolver` fulfills `TokenResolverInterface` i.e. `credentialsFromToken` method.

The other one is anonymous function like this:

```php
            'pipelines' => [
                [
                    'resolver' => function ($value, &$decoded) {
                        $decoded = $value;
                        return [ 'custom_id' => $value ];
                    },
                ]
            ]
```

The signature of this anonymous function is similar with `TokenResolverInterface::credentialsFromToken()`.

> [!WARNING]
> If you don't care decoded token in trailing process (ex. Controller) you can omit and ignore the second parameter like this,
> ```php
>                   'resolver' => function ($value) {
>                       return [ 'custom_id' => $value ];
>                   },
> ```
> although it is NOT recommended.

Resolver works for these processes:
 * if the input token has certain encoding, encrypting, format and so on, resolver can decompose raw token string into appropriate structure.
 * (if necessary) check the token if it is available or not - expiration limit, ownership of token, source address, etc.
 * arrange the information of token into "credentials" - for `UserProvider` use.

#### Users (= UserProvider)

Users(`UserProvider`) has role for User search that matches with credentials derived from previous processes.
`UserProvider` is similar with ordinary ones defined in Laravel Auth.
"Reauth" package provides additional functionality using User-related model classes.
 * AccessTokenUserProvider
 * AttributionalUserProvider
 * OpenIdUserProvider

###
