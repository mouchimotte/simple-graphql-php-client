# simple-graphql-php-client

The purpose of those class is to transform an array that describe GraphQL queries or mutations, into a valid request body.
So transform the description into a valid Graphql string and build a request array with it.

Based on works of https://github.com/christiangoltz/graphql-client-php

It supports:
- multiple queries or mutations
- variables
- aliases

It doesn't support:
- fragment

## Syntax

- queries_description: `[<query>,...]`
- mutations_description: `[<query>,...]`

Where `<query>`:
```
[
  name: string
  fields?: [string]
  params?: {string: Variable|integer|string}
  alias?: string
  queries?: [<query>,...]
]
```

## Usage

Query you may want to execute:
```
query {
users (
  limit: 3
  skip: 2
) {
  id
  first_name
  last_name
  comments {
    id
    comment
  }
}
}
```

So in PHP we can do:
```
$request_data = Client::queries_description_to_request([
   [
       'name' => 'me',
       'params' => ['limit' => 3, 'skip' => new Variable('skip', 2, 'Int')],
       'fields' => ['id', 'first_name', 'last_name'],
       'queries' => [
           ['name' => 'comments', 'fields' => ['id', 'comment']],
       ],
   ]
]);
$response_data = $this->post('/graphql/admin', $request_data);
```
