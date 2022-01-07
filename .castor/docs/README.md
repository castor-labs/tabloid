Castor Tabloid Documentation
============================

Castor Tabloid is an Object Relational Mapper (ORM) that makes a breeze to retrieve records from your database and
map them to objects.

# Why Objects and ORMs?

People are very critical of ORMs, and sometimes with due reason. There is price that is paid in order to be able to work
with objects mapped from database records, specially when these are mapped from a traditional RDBMS.

First, the impedance mismatch that is caused by trying to match tree-like hierarchical structures (objects and nested
objects in them) to two-dimensional records in multiple tables requires the implementation of complex techniques to
transform from one structural model to the other. There are multiple layers and code paths to this complex process of
transformation.

This, in turn, manifests in the second big drawback of ORMs, which is performance. It is obviously cheaper to return simpler
data structures (like associative arrays) from our database calls, than to return hydrated objects. Hydrated objects
require a lot of logic to be serialized from and to the database.

## The benefits of Objects

So, why then keep absorbing the cost of these drawbacks in order to work with objects? I can think of at least three main
benefits of using objects mapped from database records in our application.

### Objects provide encapsulation

The main reason is that objects provide encapsulation. When you return values from your database like identifiers, you
might want to make those available read-only to the rest of your application (there is virtually no reason to change
the id of a record). Or if you are storing user passwords, you might want to not expose those at all to the rest of your
program. 

State encapsulation prevents bugs, enables safety and hides unnecessary details from client code.

### Objects provide controlled state mutations

Encapsulation not only lets you choose which information from your database records you want to expose, but also to control
how to change it. Encapsulation in object-oriented programming means you can provide apis to client code to mutate the state
of an object in a controlled and consistent way.

When database records like arrays are exposed, they can be mutated at will and those mutations could be inconsistent. For
instance, take this silly example.

Suppose you have an `user_accounts` table that has a column called `deactivation_date` and another called `is_active`. If
you return this an array and someone wants to deactivate the account, they need specific knowledge to mutate this in a
consistent way: not only they need to set the `deactivation_date` to the current time, but also set the `is_active` column
to false. If one of those parts is omitted, the mutation is inconsistent, and it could cause bugs and side effects in
the application.

If this state is placed in an object instead, and hide property behind private properties, then the object can expose
a `deactivate` method that does the required internal state mutations. There is no way then that the object can end up
in an inconsistent state. Moreover, those controlled state mutations could throw errors when conditions from that state
mutation are not met. For instance, an admin account cannot be deactivated.

The result is a powerful api for client code that cannot be misused.

### Objects have meaning

This leads to the third point, which is that objects give meaning to your application. The name of the object itself and
the names of the methods it exposes, if good, automatically give meaning to the consumers of that code. The api of your
models becomes clear, easy to use and IDE friendly. 

Clarity is one of the most powerful benefits of object-oriented programming.

### Bottom Line

So, when using an ORM you are bringing a complexity that is hard to get your head around. This complexity degrades the
performance of your application. But it gives you the benefit of working with objects that have powerful apis and make
your code safer. 

I honestly believe that both ORMs and plain SQL can be used at the same time. You can use the ORM for the writing / state
changing part of your application and plain SQL for the read part.

The choice is yours. As with everything in software architecture, there are no solutions, only tradeoffs. You need to have
clarity of the tradeoffs you have chosen and why.

# Comparison with Doctrine

This ORM is not as advanced as other data-mapper ORMs like Doctrine. This is intentional. The more features you have,
the more complex and less performant the ORM becomes. 

This is a middle-weigh ORM designed primarily for applications that already have a schema. It does not attempt to take
control of your schema, but deals only with the issue of mapping. That's why it does not have migrations, and types
are unaware of the platform engine or other implementation details.