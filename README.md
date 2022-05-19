# DB PLANNER - open meeting scheduling tool

## Intro
DB PLANNER is an open-source alternative to Doodle polls built with Svelte and GraphQL.

#### Packaging
This repository provides the following components of DB PLANNER:
- the UI, built with Svelte framework
- the GraphQL service with DB backend

## License
DB PLANNER is distributed under the terms of [MIT License](https://en.wikipedia.org/wiki/MIT_License)

## Requirements
- php 7.4+ for the GraphQL service
- DB backend: MySQL or PostgreSQL

## Build Requirements
- npm, WebPack, composer

## Build: Svelte UI
```
npm install
npm run build
```

## Build: GraphQL service
```
composer install
...then see mysql-init.sql for the DB initialization
```

[MIT](https://en.wikipedia.org/wiki/MIT_License) © [Dmitry Arkhipkin](https://github.com/dmarkh)